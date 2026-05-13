<template>
    <div class="space-y-6 animate-fade-in">
        <!-- Table Card -->
        <div class="bg-[#111] border border-white/5 rounded-xl overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Barber</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Specialty</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Contact</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="barber in barbers" :key="barber.id" class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 border border-white/10 flex-shrink-0 overflow-hidden">
                                    <img v-if="barber.avatar_url" :src="barber.avatar_url" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-gold font-serif">
                                        {{ barber.name.charAt(0) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white group-hover:text-gold transition-colors">{{ barber.name }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ barber.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-300">{{ barber.specialty || 'Generalist' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-300">{{ barber.phone || 'No phone' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="editBarber(barber)" class="p-2 text-gray-500 hover:text-gold transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button class="p-2 text-gray-500 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="barbers.length === 0" class="p-12 text-center">
                <div class="text-gray-500 text-sm mb-4">No barbers found in your team.</div>
            </div>
        </div>

        <!-- Add Barber Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
            <div class="bg-[#111] border border-white/10 w-full max-w-md p-8 shadow-2xl relative animate-fade-in">
                <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors text-2xl">&times;</button>
                <h3 class="text-2xl font-serif text-white italic mb-6">{{ isEditMode ? 'Edit Barber' : 'Add New Barber' }}</h3>
                
                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Avatar Upload -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-full bg-zinc-800 border border-white/10 flex-shrink-0 overflow-hidden relative group">
                            <img v-if="avatarPreview || form.avatar_url" :src="avatarPreview || form.avatar_url" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            </div>
                            <input type="file" accept="image/*" @change="handleAvatarChange" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div class="text-[10px] uppercase tracking-widest text-gray-400">Profile Photo<br><span class="text-gray-600 lowercase tracking-normal">Max 2MB</span></div>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2">Name</label>
                        <input v-model="form.name" type="text" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2">Email</label>
                        <input v-model="form.email" type="email" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2">Password <span v-if="isEditMode" class="text-gray-600 normal-case">(Leave blank to keep current)</span></label>
                        <input v-model="form.password" type="password" :required="!isEditMode" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2">Specialty</label>
                            <input v-model="form.specialty" type="text" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors" placeholder="e.g. Master Barber">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors">
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <input v-model="form.show_in_gallery" type="checkbox" id="show_in_gallery" class="w-4 h-4 accent-gold bg-[#161616] border-white/10">
                        <label for="show_in_gallery" class="text-[10px] uppercase tracking-widest text-gray-400">Show in Public Gallery</label>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full bg-gold text-dark font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-gold-light transition-all mt-6 shadow-[0_0_15px_rgba(197,160,89,0.2)] disabled:opacity-50">
                        <span v-if="!loading">{{ isEditMode ? 'Update Barber' : 'Save Barber' }}</span>
                        <span v-else>Saving...</span>
                    </button>
                    <div v-if="errorMsg" class="text-red-400 text-xs mt-3 text-center bg-red-400/10 py-2 border border-red-400/20">{{ errorMsg }}</div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const props = defineProps({
    initialBarbers: {
        type: Array,
        default: () => []
    }
})

const barbers = ref(props.initialBarbers)
const isModalOpen = ref(false)
const isEditMode = ref(false)
const editingBarberId = ref(null)
const loading = ref(false)
const errorMsg = ref('')
const avatarPreview = ref(null)

const form = reactive({
    name: '',
    email: '',
    password: '',
    specialty: '',
    phone: '',
    show_in_gallery: true,
    avatar_url: null,
    avatar: null
})

const closeModal = () => {
    isModalOpen.value = false
    isEditMode.value = false
    editingBarberId.value = null
    errorMsg.value = ''
    avatarPreview.value = null
    form.name = ''
    form.email = ''
    form.password = ''
    form.specialty = ''
    form.phone = ''
    form.show_in_gallery = true
    form.avatar_url = null
    form.avatar = null
}

const editBarber = (barber) => {
    isEditMode.value = true
    editingBarberId.value = barber.id
    form.name = barber.name
    form.email = barber.email
    form.password = ''
    form.specialty = barber.specialty || ''
    form.phone = barber.phone || ''
    form.show_in_gallery = barber.show_in_gallery
    form.avatar_url = barber.avatar_url
    form.avatar = null
    avatarPreview.value = null
    isModalOpen.value = true
}

const handleAvatarChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.avatar = file
        avatarPreview.value = URL.createObjectURL(file)
    }
}

const submitForm = async () => {
    loading.value = true
    errorMsg.value = ''
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        
        const formData = new FormData()
        formData.append('name', form.name)
        formData.append('email', form.email)
        if (form.password) formData.append('password', form.password)
        formData.append('specialty', form.specialty)
        formData.append('phone', form.phone)
        formData.append('show_in_gallery', form.show_in_gallery)
        if (form.avatar) {
            formData.append('avatar', form.avatar)
        }

        const url = isEditMode.value ? `/admin/barbers/${editingBarberId.value}` : '/admin/barbers'
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })

        if (!response.ok) {
            const data = await response.json()
            throw new Error(data.message || 'An error occurred while saving.')
        }

        const savedBarber = await response.json()
        
        if (isEditMode.value) {
            const idx = barbers.value.findIndex(b => b.id === savedBarber.id)
            if (idx !== -1) barbers.value[idx] = savedBarber
        } else {
            barbers.value.push(savedBarber)
        }
        
        closeModal()
    } catch (error) {
        errorMsg.value = error.message
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    window.addEventListener('open-barber-modal', () => {
        isModalOpen.value = true
    })
})
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
