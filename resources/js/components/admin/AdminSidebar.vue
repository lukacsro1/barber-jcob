<template>
    <ul class="flex flex-col gap-y-1 mt-4 px-2">
        <li v-for="item in menuItems" :key="item.path">
            <a
                :href="'/' + item.path"
                class="relative flex flex-row items-center gap-x-3 w-full rounded-lg px-3 py-2 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
                :class="isActive(item.path) ? 'bg-gray-100 dark:bg-white/5' : ''"
            >
                <div
                    class="flex items-center justify-center shrink-0 w-6 h-6"
                    :class="isActive(item.path)
                        ? 'text-gold'
                        : 'text-gray-400 dark:text-gray-500'"
                >
                    <component :is="item.icon" class="w-full h-full stroke-2" />
                </div>

                <span
                    class="flex-1 truncate text-left text-sm font-medium"
                    :class="isActive(item.path)
                        ? 'text-gold'
                        : 'text-gray-700 dark:text-gray-200'"
                >
                    {{ item.label }}
                </span>
            </a>
        </li>
    </ul>
</template>

<script setup>
import { computed } from 'vue'

import { HomeIcon, CalendarIcon, UsersIcon, PlusCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    currentPath: {
        type: String,
        default: 'admin'
    },
    userData: {
        type: Object,
        default: () => ({ name: '', role: '' })
    }
})

const menuItems = computed(() => {
    const items = [
        {
            label: 'Dashboard',
            path: 'admin',
            icon: HomeIcon
        },
        {
            label: 'Appointments',
            path: 'admin/appointments',
            icon: CalendarIcon
        },
         {
            label: 'Clients',
            path: 'admin/clients',
            icon: UsersIcon,
        }

    ]

    if (props.userData.role === 'admin') {
        items.push({
            label: 'Barbers',
            path: 'admin/barbers',
            icon: UsersIcon
        })
        items.push({
            label: 'Services',
            path: 'admin/services',
            icon: PlusCircleIcon
        })

    }


    return items
})

const isActive = (path) => {
    return props.currentPath === path
}
</script>
