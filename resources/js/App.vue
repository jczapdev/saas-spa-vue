<template>
    <component :is="layout" v-if="layout">
        <router-view />
    </component>
    <router-view v-else />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const layout = computed(() => {
    const meta = route.meta?.layout;
    if (!meta) return null;
    
    // If layout is an array, return a wrapper component
    if (Array.isArray(meta)) {
        const [OuterLayout, InnerLayout] = meta;
        return {
            components: { OuterLayout, InnerLayout },
            template: '<OuterLayout><InnerLayout><router-view /></InnerLayout></OuterLayout>',
        };
    }
    
    return meta;
});
</script>
