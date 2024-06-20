<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TempItem from "@/Pages/Crawler/TempItem.vue";
import { Head } from "@inertiajs/vue3";
import CrawlerEngine from "./CrawlerEngine.vue";
import { MapPinIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

const keyword = ref("");
const location = ref("");

const props = defineProps({
    crawlers: Object,
    user_id: Number,
});
</script>

<template>
    <Head title="Crawler" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Crawler Service
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                <div class="grid grid-col-1 md:flex lg:flex gap-5">
                    <label
                        class="input input-bordered flex items-center gap-2 mb-8 w-auto md:w-80 lg:w-80"
                    >
                        <input
                            v-model="keyword"
                            type="text"
                            class="grow border-0"
                            placeholder="Keyword"
                        />
                        <MagnifyingGlassIcon class="h-5 w-5" />
                    </label>
                    <!-- <label
                        class="input input-bordered flex items-center gap-2 mb-8 w-auto md:w-80 lg:w-80"
                    >
                        <input
                            v-model="location"
                            type="text"
                            class="grow border-0"
                            placeholder="Filter Lokasi"
                        />
                        <MapPinIcon class="h-5 w-5" />
                    </label> -->
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4"
                >
                    <CrawlerEngine
                        v-for="item in props.crawlers"
                        :key="item.id"
                        :marketplace="item.name.toLowerCase()"
                        :keyword="keyword"
                        :marketplace_id="item.id"
                        :maintenance="item.maintenance"
                        :icon="item.logo"
                        :location="location"
                        :user_id="props.user_id"
                    />
                </div>
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-5"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <TempItem
                            class="mx-auto"
                            :marketplaces="props.crawlers"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
