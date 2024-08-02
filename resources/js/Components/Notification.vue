<script setup>
import {
    BellIcon,
    ClockIcon,
    BellAlertIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    XCircleIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/solid";
import Dropdown from "@/Components/Dropdown.vue";
import { ref, onMounted, computed } from "vue";
import moment from "moment";
import Pusher from "pusher-js";

const notifications = ref({});

const loading = ref(false);

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
});
const channel = pusher.subscribe("crawler-channel");
channel.bind("refreshNotification", () => {
    getNotification();
});

onMounted(() => {
    getNotification();
});

const getNotification = async () => {
    axios
        .get(route("notification.user"))
        .then((response) => {
            notifications.value = response.data;
        })
        .catch((response) => {
            console.log(response);
        });
};

const isNotificationEmpty = computed(() => {
    return Object.keys(notifications.value).length === 0;
});

const readNotification = async (notification_id) => {
    axios
        .patch(route("notification.read", notification_id))
        .then((response) => {
            console.log(response);
        })
        .catch((response) => {
            console.log(response);
        });
};

const deleteNotification = async (notification_id) => {
    axios
        .delete(route("notification.delete", notification_id))
        .then((response) => {
            console.log(response);
        })
        .catch((response) => {
            console.log(response);
        });
};

const deleteAllNotification = async () => {
    loading.value = true;
    axios
        .delete(route("notification.delete_all"))
        .then((response) => {
            getNotification();
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
<template>
    <Dropdown align="right" width="100">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                >
                    <BellIcon
                        class="h-5 w-5"
                        v-if="isNotificationEmpty"
                    /><BellAlertIcon v-else class="h-5 w-5" />
                </button>
            </span>
        </template>

        <template #content>
            <div v-if="!loading">
                <div v-if="!isNotificationEmpty" class="overflow-y-scroll h-96">
                    <div class="relative p-5">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-dark absolute top-0 right-1 items-center"
                            @click="deleteAllNotification"
                        >
                            Clear All
                        </button>
                    </div>
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="w-96"
                    >
                        <div
                            class="group flex justify-between items-center cursor-pointer p-2 dark:hover:bg-gray-800 hover:bg-gray-200"
                        >
                            <p class="text-sm">
                                {{ notification.message }}<br />
                                <small class="flex items-center"
                                    ><ClockIcon class="h-3 w-3" />&nbsp;{{
                                        moment(notification.created_at)
                                            .locale("id")
                                            .format("DD MMMM YYYY HH:mm:ss")
                                    }}</small
                                >
                            </p>
                            <div>
                                <CheckCircleIcon
                                    v-if="notification.category == 'success'"
                                    class="h-4 w-4 text-success"
                                /><XCircleIcon
                                    v-else-if="notification.category == 'error'"
                                    class="h-3 w-3 text-error"
                                /><ExclamationCircleIcon
                                    v-else-if="
                                        notification.category == 'warning'
                                    "
                                    class="h-3 w-3 text-warning"
                                /><InformationCircleIcon class="h-3 w-3 text-info" v-else />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="p-2 text-center w-96">No Notification</div>
            </div>
            <div v-else class="p-2 text-center w-96">
                <span class="loading loading-spinner loading-sm"></span>
            </div>
        </template>
    </Dropdown>
</template>
