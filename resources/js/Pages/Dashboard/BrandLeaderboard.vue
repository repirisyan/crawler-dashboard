<script setup>
import { usePage } from "@inertiajs/vue3";
import VueApexCharts from "vue3-apexcharts";
import { onMounted, ref } from "vue";
import axios from "axios";
import moment from "moment";

const page = usePage();

const chartCategories = ref([]);
const chartOptions = ref({});
const chartData = ref([]);
const chartSeries = ref([]);
const year = ref(moment().year());
const month = ref(moment().month() + 1);
const chartLoad = ref(true);
const isDataEmpty = ref(false);

onMounted(async () => {
    await getBrandLeaderboard();
});

const getBrandLeaderboard = async () => {
    // Reset chart values
    chartCategories.value = [];
    chartData.value = [];
    chartLoad.value = true;

    try {
        // Make API call to get brand leaderboard
        const response = await axios.get(
            `${import.meta.env.VITE_APP_CRAWLER_API}/brand-leaderboard`,
            {
                headers: {
                    Authorization: `Bearer ${page.props.auth.user.remember_token}`,
                },
                params: {
                    year: year.value,
                    month: month.value,
                },
            },
        );

        // Destructure response data
        const { data } = response;

        // Populate chart data and categories
        data.forEach(({ _id: { brand }, totalProduct }) => {
            chartCategories.value.push(brand);
            chartData.value.push(totalProduct);
        });

        // Check if data is empty and update chart options
        updateChart();
    } catch (error) {
        console.error("Error fetching brand leaderboard:", error);
    } finally {
        chartLoad.value = false;
    }
};

// Separate function to update chart options and series
const updateChart = () => {
    const data = chartData.value;
    isDataEmpty.value = data.length === 0;

    if (!isDataEmpty.value) {
        chartOptions.value = {
            chart: {
                id: "BrandLeaderboardChart",
                width: "100%",
                background: "#FFFFFF00", // Transparent background
            },
            xaxis: {
                categories: chartCategories.value,
            },
            theme: {
                mode: window.matchMedia("(prefers-color-scheme: dark)").matches
                    ? "dark"
                    : "light",
            },
        };
        chartSeries.value = [
            {
                name: "Total Product",
                data: chartData.value,
            },
        ];
    }
};
</script>
<style scoped>
#SellerDistributionChart {
    border-radius: 25px;
}
</style>
<template>
    <div class="grid grid-cols-1 mt-5">
        <div
            class="card bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <div class="card-header">
                <h1 class="text-center text-2xl font-extrabold mt-5">
                    Top 20 Brand Chart
                    {{ moment(month, "M").format("MMMM") }}
                    {{ year }}
                </h1>
                <div class="px-10 pt-7 flex gap-3">
                    <select
                        v-model.lazy="month"
                        class="select select-bordered"
                        @change="getBrandLeaderboard"
                    >
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                    <select
                        v-model.lazy="year"
                        class="select select-bordered"
                        @change="getBrandLeaderboard"
                    >
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center" v-if="chartLoad">
                    <span class="loading loading-spinner loading-md"></span>
                </div>
                <div v-else>
                    <VueApexCharts
                        v-if="!isDataEmpty"
                        type="bar"
                        height="450px"
                        :options="chartOptions"
                        :series="chartSeries"
                    ></VueApexCharts>
                    <p v-else class="text-center">No Data Available</p>
                </div>
            </div>
        </div>
    </div>
</template>
