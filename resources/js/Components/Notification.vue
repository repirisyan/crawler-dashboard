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

const loadingState = ref({
    delete: {},
    load: {},
    status: {},
});

const last_page = ref(1);
const next_page = ref(null);
const status = ref("");

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
    await axios
        .get(
            `${route("notification.user")}?page=${last_page.value}&status=${status.value}`,
        )
        .then((response) => {
            if (Object.keys(notifications.value).length != 0) {
                notifications.value = [
                    ...notifications.value,
                    ...response.data.data,
                ];
            } else {
                notifications.value = response.data.data;
            }

            next_page.value = response.data.next_page_url;
        })
        .catch((response) => {
            console.log(response);
        });
};

const loadMoreNotification = async (event) => {
    event.preventDefault();
    last_page.value = last_page.value + 1;
    await getNotification();
};

const isNotificationEmpty = computed(() => {
    return Object.keys(notifications.value).length === 0;
});

const filterStatus = (status_value, event) => {
    event.preventDefault();
    notifications.value = {};
    status.value = status_value;
    getNotification();
};

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
    loadingState["delete"][0].value = true;
    axios
        .delete(route("notification.delete_all"))
        .then(() => {
            getNotification();
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loadingState["delete"][0].value = false;
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
            <div class="flex gap-3 w-full p-2">
                <button
                    :disabled="loadingState['status'][0]"
                    @click.stop="filterStatus('', $event)"
                    type="button"
                    class="btn btn-sm btn-outline btn-primary top-0 items-center"
                >
                    All
                    <span
                        v-show="loadingState['status'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <button
                    :disabled="loadingState['status'][0]"
                    @click.stop="filterStatus('success', $event)"
                    type="button"
                    class="btn btn-sm btn-success top-0 items-center"
                    :class="status == 'success' ? '' : 'btn-outline'"
                >
                    <CheckCircleIcon class="w-4 h-4" />
                    <span
                        v-show="loadingState['status'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <button
                    :disabled="loadingState['status'][0]"
                    type="button"
                    @click.stop="filterStatus('info', $event)"
                    class="btn btn-sm btn-info top-0 items-center"
                    :class="status == 'info' ? '' : 'btn-outline'"
                >
                    <InformationCircleIcon class="w-4 h-4" />
                    <span
                        v-show="loadingState['status'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <button
                    :disabled="loadingState['status'][0]"
                    @click.stop="filterStatus('warning', $event)"
                    type="button"
                    class="btn btn-sm btn-warning top-0 items-center"
                    :class="status == 'warning' ? '' : 'btn-outline'"
                >
                    <ExclamationCircleIcon class="w-4 h-4" />
                    <span
                        v-show="loadingState['status'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <button
                    :disabled="loadingState['status'][0]"
                    type="button"
                    @click.stop="filterStatus('error', $event)"
                    class="btn btn-sm btn-error top-0 items-center"
                    :class="status == 'error' ? '' : 'btn-outline'"
                >
                    <XCircleIcon class="w-4 h-4" />
                    <span
                        v-show="loadingState['status'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
                <div class="divider divider-horizontal mx-1"></div>
                <button
                    :disabled="loadingState['delete'][0]"
                    type="button"
                    class="btn btn-sm btn-outline top-0 items-center"
                    @click="deleteAllNotification"
                >
                    Clear All
                    <span
                        v-show="loadingState['delete'][0]"
                        class="loading loading-spinner loading-sm"
                    ></span>
                </button>
            </div>
            <hr />
            <div v-if="!isNotificationEmpty" class="overflow-y-scroll h-96">
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
                                v-else-if="notification.category == 'warning'"
                                class="h-3 w-3 text-warning"
                            /><InformationCircleIcon
                                class="h-3 w-3 text-info"
                                v-else
                            />
                        </div>
                    </div>
                </div>
                <div class="text-center" v-show="next_page != null">
                    <a
                        href="javascript:;"
                        v-if="!loadingState['load'][0]"
                        class="text-blue-400"
                        @click.stop="loadMoreNotification"
                        >Load More</a
                    >
                    <span
                        v-else
                        class="loading loading-spinner loading-sm"
                    ></span>
                </div>
            </div>
            <div v-else class="p-2 text-center text-sm w-96">
                No Notification
            </div>
        </template>
    </Dropdown>
</template>
