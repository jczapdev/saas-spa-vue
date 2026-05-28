<script setup lang="ts">
import { watchEffect } from 'vue';
import { useTitle } from '@vueuse/core';

const props = defineProps<{
    title?: string;
}>();

const title = useTitle();

watchEffect(() => {
    if (props.title) {
        title.value = props.title;
    }
});
</script>

<template>
    <slot />
</template>
