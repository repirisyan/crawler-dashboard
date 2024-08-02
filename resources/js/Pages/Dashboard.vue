<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import CrawlerEngine from "./Crawler/CrawlerEngine.vue";

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
                    <div
                        class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4"
                    >
                        <CrawlerEngine
                            v-for="item in props.crawlers"
                            :key="item.id"
                            :marketplace="item.name.toLowerCase()"
                            :marketplace_id="item.id"
                            :maintenance="item.maintenance"
                            :icon="item.logo"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
