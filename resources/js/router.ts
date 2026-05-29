import { h, watch } from 'vue';
import { createRouter, createWebHistory, type RouteRecordRaw, RouterView } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { useAuth } from '@/composables/useUser';

// Define all routes manually or auto-discover from pages
const routes: RouteRecordRaw[] = [
    {
        path: '/',
        redirect: '/auth/login',
    },
    {
        path: '/auth',
        component: { render: () => h(RouterView) },
        children: [
            {
                path: 'login',
                component: () => import('@/pages/system/auth/Login.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'register',
                component: () => import('@/pages/system/auth/Register.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'forgot-password',
                component: () => import('@/pages/system/auth/ForgotPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'reset-password/:token',
                component: () => import('@/pages/system/auth/ResetPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'verify-email/:id/:hash',
                component: () => import('@/pages/system/auth/VerifyEmail.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'confirm-password',
                component: () => import('@/pages/system/auth/ConfirmPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'two-factor',
                component: () => import('@/pages/system/auth/TwoFactorChallenge.vue'),
                meta: { layout: AuthLayout },
            },
        ],
    },
    {
        path: '/dashboard',
        component: () => import('@/pages/system/Dashboard.vue'),
        meta: { layout: AppLayout, requiresAuth: true },
    },
    {
        path: '/settings',
        component: { render: () => h(RouterView) },
        meta: { layout: AppLayout, requiresAuth: true },
        children: [
            {
                path: 'profile',
                component: () => import('@/pages/system/settings/Profile.vue'),
                meta: { layout: [AppLayout, SettingsLayout] },
            },
            {
                path: 'security',
                component: () => import('@/pages/system/settings/Security.vue'),
                meta: { layout: [AppLayout, SettingsLayout] },
            },
            {
                path: 'appearance',
                component: () => import('@/pages/system/settings/Appearance.vue'),
                meta: { layout: [AppLayout, SettingsLayout] },
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/auth/login',
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const { isAuthenticated, isAuthReady } = useAuth();

    // Wait for auth initialization to complete before making redirect decisions
    if (!isAuthReady.value) {
        await new Promise<void>((resolve) => {
            const stop = watch(isAuthReady, (ready) => {
                if (ready) {
                    stop();
                    resolve();
                }
            });
        });
    }

    if (to.meta.requiresAuth && !isAuthenticated.value) {
        return { path: '/auth/login' };
    }

    if (to.path.startsWith('/auth') && isAuthenticated.value) {
        return { path: '/dashboard' };
    }
});

export default router;
