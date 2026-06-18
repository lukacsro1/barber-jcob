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
                                <button @click="manageSchedule(barber)" class="p-2 text-gray-500 hover:text-gold transition-colors" title="Manage Schedule">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </button>
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
        <Teleport to="body">
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
                            <div class="text-[10px] uppercase tracking-widest text-gray-400">
                                Profile Photo<br>
                                <span class="text-gray-600 lowercase tracking-normal">Max 2MB</span>
                                <div v-if="avatarPreview || form.avatar_url" class="mt-2">
                                    <button type="button" @click="removeAvatar" class="text-red-400 hover:text-red-300 font-bold uppercase tracking-wider text-[9px] flex items-center gap-1 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
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
        </Teleport>

        <!-- Schedule Modal -->
        <Teleport to="body">
            <div v-if="isScheduleModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 overflow-y-auto animate-fade-in">
                <div class="bg-[#111] border border-white/10 w-full max-w-2xl p-8 shadow-2xl relative my-8">
                    <button @click="closeScheduleModal" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors text-2xl">&times;</button>
                    <h3 class="text-2xl font-serif text-white italic mb-6">Manage Schedule for {{ activeBarber?.name }}</h3>
                    
                    <div class="flex border-b border-white/5 mb-6">
                        <button @click="scheduleTab = 'hours'" class="px-4 py-2 text-xs uppercase tracking-widest font-bold transition-all border-b-2" :class="scheduleTab === 'hours' ? 'border-gold text-gold' : 'border-transparent text-gray-500 hover:text-white'">Weekly Hours</button>
                        <button @click="scheduleTab = 'daysoff'" class="px-4 py-2 text-xs uppercase tracking-widest font-bold transition-all border-b-2" :class="scheduleTab === 'daysoff' ? 'border-gold text-gold' : 'border-transparent text-gray-500 hover:text-white'">Days Off / Holidays</button>
                    </div>

                    <!-- TAB 1: Weekly Hours -->
                    <div v-if="scheduleTab === 'hours'" class="space-y-4">
                        <div v-for="(dayName, idx) in weekdayNames" :key="idx" class="grid grid-cols-12 gap-4 items-center p-3 bg-[#161616] border border-white/5 rounded">
                            <div class="col-span-3 flex items-center gap-2">
                                <input type="checkbox" :id="'day_' + idx" v-model="localSchedules[idx].is_working" class="w-4 h-4 accent-gold bg-[#111] border-white/10">
                                <label :for="'day_' + idx" class="text-xs font-bold text-gray-300 capitalize cursor-pointer">{{ dayName }}</label>
                            </div>
                            <div class="col-span-4 flex items-center gap-2">
                                <label class="text-[9px] uppercase tracking-wider text-gray-600">Start</label>
                                <input type="time" v-model="localSchedules[idx].start_time" :disabled="!localSchedules[idx].is_working" class="bg-[#111] border border-white/10 rounded px-2 py-1 text-xs text-white focus:outline-none focus:border-gold disabled:opacity-30">
                            </div>
                            <div class="col-span-4 flex items-center gap-2">
                                <label class="text-[9px] uppercase tracking-wider text-gray-600">End</label>
                                <input type="time" v-model="localSchedules[idx].end_time" :disabled="!localSchedules[idx].is_working" class="bg-[#111] border border-white/10 rounded px-2 py-1 text-xs text-white focus:outline-none focus:border-gold disabled:opacity-30">
                            </div>
                        </div>

                        <button @click="saveSchedules" :disabled="savingSchedule" class="w-full bg-gold text-dark font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-gold-light transition-all mt-6 shadow-[0_0_15px_rgba(197,160,89,0.2)] disabled:opacity-50">
                            {{ savingSchedule ? 'Saving...' : 'Save Weekly Hours' }}
                        </button>
                        <div v-if="scheduleSuccessMsg" class="text-green-400 text-xs mt-3 text-center bg-green-400/10 py-2 border border-green-400/20">{{ scheduleSuccessMsg }}</div>
                    </div>

                    <!-- TAB 2: Days Off -->
                    <div v-if="scheduleTab === 'daysoff'" class="space-y-6">
                        <!-- Add Day Off Form -->
                        <form @submit.prevent="addDayOff" class="grid grid-cols-12 gap-4 items-end bg-[#161616] p-4 border border-white/5 rounded">
                            <div class="col-span-5 space-y-2">
                                <label class="block text-[9px] uppercase tracking-widest text-gray-400">Date</label>
                                <input type="date" v-model="newDayOff.date" required class="w-full bg-[#111] border border-white/10 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-gold">
                            </div>
                            <div class="col-span-5 space-y-2">
                                <label class="block text-[9px] uppercase tracking-widest text-gray-400">Reason</label>
                                <input type="text" v-model="newDayOff.reason" placeholder="e.g. Vacation" class="w-full bg-[#111] border border-white/10 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-gold">
                            </div>
                            <div class="col-span-2">
                                <button type="submit" :disabled="addingDayOff" class="w-full bg-gold text-dark font-bold py-2 rounded text-xs hover:bg-gold-light transition-colors disabled:opacity-50">Add</button>
                            </div>
                        </form>

                        <!-- Days Off List -->
                        <div class="space-y-2 max-h-60 overflow-y-auto no-scrollbar">
                            <h4 class="text-xs uppercase tracking-widest text-gray-500 font-bold">Planned Days Off</h4>
                            <div v-if="daysOff.length === 0" class="text-gray-600 text-xs italic py-4 text-center">No days off planned.</div>
                            <div v-else v-for="day in daysOff" :key="day.id" class="flex justify-between items-center bg-[#161616] p-3 border border-white/5 rounded animate-fade-in">
                                <div>
                                    <div class="text-xs font-bold text-white">{{ day.date }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ day.reason || 'No reason specified' }}</div>
                                </div>
                                <button type="button" @click="deleteDayOff(day.id)" class="text-gray-500 hover:text-red-400 p-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
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
const deleteAvatarFlag = ref(false)

