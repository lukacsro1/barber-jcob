<template>
    <div class="space-y-8 animate-fade-in">
        <!-- Horizontal Day Picker -->
        <div class="bg-[#111] border border-white/5 rounded-xl p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs uppercase tracking-[0.3em] text-gold font-bold">{{ currentMonthName }} {{ currentYear }}</h3>
                <div class="flex gap-2">
                    <button @click="scrollDays(-1)" class="p-2 bg-white/5 hover:bg-white/10 rounded-full transition-colors text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button @click="scrollDays(1)" class="p-2 bg-white/5 hover:bg-white/10 rounded-full transition-colors text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide select-none" ref="scrollContainer">
                <div 
                    v-for="day in daysInMonth" 
                    :key="day.dateString"
                    @click="selectDay(day)"
                    class="flex-shrink-0 w-16 h-24 flex flex-col items-center justify-center rounded-xl border transition-all cursor-pointer"
                    :class="isSelected(day) 
                        ? 'bg-gold border-gold text-dark shadow-[0_0_15px_rgba(197,160,89,0.3)]' 
                        : 'bg-zinc-900 border-white/5 text-gray-500 hover:border-white/20'"
                >
                    <span class="text-[10px] uppercase tracking-widest font-bold mb-1">{{ day.dayName }}</span>
                    <span class="text-xl font-serif font-bold">{{ day.dayNumber }}</span>
                </div>
            </div>
        </div>

        <!-- Selected Day Schedule -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Time Slots -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h2 class="text-xl font-serif text-white">Daily Schedule</h2>
                    <div v-if="userRole === 'admin'" class="flex bg-zinc-900 border border-white/5 rounded-lg p-1">
                        <button 
                            @click="selectedBarberId = null"
                            class="px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold transition-all rounded-md"
                            :class="!selectedBarberId ? 'bg-gold text-dark' : 'text-gray-500 hover:text-gray-300'"
                        >
                            All
                        </button>
                        <button 
                            v-for="barber in barbers" 
                            :key="barber.id"
                            @click="selectedBarberId = barber.id"
                            class="px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold transition-all rounded-md"
                            :class="selectedBarberId === barber.id ? 'bg-gold text-dark' : 'text-gray-500 hover:text-gray-300'"
                        >
                            {{ barber.name.split(' ')[0] }}
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div 
                        v-for="slot in timeSlots" 
                        :key="slot.time"
                        class="group bg-[#111] border border-white/5 rounded-xl p-4 flex items-center gap-6 hover:border-gold/30 transition-all relative overflow-hidden"
                    >
                        <div class="w-20 text-xs font-bold text-gray-400 tracking-widest">{{ slot.time }}</div>
                        
                        <div v-if="slot.appointment" class="flex-1 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-white">{{ slot.appointment.customer_name }}</div>
                                <div class="text-[10px] uppercase tracking-widest text-gold/60 font-bold">{{ slot.appointment.service }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-gray-500 uppercase font-bold">{{ slot.appointment.barber.name }}</div>
                                <div class="flex gap-1 mt-1 justify-end">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_5px_rgba(245,158,11,0.5)]"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="flex-1 flex items-center justify-between opacity-30 group-hover:opacity-100 transition-opacity">
                            <span class="text-xs italic text-gray-600">Available Slot</span>
                            <button class="text-[10px] uppercase tracking-widest text-gold hover:text-white font-bold transition-colors">Book Now</button>
                        </div>

                        <!-- Status Indicator line -->
                        <div v-if="slot.appointment" class="absolute left-0 top-0 h-full w-[2px] bg-gold"></div>
                    </div>
                </div>
            </div>

            <!-- Summary Side Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-zinc-900 border border-white/5 rounded-xl p-6">
                    <h3 class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-6">Day Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">{{ formattedSelectedDate }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-white/5 pb-4">
                            <span class="text-xs text-gray-400">Total Booked</span>
                            <span class="text-2xl font-serif text-white">{{ bookedCount }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-white/5 pb-4">
                            <span class="text-xs text-gray-400">Estimated Revenue</span>
                            <span class="text-2xl font-serif text-gold">{{ estimatedRevenue }} RON</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    appointments: {
        type: Array,
        default: () => []
    },
    barbers: {
        type: Array,
        default: () => []
    }
})

// State
const today = new Date()
const selectedDate = ref(new Date())
const selectedBarberId = ref(null)
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())
const userRole = ref('')

// Data Computed
const currentMonthName = computed(() => {
    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(currentYear.value, currentMonth.value))
})

const daysInMonth = computed(() => {
    const date = new Date(currentYear.value, currentMonth.value, 1)
    const days = []
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()

    for (let i = 1; i <= lastDay; i++) {
        const d = new Date(currentYear.value, currentMonth.value, i)
        days.push({
            dayNumber: i,
            dayName: new Intl.DateTimeFormat('en-US', { weekday: 'short' }).format(d),
            dateString: d.toISOString().split('T')[0],
            fullDate: d
        })
    }
    return days
})

const formattedSelectedDate = computed(() => {
    return selectedDate.value.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
})

// Time Slots (9:00 - 18:00)
const timeSlots = computed(() => {
    const slots = []
    const selectedDateStr = selectedDate.value.toISOString().split('T')[0]
    
    for (let hour = 9; hour <= 18; hour++) {
        const timeStr = `${hour.toString().padStart(2, '0')}:00`
        
        // Find appointment that matches this day, hour, and (optionally) barber
        const app = props.appointments.find(a => {
            const appDate = new Date(a.start_at)
            const appDateStr = appDate.toISOString().split('T')[0]
            const appHour = appDate.getHours()
            
            const matchesDate = appDateStr === selectedDateStr && appHour === hour
            const matchesBarber = !selectedBarberId.value || a.user_id === selectedBarberId.value
            
            return matchesDate && matchesBarber
        })

        slots.push({
            time: timeStr,
            appointment: app
        })
    }
    return slots
})

const bookedCount = computed(() => timeSlots.value.filter(s => s.appointment).length)
const estimatedRevenue = computed(() => bookedCount.value * 150) // Assuming 150 RON average

// Actions
const selectDay = (day) => {
    selectedDate.value = day.fullDate
}

const isSelected = (day) => {
    return selectedDate.value.toISOString().split('T')[0] === day.dateString
}

onMounted(() => {
    const el = document.getElementById('appointment-calendar')
    if (el) {
        userRole.value = el.dataset.role || ''
    }
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
