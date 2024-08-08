<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Paginate from "./Paginate.vue";
import HierarchyCategory from "@/Components/HierarchyCategory.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import moment from "moment";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    TagIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    keywords: Object,
    comodities: Object,
    params: Object,
});

const filter_comodity = ref(props.params?.comodity ?? []);
const search = ref(props.params?.search ?? "");
const filter_status = ref(props.params?.status ?? "");
const per_page = ref(props.params?.per_page ?? 15);

const loadingStates = ref({
    comodity: {},
    status: {},
});

const isTableEmpty = computed(() => {
    return Object.keys(props.keywords.data).length === 0;
});

const filterData = () => {
    loadingStates.value["comodity"][0] = true;
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
            onFinish: () => {
                loadingStates.value["comodity"][0] = false;
            },
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
                                    <button
                                        class="btn"
                                        onclick="modalFilterCategory.showModal()"
                                    >
                                        Filter Category
                                        <FunnelIcon class="w-4 h-4" />
                                        <span
                                            v-show="filter_comodity.length > 0"
                                            class="text-success"
                                            >{{ filter_comodity.length }}</span
                                        >
                                    </button>
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
                                    class="grid grid-cols-1 gap-3 md:flex lg:flex md:justify-between lg:justify-between mb-2"
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
                                    <Paginate
                                        v-show="!isTableEmpty"
                                        :search="search"
                                        :per_page="per_page"
                                        :comodity="filter_comodity"
                                        :status="filter_status"
                                        :current_page="
                                            props.keywords.current_page
                                        "
                                        :last_page="props.keywords.last_page"
                                        :next_page_url="
                                            props.keywords.next_page_url
                                        "
                                    />
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
                                                    <HierarchyCategory
                                                        :comodity="
                                                            item.comodity.name
                                                        "
                                                        :sub_comodity="
                                                            item.sub_comodity
                                                        "
                                                        :second_level_sub_comodity="
                                                            item.second_level_sub_comodity
                                                        "
                                                        :third_level_sub_comodity="
                                                            item.third_level_sub_comodity
                                                        "
                                                    />
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
                                        <tfoot
                                            class="text-info"
                                            v-show="!isTableEmpty"
                                        >
                                            <tr>
                                                <th>Category</th>
                                                <th>Keyword</th>
                                                <th>Status</th>
                                                <th>Last Update</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <Paginate
                                    v-show="!isTableEmpty"
                                    :search="search"
                                    :per_page="per_page"
                                    :comodity="filter_comodity"
                                    :status="filter_status"
                                    :current_page="props.keywords.current_page"
                                    :last_page="props.keywords.last_page"
                                    :next_page_url="
                                        props.keywords.next_page_url
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <dialog id="modalFilterCategory" class="modal">
            <div class="modal-box w-11/12 max-w-4xl">
                <form method="dialog">
                    <button
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                    >
                        ✕
                    </button>
                </form>
                <div class="flex gap-3">
                    <h3 class="text-lg font-bold align-middle flex gap-3">
                        <TagIcon class="w-5 h-5 my-auto" /> Filter Category
                    </h3>
                </div>
                <div class="divider"></div>
                <div class="grid grid-cols-4 gap-4">
                    <div
                        class="form-control border border-solid rounded-md border-teal-700"
                        v-for="comodity in props.comodities"
                        :key="comodity.id"
                    >
                        <label class="cursor-pointer label">
                            <span class="label-text">{{ comodity.name }}</span>
                            <input
                                :id="`checkbox_comodity${comodity.id}`"
                                type="checkbox"
                                :value="comodity.id"
                                v-model="filter_comodity"
                                class="checkbox checkbox-success"
                            />
                        </label>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="modal-action">
                    <button
                        class="btn btn-outline btn-success btn-sm"
                        :disabled="loadingStates['comodity'][0]"
                        @click="filterData"
                    >
                        Save
                        <span
                            class="loading loading-spinner loading-sm"
                            v-show="loadingStates['comodity'][0]"
                        ></span>
                    </button>
                </div>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