const isScheduleModalOpen = ref(false)
const scheduleTab = ref('hours')
const activeBarber = ref(null)
const weekdayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

const localSchedules = ref([
    { day_of_week: 0, is_working: false, start_time: '', end_time: '' },
    { day_of_week: 1, is_working: true, start_time: '09:00', end_time: '17:00' },
    { day_of_week: 2, is_working: true, start_time: '09:00', end_time: '17:00' },
    { day_of_week: 3, is_working: true, start_time: '09:00', end_time: '17:00' },
    { day_of_week: 4, is_working: true, start_time: '09:00', end_time: '17:00' },
    { day_of_week: 5, is_working: true, start_time: '09:00', end_time: '17:00' },
    { day_of_week: 6, is_working: false, start_time: '', end_time: '' }
])

const daysOff = ref([])
const newDayOff = reactive({ date: '', reason: '' })

const savingSchedule = ref(false)
const scheduleSuccessMsg = ref('')
const addingDayOff = ref(false)

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
    deleteAvatarFlag.value = false
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
    deleteAvatarFlag.value = false
    isModalOpen.value = true
}

const handleAvatarChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.avatar = file
        avatarPreview.value = URL.createObjectURL(file)
        deleteAvatarFlag.value = false
    }
}

const removeAvatar = () => {
    form.avatar = null
    form.avatar_url = null
    avatarPreview.value = null
    deleteAvatarFlag.value = true
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
        formData.append('delete_avatar', deleteAvatarFlag.value ? 'true' : 'false')
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

const manageSchedule = async (barber) => {
    activeBarber.value = barber
    scheduleTab.value = 'hours'
    isScheduleModalOpen.value = true
    scheduleSuccessMsg.value = ''
    
    // Fetch schedule
    try {
        const response = await fetch(`/admin/barbers/${barber.id}/schedule`)
        if (response.ok) {
            const data = await response.json()
            
            // Map schedules (0 to 6)
            for (let i = 0; i < 7; i++) {
                const found = data.schedules.find(s => s.day_of_week === i)
                if (found) {
                    localSchedules.value[i] = {
                        day_of_week: i,
                        is_working: found.is_working,
                        start_time: found.start_time ? found.start_time.substring(0, 5) : '09:00',
                        end_time: found.end_time ? found.end_time.substring(0, 5) : '17:00'
                    }
                } else {
                    localSchedules.value[i] = {
                        day_of_week: i,
                        is_working: i >= 1 && i <= 5,
                        start_time: '09:00',
                        end_time: '17:00'
                    }
                }
            }
            
            // Map days off
            daysOff.value = data.days_off.map(d => ({
                id: d.id,
                date: d.date.split('T')[0],
                reason: d.reason
            }))
        }
    } catch (error) {
        console.error('Error fetching schedule:', error)
    }
}

const closeScheduleModal = () => {
    isScheduleModalOpen.value = false
    activeBarber.value = null
    daysOff.value = []
    newDayOff.date = ''
    newDayOff.reason = ''
}

const saveSchedules = async () => {
    savingSchedule.value = true
    scheduleSuccessMsg.value = ''
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch(`/admin/barbers/${activeBarber.value.id}/schedule`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                schedules: localSchedules.value
            })
        })
        
        if (response.ok) {
            scheduleSuccessMsg.value = 'Weekly schedule updated successfully!'
            setTimeout(() => scheduleSuccessMsg.value = '', 3000)
        } else {
            const data = await response.json()
            throw new Error(data.message || 'Error updating schedule.')
        }
    } catch (error) {
        alert(error.message)
    } finally {
        savingSchedule.value = false
    }
}

const addDayOff = async () => {
    addingDayOff.value = true
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch(`/admin/barbers/${activeBarber.value.id}/days-off`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(newDayOff)
        })
        
        if (response.ok) {
            const created = await response.json()
            daysOff.value.push({
                id: created.id,
                date: created.date.split('T')[0],
                reason: created.reason
            })
            daysOff.value.sort((a, b) => new Date(a.date) - new Date(b.date))
            newDayOff.date = ''
            newDayOff.reason = ''
        } else {
            const data = await response.json()
            throw new Error(data.message || 'Error adding day off.')
        }
    } catch (error) {
        alert(error.message)
    } finally {
        addingDayOff.value = false
    }
}

const deleteDayOff = async (id) => {
    if (!confirm('Are you sure you want to remove this day off?')) return
    
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch(`/admin/barbers/${activeBarber.value.id}/days-off/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        
        if (response.ok) {
            daysOff.value = daysOff.value.filter(d => d.id !== id)
        }
    } catch (error) {
        console.error('Error deleting day off:', error)
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
