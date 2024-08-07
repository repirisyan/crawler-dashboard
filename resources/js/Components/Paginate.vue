<script setup>
const current_page = ref(0);
const next_page = ref(null);
const per_page = ref(15);
const from = ref(1);
const to = ref(1);
const total = ref(1);
const last_page = ref(0);
</script>
<template>
    <div class="join mt-2 mx-auto" v-show="!isTableEmpty">
        <button
            :disabled="loading['refresh'][0]"
            type="button"
            @click="getData(1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="current_page > 10"
        >
            1
        </button>
        <button
            :disabled="loading['refresh'][0]"
            type="button"
            @click="getData(current_page - 2)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="current_page - 2 > 0"
        >
            {{ current_page - 2 }}
        </button>
        <button
            :disabled="loading['refresh'][0]"
            type="button"
            @click="getData(current_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-if="current_page - 1 > 0"
        >
            {{ current_page - 1 }}
        </button>
        <button
            type="button"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-950 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-active"
        >
            {{ current_page ?? "" }}
        </button>
        <button
            :disabled="loading['refresh'][0]"
            @click="getData(current_page + 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="next_page != null"
        >
            {{ current_page + 1 }}
        </button>
        <button
            :disabled="loading['refresh'][0]"
            @click="getData(current_page + 2)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
            v-show="current_page + 2 <= last_page"
        >
            {{ current_page + 2 }}
        </button>
        <button
            v-show="current_page < last_page - 2"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm btn-disabled"
        >
            ...
        </button>
        <button
            v-show="current_page < last_page - 3"
            @click="getData(last_page - 1)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ last_page - 1 }}
        </button>
        <button
            v-show="current_page < last_page - 2"
            @click="getData(last_page)"
            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
        >
            {{ last_page }}
        </button>
    </div>
</template>
