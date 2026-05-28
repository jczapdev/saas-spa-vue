<script setup lang="ts">
import { Link, router } from '@/lib/inertia-spa';
import { LogOut, Settings } from 'lucide-vue-next';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

const handleLogout = () => {
    router.post(logout());
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem class="cursor-pointer" @click="router.get(edit())">
            <Settings class="mr-2 h-4 w-4" />
            Settings
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem class="cursor-pointer" @click="handleLogout" data-test="logout-button">
        <LogOut class="mr-2 h-4 w-4" />
        Log out
    </DropdownMenuItem>
</template>
