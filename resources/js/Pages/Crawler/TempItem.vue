<script setup>
import HierarchyCategoryProd from "@/Components/HierarchyCategoryProd.vue";
import SimplePaginateAPI from "@/Components/SimplePaginateAPI.vue";
import PerPage from "@/Components/PerPage.vue";
import { onMounted, ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
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
    CalendarIcon,
    CalendarDaysIcon,
    ShoppingBagIcon,
    ScaleIcon,
    CheckIcon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import VueEasyLightbox from "vue-easy-lightbox";
// import Pusher from "pusher-js";

const props = defineProps({
    marketplaces: Object,
    comodities: Object,
});

const auth = usePage();

const temp_item = ref({});
const total_data = ref(0);
const description = ref("");

// Image Gallery
const visibleImgGallery = ref(false);
const indexRef = ref(0); // default 0
const imgsRef = ref([]);

// Paginate
const current_page = ref(1);
const next_page = ref(null);
const prev_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);

const checkbox = ref([]);
const checkAllButton = ref(false);
const optionMenu = ref(false);

const loading = ref({
    data: {},
    comodity: {},
    marketplace: {},
    refresh: {},
    truncate: {},
    delete: {},
    supervision: {},
    certificate: {},
});

// Filter Data
const search = ref("");
const filter_marketplace = ref([]);
const filter_comodity = ref([]);
const filter_certificate = ref([]);

const certificate = ["BPOM", "SNI", "No Reg Produk", "Halal"];

// const date = ref(moment().format("YYYY-MM-DD"));

const isTableEmpty = computed(() => {
    return Object.keys(temp_item.value).length === 0;
});

onMounted(async () => {
    loading.value["data"][0] = true;
    await getTotalData();
    await getData();
    loading.value["data"][0] = false;
});

