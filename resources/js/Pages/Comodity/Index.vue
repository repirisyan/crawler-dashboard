<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import Paginate from "./Paginate.vue";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    ArrowUpTrayIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/solid";
import axios from "axios";

const props = defineProps({
    comodities: Object,
    params: Object,
});

const modalCreate = ref(null);
const modalEdit = ref(null);

const fileInput = ref(null);

const search = ref(props.params?.search ?? "");
const per_page = ref(props.params?.per_page ?? 15);

const loadingStates = ref({
    import: {},
    delete: {},
    update: {},
});

const form = useForm({
    name: "",
});
const formEdit = useForm({
    name: "",
});

const isTableEmpty = computed(() => {
    return Object.keys(props.comodities.data).length === 0;
});

const triggerFileInput = () => {
    fileInput.value.click();
};

const handleFileUpload = (event) => {
    fileInput.value = event.target.files[0];
    importData();
};

const importData = () => {
    router.post(
        route("comodity.import"),
        {
            file: fileInput.value,
        },
        {
            forceFormData: true,
            onBefore: () => {
                loadingStates.value["import"][0] = true;
            },
            onSuccess: (response) => {
                if (response.props.flash.message[0] == 400) {
                    toast.error(response.props.flash.message[1]);
                } else {
                    toast.success(response.props.flash.message[1]);
                }
            },
            onError: (response) => {
                if (response.props?.flash.message[1]) {
                    toast.error(response.props?.flash.message[1]);
                }
            },
            onFinish: () => {
                loadingStates.value["import"][0] = false;
                fileInput.value = null;
            },
        },
    );
};

const filterData = () => {
    router.visit(
        route("comodity.index", {
            search: search.value,
            per_page: per_page.value,
            page: 1,
        }),
        {
            only: ["comodities", "params"],
        },
    );
};

const storeData = () => {
    form.post(route("comodity.store"), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            form.clearErrors();
            modalCreate.value.close();
            toast.success(response.props.flash.message[1]);
        },
        onError: (response) => {
            formEdit.reset();
            if (response.props?.flash.message[1]) {
                toast.error(response.props?.flash.message[1]);
            }
        },
    });
};

const editData = (id) => {
    modalEdit.value.showModal();
    axios.get(route("comodity.edit", id)).then((response) => {
        formEdit.name = response.data.name;
        formEdit.id = id;
    });
};

const updateData = () => {
    formEdit.patch(route("comodity.update", formEdit.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            formEdit.reset();
            formEdit.clearErrors();
            modalEdit.value.close();
            toast.success(response.props.flash.message[1]);
        },
        onError: (response) => {
            formEdit.reset();
            toast.error(response.props.flash.message[1]);
        },
    });
};

const destroy = (id) => {
    if (confirm("Are you sure want to delete this data ?")) {
        router.delete(route("comodity.destroy", id), {
            preserveScroll: true,
            onBefore: () => {
                loadingStates.value["delete"][id] = true;
            },
            onSuccess: (response) => {
                toast.success(response.props.flash.message[1]);
            },
            onError: (response) => {
                toast.error(response.props.flash.message[1]);
            },
            onFinish: () => {
                loadingStates.value["delete"][id] = false;
            },
        });
    }
};
</script>

