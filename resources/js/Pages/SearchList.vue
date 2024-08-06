<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import moment from "moment";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    keywords: Object,
    comodities: Object,
    params: Object,
});

const filter_comodity = ref(props.params?.comodity ?? "");
const search = ref(props.params?.search ?? "");
const filter_status = ref(props.params?.status ?? "");
const per_page = ref(props.params?.per_page ?? 15);

const loadingStates = ref({
    status: {},
});

const isTableEmpty = computed(() => {
    return Object.keys(props.keywords.data).length === 0;
});

const filterData = () => {
    router.visit(
        route("search-list.index", {
            comodity: filter_comodity.value,
            search: search.value,
            status: filter_status.value,
            page: 1,
        }),
        {
            preserveScroll: true,
            only: ["keywords", "params"],
        },
    );
};

const toggleStatus = (id, status) => {
    loadingStates.value["status"][id] = true;
    axios
        .patch(route("search-list.change_status", id), {
            status: status,
        })
        .then(() => {
            router.reload({ only: ["keywords"] });
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loadingStates.value["status"][id] = false;
        });
};
</script>

<template>
    <Head title="Search List" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="breadcrumbs font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                <ul>
                    <li>Master Data</li>
                    <li>Search List</li>
                </ul>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="card mx-auto">
                            <div class="card-header flex justify-between">
                                <div></div>
                                <div class="flex gap-3">
                                    <select
                                        class="select select-bordered"
                                        v-model.lazy="filter_status"
                                        @change="filterData"
                                    >
                                        <option value="">-- Status --</option>
                                        <option value="1">Active</option>
                                        <option value="0">Non Active</option>
                                    </select>
                                    <select
                                        class="select select-bordered"
                                        v-model.lazy="filter_comodity"
                                        @change="filterData"
                                    >
                                        <option value="">-- Category --</option>
                                        <option
                                            :value="comodity.id"
                                            v-for="comodity in props.comodities"
                                            :key="comodity.id"
                                        >
                                            {{ comodity.name }}
                                        </option>
                                    </select>
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto md:w-80 lg:w-80"
                                    >
                                        <input
                                            v-model.lazy="search"
                                            type="text"
                                            class="grow border-0"
                                            @change="filterData"
                                            placeholder="Search Keyword"
                                        />
                                        <MagnifyingGlassIcon class="h-5 w-5" />
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div
                                    v-show="!isTableEmpty"
                                    class="grid grid-cols-1 gap-3 md:flex lg:flex md:justify-between lg:justify-between"
                                >
                                    <div class="flex gap-3 items-center">
                                        <select
                                            @change="filterData"
                                            v-model.lazy="per_page"
                                            class="select select-bordered select-sm max-w-xs text-xs"
                                        >
                                            <option value="15">15</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <label
                                            class="text-xs md:text-base lg:text-base"
                                            >entries per page</label
                                        >
                                    </div>
                                    <div
                                        class="text-xs md:text-base lg:text-base"
                                    >
                                        Showing {{ props.keywords.from }} to
                                        {{ props.keywords.to }} from
                                        {{ props.keywords.total }} Entries
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead class="text-info">
                                            <tr>
                                                <th>Category</th>
                                                <th>Keyword</th>
                                                <th>Status</th>
                                                <th>Last Update</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="!isTableEmpty">
                                            <tr
                                                v-for="item in props.keywords
                                                    .data"
                                                :key="item.id"
                                            >
                                                <td>
                                                    <ul
                                                        class="menu rounded-box w-auto"
                                                    >
                                                        <li>
                                                            <a>{{
                                                                item.comodity
                                                                    .name
                                                            }}</a>
                                                            <ul
                                                                v-show="
                                                                    item.sub_comodity
                                                                "
                                                            >
                                                                <li>
                                                                    <a>{{
                                                                        item.sub_comodity
                                                                    }}</a>
                                                                    <ul
                                                                        v-show="
                                                                            item.second_level_sub_comodity
                                                                        "
                                                                    >
                                                                        <li>
                                                                            <a
                                                                                >{{
                                                                                    item.second_level_sub_comodity
                                                                                }}</a
                                                                            >
                                                                            <ul
                                                                                v-show="
                                                                                    item.third_level_sub_comodity
                                                                                "
                                                                            >
                                                                                <li>
                                                                                    <a
                                                                                        >{{
                                                                                            item.third_level_sub_comodity
                                                                                        }}</a
                                                                                    >
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td>{{ item.name }}</td>
                                                <td>
                                                    <a
                                                        @click="
                                                            toggleStatus(
                                                                item.id,
                                                                item.status,
                                                            )
                                                        "
                                                        href="javascript:;"
                                                        role="button"
                                                        class="badge badge-sm block"
                                                        :class="
                                                            item.status
                                                                ? 'badge-success'
                                                                : 'badge-error'
                                                        "
                                                    >
                                                        <span
                                                            v-if="
                                                                !loadingStates[
                                                                    'status'
                                                                ][item.id]
                                                            "
                                                        >
                                                            {{
                                                                item.status
                                                                    ? "Active"
                                                                    : "Non Active"
                                                            }}
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="loading loading-spinner loading-xs"
                                                        ></span>
                                                    </a>
                                                </td>
                                                <td>
                                                    {{
                                                        moment(item.updated_at)
                                                            .locale("id")
                                                            .format(
                                                                "DD MMMM YYYY HH:mm",
                                                            )
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td
                                                    colspan="4"
                                                    class="text-center"
                                                >
                                                    No Data Available
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div
                                    class="join mt-2 mx-auto"
                                    v-show="!isTableEmpty"
                                >
                                    <Link
                                        :href="`${route('search-list.index')}?page=1&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page > 10
                                        "
                                    >
                                        1
                                    </Link>
                                    <Link
                                        :href="`${route('search-list.index')}?page=${props.keywords.current_page - 2}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page - 2 > 0
                                        "
                                    >
                                        {{ props.keywords.current_page - 2 }}
                                    </Link>
                                    <Link
                                        :href="`${route('search-list.index')}?page=${props.keywords.current_page - 1}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page - 1 > 0
                                        "
                                    >
                                        {{ props.keywords.current_page - 1 }}
                                    </Link>
                                    <button
                                        type="button"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
                                    >
                                        {{ props.keywords.current_page ?? "" }}
                                    </button>
                                    <Link
                                        :href="`${route('search-list.index')}?page=${props.keywords.current_page + 1}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.next_page_url != null
                                        "
                                    >
                                        {{ props.keywords.current_page + 1 }}
                                    </Link>
                                    <Link
                                        :href="`${route('search-list.index')}?page=${props.keywords.current_page + 2}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page + 2 <=
                                            props.keywords.last_page
                                        "
                                    >
                                        {{ props.keywords.current_page + 2 }}
                                    </Link>
                                    <button
                                        v-show="
                                            props.keywords.current_page <
                                            props.keywords.last_page - 2
                                        "
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
                                    >
                                        ...
                                    </button>
                                    <Link
                                        v-show="
                                            props.keywords.current_page <
                                            props.keywords.last_page - 3
                                        "
                                        :href="`${route('search-list.index')}?page=${props.keywords.last_page - 1}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    >
                                        {{ props.keywords.last_page - 1 }}
                                    </Link>
                                    <Link
                                        v-show="
                                            props.keywords.current_page <
                                            props.keywords.last_page - 2
                                        "
                                        :href="`${route('search-list.index')}?page=${props.keywords.last_page}&search=${search}&per_page=${per_page}&comodity=${filter_comodity}&status=${filter_status}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    >
                                        {{ props.keywords.last_page }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
