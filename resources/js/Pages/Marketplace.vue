<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
const props = defineProps({
    marketplaces: Object,
});

const loading = ref({
    refresh: {},
    maintenance: {},
});

const getData = () => {
    router.reload({
        onBefore: () => {
            loading.value["refresh"][0] = true;
        },
        onFinish: () => {
            loading.value["refresh"][0] = false;
        },
        only: ["marketplace"],
    });
};

const isTableEmpty = computed(() => {
    return Object.keys(props.marketplaces).length === 0;
});

const toggleStatus = (id, maintenance) => {
    loading.value["maintenance"][id] = true;
    axios
        .patch(route("marketplace.maintenance", id), {
            maintenance: maintenance,
        })
        .catch((response) => {
            toast.error(response.data);
        })
        .finally(() => {
            router.reload({ only: ["marketplaces"] });
            loading.value["maintenance"][id] = false;
        });
};
</script>

<template>
    <Head title="Crawler Engine" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Crawler Engine
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="card mx-auto">
                            <div
                                class="card-header flex justify-between verflow-x-auto"
                            >
                                <div
                                    class="grid grid-cols-1 md:flex lg:flex lg:gap-5 mb-5"
                                >
                                    <button
                                        @click="getData"
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
                            </div>
                            <div class="card-body">
                                <div class="overflow-x-auto">
                                    <table class="table">
                                        <thead class="text-info">
                                            <tr>
                                                <th>Name</th>
                                                <th>Maintenance</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="!isTableEmpty">
                                            <tr
                                                v-for="item in props.marketplaces"
                                                :key="item.id"
                                            >
                                                <td class="whitespace-nowrap">
                                                    <img
                                                        :src="`/assets/img/marketplace/${item.logo}`"
                                                        style="
                                                            max-height: 40px;
                                                            max-width:;
                                                        "
                                                    />
                                                </td>
                                                <td>
                                                    <a
                                                        v-if="!item.status"
                                                        @click="
                                                            toggleStatus(
                                                                item.id,
                                                                item.maintenance,
                                                            )
                                                        "
                                                        href="javascript:;"
                                                        role="button"
                                                        class="badge badge-sm block"
                                                        :class="
                                                            item.maintenance
                                                                ? 'badge-error'
                                                                : 'badge-success'
                                                        "
                                                    >
                                                        <span
                                                            v-if="
                                                                !loading[
                                                                    'maintenance'
                                                                ][item.id]
                                                            "
                                                        >
                                                            {{
                                                                item.maintenance
                                                                    ? "Maintenance"
                                                                    : "Active"
                                                            }}
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="loading loading-spinner loading-sm"
                                                        ></span>
                                                    </a>
                                                    <p v-else>
                                                        <span
                                                            class="badge badge-sm badge-warning"
                                                        >
                                                            Engine Still Running
                                                        </span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td
                                                    colspan="2"
                                                    class="text-center"
                                                >
                                                    Tidak ada data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
