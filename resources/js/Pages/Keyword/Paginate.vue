<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    search: String,
    next_page_url: String,
    filter_comodity: Array,
    per_page: Number | String,
    current_page: Number,
    last_page: Number,
});

const visitPage = (pagenumber) => {
    router.get(
        route("keyword.index"),
        {
            page: pagenumber,
            search: props.search,
            comodity: props.filter_comodity,
            per_page: props.per_page,
        },
        {
            preserveScroll: true,
        },
    );
};
</script>
<template>
    <div class="join mt-2 mx-auto">
        <button
            type="button"
            @click="visitPage(1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page > 10"
        >
            1
        </button>
        <button
            type="button"
            @click="visitPage(props.current_page - 2)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page - 2 > 0"
        >
            {{ props.current_page - 2 }}
        </button>
        <button
            type="button"
            @click="visitPage(props.current_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page - 1 > 0"
        >
            {{ props.current_page - 1 }}
        </button>
        <button
            type="button"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
        >
            {{ props.current_page ?? "" }}
        </button>
        <button
            type="button"
            @click="visitPage(props.current_page + 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.next_page_url != null"
        >
            {{ props.current_page + 1 }}
        </button>
        <button
            type="button"
            @click="visitPage(props.current_page + 2)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page + 2 <= props.last_page"
        >
            {{ props.current_page + 2 }}
        </button>
        <button
            v-show="props.current_page < props.last_page - 2"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
        >
            ...
        </button>
        <button
            v-show="props.current_page < props.last_page - 3"
            type="button"
            @click="visitPage(props.last_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page - 1 }}
        </button>
        <button
            v-show="props.current_page < props.last_page - 2"
            type="button"
            @click="visitPage(props.last_page)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page }}
        </button>
    </div>
</template>
