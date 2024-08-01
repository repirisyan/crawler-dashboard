<script setup>
import { Link } from "@inertiajs/vue3";

const darkmode = window.matchMedia('(prefers-color-scheme: dark)').matches
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <img v-if="darkmode" src="/assets/img/logo/logo-dark.svg" class="w-auto h-20">
                <img v-else src="/assets/img/logo/logo.svg" class="w-auto h-20">
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
