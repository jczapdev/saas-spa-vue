<script setup lang="ts">
type Props = {
    href: string;
    tabindex?: number;
};

defineProps<Props>();
</script>

<template>
    <router-link
        :to="href"
        :tabindex="tabindex"
        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
    >
        <slot />
    </router-link>
</template>
