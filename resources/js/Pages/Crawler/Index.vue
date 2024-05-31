<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CrawlerEngine  from './CrawlerEngine.vue';
</script>

<template>
    <Head title="Crawler" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Crawler Service</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-4 gap-4">
                    <CrawlerEngine marketplace="bukalapak" :marketplace_id="1" icon="bukalapak.svg" location="Jawa Barat"/>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-5">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
