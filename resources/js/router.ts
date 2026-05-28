import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

// Define all routes manually or auto-discover from pages
const routes: RouteRecordRaw[] = [
    {
        path: '/',
        redirect: '/auth/login',
    },
    {
        path: '/auth',
        component: { template: '<router-view />' },
        children: [
            {
                path: 'login',
                component: () => import('@/pages/auth/Login.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'register',
                component: () => import('@/pages/auth/Register.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'forgot-password',
                component: () => import('@/pages/auth/ForgotPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'reset-password/:token',
                component: () => import('@/pages/auth/ResetPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'verify-email/:id/:hash',
                component: () => import('@/pages/auth/VerifyEmail.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'confirm-password',
                component: () => import('@/pages/auth/ConfirmPassword.vue'),
                meta: { layout: AuthLayout },
            },
            {
                path: 'two-factor',
                component: () => import('@/pages/auth/TwoFactorChallenge.vue'),
                meta: { layout: AuthLayout },
            },
        ],
    },
    {
        path: '/dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { layout: AppLayout, requiresAuth: true },
    },
    {
        path: '/settings',
        component: { template: '<router-view />' },
        meta: { layout: AppLayout, requiresAuth: true },
        children: [
            {
                path: 'profile',
                component: () => import('@/pages/settings/Profile.vue'),
                meta: { layout: [AppLayout, SettingsLayout] },
            },
            {
                path: 'security',
                component: () => import('@/pages/settings/Security.vue'),
                meta: { layout: [AppLayout, SettingsLayout] },
            },
            {
                path: 'appearance',
                component: () => import('@/pages/settings/Appearance.vue'),
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

export default router;
