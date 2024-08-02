<script setup>
import { onMounted, ref, computed, watch } from "vue";
import moment from "moment";
import { Link } from "@inertiajs/vue3";
import {
    StarIcon,
    MapPinIcon,
    MagnifyingGlassIcon,
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    FlagIcon,
} from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
// import Pusher from "pusher-js";

const props = defineProps({
    marketplaces: Object,
    comodities: Object,
});

const temp_item = ref({});

// Paginate
const current_page = ref(0);
const next_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);
const total = ref(1);
const last_page = ref(0);

const checkbox = ref([]);
const checkAllButton = ref(false);
const optionMenu = ref(false);

const loading = ref({
    refresh: {},
    truncate: {},
    delete: {},
    supervision: {},
});

// Realtime Data
// const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
// });
// const channel = pusher.subscribe("crawler-channel");
// channel.bind("refreshData", () => {
//     getData();
// });

// Filter Data
const search = ref("");
const marketplace_id = ref("");
const comodity_id = ref("");
// const date = ref(moment().format("YYYY-MM-DD"));

const isTableEmpty = computed(() => {
    return Object.keys(temp_item.value).length === 0;
});

onMounted(() => {
    getData();
});

const getData = async (page = null) => {
    loading.value["refresh"][0] = true;
    axios
        .get(route("temp-item.data"), {
            params: {
                page: page,
                search: search.value,
                marketplace_id: marketplace_id.value,
                comodity_id: comodity_id.value,
                // date: date.value,
                entries: per_page.value,
            },
        })
        .then((response) => {
            // Setting Checkbox
            checkbox.value.length = 0;
            optionMenu.value = false;
            checkAllButton.value = false;
            response.data.data.forEach((element) => {
                checkbox.value.push({ id: element.id, checked: false });
            });

            temp_item.value = response.data.data;
            current_page.value = response.data.current_page;
            next_page.value = response.data.next_page_url;
            from.value = response.data.from;
            to.value = response.data.to;
            total.value = response.data.total;
            last_page.value = response.data.last_page;
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value["refresh"][0] = false;
        });
};

const deleteItem = () => {
    if (confirm("Apa anda yakin ingin menghapus data ini ?")) {
        loading.value["delete"][0] = true;
        const checkedItems = computed(() => {
            return checkbox.value
                .filter((item) => item.checked)
                .map((item) => item.id);
        });
        axios
            .delete(route("temp-item.delete"), {
                data: { ids: checkedItems.value },
            })
            .catch((response) => {
                console.log(response.data);
            })
            .finally(() => {
                getData();
                loading.value["delete"][0] = false;
            });
    }
};

const supervisionItem = () => {
    if (confirm("Lakukan pengawasan terhadap produk ini ?")) {
        loading.value["supervision"][0] = true;
        const checkedItems = computed(() => {
            return checkbox.value
                .filter((item) => item.checked)
                .map((item) => item.id);
        });
        axios
            .post(route("supervision.store"), {
                ids: checkedItems.value,
            })
            .then((response) => {
                toast.success(response.data);
            })
            .catch((response) => {
                console.log(response);
            })
            .finally(() => {
                getData();
                loading.value["supervision"][0] = false;
            });
    }
};

const checkOptionMenu = () => {
    const isAtLeastOneChecked = computed(() => {
        return checkbox.value.some((item) => item.checked);
    });
    optionMenu.value = isAtLeastOneChecked.value;
};

