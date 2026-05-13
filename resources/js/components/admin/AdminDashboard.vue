<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center bg-zinc-900 p-6 rounded-xl border border-white/5">
            <div>
                <h1 class="text-2xl font-serif text-white">Dashboard</h1>
                <p class="text-sm text-gray-400 mt-1">Welcome back, {{ user.name }}!</p>
            </div>
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-sm font-medium hover:bg-amber-500/20 transition-all">
                    Új Foglalás
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
           

            <!-- Stat Card 2 -->
            <div class="bg-zinc-900 p-6 rounded-xl border border-white/5 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="text-sm text-gray-400 font-medium mb-1">Mai Vendégek</div>
                    <div class="text-3xl font-serif text-white mb-2">{{ stats.today_appointments }}</div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <span>{{ stats.pending_appointments }} még hátravan</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div v-if="user.role === 'admin'" class="bg-zinc-900 p-6 rounded-xl border border-white/5 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="text-sm text-gray-400 font-medium mb-1">Aktív Borbélyok</div>
                    <div class="text-3xl font-serif text-white mb-2">{{ stats.active_barbers }}</div>
                    <div class="text-xs text-amber-500 flex items-center gap-1">
                        <span>Mindenki bejelentkezve</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Management Area -->
        <div v-if="user.role === 'admin'" class="bg-zinc-900 rounded-xl border border-white/5 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                <h2 class="text-xl font-serif text-white flex items-center gap-3">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Services & Pricing
                </h2>
                <button @click="showServiceForm = !showServiceForm" class="text-[10px] uppercase tracking-widest font-bold text-gold hover:text-white transition-colors">
                    {{ showServiceForm ? 'Cancel' : '+ Add Service' }}
                </button>
            </div>

            <!-- Add Service Form -->
            <div v-if="showServiceForm" class="p-8 bg-black/20 border-b border-white/5 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Service Name</label>
                        <input v-model="newService.name" type="text" placeholder="e.g. Classic Haircut" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Price (RON)</label>
                        <input v-model="newService.price" type="number" placeholder="35.00" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Duration (min)</label>
                        <input v-model="newService.duration_minutes" type="number" placeholder="30" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button @click="saveService" :disabled="isSavingService" class="bg-gold text-dark font-bold px-8 py-2.5 rounded-lg uppercase tracking-widest text-[10px] hover:bg-white hover:text-black transition-all disabled:opacity-50">
                        {{ isSavingService ? 'Saving...' : 'Create Service' }}
                    </button>
                </div>
            </div>

            <!-- Services List -->
            <div class="p-0">
                <div v-if="services.length === 0" class="p-12 text-center text-gray-500 text-sm italic">
                    No services configured yet.
                </div>
                <div v-else class="divide-y divide-white/5">
                    <div v-for="service in services" :key="service.id" class="p-6 flex items-center justify-between hover:bg-white/[0.01] transition-colors group">
                        <div class="flex items-center gap-6">
                            <div class="w-10 h-10 rounded-lg bg-zinc-800 border border-white/5 flex items-center justify-center text-gold group-hover:border-gold/30 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L5 19m0-14l4.121 4.121" /></svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-white">{{ service.name }}</div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ service.duration_minutes }} minutes</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-8">
                            <div class="text-lg font-serif text-gold">{{ service.price }} RON</div>
                            <button @click="deleteService(service.id)" class="p-2 text-gray-600 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings Area -->
        <div class="bg-zinc-900 p-8 rounded-xl border border-white/5 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-gold opacity-50 group-hover:opacity-100 transition-opacity"></div>
            
            <h2 class="text-xl font-serif text-white mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Profile Settings
            </h2>

            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-lg hover:border-gold/30 transition-all">
                    <div>
                        <div class="text-sm font-medium text-white mb-1">Appear in Public Gallery</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest">Show your profile in the barber gallery for clients to book.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="user.show_in_gallery" @change="updateProfile" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-500 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold peer-checked:after:bg-dark"></div>
                    </label>
                </div>
            </div>

            <div v-if="saveStatus" class="mt-4 text-[10px] uppercase tracking-[0.2em] animate-pulse" :class="saveStatus === 'success' ? 'text-green-500' : 'text-red-500'">
                {{ saveStatus === 'success' ? 'Settings saved successfully' : 'Error saving settings' }}
            </div>
        </div>

        <!-- Interactive Vue Area -->
        <div class="bg-zinc-900 p-6 rounded-xl border border-white/5 opacity-50">
            <h2 class="text-xs uppercase tracking-widest text-gray-500 mb-4">Debug Actions</h2>
            <div class="flex gap-4">
                <button @click="testCount++" class="px-4 py-2 bg-white/5 border border-white/10 text-[10px] uppercase tracking-widest text-gray-400 rounded hover:bg-white/10 transition-all">
                    Vue Count: {{ testCount }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    userData: {
        type: Object,
        default: () => ({ name: 'Admin', role: 'admin', show_in_gallery: false })
    },
    statsData: {
        type: Object,
        default: () => ({
            revenue: 0,
            active_barbers: 0,
            today_appointments: 0,
            pending_appointments: 0
        })
    },
    servicesData: {
        type: Array,
        default: () => []
    }
})

const user = ref(props.userData)
const stats = ref(props.statsData)
const services = ref(props.servicesData)
const testCount = ref(0)
const saveStatus = ref(null)

// Service Management State
const showServiceForm = ref(false)
const isSavingService = ref(false)
const newService = ref({
    name: '',
    price: '',
    duration_minutes: 30
})

const saveService = async () => {
    if (!newService.value.name || !newService.value.price) return
    
    isSavingService.value = true
    try {
        const response = await fetch('/admin/services', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(newService.value)
        })

        if (response.ok) {
            const created = await response.json()
            services.value.push(created)
            newService.value = { name: '', price: '', duration_minutes: 30 }
            showServiceForm.value = false
        }
    } catch (error) {
        console.error('Error saving service:', error)
    } finally {
        isSavingService.value = false
    }
}

const deleteService = async (id) => {
    if (!confirm('Are you sure you want to delete this service?')) return
    
    try {
        const response = await fetch(`/admin/services/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })

        if (response.ok) {
            services.value = services.value.filter(s => s.id !== id)
        }
    } catch (error) {
        console.error('Error deleting service:', error)
    }
}

const updateProfile = async () => {
    saveStatus.value = 'saving'
    try {
        const response = await fetch('/admin/profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                show_in_gallery: user.value.show_in_gallery
            })
        })

        if (response.ok) {
            saveStatus.value = 'success'
            setTimeout(() => saveStatus.value = null, 3000)
        } else {
            saveStatus.value = 'error'
        }
    } catch (error) {
        saveStatus.value = 'error'
    }
}
</script>

<style scoped>
/* Optional: add any component specific styles here */
</style>
