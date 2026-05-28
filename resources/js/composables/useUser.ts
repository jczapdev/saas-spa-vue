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
}

const authState = ref<AuthState>({
    user: null,
});

export function useAuth() {
    const user = computed(() => authState.value.user);
    const isAuthenticated = computed(() => authState.value.user !== null);

    const setUser = (newUser: User | null) => {
        authState.value.user = newUser;
    };

    const logout = () => {
        authState.value.user = null;
    };

    return {
        user,
        isAuthenticated,
        setUser,
        logout,
    };
}

// Initialize auth from server on app load
export async function initializeAuth() {
    try {
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
    }
}
