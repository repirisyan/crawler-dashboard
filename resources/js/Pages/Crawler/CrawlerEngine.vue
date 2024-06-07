<script setup>
import { onBeforeMount, ref, watch } from "vue";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import Pusher from "pusher-js";

const props = defineProps({
    marketplace_id: Number,
    marketplace: String,
    icon: String,
    location: String,
    keyword: String,
    maintenance: Number,
    user_id: Number,
});

const status = ref(null);
const loading = ref(false);
const keyword = ref(props.keyword);

watch(
    () => props.keyword,
    (newVal) => {
        keyword.value = newVal;
    },
);

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
});
const channel = pusher.subscribe("crawler-channel");
channel.bind("refreshEngine", function (data) {
    checkStatus();
});

const checkStatus = async () => {
    status.value = null;
    axios
        .get(route("crawler.status", props.marketplace_id))
        .then((response) => {
            status.value = response.data;
        });
};

onBeforeMount(() => {
    checkStatus();
});

const crawlerData = async () => {
    if (confirm("Lakukan Crawling Data ?")) {
        loading.value = true;
        axios
            .post(route("crawler.run"), {
                marketplace_id: props.marketplace_id,
                marketplace: props.marketplace,
                location: props.location,
                keyword: props.keyword,
                user_id: props.user_id,
            })
            .then((response) => {
                checkStatus();
            })
            .catch((response) => {
                console.log(response);
            })
            .finally(() => {
                loading.value = false;
            });
    }
};
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
    >
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <img :src="`/assets/img/marketplace/${props.icon}`" />
            <div class="divider"></div>
            <div class="flex text-xs md:text-base lg:text-base">
                Engine Status :
                <span
                    :class="
                        status
                            ? 'text-accent'
                            : props.maintenance
                              ? 'text-error'
                              : 'text-info'
                    "
                    class="flex"
                    v-if="status != null"
                    >{{
                        status
                            ? "Working"
                            : props.maintenance
                              ? "Maintenance"
                              : "Iddle"
                    }}&nbsp;<a
                        @click="checkStatus"
                        class="align-middle"
                        href="javascript:;"
                    >
                        <ArrowPathIcon
                            class="w-3 h-3 md:h-5 lg:h-5 md:w-5 lg:w-5 text-blue-500" /></a></span
                ><span class="loading loading-spinner loading-sm" v-else></span>
            </div>
            <button
                class="btn btn-xs md:btn-sm lg:btn-sm btn-outline btn-accent mt-5"
                :disabled="
                    status || loading || keyword == '' || props.maintenance
                "
                v-if="status != null"
                @click="crawlerData"
            >
                <span v-if="!status">
                    <span v-if="!loading">Start</span>
                    <span
                        class="loading loading-spinner loading-sm"
                        v-else
                    ></span>
                </span>
                <span v-else class="loading loading-bars loading-sm"></span>
            </button>
        </div>
    </div>
</template>
