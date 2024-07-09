<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { PlusIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/solid";
import axios from "axios";

const props = defineProps({
    keywords: Object,
});

const modalCreate = ref(null);
const modalEdit = ref(null);

const loadingStates = ref({
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
    return Object.keys(props.keywords.data).length === 0;
});

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
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Keyword
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
                                    Keyword <PlusIcon class="h-3 w-3" />
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead class="text-info">
                                            <tr>
                                                <th>Name</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="!isTableEmpty">
                                            <tr
                                                v-for="item in props.keywords
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
                                <div
                                    class="join mx-auto"
                                    v-show="!isTableEmpty"
                                >
                                    <Link
                                        class="join-item btn btn-sm"
                                        v-show="props.keywords.prev_page_url"
                                        :href="
                                            props.keywords.prev_page_url ??
                                            'javascript:;'
                                        "
                                        >«</Link
                                    >
                                    <button class="join-item btn btn-sm">
                                        Page {{ props.keywords.current_page }}
                                    </button>
                                    <Link
                                        class="join-item btn btn-sm"
                                        v-show="props.keywords.next_page_url"
                                        :href="
                                            props.keywords.next_page_url ??
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
