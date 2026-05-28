import { ref } from 'vue';
import Form from '@/components/Form.vue';
import Head from '@/components/Head.vue';
import Link from '@/components/Link.vue';
import { router as vueRouter } from '@/router';

function getCsrfToken() {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; XSRF-TOKEN=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    return '';
}

// Basic router shim
export const router = {
    get: (route: string | UrlMethodPair) => {
        const url = typeof route === 'string' ? route : route.url;
        return vueRouter.push(url);
    },
    post: (route: string | UrlMethodPair, data?: any) => {
         const url = typeof route === 'string' ? route : route.url;
         return fetch(url, {
             method: 'POST',
             headers: {
                 'X-Requested-With': 'XMLHttpRequest',
                 'Accept': 'application/json',
                 'Content-Type': 'application/json',
                 'X-XSRF-TOKEN': getCsrfToken()
             },
             credentials: 'same-origin',
             body: JSON.stringify(data || {})
         }).then(res => {
             if (res.redirected) window.location.href = res.url;
             // Also handle manual reload if the response implies successful logout
             else if (res.ok) window.location.href = '/auth/login';
         });
    },
    put: (route: string | UrlMethodPair, data?: any) => {
         const url = typeof route === 'string' ? route : route.url;
         return fetch(url, {
             method: 'PUT',
             headers: {
                 'X-Requested-With': 'XMLHttpRequest',
                 'Accept': 'application/json',
                 'Content-Type': 'application/json',
                 'X-XSRF-TOKEN': getCsrfToken()
             },
             credentials: 'same-origin',
             body: JSON.stringify(data || {})
         }).then(res => {
             if (res.redirected) window.location.href = res.url;
         });
    },
    delete: (route: string | UrlMethodPair, data?: any) => {
         const url = typeof route === 'string' ? route : route.url;
         return fetch(url, {
             method: 'DELETE',
             headers: {
                 'X-Requested-With': 'XMLHttpRequest',
                 'Accept': 'application/json',
                 'Content-Type': 'application/json',
                 'X-XSRF-TOKEN': getCsrfToken()
             },
             credentials: 'same-origin',
             body: JSON.stringify(data || {})
         }).then(res => {
             if (res.redirected) window.location.href = res.url;
             else if (res.ok) window.location.reload();
         });
    },
    visit: (route: string | UrlMethodPair) => {
        const url = typeof route === 'string' ? route : route.url;
        return vueRouter.push(url);
    }
};

export const usePage = () => {
    return {
        props: ref({}),
        url: window.location.pathname
    };
};

export const setLayoutProps = () => {};

export type InertiaLinkProps = {
    href: string;
};

export type UrlMethodPair = {
    url: string;
    method: string;
};

export type InertiaConfig = any;

export { Form, Head, Link };
