<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps<{
    action?: string;
    method?: string;
    resetOnSuccess?: string[];
}>();

const emit = defineEmits(['success', 'error', 'submit']);

const processing = ref(false);
const errors = ref<Record<string, string>>({});
const router = useRouter();

function getCsrfToken() {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; XSRF-TOKEN=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    return '';
}

const submit = async (e: Event) => {
    e.preventDefault();
    if (!props.action) return;

    processing.value = true;
    errors.value = {};

    emit('submit', e);

    const form = e.target as HTMLFormElement;
    const formData = new FormData(form);

    const dataObj: Record<string, any> = {};
    formData.forEach((value, key) => {
        dataObj[key] = value;
    });

    const method = (props.method || 'post').toUpperCase();

    try {
        const response = await fetch(props.action, {
            method: method === 'GET' ? 'GET' : 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
                ...(method !== 'GET' && method !== 'POST' ? { 'X-HTTP-Method-Override': method } : {}),
            },
            credentials: 'same-origin',
            body: method === 'GET' ? undefined : JSON.stringify(dataObj),
        });

        // Fortify returns 200/204 on success when Accept: application/json is set.
        // response.redirected happens when fetch follows a 302 (e.g. logout).
        if (response.ok || response.redirected) {
            const data = response.ok ? await response.json().catch(() => ({})) : {};

            if (props.resetOnSuccess) {
                form.querySelectorAll('input[type="password"]').forEach((input) => {
                    (input as HTMLInputElement).value = '';
                });
            }

            emit('success', data);

            if (data?.redirect) {
                await router.push(data.redirect);
            }

            return;
        }

        // Error responses
        const data = await response.json().catch(() => ({}));

        if (response.status === 422 && data.errors) {
            for (const key in data.errors) {
                errors.value[key] = Array.isArray(data.errors[key])
                    ? data.errors[key][0]
                    : data.errors[key];
            }
        } else if (response.status === 419) {
            // CSRF token expired — reload to get a fresh one
            window.location.reload();

            return;
        }

        emit('error', data);
    } catch (err) {
        emit('error', err);
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <form @submit="submit" :action="action" :method="method">
        <slot :errors="errors" :processing="processing" />
    </form>
</template>
