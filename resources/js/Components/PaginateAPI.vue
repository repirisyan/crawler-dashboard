<script setup>
const props = defineProps({
    has_next_page: Boolean,
    current_page: Number,
    last_page: Number,
    loading: Boolean,
});
</script>
<template>
    <div class="join mt-2 mx-auto">
        <button
            :disabled="loading"
            type="button"
            @click="$emit('getData', 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="props.current_page > 3"
        >
            1
        </button>
        <button
            v-show="props.current_page > 4"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
        >
            ...
        </button>
        <button
            :disabled="loading"
            type="button"
            @click="$emit('getData', props.current_page - 2)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="props.current_page - 2 > 0"
        >
            {{ props.current_page - 2 }}
        </button>
        <button
            :disabled="loading"
            type="button"
            @click="$emit('getData', props.current_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="props.current_page - 1 > 0"
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
            :disabled="loading"
            @click="$emit('getData', props.current_page + 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="props.has_next_page"
        >
            {{ props.current_page + 1 }}
        </button>
        <button
            :disabled="loading"
            @click="$emit('getData', props.current_page + 2)"
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
            @click="$emit('getData', props.last_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page - 1 }}
        </button>
        <button
            v-show="props.current_page < props.last_page - 2"
            @click="$emit('getData', props.last_page)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ props.last_page }}
        </button>
    </div>
</template>
