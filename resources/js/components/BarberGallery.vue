<template>
    <div class="min-h-screen bg-[#0a0a0a] flex flex-col items-center justify-center p-8 overflow-hidden selection:bg-gold selection:text-dark">
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
            <a href="/politica-de-confidentialitate" target="_blank" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gold transition-colors font-bold">{{ translations.privacy_policy || 'Privacy Policy' }}</a>
        </div>

        <!-- Consent Banner -->
        <transition name="slide-up-fade">
            <div v-if="showConsentBanner" class="fixed bottom-6 left-6 right-6 md:left-auto md:right-6 md:w-[420px] bg-[#111]/90 backdrop-blur-md border border-white/10 p-6 shadow-2xl z-50 flex flex-col gap-4">
                <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-gold to-transparent opacity-50"></div>
                <div class="text-[11px] text-gray-300 leading-relaxed font-sans">
                    {{ translations.consent_text || 'We use cookies and process personal data to ensure the best booking experience.' }}
                    <span class="block mt-1 font-semibold text-gray-400">
                        {{ translations.consent_read_more || 'Find out more in our' }}
                        <a href="/politica-de-confidentialitate" target="_blank" class="text-gold hover:underline font-bold">
                            {{ translations.privacy_policy || 'Privacy Policy' }}
                        </a>
                    </span>
                </div>
                <div class="flex justify-end mt-1">
                    <button @click="acceptConsent" class="px-6 py-2 border border-gold bg-gold text-dark hover:bg-gold-light hover:border-gold-light text-[10px] uppercase tracking-widest font-bold transition-all transform active:scale-95 cursor-pointer">
                        {{ translations.consent_accept || 'Accept' }}
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const barbers = ref([])
const translations = ref({})
const locale = ref('ro')
const showConsentBanner = ref(false)

onMounted(() => {
    const el = document.getElementById('barber-gallery')
    barbers.value = JSON.parse(el.dataset.barbers || '[]')
    translations.value = JSON.parse(el.dataset.translations || '{}')
    locale.value = el.dataset.locale || 'ro'

    if (!localStorage.getItem('jcob_consent_accepted')) {
        setTimeout(() => {
            showConsentBanner.value = true
        }, 800)
    }
})

const selectBarber = (barber) => {
    window.location.href = `/book?barber_id=${barber.id}`
}

const acceptConsent = () => {
    localStorage.setItem('jcob_consent_accepted', 'true')
    showConsentBanner.value = false
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

/* Slide up fade transition */
.slide-up-fade-enter-active,
.slide-up-fade-leave-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-fade-enter-from,
.slide-up-fade-leave-to {
    opacity: 0;
    transform: translateY(40px);
}
</style>
