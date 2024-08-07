<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HierarchyCategory from "@/Components/HierarchyCategory.vue";
import Paginate from "./Paginate.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
} from "@heroicons/vue/24/solid";
import axios from "axios";

const props = defineProps({
    keywords: Object,
    comodities: Object,
    params: Object,
});

const modalCreate = ref(null);
const modalEdit = ref(null);

const filter_comodity = ref(props.params?.comodity ?? []);
const search = ref(props.params?.search ?? "");
const per_page = ref(props.params?.per_page ?? 15);

const loadingStates = ref({
    comodity: {},
    delete: {},
    update: {},
});

const form = useForm({
    comodity_id: "",
    name: "",
});
const formEdit = useForm({
    comodity_id: "",
    name: "",
});

const isTableEmpty = computed(() => {
    return Object.keys(props.keywords.data).length === 0;
});

const filterData = () => {
    loadingStates.value["comodity"][0] = true;
    router.get(
        route("keyword.index", {
            comodity: filter_comodity.value,
            search: search.value,
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

const storeData = () => {
    form.post(route("keyword.store"), {
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
    axios.get(route("keyword.edit", id)).then((response) => {
        formEdit.name = response.data.name;
        formEdit.id = id;
        formEdit.comodity_id = response.data.comodity_id;
    });
};

const updateData = () => {
    formEdit.patch(route("keyword.update", formEdit.id), {
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
        router.delete(route("keyword.destroy", id), {
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
    <Head title="Keyword" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="breadcrumbs font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                <ul>
                    <li>Master Data</li>
                    <li>Keyword</li>
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
                                <button
                                    class="btn btn-outline btn-success btn-sm"
                                    onclick="modalCreate.showModal()"
                                >
                                    Keyword <PlusIcon class="h-3 w-3" />
                                </button>
                                <div class="flex gap-3">
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
                                        :filter_comodity="filter_comodity"
                                        :per_page="per_page"
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
                                                <th>Action</th>
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
                                                    colspan="3"
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
                                    :filter_comodity="filter_comodity"
                                    :per_page="per_page"
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

        <dialog id="modalCreate" ref="modalCreate" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Form Add Data</h3>
                <div class="divider"></div>
                <form @submit.prevent="storeData">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Category</span>
                        </div>
                        <select
                            v-model.lazy="form.comodity_id"
                            :class="
                                form.errors.comodity_id ? 'select-error' : ''
                            "
                            :readonly="form.processing"
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="" selected>
                                -- Select Category --
                            </option>
                            <option
                                v-for="item in comodities"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <div class="label" v-show="form.errors.comodity_id">
                            <span class="label-text-alt text-error">{{
                                form.errors.comodity_id
                            }}</span>
                        </div>
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Keyword</span>
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
                    <h3 class="text-lg font-bold">Filter Category</h3>
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
                <div class="divider"></div>
                <div class="grid grid-cols-4 gap-4">
                    <div
                        class="form-control"
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
            </div>
        </dialog>

        <dialog id="modalEdit" ref="modalEdit" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Form Edit Data</h3>
                <div class="divider"></div>
                <form @submit.prevent="updateData">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Category</span>
                        </div>
                        <select
                            v-model.lazy="formEdit.comodity_id"
                            :class="
                                formEdit.errors.comodity_id
                                    ? 'select-error'
                                    : ''
                            "
                            :readonly="formEdit.processing"
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="" selected>
                                -- Select Category --
                            </option>
                            <option
                                v-for="item in comodities"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <div class="label" v-show="formEdit.errors.comodity_id">
                            <span class="label-text-alt text-error">{{
                                formEdit.errors.comodity_id
                            }}</span>
                        </div>
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Keyword</span>
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
