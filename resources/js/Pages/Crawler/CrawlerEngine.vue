<script setup>
import { onBeforeMount,ref } from 'vue';
import { ArrowPathIcon } from '@heroicons/vue/24/solid'


const props = defineProps({
    marketplace_id: Number,
    marketplace: String,
    icon: String,
    location: String
})

const status = ref(null)
const loading = ref(false)

const checkStatus = async() => {
    status.value = null
    await axios.get(route('crawler.status',1)).then((response)=>{
        status.value = response.data
    })
}

onBeforeMount(()=>{
    checkStatus()
})

const crawlerData = async() => {
    if(confirm('Lakukan Crawling Data ?')){
        loading.value = true
        await axios.post(route('crawler.run'),{
        marketplace_id: props.marketplace_id,
        marketplace: props.marketplace,
        location: props.location,
        keyword: 'wardah'
        }).then((response)=>{
            checkStatus()
        }).catch((response)=>{
            console.log(response)
        }).finally(() => {
            loading.value = false
        })
    }
}

</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <img :src="`/assets/img/marketplace/${props.icon}`">
        <div class="divider"></div>
        <div class="flex">
            Engine Status : <span :class="status ? 'text-accent' : 'text-info'" class="flex" v-if="status != null">{{status ? 'Working' : 'Iddle'}}&nbsp;<a @click="checkStatus" class="align-middle" href="javascript:;"> <ArrowPathIcon class="h-5 w-5 text-blue-500" /></a></span><span class="loading loading-spinner loading-sm" v-else></span>
        </div>
        <button class="btn btn-sm btn-outline btn-accent mt-5" :disabled="status || loading" v-if="status != null" @click="crawlerData">
            <span v-if="!status">
                <span v-if="!loading">Start</span>
                <span class="loading loading-spinner loading-sm" v-else></span>
            </span>
            <span v-else class="loading loading-bars loading-sm"></span>
        </button>
        </div>
    </div>
</template>
