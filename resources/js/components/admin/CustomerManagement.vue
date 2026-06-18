<template>
    <div class="space-y-6 animate-fade-in">
        <!-- Table Card -->
        <div class="bg-[#111] border border-white/5 rounded-xl overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
                <div class="relative w-72">
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Search customers..." 
                        class="w-full bg-[#161616] border border-white/10 rounded-lg px-4 py-2 text-xs text-white focus:border-gold outline-none transition-all pl-10"
                    >
                    <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                    Total: {{ filteredCustomers.length }} Customers
                </div>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Customer</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Contact Info</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Joined</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 border border-white/10 flex-shrink-0 flex items-center justify-center text-gold font-serif">
                                    {{ customer.name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white group-hover:text-gold transition-colors">{{ customer.name }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ customer.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-300">{{ customer.phone || 'No phone' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500">{{ formatDate(customer.created_at) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="editCustomer(customer)" class="p-2 text-gray-500 hover:text-gold transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button class="p-2 text-gray-500 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="filteredCustomers.length === 0" class="p-12 text-center">
                <div class="text-gray-500 text-sm mb-4">No customers found matching your search.</div>
            </div>
        </div>

        <!-- Add/Edit Customer Modal -->
        <Teleport to="body">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
                <div class="bg-[#111] border border-white/10 w-full max-w-md p-8 shadow-2xl relative animate-fade-in">
                    <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors text-2xl">&times;</button>
                    <h3 class="text-2xl font-serif text-white italic mb-6">{{ isEditMode ? 'Edit Customer' : 'Add New Customer' }}</h3>
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Full Name</label>
                            <input v-model="form.name" type="text" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Email Address</label>
                            <input v-model="form.email" type="email" required class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Phone Number</label>
                            <input v-model="form.phone" type="text" class="w-full bg-[#161616] border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-gold transition-colors text-sm" placeholder="+40 7xx xxx xxx">
                        </div>

                        <div class="pt-4">
                            <button type="submit" :disabled="loading" class="w-full bg-gold text-dark font-bold py-4 uppercase tracking-[0.2em] text-xs hover:bg-gold-light transition-all shadow-[0_0_15px_rgba(197,160,89,0.2)] disabled:opacity-50">
                                <span v-if="!loading">{{ isEditMode ? 'Update Customer' : 'Save Customer' }}</span>
                                <span v-else>Saving...</span>
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
import { ref, reactive, onMounted, computed } from 'vue'

const props = defineProps({
    initialCustomers: {
        type: Array,
        default: () => []
    }
})

const customers = ref(props.initialCustomers)
const searchQuery = ref('')
const isModalOpen = ref(false)
const isEditMode = ref(false)
const editingCustomerId = ref(null)
const loading = ref(false)
const errorMsg = ref('')

const form = reactive({
    name: '',
    email: '',
    phone: ''
})

const filteredCustomers = computed(() => {
    if (!searchQuery.value) return customers.value
    const query = searchQuery.value.toLowerCase()
    return customers.value.filter(c => 
        c.name.toLowerCase().includes(query) || 
        c.email.toLowerCase().includes(query) ||
        (c.phone && c.phone.includes(query))
    )
})

const closeModal = () => {
    isModalOpen.value = false
    isEditMode.value = false
    editingCustomerId.value = null
    errorMsg.value = ''
    form.name = ''
    form.email = ''
    form.phone = ''
}

const editCustomer = (customer) => {
    isEditMode.value = true
    editingCustomerId.value = customer.id
    form.name = customer.name
    form.email = customer.email
    form.phone = customer.phone || ''
    isModalOpen.value = true
}

const submitForm = async () => {
    loading.value = true
    errorMsg.value = ''
    try {
        const token = document.head.querySelector('meta[name="csrf-token"]').content
        
        const url = isEditMode.value ? `/admin/clients/${editingCustomerId.value}` : '/admin/clients'
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(form)
        })

        if (!response.ok) {
            const data = await response.json()
            throw new Error(data.message || 'An error occurred while saving.')
        }

        const savedCustomer = await response.json()
        
        if (isEditMode.value) {
            const idx = customers.value.findIndex(c => c.id === savedCustomer.id)
            if (idx !== -1) customers.value[idx] = savedCustomer
        } else {
            customers.value.unshift(savedCustomer)
        }
        
        closeModal()
    } catch (error) {
        errorMsg.value = error.message
    } finally {
        loading.value = false
    }
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(() => {
    window.addEventListener('open-customer-modal', () => {
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
