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

const submit = async (e: Event) => {
    e.preventDefault();
    if (!props.action) return;

    processing.value = true;
    errors.value = {};
    
    emit('submit', e);

    const form = e.target as HTMLFormElement;
    const formData = new FormData(form);

    // Convert FormData to JSON for Laravel
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
                ...(method !== 'GET' && method !== 'POST' ? { 'X-HTTP-Method-Override': method } : {})
            },
            body: method === 'GET' ? undefined : JSON.stringify(dataObj)
        });

        if (response.redirected) {
             // Handle redirect if needed
             window.location.href = response.url;
             return;
        }

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                // Laravel validation errors
                for (const key in data.errors) {
                    errors.value[key] = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
                }
            } else if (response.status === 401 || response.status === 419) {
                 window.location.reload();
            }
            emit('error', data);
        } else {
            if (props.resetOnSuccess) {
                // Just clear the password inputs or do a full reset
                const inputs = form.querySelectorAll('input[type="password"]');
                inputs.forEach((input) => {
                    (input as HTMLInputElement).value = '';
                });
            }
            emit('success', data);
            
            // If the endpoint usually redirects on success, we might need to check if there is a redirect path
            if (data?.redirect) {
                router.push(data.redirect);
            }
        }
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
