<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PaginateAPI from "@/Components/PaginateAPI.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import moment from "moment";

import {
    MagnifyingGlassIcon,
    ArrowPathIcon,
    FunnelIcon,
    TagIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    marketplaces: Object,
});

const products = ref({});

const auth = usePage();

// Paginate
const current_page = ref(0);
const next_page = ref(null);
const has_next_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);
const total = ref(1);
const last_page = ref(0);

const loading = ref({
    refresh: {},
    marketplace: {},
});

// Filter Data
const search = ref("");
const filter_marketplace = ref([]);
const filter_date_from = ref(null);
const filter_date_until = ref(null);

const isTableEmpty = computed(() => {
    return Object.keys(products.value).length === 0;
});

onMounted(() => {
    getData();
});

const getData = async (page = 1) => {
    loading.value["refresh"][0] = true;
    loading.value["marketplace"][0] = true;
    axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/trending-product`, {
            headers: {
                Authorization: `Bearer ${auth.props.auth.user.remember_token}`,
            },
            params: {
                page: page,
                search: search.value,
                per_page: per_page.value,
                marketplaces: JSON.stringify(filter_marketplace.value),
                date_from: filter_date_from.value,
                date_until: filter_date_until.value,
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
            loading.value["marketplace"][0] = false;
        });
};
</script>
<template>
    <Head title="Trending Data" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Trending Data
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
                                class="card-header flex justify-between overflow-x-auto mb-4"
                            >
                                <div
                                    class="grid grid-cols-1 md:flex lg:flex lg:gap-5 mb-5"
                                >
                                    <button
                                        @click="getData(1)"
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
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto"
                                    >
                                        <div class="label">
                                            <span class="label-text">From</span>
                                        </div>
                                        <input
                                            v-model="filter_date_from"
                                            type="date"
                                            class="grow border-0"
                                            @change="getData(1)"
                                        />
                                    </label>
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto"
                                    >
                                        <div class="label">
                                            <span class="label-text"
                                                >Until</span
                                            >
                                        </div>
                                        <input
                                            v-model="filter_date_until"
                                            type="date"
                                            class="grow border-0"
                                            @change="getData(1)"
                                        />
                                    </label>
                                    <button
                                        class="btn"
                                        onclick="modalFilterMarketplace.showModal()"
                                    >
                                        Filter Materketplace
                                        <FunnelIcon class="w-4 h-4" />
                                        <span
                                            v-show="
                                                filter_marketplace.length > 0
                                            "
                                            class="text-success"
                                            >{{
                                                filter_marketplace.length
                                            }}</span
                                        >
                                    </button>
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto md:w-80 lg:w-80"
                                    >
                                        <input
                                            v-model.lazy="search"
                                            type="text"
                                            class="grow border-0"
                                            @change="getData(1)"
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
                                        @change="getData(1)"
                                        v-model="per_page"
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
                                            <th>Sold</th>
                                            <th>Marketplace</th>
                                            <th>Date</th>
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
                                                                    item?.imageURL ??
                                                                    '/assets/img/icon/no-image.svg'
                                                                "
                                                                :alt="`Gambar ${item?.keyword}`"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold">
                                                            <a
                                                                class="hover:link hover:link-info"
                                                                :href="
                                                                    item?.url
                                                                "
                                                                target="_blank"
                                                                >{{
                                                                    item?.keyword
                                                                }}</a
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <span
                                                    class="badge badge-ghost badge-sm"
                                                    >{{
                                                        item?.productCount
                                                    }}
                                                    Sold</span
                                                >
                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{ item.marketplace }}
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
                                            <td colspan="4" class="text-center">
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
                                            <th>Sold</th>
                                            <th>Marketplace</th>
                                            <th>Date</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <PaginateAPI
                                v-show="!isTableEmpty"
                                @get-data="getData"
                                :loading="loading['refresh'][0]"
                                :last_page="last_page"
                                :current_page="current_page"
                                :has_next_page="has_next_page"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <dialog id="modalFilterMarketplace" class="modal">
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
                        <TagIcon class="w-5 h-5 my-auto" /> Filter Marketplace
                    </h3>
                </div>
                <div class="divider"></div>
                <div class="grid grid-cols-4 gap-4">
                    <div
                        class="form-control border border-solid rounded-md border-teal-700"
                        v-for="marketplace in props.marketplaces"
                        :key="marketplace.id"
                    >
                        <label class="cursor-pointer label">
                            <span class="label-text">{{
                                marketplace.name
                            }}</span>
                            <input
                                :id="`checkbox_marketplace${marketplace.id}`"
                                type="checkbox"
                                :value="marketplace.name"
                                v-model="filter_marketplace"
                                class="checkbox checkbox-success"
                            />
                        </label>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="modal-action">
                    <button
                        class="btn btn-outline btn-success btn-sm"
                        :disabled="loading['marketplace'][0]"
                        @click="getData(1)"
                    >
                        Save
                        <span
                            class="loading loading-spinner loading-sm"
                            v-show="loading['marketplace'][0]"
                        ></span>
                    </button>
                </div>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
