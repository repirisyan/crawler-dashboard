<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PaginateAPI from "@/Components/PaginateAPI.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {
    MagnifyingGlassIcon,
    ArrowPathIcon,
    TrashIcon,
    PlusIcon,
    PencilSquareIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    marketplaces: Object,
});

const brands = ref({});

// Paginate
const current_page = ref(0);
const next_page = ref(null);
const has_next_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);
const total = ref(1);
const last_page = ref(0);

const checkbox = ref([]);
const checkAllButton = ref(false);
const optionMenu = ref(false);

const form = useForm({
    id: null,
    name: null,
});

const loading = ref({
    refresh: {},
    store: {},
    update: {},
    data: {},
    delete: {},
});

// Filter Data
const search = ref("");

const isTableEmpty = computed(() => {
    return Object.keys(brands.value).length === 0;
});

onMounted(() => {
    loading.value["data"][0] = true;
    getData();
    loading.value["data"][0] = false;
});

const getData = async (page = 1) => {
    loading.value["refresh"][0] = true;
    axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/brand`, {
            params: {
                page: page,
                search: search.value,
                per_page: per_page.value,
            },
        })
        .then((response) => {
            checkbox.value.length = 0;
            optionMenu.value = false;
            checkAllButton.value = false;
            response.data.docs.forEach((element) => {
                checkbox.value.push({
                    id: element._id,
                    checked: false,
                });
            });

            brands.value = response.data.docs;
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
        if (!element.status) {
            element.checked = event.target.checked;
        }
    });
};

const storeData = () => {
    loading.value["store"][0] = true;
    axios
        .post(`${import.meta.env.VITE_APP_CRAWLER_API}/brand`, form)
        .then((response) => {
            toast.success(response.data.message || "Data Saved");
            modalCreate.close();
        })
        .catch((error) => {
            console.log(error);
            toast.error(error.response?.data.message || "an error occured");
        })
        .finally(() => {
            getData(current_page.value);
            form.reset();
            form.clearErrors();
            loading.value["store"][0] = false;
        });
};

const editData = (id) => {
    axios
        .get(`${import.meta.env.VITE_APP_CRAWLER_API}/brand/${id}`)
        .then((response) => {
            form.name = response.data[0].name;
            form.id = id;
        })
        .catch((error) => {
            console.log(error);
            toast.error(error.response?.data.message);
        });
};

const updateData = () => {
    loading.value["update"][0] = false;
    axios
        .patch(`${import.meta.env.VITE_APP_CRAWLER_API}/brand`, form)
        .then((response) => {
            toast.success(response.data.message || "Update Success");
            modalUpdate.close();
        })
        .catch((error) => {
            console.log(error);
            toast.error(error.response?.data.message);
        })
        .finally(() => {
            getData(current_page.value);
            form.reset();
            form.clearErrors();
            loading.value["update"][0] = false;
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
            .delete(`${import.meta.env.VITE_APP_CRAWLER_API}/brand`, {
                data: {
                    ids: checkedItems.value,
                },
            })
            .then((response) => {
                toast.success(response.data.message || "Delete Success");
            })
            .catch((error) => {
                console.log(error);
                toast.error(
                    error.response?.data.message || "An error occurred",
                );
            })
            .finally(() => {
                getData(current_page.value);
                loading.value["delete"][0] = false;
            });
    }
};

const destroy = (id) => {
    if (confirm("Apa anda yakin ingin menghapus data ini ?")) {
        loading.value["delete"][0] = true;
        axios
            .delete(`${import.meta.env.VITE_APP_CRAWLER_API}/brand`, {
                data: {
                    ids: [id],
                },
            })
            .then((response) => {
                toast.success(response.data.message || "Delete Success");
            })
            .catch((error) => {
                console.log(error);
                toast.error(
                    error.response?.data.message || "An error occurred",
                );
            })
            .finally(() => {
                getData(current_page.value);
                loading.value["delete"][0] = false;
            });
    }
};
</script>
<template>
    <Head title="Brand Data" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Brand
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
                                    <button
                                        class="btn btn-outline btn-success btn-sm"
                                        onclick="modalCreate.showModal()"
                                    >
                                        Brand <PlusIcon class="h-3 w-3" />
                                    </button>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:flex lg:flex gap-3 float-end"
                                >
                                    <label
                                        class="input input-bordered items-center flex gap-2 input-md w-auto md:w-80 lg:w-80"
                                    >
                                        <input
                                            v-model.lazy="search"
                                            type="text"
                                            class="grow border-0"
                                            @change="getData(1)"
                                            placeholder="Search Brand Name"
                                        />
                                        <MagnifyingGlassIcon class="h-5 w-5" />
                                    </label>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-1 md:block md:gap-5 lg:block lg:gap-5 mb-5"
                                v-show="optionMenu"
                            >
                                <span class="text-sm mr-3">
                                    {{
                                        checkbox.filter((item) => item.checked)
                                            .length
                                    }}
                                    Item Selected
                                </span>
                                <button
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
                                            <th class="w-4">
                                                <label v-if="!isTableEmpty">
                                                    <input
                                                        :checked="
                                                            checkAllButton
                                                        "
                                                        @change="checkAll"
                                                        type="checkbox"
                                                        class="checkbox checkbox-sm"
                                                    />
                                                </label>
                                            </th>
                                            <th>Name</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="!isTableEmpty">
                                        <tr
                                            v-for="(item, index) in brands"
                                            :key="item?.id"
                                        >
                                            <th>
                                                <label>
                                                    <input
                                                        v-model="
                                                            checkbox[index]
                                                                .checked
                                                        "
                                                        @change="
                                                            checkbox[
                                                                index
                                                            ].checked =
                                                                $event.target.checked;
                                                            checkOptionMenu();
                                                        "
                                                        type="checkbox"
                                                        class="checkbox checkbox-sm"
                                                    />
                                                </label>
                                            </th>
                                            <td class="w-auto">
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
                                                                :alt="`Gambar ${item?.name}`"
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
                                                                    item?.name
                                                                }}</a
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="flex gap-2">
                                                <button
                                                    @click="editData(item.id)"
                                                    onclick="modalUpdate.showModal()"
                                                    class="btn btn-warning btn-sm"
                                                >
                                                    <PencilSquareIcon
                                                        class="h-3 w-3"
                                                    />
                                                </button>
                                                <button
                                                    @click="destroy(item.id)"
                                                    :disabled="
                                                        loading['delete'][
                                                            item.id
                                                        ]
                                                    "
                                                    class="btn btn-error btn-sm"
                                                >
                                                    <span
                                                        class="loading loading-spinner loading-sm"
                                                        v-if="
                                                            loading['delete'][
                                                                item.id
                                                            ]
                                                        "
                                                    ></span>
                                                    <TrashIcon
                                                        class="h-3 w-3"
                                                        v-else
                                                    />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                No Data Available
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot
                                        class="text-info"
                                        v-show="!isTableEmpty"
                                    >
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th></th>
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

        <dialog id="modalCreate" ref="modalCreate" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Form Add Data</h3>
                <div class="divider"></div>
                <form @submit.prevent="storeData">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Name</span>
                        </div>
                        <input
                            type="text"
                            v-model.lazy="form.name"
                            :readonly="loading['store'][0]"
                            placeholder="Type here"
                            class="input input-bordered w-full"
                            :class="form.errors.name ? 'input-error' : ''"
                        />
                        <div class="label" v-show="form.errors.name">
                            <span class="label-text-alt text-error">{{
                                form.errors.name
                            }}</span>
                        </div>
                    </label>
                    <div class="divider"></div>
                    <div class="modal-action flex justify-between">
                        <form method="dialog">
                            <!-- if there is a button in form, it will close the modal -->
                            <button
                                :disabled="loading['store'][0]"
                                @click="
                                    {
                                        form.reset(), form.clearErrors();
                                    }
                                "
                                class="btn btn-sm"
                            >
                                Cancel
                            </button>
                        </form>
                        <button
                            type="submit"
                            :disabled="loading['store'][0]"
                            class="btn btn-sm btn-success"
                        >
                            Save&nbsp;<span
                                class="loading loading-spinner loading-sm"
                                v-show="loading['store'][0]"
                            ></span>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="modalUpdate" ref="modalCreate" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Form Edit Data</h3>
                <div class="divider"></div>
                <form @submit.prevent="updateData">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Name</span>
                        </div>
                        <input
                            type="text"
                            v-model.lazy="form.name"
                            :readonly="loading['update'][0]"
                            placeholder="Type here"
                            class="input input-bordered w-full"
                            :class="form.errors.name ? 'input-error' : ''"
                        />
                        <div class="label" v-show="form.errors.name">
                            <span class="label-text-alt text-error">{{
                                form.errors.name
                            }}</span>
                        </div>
                    </label>
                    <div class="divider"></div>
                    <div class="modal-action flex justify-between">
                        <form method="dialog">
                            <!-- if there is a button in form, it will close the modal -->
                            <button
                                :disabled="loading['update'][0]"
                                @click="
                                    {
                                        form.reset(), form.clearErrors();
                                    }
                                "
                                class="btn btn-sm"
                            >
                                Cancel
                            </button>
                        </form>
                        <button
                            type="submit"
                            :disabled="loading['update'][0]"
                            class="btn btn-sm btn-success"
                        >
                            Save&nbsp;<span
                                class="loading loading-spinner loading-sm"
                                v-show="loading['update'][0]"
                            ></span>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
