<script setup>
const props = defineProps({
    comodity: String,
    sub_comodity: String,
    second_level_sub_comodity: String,
    third_level_sub_comodity: String,
});
</script>
<template>
    <ul class="menu rounded-box w-auto">
        <li>
            <a>{{ props.comodity }}</a>
            <ul v-show="props.sub_comodity">
                <li>
                    <a>{{ props.sub_comodity }}</a>
                    <ul v-show="props.second_level_sub_comodity">
                        <li>
                            <a>{{ props.second_level_sub_comodity }}</a>
                            <ul v-show="props.third_level_sub_comodity">
                                <li>
                                    <a>{{ props.third_level_sub_comodity }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</template>
