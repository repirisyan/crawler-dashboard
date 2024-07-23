<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import moment from "moment";

import {
    StarIcon,
    MapPinIcon,
    MagnifyingGlassIcon,
    ArrowPathIcon,
    FlagIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    marketplaces: Object,
    comodities: Object,
});

const products = ref({});

// Paginate
const current_page = ref(0);
const next_page = ref(null);
const has_next_page = ref(null);
const limit = ref(15);
const from = ref(1);
const to = ref(1);
const total = ref(1);
const last_page = ref(0);

const loading = ref({
    refresh: {},
});

// Filter Data
const search = ref("");
const marketplace = ref("");
const comodity = ref("");

const isTableEmpty = computed(() => {
    return Object.keys(products.value).length === 0;
});

onMounted(() => {
    getData();
});

const getData = async (page = null) => {
    loading.value["refresh"][0] = true;
    axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/product`, {
            params: {
                page: page,
                search: search.value,
                limit: limit.value,
                marketplace: marketplace.value,
                comodity: comodity.value,
            },
        })
        .then((response) => {
            products.value = response.data.docs;
            current_page.value = response.data.page;
            next_page.value = `${import.meta.env.VITE_APP_CRAWLER_APP}?page=${response.data.nextPage}`;
            has_next_page.value = response.data.hasNextPage;
            from.value = (response.data.page - 1) * response.data.limit + 1;
            to.value = response.data.page * response.data.limit;
            total.value = response.data.totalDocs;
            last_page.value = response.data.totalPages;
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value["refresh"][0] = false;
        });
};
</script>
<template>
    <Head title="Indexing Data" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Indexing Data
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-5"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="card">
                            <div
                                class="card-header flex justify-between verflow-x-auto mb-4"
                            >
                                <div
                                    class="grid grid-cols-1 md:flex lg:flex lg:gap-5 mb-5"
                                >
                                    <button
                                        @click="getData"
                                        class="btn btn-sm btn-outline btn-info"
                                        :disabled="loading['refresh'][0]"
                                    >
                                        <span
                                            v-if="!loading['refresh'][0]"
                                            class="flex"
                                            >Refresh&nbsp;<ArrowPathIcon
                                                class="h-3 w-3"
                                        /></span>
                                        <span
                                            v-else
                                            class="loading loading-spinner loading-sm"
                                        ></span>
                                    </button>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:flex lg:flex gap-3 float-end"
                                >
                                    <select
                                        class="select select-bordered"
                                        v-model="comodity"
                                        @change="getData"
                                    >
                                        <option value="">-- Category --</option>
                                        <option
                                            :value="comodity.name"
                                            v-for="comodity in props.comodities"
                                            :key="comodity.id"
                                        >
                                            {{ comodity.name }}
                                        </option>
                                    </select>
                                    <select
                                        class="select select-bordered"
                                        v-model="marketplace"
                                        @change="getData"
                                    >
                                        <option value="">
                                            -- Marketplace --
                                        </option>
                                        <option
                                            :value="marketplace.name"
                                            v-for="marketplace in props.marketplaces"
                                            :key="marketplace.id"
                                        >
                                            {{ marketplace.name }}
                                        </option>
                                    </select>
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto md:w-80 lg:w-80"
                                    >
                                        <input
                                            v-model.lazy="search"
                                            type="text"
                                            class="grow border-0"
                                            @change="getData"
                                            placeholder="Filter Data"
                                        />
                                        <MagnifyingGlassIcon class="h-5 w-5" />
                                    </label>
                                </div>
                            </div>
                            <div
                                v-show="!isTableEmpty"
                                class="grid grid-cols-1 gap-3 md:flex lg:flex md:justify-between lg:justify-between"
                            >
                                <div class="flex gap-3 items-center">
                                    <select
                                        @change="getData"
                                        v-model="limit"
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
                                <div class="text-xs md:text-base lg:text-base">
                                    Showing {{ from }} to {{ to }} from
                                    {{ total }} Entries
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <!-- head -->
                                    <thead class="text-info">
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Rating</th>
                                            <th>Price</th>
                                            <th>Marketplace</th>
                                            <th>Seller</th>
                                            <th>Crawling Date</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="!isTableEmpty">
                                        <tr
                                            v-for="item in products"
                                            :key="item?.id"
                                        >
                                            <td>
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <div class="avatar">
                                                        <div
                                                            class="mask mask-squircle w-12 h-12"
                                                        >
                                                            <img
                                                                :src="
                                                                    item?.image ??
                                                                    '/assets/img/icon/no-image.svg'
                                                                "
                                                                :alt="`Gambar ${item?.title}`"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold">
                                                            <FlagIcon
                                                                class="h-3 w-3 text-success"
                                                                v-show="
                                                                    item.flag
                                                                "
                                                            />
                                                            <a
                                                                class="hover:link hover:link-info"
                                                                :href="
                                                                    item?.link
                                                                "
                                                                target="_blank"
                                                                >{{
                                                                    item?.title
                                                                }}</a
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{ item.comodity }}
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <span class="flex"
                                                    ><StarIcon
                                                        class="h-4 w-4 text-yellow-500"
                                                    />&nbsp;{{
                                                        item?.rating
                                                    }}</span
                                                >
                                                <span
                                                    class="badge badge-ghost badge-sm"
                                                    >{{ item?.sold }} Sold</span
                                                >
                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{
                                                    new Intl.NumberFormat(
                                                        "id-ID",
                                                        {
                                                            style: "currency",
                                                            currency: "IDR",
                                                        },
                                                    ).format(item?.price)
                                                }}
                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{ item.marketplace }}
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <div class="font-bold">
                                                    {{ item?.seller }}
                                                </div>
                                                <div class="flex">
                                                    <MapPinIcon
                                                        class="w-5 h-5 text-red-400"
                                                    />&nbsp;{{ item?.location }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{
                                                    moment(item.created_at)
                                                        .locale("id")
                                                        .format("DD MMMM YYYY")
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                No Data Available
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot
                                        class="text-info"
                                        v-show="!isTableEmpty"
                                    >
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Rating</th>
                                            <th>Price</th>
                                            <th>Marketplace</th>
                                            <th>Seller</th>
                                            <th>Crawling Date</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div
                                class="join mt-2 mx-auto"
                                v-show="!isTableEmpty"
                            >
                                <button
                                    :disabled="loading['refresh'][0]"
                                    type="button"
                                    @click="getData(1)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    v-if="current_page > 10"
                                >
                                    1
                                </button>
                                <button
                                    :disabled="loading['refresh'][0]"
                                    type="button"
                                    @click="getData(current_page - 2)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    v-if="current_page - 2 > 0"
                                >
                                    {{ current_page - 2 }}
                                </button>
                                <button
                                    :disabled="loading['refresh'][0]"
                                    type="button"
                                    @click="getData(current_page - 1)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    v-if="current_page - 1 > 0"
                                >
                                    {{ current_page - 1 }}
                                </button>
                                <button
                                    type="button"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
                                >
                                    {{ current_page ?? "" }}
                                </button>
                                <button
                                    :disabled="loading['refresh'][0]"
                                    @click="getData(current_page + 1)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    v-show="has_next_page"
                                >
                                    {{ current_page + 1 }}
                                </button>
                                <button
                                    :disabled="loading['refresh'][0]"
                                    @click="getData(current_page + 2)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    v-show="current_page + 2 <= last_page"
                                >
                                    {{ current_page + 2 }}
                                </button>
                                <button
                                    v-show="current_page < last_page - 2"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
                                >
                                    ...
                                </button>
                                <button
                                    v-show="current_page < last_page - 3"
                                    @click="getData(last_page - 1)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                >
                                    {{ last_page - 1 }}
                                </button>
                                <button
                                    v-show="current_page < last_page - 2"
                                    @click="getData(last_page)"
                                    class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                >
                                    {{ last_page }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
