import type { ComputedRef } from 'vue';
import { computed, ref } from 'vue';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    two_factor_enabled?: boolean;
    appearance?: 'light' | 'dark' | 'system';
    [key: string]: any;
}

export interface AuthState {
    user: User | null;
    ready: boolean;
}

const authState = ref<AuthState>({
    user: null,
    ready: false,
});

export function useAuth() {
    const user = computed(() => authState.value.user);
    const isAuthenticated = computed(() => authState.value.user !== null);
    const isAuthReady = computed(() => authState.value.ready);

    const setUser = (newUser: User | null) => {
        authState.value.user = newUser;
    };

    const logout = () => {
        authState.value.user = null;
    };

    return {
        user,
        isAuthenticated,
        isAuthReady,
        setUser,
        logout,
    };
}

// Initialize auth from server on app load
export async function initializeAuth() {
    try {
        await fetch('/sanctum/csrf-cookie', {
            headers: { 'Accept': 'application/json' }
        });

        const response = await fetch('/api/user', {
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'include',
        });

        if (response.ok) {
            const user = await response.json();
            const { setUser } = useAuth();
            setUser(user);
        }
    } catch {
        // User is not authenticated
    } finally {
        // Mark auth as ready regardless of success or failure
        authState.value.ready = true;
    }
}

