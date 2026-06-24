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
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-serif text-white">{{ viewMode === 'calendar' ? 'Daily Schedule' : 'All Upcoming Appointments' }}</h2>
                    <div class="flex gap-4 items-center flex-wrap">
                        <!-- View Mode Toggle -->
                        <div class="flex bg-zinc-900 border border-white/5 rounded-lg p-1">
                            <button @click="viewMode = 'calendar'" class="px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold transition-all rounded-md" :class="viewMode === 'calendar' ? 'bg-gold text-dark' : 'text-gray-500 hover:text-gray-300'">Calendar</button>
                            <button @click="viewMode = 'list'" class="px-3 py-1.5 text-[10px] uppercase tracking-widest font-bold transition-all rounded-md" :class="viewMode === 'list' ? 'bg-gold text-dark' : 'text-gray-500 hover:text-gray-300'">List All</button>
                        </div>

                        <!-- Barber Filter (Admin only) -->
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
                </div>

                <!-- Calendar View (Time Slots) -->
                <div v-if="viewMode === 'calendar'" class="space-y-3">
                    <div 
                        v-for="slot in timeSlots" 
                        :key="slot.time"
                        @click="handleSlotClick(slot)"
                        class="group bg-[#111] border border-white/5 rounded-xl p-4 flex items-center gap-6 hover:border-gold/30 transition-all relative overflow-hidden cursor-pointer"
                    >
                        <div class="w-20 text-xs font-bold text-gray-400 tracking-widest">{{ slot.time }}</div>
                        
                        <div v-if="slot.appointment" class="flex-1 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-white group-hover:text-gold transition-colors">{{ slot.appointment.customer_name }}</div>
                                <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                    {{ slot.appointment.customer_phone }} 
                                    <span v-if="slot.appointment.customer_email" class="lowercase ml-2 text-gold/60">{{ slot.appointment.customer_email }}</span>
                                </div>
                                <div class="text-[10px] uppercase tracking-widest text-gold/60 font-bold">{{ slot.appointment.service }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-gray-500 uppercase font-bold">{{ slot.appointment.barber ? slot.appointment.barber.name : 'Unknown' }}</div>
                                <div class="flex gap-1 mt-1 justify-end">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider" 
                                        :class="{
                                            'bg-gold/10 text-gold border border-gold/20': slot.appointment.status === 'scheduled',
                                            'bg-green-500/10 text-green-400 border border-green-500/20': slot.appointment.status === 'completed',
                                            'bg-red-500/10 text-red-400 border border-red-500/20': slot.appointment.status === 'cancelled',
                                        }"
                                    >
                                        {{ slot.appointment.status || 'scheduled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="flex-1 flex items-center justify-between opacity-30 group-hover:opacity-100 transition-opacity">
                            <span class="text-xs italic text-gray-600">Available Slot</span>
                            <button class="text-[10px] uppercase tracking-widest text-gold hover:text-white font-bold transition-colors">Book Now</button>
                        </div>

                        <!-- Status Indicator line -->
                        <div v-if="slot.appointment" class="absolute left-0 top-0 h-full w-[2px]"
                            :class="{
                                'bg-gold': slot.appointment.status === 'scheduled',
                                'bg-green-500': slot.appointment.status === 'completed',
                                'bg-red-500': slot.appointment.status === 'cancelled',
                            }"
                        ></div>
                    </div>
                </div>
                </div>

                <!-- List View (All Appointments) -->
                <div v-else class="space-y-3">
                    <div v-if="allUpcomingAppointments.length === 0" class="text-gray-500 text-sm italic py-8 text-center border border-white/5 bg-[#111] rounded-xl">No upcoming appointments found.</div>
                    
                    <div 
                        v-for="app in allUpcomingAppointments" 
                        :key="app.id"
                        @click="openEditModal(app)"
                        class="group bg-[#111] border border-white/5 rounded-xl p-4 flex items-center gap-6 hover:border-gold/30 transition-all relative overflow-hidden cursor-pointer"
                    >
                        <div class="w-24 text-center border-r border-white/5 pr-4">
                            <div class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-1">{{ new Date(app.start_at).toLocaleDateString('ro-RO', {month: 'short', day: 'numeric'}) }}</div>
                            <div class="text-lg font-serif text-gold">{{ new Date(app.start_at).toLocaleTimeString('ro-RO', {hour: '2-digit', minute: '2-digit'}) }}</div>
                        </div>
                        
                        <div class="flex-1 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-white group-hover:text-gold transition-colors">{{ app.customer_name }}</div>
                                <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                    {{ app.customer_phone }} 
                                </div>
                                <div class="text-[10px] uppercase tracking-widest text-gold/60 font-bold">{{ app.service }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-gray-500 uppercase font-bold">{{ app.barber ? app.barber.name : 'Unknown' }}</div>
                                <div class="flex gap-1 mt-1 justify-end">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider" 
                                        :class="{
                                            'bg-gold/10 text-gold border border-gold/20': app.status === 'scheduled',
                                            'bg-green-500/10 text-green-400 border border-green-500/20': app.status === 'completed',
                                            'bg-red-500/10 text-red-400 border border-red-500/20': app.status === 'cancelled',
                                        }"
                                    >
                                        {{ app.status || 'scheduled' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Indicator line -->
                        <div class="absolute left-0 top-0 h-full w-[2px]"
                            :class="{
                                'bg-gold': app.status === 'scheduled',
                                'bg-green-500': app.status === 'completed',
                                'bg-red-500': app.status === 'cancelled',
                            }"
                        ></div>
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

        <!-- Appointment Edit/Create Modal -->
        <Teleport to="body">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
                <div class="bg-[#111] border border-white/10 w-full max-w-md p-8 shadow-2xl relative animate-fade-in text-left">
                    <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors text-2xl">&times;</button>
                    <h3 class="text-2xl font-serif text-white italic mb-6">
                        {{ isEditMode ? 'Edit Appointment' : 'Book Appointment' }}
                    </h3>
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Barber</label>
                            <select v-model="form.user_id" required :disabled="userRole !== 'admin'" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm cursor-pointer disabled:opacity-50">
                                <option v-for="barber in barbers" :key="barber.id" :value="barber.id">{{ barber.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Customer Name</label>
                            <input v-model="form.customer_name" type="text" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Customer Phone</label>
                            <input v-model="form.customer_phone" type="text" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Customer Email (Optional)</label>
                            <input v-model="form.customer_email" type="email" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Service</label>
                            <select v-model="form.service" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm cursor-pointer">
                                <option value="" disabled>Select Service</option>
                                <optgroup v-for="(groupServices, category) in groupedServices" :key="category" :label="category" class="bg-[#111] text-gray-400 font-bold">
                                    <option v-for="service in groupServices" :key="service.id" :value="service.name" class="text-white font-normal">
                                        {{ service.name }} ({{ service.price }} RON)
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Date</label>
                                <input v-model="form.date" type="date" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Time</label>
                                <select v-model="form.time" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm cursor-pointer">
                                    <option v-for="timeStr in availableTimeOptions" :key="timeStr" :value="timeStr">{{ timeStr }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="isEditMode">
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Status</label>
                            <select v-model="form.status" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm cursor-pointer">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="pt-4 flex flex-col gap-2">
                            <button type="submit" :disabled="loading" class="w-full bg-gold text-dark font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-gold-light transition-all shadow-[0_0_15px_rgba(197,160,89,0.2)] disabled:opacity-50">
                                <span v-if="!loading">{{ isEditMode ? 'Update Appointment' : 'Book Appointment' }}</span>
                                <span v-else>Saving...</span>
                            </button>
                            <button v-if="isEditMode" type="button" @click="deleteAppointment" :disabled="loading" class="w-full bg-red-950/40 border border-red-500/20 text-red-200 font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-red-900/40 transition-all disabled:opacity-50">
                                <span v-if="!loading">Cancel / Delete Appointment</span>
                                <span v-else>Deleting...</span>
                            </button>
                        </div>
                        
                        <div v-if="errorMsg" class="text-red-400 text-[10px] uppercase tracking-widest mt-4 text-center bg-red-400/10 py-3 border border-red-400/20">
                            {{ errorMsg }}
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'

const props = defineProps({
    appointments: {
        type: Array,
        default: () => []
    },
    barbers: {
        type: Array,
        default: () => []
    },
    services: {
        type: Array,
        default: () => []
    }
})

// State
const today = new Date()
const appointments = ref([...props.appointments])
const selectedDate = ref(new Date())
const selectedBarberId = ref(null)
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())
const userRole = ref('')
const scrollContainer = ref(null)

// Modal state
const isModalOpen = ref(false)
const isEditMode = ref(false)
const editingAppointmentId = ref(null)
const loading = ref(false)
const errorMsg = ref('')
const viewMode = ref('calendar')

const form = reactive({
    user_id: '',
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    service: '',
    date: '',
    time: '',
    status: 'scheduled'
})

const availableTimeOptions = computed(() => {
    const allTimes = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00'
    ]
    
    if (!form.user_id || !form.date || !form.service) return allTimes

    const selectedServiceObj = props.services.find(s => s.name === form.service)
    const selectedDuration = selectedServiceObj ? parseInt(selectedServiceObj.duration_minutes) : 30

    // Fallback shift end time if no schedule is found
    let shiftEndMinutes = 18 * 60 
    const barber = props.barbers.find(b => b.id === form.user_id)
    
    if (barber && barber.schedules) {
        const d = new Date(form.date)
        const schedule = barber.schedules.find(s => s.day_of_week === d.getDay())
        if (schedule && schedule.end_time) {
            shiftEndMinutes = schedule.end_time.split(':').map(Number).reduce((h, m) => h * 60 + m)
        }
    }

    const booked = appointments.value
        .filter(app => app.user_id === form.user_id && app.start_at.startsWith(form.date))
        .filter(app => app.id !== editingAppointmentId.value)
        .map(app => {
            const appServiceObj = props.services.find(s => s.name === app.service)
            const appDuration = appServiceObj ? parseInt(appServiceObj.duration_minutes) : 30
            const appDate = new Date(app.start_at)
            const appStart = appDate.getHours() * 60 + appDate.getMinutes()
            return { start: appStart, end: appStart + appDuration }
        })

    return allTimes.filter(time => {
        const [h, m] = time.split(':').map(Number)
        const slotStart = h * 60 + m
        const slotEnd = slotStart + selectedDuration
        
        if (slotEnd > shiftEndMinutes) return false
        
        for (const app of booked) {
            if (slotStart < app.end && slotEnd > app.start) {
                return false
            }
        }
        return true
    })
})

// Data Computed
const groupedServices = computed(() => {
    const groups = {}
    props.services.forEach(service => {
        const cat = service.category || 'Alte Servicii'
        if (!groups[cat]) groups[cat] = []
        groups[cat].push(service)
    })
    return groups
})

const currentMonthName = computed(() => {
    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(currentYear.value, currentMonth.value))
})

const daysInMonth = computed(() => {
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
        const app = appointments.value.find(a => {
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
const estimatedRevenue = computed(() => {
    return timeSlots.value.reduce((total, slot) => {
        if (!slot.appointment) return total
        const serviceObj = props.services.find(s => s.name === slot.appointment.service)
        const price = serviceObj ? parseFloat(serviceObj.price) : 150
        return total + price
    }, 0)
})

const allUpcomingAppointments = computed(() => {
    const todayStr = new Date().toISOString().split('T')[0]
    return appointments.value
        .filter(app => {
            const matchesBarber = !selectedBarberId.value || app.user_id === selectedBarberId.value
            const appDateStr = app.start_at.split(' ')[0]
            const isFuture = appDateStr >= todayStr
            return matchesBarber && isFuture
        })
        .sort((a, b) => new Date(a.start_at).getTime() - new Date(b.start_at).getTime())
})

// Actions
const selectDay = (day) => {
    selectedDate.value = day.fullDate
}

const isSelected = (day) => {
    return selectedDate.value.toISOString().split('T')[0] === day.dateString
}

const scrollDays = (direction) => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollBy({
            left: direction * 200,
            behavior: 'smooth'
        })
    }
}

const handleSlotClick = (slot) => {
    if (slot.appointment) {
        openEditModal(slot.appointment)
    } else {
        openCreateModal(slot.time)
    }
}

const openCreateModal = (timeStr) => {
    isEditMode.value = false
    editingAppointmentId.value = null
    errorMsg.value = ''
    
    form.user_id = selectedBarberId.value || (props.barbers[0] ? props.barbers[0].id : '')
    form.customer_name = ''
    form.customer_phone = ''
    form.customer_email = ''
    form.service = props.services[0] ? props.services[0].name : ''
    
    const d = selectedDate.value
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    form.date = `${year}-${month}-${day}`
    
    form.time = timeStr || '09:00'
    form.status = 'scheduled'
    
    isModalOpen.value = true
}

const openEditModal = (app) => {
    isEditMode.value = true
    editingAppointmentId.value = app.id
    errorMsg.value = ''
    
    form.user_id = app.user_id
    form.customer_name = app.customer_name
    form.customer_phone = app.customer_phone
    form.customer_email = app.customer_email || ''
    form.service = app.service
    
    const appDate = new Date(app.start_at)
    const year = appDate.getFullYear()
    const month = String(appDate.getMonth() + 1).padStart(2, '0')
    const day = String(appDate.getDate()).padStart(2, '0')
    form.date = `${year}-${month}-${day}`
    
    const hours = String(appDate.getHours()).padStart(2, '0')
    const minutes = String(appDate.getMinutes()).padStart(2, '0')
    form.time = `${hours}:${minutes}`
    
    form.status = app.status || 'scheduled'
    
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    isEditMode.value = false
    editingAppointmentId.value = null
    errorMsg.value = ''
}

const submitForm = async () => {
    loading.value = true
    errorMsg.value = ''
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        const startAt = `${form.date} ${form.time}:00`
        
        const payload = {
            user_id: form.user_id,
            customer_name: form.customer_name,
            customer_phone: form.customer_phone,
            customer_email: form.customer_email,
            service: form.service,
            start_at: startAt,
            status: form.status
        }
        
        const url = isEditMode.value 
            ? `/admin/appointments/${editingAppointmentId.value}` 
            : '/admin/appointments'
            
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        
        if (!response.ok) {
            const data = await response.json()
            throw new Error(data.message || 'An error occurred while saving.')
        }
        
        const savedApp = await response.json()
        
        if (isEditMode.value) {
            const idx = appointments.value.findIndex(a => a.id === savedApp.id)
            if (idx !== -1) {
                appointments.value[idx] = savedApp
            }
        } else {
            appointments.value.push(savedApp)
        }
        
        closeModal()
    } catch (err) {
        errorMsg.value = err.message
    } finally {
        loading.value = false
    }
}

const deleteAppointment = async () => {
    if (!confirm('Are you sure you want to cancel/delete this appointment?')) return
    
    loading.value = true
    errorMsg.value = ''
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        const response = await fetch(`/admin/appointments/${editingAppointmentId.value}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        
        if (!response.ok) {
            const data = await response.json()
            throw new Error(data.message || 'An error occurred while deleting.')
        }
        
        appointments.value = appointments.value.filter(a => a.id !== editingAppointmentId.value)
        closeModal()
    } catch (err) {
        errorMsg.value = err.message
    } finally {
        loading.value = false
    }
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
