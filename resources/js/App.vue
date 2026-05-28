<template>
    <component :is="layout" v-if="layout">
        <router-view />
    </component>
    <router-view v-else />
</template>

<script setup lang="ts">
import { h, computed } from 'vue';
import { useRoute, RouterView } from 'vue-router';

const route = useRoute();

const layout = computed(() => {
    const meta = route.meta?.layout;
    if (!meta) return null;
    
    // If layout is an array, return a wrapper component
    if (Array.isArray(meta)) {
        const [OuterLayout, InnerLayout] = meta;
        return {
            render() {
                return h(OuterLayout, null, {
                    default: () => h(InnerLayout, null, {
                        default: () => h(RouterView)
                    })
                });
            }
        };
    }
    
    return meta;
});
</script>
