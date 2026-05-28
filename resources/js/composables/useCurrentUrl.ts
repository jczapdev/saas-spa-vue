import type { ComputedRef, DeepReadonly } from 'vue';
import { computed, readonly } from 'vue';
import { useRoute } from 'vue-router';
import { toUrl } from '@/lib/utils';

export type UseCurrentUrlReturn = {
    currentUrl: DeepReadonly<ComputedRef<string>>;
    isCurrentUrl: (
        urlToCheck: string | { url?: string },
        currentUrl?: string,
        startsWith?: boolean,
    ) => boolean;
    isCurrentOrParentUrl: (
        urlToCheck: string | { url?: string },
        currentUrl?: string,
    ) => boolean;
    whenCurrentUrl: <T, F = null>(
        urlToCheck: string | { url?: string },
        ifTrue: T,
        ifFalse?: F,
    ) => T | F;
};

export function useCurrentUrl(): UseCurrentUrlReturn {
    const route = useRoute();
    const currentUrlReactive = computed(() => route.path);

    
    function isCurrentUrl(
        urlToCheck: string | { url?: string },
        currentUrl?: string,
        startsWith: boolean = false,
    ) {
        const urlToCompare = currentUrl ?? currentUrlReactive.value;
        const urlString = toUrl(urlToCheck);

        const comparePath = (path: string): boolean =>
            startsWith ? urlToCompare.startsWith(path) : path === urlToCompare;

        if (!urlString.startsWith('http')) {
            return comparePath(urlString);
        }

        try {
            const absoluteUrl = new URL(urlString);

            return comparePath(absoluteUrl.pathname);
        } catch {
            return false;
        }
    }

    function isCurrentOrParentUrl(
        urlToCheck: string | { url?: string },
        currentUrl?: string,
    ) {
        return isCurrentUrl(urlToCheck, currentUrl, true);
    }

    function whenCurrentUrl(
        urlToCheck: string | { url?: string },
        ifTrue: any,
        ifFalse: any = null,
    ) {
        return isCurrentUrl(urlToCheck) ? ifTrue : ifFalse;
    }

    return {
        currentUrl: readonly(currentUrlReactive),
        isCurrentUrl,
        isCurrentOrParentUrl,
        whenCurrentUrl,
    };
}

