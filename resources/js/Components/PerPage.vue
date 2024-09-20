<script setup>
const props = defineProps({
    per_page: String | Number,
});
console.log(props.per_page);
const emit = defineEmits(["update:modelValue", "getData"]);

const handleSelectChange = (event) => {
    const selectedValue = event.target.value;
    // Emit two events
    emit("update:per_page", selectedValue);
    emit("getData", 1); // Custom event
};
</script>
<template>
    <div class="flex gap-3 items-center">
        <select
            @change="handleSelectChange"
            :value="props.per_page"
            class="select select-bordered select-sm max-w-xs text-xs"
        >
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <label class="text-xs md:text-base lg:text-base"
            >entries per page</label
        >
    </div>
</template>
