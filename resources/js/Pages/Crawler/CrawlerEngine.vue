<script setup>
import { onBeforeMount, ref, watch } from "vue";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import Pusher from "pusher-js";

const props = defineProps({
    marketplace_id: Number,
    comodity_id: String,
    marketplace: String,
    icon: String,
    location: String,
    keyword: String,
    keyword_id: String,
    maintenance: Number,
    user_id: Number,
});

const loading = ref({
    marketplace: {},
    status: {},
    check: {},
});
const keyword = ref(props.keyword);
const comodity_id = ref(props.comodity_id);
const keyword_id = ref(props.keyword_id);

watch(
    [() => props.comodity_id, () => props.keyword, () => props.keyword_id],
    ([newComodity, newKeyword, newKeywordId]) => {
        keyword.value = newKeyword;
        comodity_id.value = newComodity;
        keyword_id.value = newKeywordId;
    },
);

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
});
const channel = pusher.subscribe("crawler-channel");
channel.bind(`refreshEngine${props.marketplace}`, function (data) {
    checkStatus();
});

const checkStatus = async () => {
    loading.value["status"][props.marketplace] = true;
    loading.value["check"][props.marketplace] = true;
    axios
        .get(route("crawler.status", props.marketplace_id))
        .then((response) => {
            loading.value["status"][props.marketplace] = response.data;
        })
        .finally(() => {
            loading.value["check"][props.marketplace] = false;
        });
};

onBeforeMount(() => {
    checkStatus();
});

const crawlerData = async () => {
    if (confirm("Lakukan Crawling Data ?")) {
        loading.value["marketplace"][props.marketplace] = true;
        axios
            .post(route("crawler.run"), {
                marketplace_id: props.marketplace_id,
                comodity_id: comodity_id.value,
                marketplace: props.marketplace,
                location: props.location,
                keyword: keyword.value,
                keyword_id: keyword_id.value,
                user_id: props.user_id,
            })
            .then((response) => {
                checkStatus();
            })
            .catch((response) => {
                console.log(response);
            })
            .finally(() => {
                loading.value["marketplace"][props.marketplace] = false;
            });
    }
};
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
    >
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <img
                :src="`/assets/img/marketplace/${props.icon}`"
                style="max-height: 40px"
            />
            <div class="divider"></div>
            <div class="flex text-xs md:text-base lg:text-base">
                Engine Status :
                <span
                    :class="
                        loading['status'][props.marketplace]
                            ? 'text-accent'
                            : props.maintenance
                              ? 'text-error'
                              : 'text-info'
                    "
                    class="flex"
                    v-if="!loading['check'][props.marketplace]"
                    >{{
                        loading["status"][props.marketplace]
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
                    loading['status'][props.marketplace] ||
                    loading['marketplace'][props.marketplace] ||
                    keyword == '' ||
                    props.maintenance ||
                    comodity_id == ''
                "
                @click="crawlerData"
            >
                <span v-if="!loading['status'][props.marketplace]">
                    <span v-if="!loading['marketplace'][props.marketplace]"
                        >Start</span
                    >
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
