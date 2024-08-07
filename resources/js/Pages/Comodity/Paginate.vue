<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    search: String,
    per_page: String,
    current_page: Number,
    last_page: Number,
    next_page_url: String,
});
</script>
<template>
    <div class="join mt-2 mx-auto">
        <Link
            :href="`${route('comodity.index')}?page=1&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page > 3"
        >
            1
        </Link>
        <Link
            :href="`${route('comodity.index')}?page=${props.current_page - 2}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page - 2 > 0"
        >
            {{ props.current_page - 2 }}
        </Link>
        <Link
            :href="`${route('comodity.index')}?page=${props.current_page - 1}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page - 1 > 0"
        >
            {{ props.current_page - 1 }}
        </Link>
        <button
            type="button"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
        >
            {{ props.current_page ?? "" }}
        </button>
        <Link
            :href="`${route('comodity.index')}?page=${props.current_page + 1}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.next_page_url != null"
        >
            {{ props.current_page + 1 }}
        </Link>
        <Link
            :href="`${route('comodity.index')}?page=${props.current_page + 2}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.current_page + 2 <= props.last_page"
        >
            {{ props.current_page + 2 }}
        </Link>
        <button
            v-show="props.current_page < props.last_page - 2"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
        >
            ...
        </button>
        <Link
            v-show="props.current_page < props.last_page - 3"
            :href="`${route('comodity.index')}?page=${props.last_page - 1}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page - 1 }}
        </Link>
        <Link
            v-show="props.current_page < props.last_page - 2"
            :href="`${route('comodity.index')}?page=${props.last_page}&search=${props.search ?? ''}&per_page=${props.per_page}`"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page }}
        </Link>
    </div>
</template>
