<script setup>
import {
    BellIcon,
    LightBulbIcon,
    ClockIcon,
    BellAlertIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import Dropdown from "@/Components/Dropdown.vue";
import { ref, onMounted, computed } from "vue";
import moment from "moment";
import Pusher from 'pusher-js';

const notifications = ref({});

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
})
const channel = pusher.subscribe('crawler-channel');
channel.bind('refreshNotification', () =>{
    getNotification();
})

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
    axios
        .delete(route("notification.delete_all"))
        .then((response) => {
            getNotification();
        })
        .catch((response) => {
            console.log(response);
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
            <div v-if="!isNotificationEmpty">
                <div class="relative p-5">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-dark absolute top-0 right-1 items-center"
                        @click="deleteAllNotification"
                    >
                        Clear All <TrashIcon class="w-3 h-3" />
                    </button>
                </div>
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="w-96"
                >
                    <div
                        class="group flex justify-between items-center cursor-pointer p-2 hover:bg-gray-800"
                    >
                        <p class="text-base">
                            {{ notification.message }}<br />
                            <small class="flex items-center"
                                ><ClockIcon class="h-3 w-3" />&nbsp;{{
                                    moment(notification.created_at)
                                        .locale("id")
                                        .format("DD MMMM YYYY hh:mm:ss")
                                }}</small
                            >
                        </p>
                        <div v-if="!notification.status">
                            <LightBulbIcon class="h-4 w-4 text-yellow-500" />
                        </div>
                        <div v-else></div>
                    </div>
                </div>
            </div>
            <div v-else class="p-2 text-center w-96">No Notification</div>
        </template>
    </Dropdown>
</template>
