<template>
    <router-link :to="href" v-bind="$attrs">
        <slot />
    </router-link>
</template>

<script setup lang="ts">
import { defineOptions } from 'vue';

defineOptions({
    inheritAttrs: false
});

defineProps<{
    href: string | object;
}>();
</script>
