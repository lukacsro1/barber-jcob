<template>
    <div class="min-h-screen flex items-center justify-center p-6 selection:bg-gold selection:text-dark">
        <!-- Language Switcher -->
        <div class="absolute top-8 right-8 flex gap-4 text-xs font-bold uppercase tracking-widest z-50">
            <a href="/lang/ro" class="transition-colors" :class="locale === 'ro' ? 'text-gold' : 'text-gray-600 hover:text-white'">RO</a>
            <span class="text-white/20">|</span>
            <a href="/lang/hu" class="transition-colors" :class="locale === 'hu' ? 'text-gold' : 'text-gray-600 hover:text-white'">HU</a>
        </div>

        <div class="w-full max-w-4xl">
            <!-- Brand -->
            <div class="text-center mb-12 animate-fade-in relative">
                <div class="text-3xl font-serif font-bold tracking-widest text-gold uppercase mb-2">Jcob</div>
                <div class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold italic">The Art of Grooming</div>
            </div>

            <!-- Booking Card -->
            <div class="bg-[#111] border border-white/5 p-8 md:p-12 shadow-2xl relative overflow-hidden group animate-slide-up">
                <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-gold to-transparent opacity-50"></div>

                <div v-if="successMsg" class="text-center py-12 animate-fade-in">
                    <div class="text-5xl text-gold mb-6 italic font-serif">{{ translations.thank_you || 'Thank You' }}</div>
                    <p class="text-gray-400 uppercase tracking-[0.2em] text-sm">{{ successMsg }}</p>
                    <a href="/" class="inline-block mt-12 px-8 py-3 border border-gold text-gold text-[10px] uppercase tracking-widest font-bold hover:bg-gold/10 transition-all">{{ translations.return_home || 'Return Home' }}</a>
                </div>

                <div v-else>
                    <!-- STEP 1: Select Barber -->
                    <div v-if="!selectedBarber" class="animate-fade-in">
                        <h2 class="text-2xl font-serif mb-8 text-white italic text-center">{{ translations.step1_title || 'Step 1: Choose Your Stylist' }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div 
                                v-for="barber in barbers" 
                                :key="barber.id"
                                @click="selectBarber(barber)"
                                class="bg-[#161616] border border-white/5 hover:border-gold/30 transition-all cursor-pointer p-4 group text-center"
                            >
                                <div class="aspect-square mb-4 overflow-hidden grayscale group-hover:grayscale-0 transition-all duration-500 rounded-full border-2 border-transparent group-hover:border-gold/50">
                                    <img :src="barber.avatar_url" :alt="barber.name" class="w-full h-full object-cover">
                                </div>
                                <div class="font-serif text-lg text-white group-hover:text-gold transition-colors">{{ barber.name }}</div>
                                <div class="text-[9px] uppercase tracking-widest text-gray-500 mt-1">{{ barber.specialty || translations.master_barber || 'Master Barber' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: The Details -->
                    <div v-else class="animate-slide-up">
                        <div class="flex items-center gap-4 mb-8 pb-8 border-b border-white/5">
                            <img :src="selectedBarber.avatar_url" class="w-16 h-16 rounded-full object-cover border border-gold/50">
                            <div>
                                <div class="text-xs uppercase tracking-widest text-gray-500 mb-1">{{ translations.your_barber || 'Your Barber' }}</div>
                                <div class="text-xl font-serif text-white italic">{{ selectedBarber.name }}</div>
                            </div>
                            <button @click="selectedBarber = null" class="ml-auto text-[9px] uppercase tracking-widest text-gold/60 hover:text-gold">{{ translations.change || 'Change' }}</button>
                        </div>

                        <form action="/book" method="POST" @submit="handleSubmit" class="space-y-8">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input type="hidden" name="user_id" :value="selectedBarber.id">

                            <div>
                                <label for="customer_name" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-3">{{ translations.your_name || 'Your Name' }}</label>
                                <input id="customer_name" name="customer_name" v-model="form.customer_name" type="text" required class="w-full bg-[#161616] border border-white/10 px-4 py-4 text-white focus:outline-none focus:border-gold transition-colors placeholder:text-gray-700" placeholder="John Doe">
                            </div>

                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-3">{{ translations.the_service || 'The Service' }}</label>
                                <select name="service" v-model="form.service" required class="w-full bg-[#161616] border border-white/10 px-4 py-4 text-white focus:outline-none focus:border-gold transition-colors appearance-none cursor-pointer">
                                    <option value="" disabled>{{ translations.select_service || 'Select Service' }}</option>
                                    <option v-for="service in services" :key="service.id" :value="service.name">{{ service.name }} — {{ service.price }} RON</option>
                                </select>
                            </div>

                            <!-- Calendar Component -->
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-3">{{ translations.date_time || 'Date & Time' }}</label>
                                <input type="hidden" name="start_at" :value="form.start_at" required>
                                
                                <div class="bg-[#161616] border border-white/10 p-6">
                                    <!-- Calendar Header -->
                                    <div class="flex justify-between items-center mb-6">
                                        <button @click.prevent="prevMonth" class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gold hover:border-gold transition-colors">&larr;</button>
                                        <div class="font-serif text-white text-lg italic">
                                            {{ monthName }} {{ currentDate.getFullYear() }}
                                        </div>
                                        <button @click.prevent="nextMonth" class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gold hover:border-gold transition-colors">&rarr;</button>
                                    </div>
                                    
                                    <!-- Weekdays -->
                                    <div class="grid grid-cols-7 gap-1 mb-3 text-center text-[9px] uppercase tracking-widest text-gray-500 font-bold">
                                        <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                                    </div>
                                    
                                    <!-- Days Grid -->
                                    <div class="grid grid-cols-7 gap-1">
                                        <div v-for="(day, idx) in daysInMonth" :key="idx" class="aspect-square">
                                            <button v-if="day" @click.prevent="selectDate(day)" 
                                                class="w-full h-full flex items-center justify-center text-sm transition-all border rounded"
                                                :class="{'bg-gold border-gold text-dark font-bold shadow-[0_0_15px_rgba(197,160,89,0.3)]': isSelectedDate(day), 'border-transparent text-gray-400 hover:border-gold/30 hover:text-gold': !isSelectedDate(day)}">
                                                {{ day.getDate() }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Time Slots -->
                                <div v-if="selectedDate" class="mt-4 p-6 bg-[#161616] border border-white/10 animate-fade-in">
                                    <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-4 text-center">{{ translations.available_times_for || 'Available Times for' }} {{ formattedSelectedDate }}</div>
                                    <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                        <button v-for="time in availableTimes" :key="time" @click.prevent="selectTime(time)"
                                            class="py-3 text-xs tracking-widest transition-all border rounded"
                                            :class="{'border-gold bg-gold/10 text-gold font-bold': selectedTime === time, 'border-white/10 text-gray-400 hover:border-gold/50 hover:text-white': selectedTime !== time}">
                                            {{ time }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="loading || !form.start_at" class="w-full bg-gold text-dark font-bold py-5 uppercase tracking-[0.3em] text-xs hover:bg-gold-light transition-all transform active:scale-[0.99] disabled:opacity-50 mt-4 shadow-[0_0_30px_rgba(197,160,89,0.2)]">
                                <span v-if="!loading">{{ translations.confirm_appointment || 'Confirm Appointment' }}</span>
                                <span v-else>{{ translations.processing_ritual || 'Processing Ritual...' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="/" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gray-400 transition-colors font-bold tracking-[0.4em]">{{ translations.back_to_gallery || 'Back to Gallery' }}</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'

const csrfToken = ref('')
const barbers = ref([])
const services = ref([])
const appointments = ref([])
const translations = ref({})
const locale = ref('ro')
const successMsg = ref('')
const loading = ref(false)
const selectedBarber = ref(null)

const form = reactive({
    customer_name: '',
    service: '',
    start_at: ''
})

// Calendar State
const currentDate = ref(new Date())
const selectedDate = ref(null)
const selectedTime = ref(null)

const monthName = computed(() => {
    return currentDate.value.toLocaleString('en-US', { month: 'long' })
})

const daysInMonth = computed(() => {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    const days = new Date(year, month + 1, 0).getDate()
    const firstDay = new Date(year, month, 1).getDay()
    
    const startDay = firstDay === 0 ? 6 : firstDay - 1
    
    const calendar = []
    for(let i=0; i<startDay; i++) {
        calendar.push(null)
    }
    for(let i=1; i<=days; i++) {
        calendar.push(new Date(year, month, i))
    }
    return calendar
})

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

const isSelectedDate = (date) => {
    if (!selectedDate.value) return false
    return date.toDateString() === selectedDate.value.toDateString()
}

const selectDate = (date) => {
    selectedDate.value = date
    selectedTime.value = null
    updateStartAt()
}

const selectTime = (time) => {
    selectedTime.value = time
    updateStartAt()
}

const updateStartAt = () => {
    if (selectedDate.value && selectedTime.value) {
        const d = selectedDate.value
        const year = d.getFullYear()
        const month = String(d.getMonth() + 1).padStart(2, '0')
        const day = String(d.getDate()).padStart(2, '0')
        form.start_at = `${year}-${month}-${day}T${selectedTime.value}`
    } else {
        form.start_at = ''
    }
}

const formattedSelectedDate = computed(() => {
    if (!selectedDate.value) return ''
    return selectedDate.value.toLocaleString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
})

const allTimes = [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', 
    '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'
]

const availableTimes = computed(() => {
    if (!selectedDate.value || !selectedBarber.value) return allTimes

    const d = selectedDate.value
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    const dateStr = `${year}-${month}-${day}`

    const booked = appointments.value
        .filter(app => app.user_id === selectedBarber.value.id && app.date === dateStr)
        .map(app => app.time)

    return allTimes.filter(time => !booked.includes(time))
})

onMounted(() => {
    const el = document.getElementById('booking-app')
    csrfToken.value = el.dataset.csrf
    barbers.value = JSON.parse(el.dataset.barbers || '[]')
    services.value = JSON.parse(el.dataset.services || '[]')
    appointments.value = JSON.parse(el.dataset.appointments || '[]')
    translations.value = JSON.parse(el.dataset.translations || '{}')
    locale.value = el.dataset.locale || 'ro'
    successMsg.value = el.dataset.success || ''

    // Handle pre-selected barber from URL
    const urlParams = new URLSearchParams(window.location.search)
    const barberId = urlParams.get('barber_id')
    if (barberId) {
        const found = barbers.value.find(b => b.id == barberId)
        if (found) selectedBarber.value = found
    }
})

const selectBarber = (barber) => {
    selectedBarber.value = barber
}

const handleSubmit = (e) => {
    if (!form.start_at) {
        e.preventDefault()
        alert(translations.value.alert_select_time || 'Please select a date and time for your appointment.')
        return
    }
    loading.value = true
}
</script>

<style>
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes slide-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 1.5s ease-out forwards; }
.animate-slide-up { animation: slide-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
