import { ref } from 'vue';
import Form from '@/components/Form.vue';
import Head from '@/components/Head.vue';
import Link from '@/components/Link.vue';
import { router as vueRouter } from '@/router';

// Basic router shim
export const router = {
    get: (url: string) => vueRouter.push(url),
    post: (url: string, data?: any) => {
         return fetch(url, {
             method: 'POST',
             headers: {
                 'X-Requested-With': 'XMLHttpRequest',
                 'Accept': 'application/json',
                 'Content-Type': 'application/json'
             },
             body: JSON.stringify(data || {})
         }).then(res => {
             if (res.redirected) window.location.href = res.url;
         });
    },
    put: () => {},
    delete: () => {},
    visit: (url: string) => vueRouter.push(url)
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
