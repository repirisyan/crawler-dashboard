<script setup>
const props = defineProps({
    loading: Boolean,
    prev_page: Number,
    current_page: Number,
    next_page: Number,
});
</script>
<template>
    <div class="join mt-2 text-center mx-auto">
        <button
            :disabled="props.loading"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.prev_page != null"
            @click="$emit('getData', current_page - 1)"
        >
            «
        </button>
        <button
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
        >
            {{ props.current_page }}
        </button>
        <button
            :show="props.next_page != null"
            :disabled="props.loading"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            @click="$emit('getData', current_page + 1)"
        >
            »
        </button>
    </div>
</template>