<template>
    <Head title="Category" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="breadcrumbs font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                <ul>
                    <li>Master Data</li>
                    <li>Category</li>
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
                                <div class="flex gap-3">
                                    <button
                                        class="btn btn-outline btn-success btn-sm"
                                        onclick="modalCreate.showModal()"
                                    >
                                        Category <PlusIcon class="h-3 w-3" />
                                    </button>
                                    <button
                                        class="btn btn-outline btn-info btn-sm"
                                        @click="triggerFileInput"
                                        :disabled="loadingStates['import'][0]"
                                    >
                                        Import
                                        <span
                                            class="loading loading-spinner loading-sm"
                                            v-if="loadingStates['import'][0]"
                                        ></span
                                        ><ArrowUpTrayIcon
                                            v-else
                                            class="h-3 w-3"
                                        />
                                    </button>
                                    <input
                                        type="file"
                                        ref="fileInput"
                                        @change="handleFileUpload"
                                        style="display: none"
                                    />
                                    <a
                                        class="btn btn-outline btn-info btn-sm"
                                        href="/assets/file/category_import_sample.xlsx"
                                        target="_blank"
                                    >
                                        Sample File
                                        <ArrowDownTrayIcon class="h-3 w-3" />
                                    </a>
                                </div>
                                <label
                                    class="input input-bordered items-center flex gap-2 input-md w-auto md:w-80 lg:w-80"
                                >
                                    <input
                                        v-model.lazy="search"
                                        type="text"
                                        class="grow border-0"
                                        @change="filterData"
                                        placeholder="Search Name"
                                    />
                                    <MagnifyingGlassIcon class="h-5 w-5" />
                                </label>
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
                                    <Paginate
                                        v-show="!isTableEmpty"
                                        :search="search"
                                        :per_page="per_page"
                                        :current_page="
                                            props.comodities.current_page
                                        "
                                        :next_page_url="
                                            props.comodities.next_page_url
                                        "
                                        :last_page="props.comodities.last_page"
                                    />
                                    <div
                                        class="text-xs md:text-base lg:text-base"
                                    >
                                        Showing {{ props.comodities.from }} to
                                        {{ props.comodities.to }} from
                                        {{ props.comodities.total }} Entries
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead class="text-info">
                                            <tr>
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="!isTableEmpty">
                                            <tr
                                                v-for="item in props.comodities
                                                    .data"
                                                :key="item.id"
                                            >
                                                <td>{{ item.name }}</td>
                                                <td class="flex gap-3">
                                                    <button
                                                        @click="
                                                            editData(item.id)
                                                        "
                                                        class="btn btn-warning btn-sm"
                                                    >
                                                        <PencilSquareIcon
                                                            class="h-3 w-3"
                                                        />
                                                    </button>
                                                    <button
                                                        @click="
                                                            destroy(item.id)
                                                        "
                                                        :disabled="
                                                            loadingStates[
                                                                'delete'
                                                            ][item.id]
                                                        "
                                                        class="btn btn-error btn-sm"
                                                    >
                                                        <span
                                                            class="loading loading-spinner loading-sm"
                                                            v-if="
                                                                loadingStates[
                                                                    'delete'
                                                                ][item.id]
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
                                                <td
                                                    colspan="2"
                                                    class="text-center"
                                                >
                                                    No Data Available
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <Paginate
                                    v-show="!isTableEmpty"
                                    :search="search"
                                    :per_page="per_page"
                                    :current_page="
                                        props.comodities.current_page
                                    "
                                    :next_page_url="
                                        props.comodities.next_page_url
                                    "
                                    :last_page="props.comodities.last_page"
                                />
                            </div>
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
                            :readonly="form.processing"
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
                                :disabled="form.processing"
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
                            :disabled="form.processing"
                            class="btn btn-sm btn-success"
                        >
                            Save&nbsp;<span
                                class="loading loading-spinner loading-sm"
                                v-show="form.processing"
                            ></span>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="modalEdit" ref="modalEdit" class="modal">
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
                            v-model.lazy="formEdit.name"
                            :readonly="formEdit.processing"
                            placeholder="Type here"
                            class="input input-bordered w-full"
                        />
                        <div class="label" v-show="formEdit.errors.name">
                            <span class="label-text-alt text-danger">{{
                                formEdit.errors.name
                            }}</span>
                        </div>
                    </label>
                    <div class="divider"></div>
                    <div class="modal-action flex justify-between">
                        <form method="dialog">
                            <!-- if there is a button in form, it will close the modal -->
                            <button
                                :disabled="formEdit.processing"
                                @click="
                                    {
                                        formEdit.reset(),
                                            formEdit.clearErrors();
                                    }
                                "
                                class="btn btn-sm"
                            >
                                Cancel
                            </button>
                        </form>
                        <button
                            type="submit"
                            :disabled="formEdit.processing"
                            class="btn btn-sm btn-success"
                        >
                            Update&nbsp;<span
                                class="loading loading-spinner loading-sm"
                                v-show="formEdit.processing"
                            ></span>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
