<template>
    <div class="min-h-screen bg-[#0a0a0a] flex flex-col items-center justify-center p-8 overflow-hidden selection:bg-gold selection:text-dark">
        <!-- Language Switcher -->
        <div class="absolute top-8 right-8 flex gap-4 text-xs font-bold uppercase tracking-widest z-50">
            <a href="/lang/ro" class="transition-colors" :class="locale === 'ro' ? 'text-gold' : 'text-gray-600 hover:text-white'">RO</a>
            <span class="text-white/20">|</span>
            <a href="/lang/hu" class="transition-colors" :class="locale === 'hu' ? 'text-gold' : 'text-gray-600 hover:text-white'">HU</a>
        </div>

        <!-- Logo -->
        <div class="mb-16 text-center animate-fade-in relative">
            <div class="text-4xl font-serif font-bold tracking-[0.3em] text-gold uppercase mb-2">Jcob</div>
            <div class="text-[10px] uppercase tracking-[0.5em] text-gray-500 font-bold">{{ translations.subtitle || 'Select Your Master Barber' }}</div>
        </div>

        <!-- Horizontal Scroll Container -->
        <div class="w-full max-w-7xl overflow-x-auto pb-12 no-scrollbar animate-slide-up">
            <div class="flex gap-8 px-4 min-w-max">
                <div 
                    v-for="barber in barbers" 
                    :key="barber.id"
                    class="w-72 bg-[#111] border border-white/5 hover:border-gold/30 transition-all duration-500 group cursor-pointer overflow-hidden relative"
                    @click="selectBarber(barber)"
                >
                    <!-- Barber Image -->
                    <div class="aspect-[3/4] overflow-hidden grayscale group-hover:grayscale-0 transition-all duration-700">
                        <img 
                            :src="barber.avatar_url || `https://i.pravatar.cc/400?u=${barber.id}`" 
                            :alt="barber.name"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000"
                        >
                    </div>

                    <!-- Overlay for hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-dark via-transparent to-transparent opacity-60"></div>

                    <!-- Info -->
                    <div class="absolute bottom-0 left-0 w-full p-6 text-center">
                        <div class="text-xl font-serif text-white group-hover:text-gold transition-colors mb-1 italic">
                            {{ barber.name }}
                        </div>
                        <div class="text-[9px] uppercase tracking-[0.3em] text-gray-400 font-bold">
                            {{ barber.specialty || translations.master_barber || 'Master Barber' }}
                        </div>
                    </div>

                    <!-- Book Action Overlay -->
                    <div class="absolute inset-0 bg-gold/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="px-6 py-2 border border-gold text-gold text-[10px] uppercase tracking-widest font-bold backdrop-blur-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                            {{ translations.book_ritual || 'Book Ritual' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 flex gap-8">
            <a href="/login" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gold transition-colors font-bold">{{ translations.admin_portal || 'Admin Portal' }}</a>
            <a href="/book" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gold transition-colors font-bold">{{ translations.quick_book || 'Quick Book' }}</a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const barbers = ref([])
const translations = ref({})
const locale = ref('ro')

onMounted(() => {
    const el = document.getElementById('barber-gallery')
    barbers.value = JSON.parse(el.dataset.barbers || '[]')
    translations.value = JSON.parse(el.dataset.translations || '{}')
    locale.value = el.dataset.locale || 'ro'
})

const selectBarber = (barber) => {
    window.location.href = `/book?barber_id=${barber.id}`
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-up {
    from { 
        opacity: 0;
        transform: translateY(40px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1.5s ease-out forwards;
}

.animate-slide-up {
    animation: slide-up 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
