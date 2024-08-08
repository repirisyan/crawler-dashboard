<script setup>
const props = defineProps({
    keyword: String,
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
            <ul v-if="props.sub_comodity">
                <li>
                    <a>{{ props.sub_comodity }}</a>
                    <ul v-if="props.second_level_sub_comodity">
                        <li>
                            <a>{{ props.second_level_sub_comodity }}</a>
                            <ul v-if="props.third_level_sub_comodity">
                                <li>
                                    <a>{{ props.third_level_sub_comodity }}</a>
                                    <ul>
                                        <li>
                                            <a>{{ props.keyword }}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul v-else>
                                <li>
                                    <a>{{ props.keyword }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul v-else>
                        <li>
                            <a>{{ props.keyword }}</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul v-else>
                <li>
                    <a>{{ props.keyword }}</a>
                </li>
            </ul>
        </li>
    </ul>
</template>
