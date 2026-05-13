<template>
    <div class="p-4 bg-dark-soft rounded-xl border border-white/5">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-serif text-gold">Welcome back, {{ user.name }}</h3>
            <span class="px-3 py-1 bg-gold/10 text-gold text-[10px] uppercase tracking-widest rounded-full font-bold">
                {{ user.role }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-dark p-4 border border-white/5 hover:border-gold/30 transition-all cursor-pointer group">
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Today's Revenue</div>
                <div class="text-2xl font-serif text-white group-hover:text-gold transition-colors">420.00 RON</div>
            </div>
            <div class="bg-dark p-4 border border-white/5 hover:border-gold/30 transition-all cursor-pointer group">
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Active Barbers</div>
                <div class="text-2xl font-serif text-white group-hover:text-gold transition-colors">12</div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-white/5">
            <button @click="count++" class="w-full py-3 border border-gold/20 text-gold text-xs uppercase tracking-widest hover:bg-gold/5 transition-all">
                Interactive Vue Action: {{ count }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps(['userData'])
const user = ref(props.userData || { name: 'Admin', role: 'admin' })
const count = ref(0)
</script>
