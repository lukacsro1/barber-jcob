<template>
    <div class="min-h-screen bg-[#0a0a0a] flex items-center justify-center p-6 selection:bg-gold selection:text-dark">
        <div class="w-full max-w-md">
            <!-- Logo / Brand -->
            <div class="text-center mb-12 animate-fade-in">
                <div class="text-3xl font-serif font-bold tracking-widest text-gold uppercase mb-2">Jcob</div>
                <div class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold">Admin Portal</div>
            </div>

            <!-- Login Card -->
            <div class="bg-[#111] border border-white/5 p-8 md:p-12 shadow-2xl relative overflow-hidden group animate-slide-up">
                <!-- Subtle decorative line -->
                <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-gold to-transparent opacity-50 group-hover:opacity-100 transition-opacity duration-700"></div>

                <h1 class="text-2xl font-serif mb-8 text-white">Login</h1>

                <!-- Real Form -->
                <form action="/login" method="POST" @submit="handleSubmit" class="space-y-6">
                    <input type="hidden" name="_token" :value="csrfToken">

                    <div>
                        <label for="email" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-2">Email Address</label>
                        <input 
                            id="email"
                            name="email"
                            v-model="form.email"
                            type="email" 
                            required
                            class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors placeholder:text-gray-700"
                            placeholder="admin@jcob.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-2">Password</label>
                        <input 
                            id="password"
                            name="password"
                            v-model="form.password"
                            type="password" 
                            required
                            class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors placeholder:text-gray-700"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="hidden peer">
                            <div class="w-4 h-4 border border-white/20 flex items-center justify-center peer-checked:bg-gold peer-checked:border-gold transition-all">
                                <div class="w-2 h-2 bg-dark opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <span class="text-xs text-gray-500 group-hover:text-gray-300 transition-colors">Remember me</span>
                        </label>
                        <a href="#" class="text-xs text-gold/60 hover:text-gold transition-colors">Forgot password?</a>
                    </div>

                    <button 
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-gold text-dark font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-gold-light transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed mt-4 shadow-[0_0_20px_rgba(197,160,89,0.15)]"
                    >
                        <span v-if="!loading">Sign In</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-dark" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing
                        </span>
                    </button>
                </form>

                <div v-if="errors.length > 0" class="mt-6 p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs text-center animate-shake">
                    <div v-for="err in errors" :key="err">{{ err }}</div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="#" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gray-400 transition-colors font-bold tracking-[0.3em]">JCOB &copy; 2024</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const csrfToken = ref('')
const errors = ref([])
const loading = ref(false)

const form = reactive({
    email: '',
    password: ''
})

onMounted(() => {
    const el = document.getElementById('app')
    csrfToken.value = el.dataset.csrf
    errors.value = JSON.parse(el.dataset.errors || '[]')
    form.email = el.dataset.oldEmail || ''
})

const handleSubmit = () => {
    loading.value = true
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
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}

.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

.animate-slide-up {
    animation: slide-up 0.8s ease-out forwards;
}

.animate-shake {
    animation: shake 0.2s ease-in-out 0s 2;
}
</style>