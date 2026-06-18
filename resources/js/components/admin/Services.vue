<template>
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
</template>

<script setup>
import { ref, reactive } from 'vue'

const props = defineProps({
    initialServices: {
        type: Array,
        default: () => []
    },
    user: {
        type: Object,
        default: () => ({})
    }
})

const services = ref(props.initialServices)
const showServiceForm = ref(false)
const isSavingService = ref(false)

const newService = reactive({
    name: '',
    price: '',
    duration_minutes: ''
})

const saveService = async () => {
    if (!newService.name || !newService.price || !newService.duration_minutes) {
        alert('Please fill out all fields.')
        return
    }
    
    isSavingService.value = true
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch('/admin/services', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(newService)
        })
        
        if (response.ok) {
            const savedService = await response.json()
            services.value.push(savedService)
            services.value.sort((a, b) => a.name.localeCompare(b.name))
            
            // Reset form
            newService.name = ''
            newService.price = ''
            newService.duration_minutes = ''
            showServiceForm.value = false
        } else {
            const data = await response.json()
            throw new Error(data.message || 'Failed to save service.')
        }
    } catch (error) {
        alert(error.message)
    } finally {
        isSavingService.value = false
    }
}

const deleteService = async (id) => {
    if (!confirm('Are you sure you want to delete this service?')) return
    
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch(`/admin/services/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        
        if (response.ok) {
            services.value = services.value.filter(s => s.id !== id)
        } else {
            const data = await response.json()
            throw new Error(data.message || 'Failed to delete service.')
        }
    } catch (error) {
        alert(error.message)
    }
}
</script>