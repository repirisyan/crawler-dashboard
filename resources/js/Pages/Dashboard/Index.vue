<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import SellerDistribution from "./SellerDistribution.vue";
import BrandLeaderboard from "./BrandLeaderboard.vue";

const props = defineProps({
    crawlers: Object,
});
</script>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <SellerDistribution />
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <BrandLeaderboard />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