const getTotalData = async () => {
    await axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/temp-item/total`, {
            headers: {
                Authorization: `Bearer ${auth.props.auth.user.remember_token}`,
            },
        })
        .then((response) => {
            total_data.value = response.data;
        })
        .catch((response) => {
            console.log(response);
        });
};

const getData = async (page = 1) => {
    loading.value["refresh"][0] = true;
    loading.value["comodity"][0] = true;
    loading.value["marketplace"][0] = true;
    loading.value["certificate"][0] = true;

    axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/temp-item`, {
            headers: {
                Authorization: `Bearer ${auth.props.auth.user.remember_token}`,
            },
            params: {
                page: page,
                search: search.value,
                marketplaces: JSON.stringify(filter_marketplace.value),
                comodities: JSON.stringify(filter_comodity.value),
                certificates: JSON.stringify(filter_certificate.value),
                per_page: per_page.value,
            },
        })
        .then((response) => {
            // Setting Checkbox
            checkbox.value.length = 0;
            optionMenu.value = false;
            checkAllButton.value = false;
            response.data.forEach((element) => {
                checkbox.value.push({ id: element._id, checked: false });
            });
            const totalPages = Math.ceil(total_data.value / per_page.value);
            const skip = (page - 1) * per_page.value;
            current_page.value = page;
            next_page.value =
                current_page.value < totalPages ? current_page.value + 1 : null;
            prev_page.value =
                current_page.value > 1 ? current_page.value - 1 : null;
            from.value = skip + 1;
            to.value = skip + response.data.length;
            temp_item.value = response.data;
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value["refresh"][0] = false;
            loading.value["comodity"][0] = false;
            loading.value["marketplace"][0] = false;
            loading.value["certificate"][0] = false;
        });
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
            .post(
                `${import.meta.env.VITE_APP_CRAWLER_API}/supervision`,
                {
                    ids: checkedItems.value,
                },
                {
                    headers: {
                        Authorization: `Bearer ${auth.props.auth.user.remember_token}`,
                    },
                },
            )
            .then((response) => {
                toast.success(response.data.message || "Operation successful");
            })
            .catch((error) => {
                console.error("Error:", error);
                toast.error(
                    error.response?.data?.message || "An error occurred",
                );
            })
            .finally(() => {
                getData(current_page.value);
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

const showGallery = (images) => {
    if (images.length > 1) {
        visibleImgGallery.value = true;
        imgsRef.value = images;
    }
};
</script>
<template>
    <div>
        <div class="card">
            <div class="card-header flex justify-between overflow-x-auto">
                <div class="grid grid-cols-1 md:flex lg:flex lg:gap-5 mb-5">
                    <button
                        @click="getData(current_page)"
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
                        onclick="modalFilterCertificate.showModal()"
                    >
                        Filter Certificate
                        <FunnelIcon class="w-4 h-4" />
                    </button>
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
                            @change="getData(1)"
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
                    <PerPage v-model:per_page="per_page" @get-data="getData" />
                    <SimplePaginateAPI
                        @get-data="getData"
                        :loading="loading['refresh'][0]"
                        :prev_page="prev_page"
                        :next_page="next_page"
                        :current_page="current_page"
                    />
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
                                <th>Product</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody v-if="!isTableEmpty">
                            <tr v-for="(item, index) in temp_item" :key="index">
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
                                <td class="w-auto">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div
                                                class="rounded w-24 h-24"
                                                :class="
                                                    item.image.large.length > 1
                                                        ? 'ring-info ring-offset-base-100 ring ring-offset-2'
                                                        : ''
                                                "
                                            >
                                                <a
                                                    href="javascript:;"
                                                    :class="
                                                        item.image.large
                                                            .length > 1
                                                            ? 'cursor-pointer'
                                                            : 'cursor-default'
                                                    "
                                                    @click="
                                                        showGallery(
                                                            item.image.large,
                                                        )
                                                    "
                                                >
                                                    <img
                                                        @error="
                                                            $event.target.src =
                                                                '/assets/img/icon/no-image.svg'
                                                        "
                                                        loading="lazy"
                                                        :src="
                                                            item?.image.small[0]
                                                        "
                                                        :alt="`Gambar ${item?.title}`"
                                                    />
                                                </a>
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
                                            <div
                                                class="font-extrabold text-lg"
                                                v-if="item.price?.discount"
                                            >
                                                {{
                                                    new Intl.NumberFormat(
                                                        "id-ID",
                                                        {
                                                            style: "currency",
                                                            currency: "IDR",
                                                        },
                                                    ).format(
                                                        (item.price
                                                            .original_price *
                                                            item.price
                                                                .discount) /
                                                            100,
                                                    )
                                                }}
                                            </div>
                                            <div
                                                v-else
                                                class="font-extrabold text-lg"
                                            >
                                                {{
                                                    new Intl.NumberFormat(
                                                        "id-ID",
                                                        {
                                                            style: "currency",
                                                            currency: "IDR",
                                                        },
                                                    ).format(item.price.price)
                                                }}
                                            </div>
                                            <div
                                                class="font-extrabold text-xs flex gap-2"
                                                v-show="item.price?.discount"
                                            >
                                                <span
                                                    class="line-through text-gray-400"
                                                    >{{
                                                        new Intl.NumberFormat(
                                                            "id-ID",
                                                            {
                                                                style: "currency",
                                                                currency: "IDR",
                                                            },
                                                        ).format(
                                                            item.price
                                                                .original_price,
                                                        )
                                                    }}</span
                                                >
                                                <span class="text-error"
                                                    >{{
                                                        item.price?.discount
                                                    }}%</span
                                                >
                                            </div>
                                            <div
                                                class="flex gap-3 mt-1 align-middle"
                                            >
                                                <span
                                                    class="text-xs flex gap-1"
                                                >
                                                    <img
                                                        class="max-w-14 max-h-4"
                                                        :src="`/assets/img/marketplace/${item.marketplace.toLowerCase()}.svg`"
                                                        v-if="
                                                            item.marketplace !=
                                                            'OLX'
                                                        "
                                                    />
                                                    <span
                                                        v-else
                                                        class="flex gap-1"
                                                    >
                                                        <BuildingStorefrontIcon
                                                            class="w-3 h-3"
                                                        />{{ item.marketplace }}
                                                    </span>
                                                </span>
                                                <span
                                                    class="text-xs flex gap-1"
                                                >
                                                    <CalendarIcon
                                                        class="w-3 h-3"
                                                    />{{
                                                        moment(item.created_at)
                                                            .locale("id")
                                                            .format(
                                                                "DD MMMM YYYY",
                                                            )
                                                    }}
                                                </span>
                                                <span
                                                    class="text-xs flex gap-1"
                                                    v-show="item.brand"
                                                >
                                                    <TagIcon
                                                        class="w-3 h-3"
                                                    />{{ item.brand }}
                                                </span>
                                            </div>
                                            <div class="mt-1 flex gap-3">
                                                <a
                                                    target="_blank"
                                                    :href="
                                                        item.seller.url ??
                                                        'javascript:;'
                                                    "
                                                    class="text-xs flex gap-1"
                                                    :class="
                                                        item.seller.url
                                                            ? 'text-blue-500 hover:link'
                                                            : ''
                                                    "
                                                >
                                                    <UserIcon class="w-3 h-3" />
                                                    {{ item.seller.name }}
                                                </a>
                                                <span
                                                    class="text-xs flex gap-1"
                                                >
                                                    <MapPinIcon
                                                        class="w-3 h-3 text-red-400"
                                                    /><span
                                                        v-show="
                                                            item.location
                                                                ?.province
                                                        "
                                                        >{{
                                                            item.location
                                                                ?.province
                                                        }}, </span
                                                    ><span
                                                        v-show="
                                                            item.location?.city
                                                        "
                                                        >{{
                                                            item.location?.city
                                                        }}</span
                                                    >
                                                    <span
                                                        v-show="
                                                            item.location
                                                                ?.district
                                                        "
                                                        >,
                                                        {{
                                                            item.location
                                                                ?.district
                                                        }}</span
                                                    >
                                                </span>
                                            </div>

                                            <div
                                                class="flex gap-3 align-middle mt-1"
                                            >
                                                <span
                                                    class="text-xs flex gap-1"
                                                    v-show="item.weight"
                                                >
                                                    <ScaleIcon
                                                        class="h-3 w-3"
                                                    />
                                                    {{ item.weight }} Gram
                                                </span>
                                                <span
                                                    class="text-xs flex align-middle"
                                                >
                                                    <ShoppingBagIcon
                                                        class="w-3 h-3"
                                                    />&nbsp;
                                                    {{ item.sold }}
                                                    Sold
                                                </span>
                                                <span
                                                    class="flex text-xs gap-1 align-middle"
                                                    ><StarIcon
                                                        class="h-3 w-3 text-yellow-500"
                                                    />{{
                                                        item.rating.rating
                                                    }}({{
                                                        item.rating.count
                                                    }})</span
                                                >
                                                <a
                                                    @click="
                                                        description =
                                                            item.description
                                                    "
                                                    href="javascript:;"
                                                    onclick="modalDescription.showModal()"
                                                    class="text-xs flex gap-1 hover:text-info"
                                                    v-show="item.description"
                                                >
                                                    <EyeIcon
                                                        class="w-3 h-3"
                                                    /><span
                                                        >Show Description</span
                                                    >
                                                </a>
                                            </div>
                                            <div
                                                v-show="item.flag"
                                                class="flex gap-3 align-middle mt-1"
                                            >
                                                <span
                                                    class="text-xs flex gap-1"
                                                >
                                                    <FlagIcon
                                                        class="h-3 w-3 text-success"
                                                    />
                                                    Supervision
                                                </span>
                                                <span
                                                    class="text-xs flex gap-1"
                                                >
                                                    <CalendarDaysIcon
                                                        class="w-3 h-3"
                                                    />{{
                                                        moment(item.updated_at)
                                                            .locale("id")
                                                            .format(
                                                                "DD MMMM YYYY",
                                                            )
                                                    }}
                                                </span>
                                            </div>
                                            <div
                                                class="flex gap-3 align-middle mt-1"
                                            >
                                                <div>
                                                    <span
                                                        class="text-xs flex gap-1 align-middle"
                                                    >
                                                        BPOM :
                                                        <CheckIcon
                                                            v-if="
                                                                item.certified
                                                                    ?.bpom
                                                            "
                                                            class="w-4 h-4 text-success"
                                                        /><XMarkIcon
                                                            v-else
                                                            class="w-4 h-4 text-error"
                                                        />
                                                    </span>
                                                    <span
                                                        class="text-xs"
                                                        v-show="
                                                            item.certified
                                                                ?.bpom_number
                                                        "
                                                    >
                                                        {{
                                                            item.certified
                                                                ?.bpom_number
                                                        }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="text-xs flex gap-1 align-middle"
                                                >
                                                    SNI :
                                                    <CheckIcon
                                                        v-if="
                                                            item.certified?.sni
                                                        "
                                                        class="w-4 h-4 text-success"
                                                    /><XMarkIcon
                                                        v-else
                                                        class="w-4 h-4 text-error"
                                                    />
                                                </span>
                                                <span
                                                    class="text-xs flex gap-1 align-middle"
                                                >
                                                    No Reg Produk :
                                                    <CheckIcon
                                                        v-if="
                                                            item.certified
                                                                ?.distribution_permit
                                                        "
                                                        class="w-4 h-4 text-success"
                                                    /><XMarkIcon
                                                        v-else
                                                        class="w-4 h-4 text-error"
                                                    />
                                                </span>
                                                <span
                                                    class="text-xs flex gap-1 align-middle"
                                                >
                                                    Halal :
                                                    <CheckIcon
                                                        v-if="
                                                            item.certified
                                                                ?.halal
                                                        "
                                                        class="w-4 h-4 text-success"
                                                    /><XMarkIcon
                                                        v-else
                                                        class="w-4 h-4 text-error"
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="min-w-64">
                                    <HierarchyCategoryProd
                                        :comodity="item.comodity.comodity"
                                        :sub_comodity="
                                            item.comodity.sub_comodity
                                        "
                                        :second_level_sub_comodity="
                                            item.comodity
                                                .second_level_sub_comodity
                                        "
                                        :third_level_sub_comodity="
                                            item.comodity
                                                .third_level_sub_comodity
                                        "
                                        :keyword="item.keyword"
                                    />
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td
                                    colspan="3"
                                    v-if="loading['data'][0]"
                                    class="text-center"
                                >
                                    <span
                                        class="loading loading-spinner loading-sm"
                                    ></span>
                                </td>
                                <td colspan="3" v-else class="text-center">
                                    No Data Available
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="text-info" v-show="!isTableEmpty">
                            <tr>
                                <th></th>
                                <th>Product</th>
                                <th>Category</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <SimplePaginateAPI
                    v-show="!isTableEmpty"
                    @get-data="getData"
                    :loading="loading['refresh'][0]"
                    :prev_page="prev_page"
                    :next_page="next_page"
                    :current_page="current_page"
                />
            </div>
        </div>
        <vue-easy-lightbox
            :visible="visibleImgGallery"
            :imgs="imgsRef"
            :index="indexRef"
            @hide="visibleImgGallery = false"
        ></vue-easy-lightbox>

        <dialog id="modalFilterCertificate" class="modal">
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
                        <TagIcon class="w-5 h-5 my-auto" /> Filter Certificate
                    </h3>
                </div>
                <div class="divider"></div>
                <div class="grid grid-cols-4 gap-4">
                    <div
                        v-for="(item, index) in certificate"
                        :key="index"
                        class="border border-solid rounded-md border-teal-700 p-5"
                    >
                        <h5>{{ item }}</h5>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text"> All </span>
                                <input
                                    v-model="filter_certificate[index]"
                                    value=""
                                    type="radio"
                                    :name="`radio-certificate-${index}`"
                                    class="radio checked:bg-neutral-500"
                                    :checked="filter_certificate[index] == null"
                                />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">
                                    <XMarkIcon class="w-5 h-5 text-error" />
                                </span>
                                <input
                                    v-model="filter_certificate[index]"
                                    :value="false"
                                    type="radio"
                                    :name="`radio-certificate-${index}`"
                                    class="radio checked:bg-red-500"
                                    :checked="
                                        filter_certificate[index] == false
                                    "
                                />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">
                                    <CheckIcon class="w-5 h-5 text-success" />
                                </span>
                                <input
                                    v-model="filter_certificate[index]"
                                    type="radio"
                                    :value="true"
                                    :name="`radio-certificate-${index}`"
                                    class="radio checked:bg-green-500"
                                    :checked="filter_certificate[index] == true"
                                />
                            </label>
                        </div>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="modal-action">
                    <button
                        class="btn btn-outline btn-success btn-sm"
                        :disabled="loading['certificate'][0]"
                        @click="getData(1)"
                    >
                        Save
                        <span
                            class="loading loading-spinner loading-sm"
                            v-show="loading['certificate'][0]"
                        ></span>
                    </button>
                </div>
            </div>
        </dialog>

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
                                :value="comodity.name"
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
                        @click="getData(1)"
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

        <!-- Open the modal using ID.showModal() method -->
        <dialog id="modalDescription" class="modal">
            <div class="modal-box w-11/12 max-w-5xl">
                <p class="py-4" v-html="description"></p>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</template>