const checkAll = (event) => {
    optionMenu.value = event.target.checked;
    checkAllButton.value = event.target.checked;
    checkbox.value.forEach((element) => {
        element.checked = event.target.checked;
    });
};
</script>
<template>
    <div class="card">
        <div class="card-header flex justify-between verflow-x-auto">
            <div class="grid grid-cols-1 md:flex lg:flex lg:gap-5 mb-5">
                <button
                    @click="getData"
                    class="btn btn-sm btn-outline btn-info"
                    :disabled="loading['refresh'][0]"
                >
                    <span v-if="!loading['refresh'][0]" class="flex"
                        >Refresh&nbsp;<ArrowPathIcon class="h-3 w-3"
                    /></span>
                    <span
                        v-else
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
            </div>
            <div class="grid grid-cols-1 md:flex lg:flex gap-3 float-end">
                <!-- <label
                    class="input input-bordered items-center flex gap-2 input-md w-auto"
                >
                    <input
                        v-model="date"
                        type="date"
                        class="grow border-0"
                        @change="getData"
                    />
                </label> -->
                <select
                    class="select select-bordered"
                    v-model="comodity_id"
                    @change="getData"
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
                <select
                    class="select select-bordered"
                    v-model="marketplace_id"
                    @change="getData"
                >
                    <option value="">-- Marketplace --</option>
                    <option
                        :value="marketplace.id"
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
                        placeholder="Search"
                    />
                    <MagnifyingGlassIcon class="h-5 w-5" />
                </label>
            </div>
        </div>
        <div class="card-body">
            <div
                class="grid grid-cols-1 md:block md:gap-5 lg:block lg:gap-5 mb-5"
                v-show="optionMenu"
            >
                <span class="text-sm mr-3">
                    {{ checkbox.filter((item) => item.checked).length }} Item
                    Selected
                </span>
                <button
                    v-show="false"
                    class="btn btn-error btn-sm btn-outline mr-3"
                    :disabled="loading['delete'][0]"
                    @click="deleteItem"
                >
                    <TrashIcon
                        v-if="!loading['delete'][0]"
                        class="h-3 w-3"
                    /><span
                        v-else
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <button
                    class="btn btn-success btn-sm btn-outline"
                    :disabled="loading['supervision'][0]"
                    @click="supervisionItem"
                >
                    <EyeIcon
                        v-if="!loading['supervision'][0]"
                        class="h-3 w-3"
                    /><span
                        v-else
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
            </div>
            <div
                v-show="!isTableEmpty"
                class="grid grid-cols-1 gap-3 md:flex lg:flex md:justify-between lg:justify-between"
            >
                <div class="flex gap-3 items-center">
                    <select
                        @change="getData"
                        v-model="per_page"
                        class="select select-bordered select-sm max-w-xs text-xs"
                    >
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="text-xs md:text-base lg:text-base"
                        >entries per page</label
                    >
                </div>
                <div class="text-xs md:text-base lg:text-base">
                    Showing {{ from }} to {{ to }} from {{ total }} Entries
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <!-- head -->
                    <thead class="text-info">
                        <tr>
                            <th>
                                <label v-if="!isTableEmpty">
                                    <input
                                        :checked="checkAllButton"
                                        @change="checkAll"
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                    />
                                </label>
                            </th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Sold</th>
                            <th>Price</th>
                            <th>Marketplace</th>
                            <th>Seller</th>
                            <th>Crawling Date</th>
                        </tr>
                    </thead>
                    <tbody v-if="!isTableEmpty">
                        <tr v-for="(item, index) in temp_item" :key="item?.id">
                            <th>
                                <label>
                                    <input
                                        v-model="checkbox[index].checked"
                                        @change="
                                            checkbox[index].checked =
                                                $event.target.checked;
                                            checkOptionMenu();
                                        "
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                    />
                                </label>
                            </th>
                            <td>
                                <div class="flex items-center gap-3">
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
                                                v-show="item.flag"
                                            />
                                            <a
                                                class="hover:link hover:link-info"
                                                :href="item?.link"
                                                target="_blank"
                                                >{{ item?.title }}</a
                                            >
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <ul class="menu rounded-box w-auto">
                                    <li>
                                        <a>{{ item.keyword.comodity.name }}</a>
                                        <ul v-show="item.keyword.sub_comodity">
                                            <li>
                                                <a>{{
                                                    item.keyword.sub_comodity
                                                }}</a>
                                                <ul
                                                    v-show="
                                                        item.keyword
                                                            .second_level_sub_comodity
                                                    "
                                                >
                                                    <li>
                                                        <a>{{
                                                            item.keyword
                                                                .second_level_sub_comodity
                                                        }}</a>
                                                        <ul
                                                            v-show="
                                                                item.keyword
                                                                    .third_level_sub_comodity
                                                            "
                                                        >
                                                            <li>
                                                                <a>{{
                                                                    item.keyword
                                                                        .third_level_sub_comodity
                                                                }}</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="flex"
                                    ><StarIcon
                                        class="h-4 w-4 text-yellow-500"
                                    />&nbsp;{{ item?.rating }}</span
                                >
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="badge badge-ghost badge-sm"
                                    >{{ item?.sold }} Sold</span
                                >
                            </td>
                            <td class="whitespace-nowrap">
                                {{
                                    new Intl.NumberFormat("id-ID", {
                                        style: "currency",
                                        currency: "IDR",
                                    }).format(item?.price)
                                }}
                            </td>
                            <td class="whitespace-nowrap">
                                {{ item.marketplace.name }}
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="font-bold">{{ item?.seller }}</div>
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
                            <td colspan="9" class="text-center">
                                No Data Available
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="text-info" v-show="!isTableEmpty">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Sold</th>
                            <th>Price</th>
                            <th>Marketplace</th>
                            <th>Seller</th>
                            <th>Crawling Date</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="join mt-2 mx-auto" v-show="!isTableEmpty">
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
                    v-if="next_page != null"
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
</template>
