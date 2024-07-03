<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { PlusIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/solid";
import axios from "axios";

const props = defineProps({
    comodities: Object,
    keywords: Object,
    search_list: Object,
});

const modalCreate = ref(null);
const modalEdit = ref(null);

const loadingStates = ref({
    delete: {},
    update: {},
});

const form = useForm({
    comodity_id: "",
    keyword_id: "",
});
const formEdit = useForm({
    comodity_id: "",
    keyword_id: "",
});

const isTableEmpty = computed(() => {
    return Object.keys(props.search_list.data).length === 0;
});

const storeData = () => {
    form.post(route("search-list.store"), {
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
    axios.get(route("search-list.edit", id)).then((response) => {
        formEdit.keyword_id = response.data.keyword_id;
        formEdit.comodity_id = response.data.comodity_id;
        formEdit.id = id;
    });
};

const updateData = () => {
    formEdit.patch(route("search-list.update", formEdit.id), {
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
        router.delete(route("search-list.destroy", id), {
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
    <Head title="Search List" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Search List
            </h2>
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
                                    Search List <PlusIcon class="h-3 w-3" />
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead class="text-info">
                                            <tr>
                                                <th>Category</th>
                                                <th>Keyword</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="!isTableEmpty">
                                            <tr
                                                v-for="item in props.search_list
                                                    .data"
                                                :key="item.id"
                                            >
                                                <td>
                                                    {{ item.comodity.name }}
                                                </td>
                                                <td>{{ item.keyword.name }}</td>
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
                                                    Tidak ada data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div
                                    class="join mx-auto"
                                    v-show="!isTableEmpty"
                                >
                                    <Link
                                        class="join-item btn btn-sm"
                                        v-show="props.search_list.prev_page_url"
                                        :href="
                                            props.search_list.prev_page_url ??
                                            'javascript:;'
                                        "
                                        >«</Link
                                    >
                                    <button class="join-item btn btn-sm">
                                        Page
                                        {{ props.search_list.current_page }}
                                    </button>
                                    <Link
                                        class="join-item btn btn-sm"
                                        v-show="props.search_list.next_page_url"
                                        :href="
                                            props.search_list.next_page_url ??
                                            'javascript:;'
                                        "
                                        >»</Link
                                    >
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
                            class="select select-bordered w-full"
                            :class="
                                form.errors.comodity_id ? 'input-error' : ''
                            "
                            :readonly="form.processing"
                            required
                            v-model.lazy="form.comodity_id"
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
                        <div class="label" v-show="form.errors.comodity_id">
                            <span class="label-text-alt text-error">{{
                                form.errors.comodity_id
                            }}</span>
                        </div>
                    </label>
                    <label class="form-control w-full mt-5">
                        <div class="label">
                            <span class="label-text">Keyword</span>
                        </div>
                        <select
                            class="select select-bordered w-full"
                            :class="form.errors.keyword_id ? 'input-error' : ''"
                            :readonly="form.processing"
                            required
                            v-model.lazy="form.keyword_id"
                        >
                            <option value="">-- Keyword --</option>
                            <option
                                :value="keyword.id"
                                v-for="keyword in props.keywords"
                                :key="keyword.id"
                            >
                                {{ keyword.name }}
                            </option>
                        </select>
                        <div class="label" v-show="form.errors.keyword_id">
                            <span class="label-text-alt text-error">{{
                                form.errors.keyword_id
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
                            class="select select-bordered w-full"
                            :class="
                                formEdit.errors.comodity_id ? 'input-error' : ''
                            "
                            :readonly="formEdit.processing"
                            required
                            v-model.lazy="formEdit.comodity_id"
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
                        <div class="label" v-show="formEdit.errors.comodity_id">
                            <span class="label-text-alt text-error">{{
                                formEdit.errors.comodity_id
                            }}</span>
                        </div>
                    </label>
                    <label class="form-control w-full mt-5">
                        <div class="label">
                            <span class="label-text">Keyword</span>
                        </div>
                        <select
                            class="select select-bordered w-full"
                            :class="
                                formEdit.errors.keyword_id ? 'input-error' : ''
                            "
                            :readonly="formEdit.processing"
                            required
                            v-model.lazy="formEdit.keyword_id"
                        >
                            <option value="">-- Keyword --</option>
                            <option
                                :value="keyword.id"
                                v-for="keyword in props.keywords"
                                :key="keyword.id"
                            >
                                {{ keyword.name }}
                            </option>
                        </select>
                        <div class="label" v-show="formEdit.errors.keyword_id">
                            <span class="label-text-alt text-error">{{
                                formEdit.errors.keyword_id
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
