import { createApp } from 'vue';
import router from '@/router';
import { initializeTheme } from '@/composables/useAppearance';
import { initializeFlashToast } from '@/lib/flashToast';
import { initializeAuth } from '@/composables/useUser';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import App from '@/App.vue';

const app = createApp(App);

app.use(router);

// Register global layout components
app.component('AppLayout', AppLayout);
app.component('AuthLayout', AuthLayout);
app.component('SettingsLayout', SettingsLayout);

// Initialize theme on app load
initializeTheme();

// Initialize flash toast notifications
initializeFlashToast();

// Initialize authentication (non-blocking - Vue reactivity handles the update)
initializeAuth();

app.mount('#app');

