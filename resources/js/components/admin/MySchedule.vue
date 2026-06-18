<template>
    <div class="space-y-6 animate-fade-in">
        <div class="bg-[#111] border border-white/5 rounded-xl overflow-hidden shadow-2xl p-8">
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
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const scheduleTab = ref('hours')
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

const loadSchedule = async () => {
    try {
        const response = await fetch(`/admin/barbers/${props.user.id}/schedule`)
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

const saveSchedules = async () => {
    savingSchedule.value = true
    scheduleSuccessMsg.value = ''
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        const response = await fetch(`/admin/barbers/${props.user.id}/schedule`, {
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
        const response = await fetch(`/admin/barbers/${props.user.id}/days-off`, {
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
        const response = await fetch(`/admin/barbers/${props.user.id}/days-off/${id}`, {
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
    loadSchedule()
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
