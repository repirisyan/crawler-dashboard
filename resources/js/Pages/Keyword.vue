<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/solid";
import axios from "axios";

const props = defineProps({
    keywords: Object,
    comodities: Object,
    params: Object,
});

const modalCreate = ref(null);
const modalEdit = ref(null);

const filter_comodity = ref(props.params?.comodity ?? "");
const search = ref(props.params?.search ?? "");
const per_page = ref(props.params?.per_page ?? 15);

const loadingStates = ref({
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
    router.visit(
        route("keyword.index", {
            comodity: filter_comodity.value,
            search: search.value,
            page: 1,
        }),
        {
            preserveScroll: true,
            only: ["keywords", "params"],
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
                                                    {{ item.comodity.name }}
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
                                <div
                                    class="join mt-2 mx-auto"
                                    v-show="!isTableEmpty"
                                >
                                    <Link
                                        :href="`${route('keyword.index')}?page=1&search=${props.params.search}&comodity=${props.params.comodity}&per_page=${props.params.per_page}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page > 10
                                        "
                                    >
                                        1
                                    </Link>
                                    <Link
                                        :href="`${route('keyword.index')}?page=${props.keywords.current_page - 2}&comodity=${props.params.comodity}&search=${props.params.search}&per_page=${props.params.per_page}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.current_page - 2 > 0
                                        "
                                    >
                                        {{ props.keywords.current_page - 2 }}
                                    </Link>
                                    <Link
                                        :href="`${route('keyword.index')}?page=${props.keywords.current_page - 1}&comodity=${props.params.comodity}&search=${props.params.search}&per_page=${props.params.per_page}`"
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
                                        :href="`${route('keyword.index')}?page=${props.keywords.current_page + 1}&comodity=${props.params.comodity}&search=${props.params.search}&per_page=${props.params.per_page}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                        v-show="
                                            props.keywords.next_page != null
                                        "
                                    >
                                        {{ props.keywords.current_page + 1 }}
                                    </Link>
                                    <Link
                                        :href="`${route('keyword.index')}?page=${props.keywords.current_page + 2}&comodity=${props.params.comodity}&search=${props.params.search}&per_page=${props.params.per_page}`"
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
                                        :href="`${route('keyword.index')}?page=${props.keywords.last_page - 1}&search=${props.params.search}&comodity=${props.params.comodity}&per_page=${props.params.per_page}`"
                                        class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                    >
                                        {{ props.keywords.last_page - 1 }}
                                    </Link>
                                    <Link
                                        v-show="
                                            props.keywords.current_page <
                                            props.keywords.last_page - 2
                                        "
                                        :href="`${route('keyword.index')}?page=${props.keywords.last_page}&search=${props.params.search}&comodity=${props.params.comodity}&per_page=${props.params.per_page}`"
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
