<script setup>
import HierarchyCategoryProd from "@/Components/HierarchyCategoryProd.vue";
import { onMounted, ref, computed } from "vue";
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
    FunnelIcon,
    TagIcon,
    UserIcon,
    BuildingStorefrontIcon,
    BanknotesIcon,
    CalendarIcon,
    CalendarDaysIcon,
    ShoppingBagIcon,
} from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
// import Pusher from "pusher-js";

const props = defineProps({
    marketplaces: Object,
    comodities: Object,
});

const temp_item = ref({});
const total_data = ref(0);

// Paginate
const current_page = ref(0);
const next_page = ref(null);
const prev_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);

const checkbox = ref([]);
const checkAllButton = ref(false);
const optionMenu = ref(false);

const loading = ref({
    comodity: {},
    marketplace: {},
    refresh: {},
    truncate: {},
    delete: {},
    supervision: {},
});

// Filter Data
const search = ref("");
const filter_marketplace = ref([]);
const filter_comodity = ref([]);
// const date = ref(moment().format("YYYY-MM-DD"));

const isTableEmpty = computed(() => {
    return Object.keys(temp_item.value).length === 0;
});

onMounted(() => {
    getData();
    getTotalData();
});

const getTotalData = async () => {
    axios
        .get(route("temp-item.total_data"))
        .then((response) => {
            total_data.value = response.data;
        })
        .catch((response) => {
            console.log(response);
        });
};

const getData = async (page = null) => {
    loading.value["refresh"][0] = true;
    loading.value["comodity"][0] = true;
    loading.value["marketplace"][0] = true;
    axios
        .get(route("temp-item.data"), {
            params: {
                page: page,
                search: search.value,
                marketplaces: filter_marketplace.value,
                comodities: filter_comodity.value,
                // date: date.value,
                per_page: per_page.value,
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
            prev_page.value = response.data?.prev_page_url;
            from.value = response.data.from;
            to.value = response.data.to;
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value["refresh"][0] = false;
            loading.value["comodity"][0] = false;
            loading.value["marketplace"][0] = false;
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
    <div>
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
                    <button
                        class="btn"
                        onclick="modalFilterMarketplace.showModal()"
                    >
                        Filter Materketplace
                        <FunnelIcon class="w-4 h-4" />
                        <span
                            v-show="filter_marketplace.length > 0"
                            class="text-success"
                            >{{ filter_marketplace.length }}</span
                        >
                    </button>
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
                        {{ checkbox.filter((item) => item.checked).length }}
                        Item Selected
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
                    class="grid grid-cols-1 gap-3 md:flex lg:flex md:justify-between lg:justify-between mb-2"
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
                    <div
                        class="join mt-2 text-center mx-auto"
                        v-show="!isTableEmpty"
                    >
                        <button
                            :disabled="loading['refresh'][0]"
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                            v-show="prev_page != null"
                            @click="getData(current_page - 1)"
                        >
                            «
                        </button>
                        <button
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
                        >
                            {{ current_page }}
                        </button>
                        <button
                            :show="next_page != null"
                            :disabled="loading['refresh'][0]"
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                            @click="getData(current_page + 1)"
                        >
                            »
                        </button>
                    </div>
                    <div class="text-xs md:text-base lg:text-base">
                        Showing {{ from }} to {{ to }} from
                        {{ total_data }} Entries
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
                            </tr>
                        </thead>
                        <tbody v-if="!isTableEmpty">
                            <tr
                                v-for="(item, index) in temp_item"
                                :key="item?.id"
                            >
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
                                <td class="max-w-80">
                                    <div class="flex justify-start gap-3">
                                        <div>
                                            <span class="text-xs flex gap-1">
                                                <BuildingStorefrontIcon
                                                    class="w-3 h-3"
                                                />
                                                {{ item.marketplace.name }}
                                            </span>
                                            <span class="text-xs flex gap-1">
                                                <UserIcon class="w-3 h-3" />
                                                {{ item?.seller }}
                                            </span>
                                            <span class="text-xs flex gap-1">
                                                <MapPinIcon
                                                    class="w-3 h-3 text-red-400"
                                                />{{ item?.location }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-xs flex gap-1">
                                                <BanknotesIcon
                                                    class="w-3 h-3"
                                                />{{
                                                    new Intl.NumberFormat(
                                                        "id-ID",
                                                        {
                                                            style: "currency",
                                                            currency: "IDR",
                                                        },
                                                    ).format(item?.price)
                                                }}
                                            </span>
                                            <span class="text-xs flex gap-1">
                                                <ShoppingBagIcon
                                                    class="w-3 h-3"
                                                />
                                                {{ item?.sold }}
                                                Sold
                                            </span>
                                            <span class="flex text-xs gap-1"
                                                ><StarIcon
                                                    class="h-3 w-3 text-yellow-500"
                                                />{{ item?.rating }}</span
                                            >
                                        </div>
                                        <div v-show="item.flag">
                                            <span class="text-xs flex gap-1">
                                                <FlagIcon
                                                    class="h-3 w-3 text-success"
                                                />
                                                Supervision
                                            </span>
                                            <span class="text-xs flex gap-1">
                                                <CalendarDaysIcon
                                                    class="w-3 h-3"
                                                />{{
                                                    moment(item.updated_at)
                                                        .locale("id")
                                                        .format("DD MMMM YYYY")
                                                }}
                                            </span>
                                        </div>
                                        <div>
                                            <a
                                                href="javascript:;"
                                                class="text-xs flex gap-1 hover:text-blue-500"
                                            >
                                                <EyeIcon class="w-3 h-3" /><span
                                                    >Show Description</span
                                                >
                                            </a>
                                            <span class="text-xs flex gap-1">
                                                <CalendarIcon
                                                    class="w-3 h-3"
                                                />{{
                                                    moment(item.created_at)
                                                        .locale("id")
                                                        .format("DD MMMM YYYY")
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <hr class="my-2" />
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div
                                                class="mask mask-squircle w-12 h-12"
                                            >
                                                <img
                                                    @error="
                                                        $event.target.src =
                                                            '/assets/img/icon/no-image.svg'
                                                    "
                                                    loading="lazy"
                                                    :src="item?.image"
                                                    :alt="`Gambar ${item?.title}`"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">
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
                                <td class="min-w-64">
                                    <HierarchyCategoryProd
                                        :comodity="item.keyword.comodity.name"
                                        :sub_comodity="
                                            item.keyword.sub_comodity
                                        "
                                        :second_level_sub_comodity="
                                            item.keyword
                                                .second_level_sub_comodity
                                        "
                                        :third_level_sub_comodity="
                                            item.keyword
                                                .third_level_sub_comodity
                                        "
                                        :keyword="item.keyword.name"
                                    />
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div
                    class="join mt-2 text-center mx-auto"
                    v-show="!isTableEmpty"
                >
                    <button
                        :disabled="loading['refresh'][0]"
                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                        v-show="prev_page != null"
                        @click="getData(current_page - 1)"
                    >
                        «
                    </button>
                    <button
                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
                    >
                        {{ current_page }}
                    </button>
                    <button
                        :show="next_page != null"
                        :disabled="loading['refresh'][0]"
                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                        @click="getData(current_page + 1)"
                    >
                        »
                    </button>
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
                        :disabled="loading['comodity'][0]"
                        @click="filterData"
                    >
                        Save
                        <span
                            class="loading loading-spinner loading-sm"
                            v-show="loading['comodity'][0]"
                        ></span>
                    </button>
                </div>
            </div>
        </dialog>

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
                                :value="marketplace.id"
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
                        @click="filterData"
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
    </div>
</template>
