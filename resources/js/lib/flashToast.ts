import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

// Store to hold flash messages
const flashMessageStore = new Map<string, FlashToast>();

export function setFlashMessage(key: string, data: FlashToast): void {
    flashMessageStore.set(key, data);
}

export function getFlashMessage(key: string): FlashToast | undefined {
    return flashMessageStore.get(key);
}

export function clearFlashMessage(key: string): void {
    flashMessageStore.delete(key);
}

export function displayFlashToast(data: FlashToast): void {
    toast[data.type](data.message);
}

export function initializeFlashToast(): void {
    // Check for flash messages in sessionStorage
    const flashJson = sessionStorage.getItem('flash_toast');
    
    if (flashJson) {
        try {
            const data = JSON.parse(flashJson) as FlashToast;
            displayFlashToast(data);
            sessionStorage.removeItem('flash_toast');
        } catch {
            // Ignore parsing errors
        }
    }
}

