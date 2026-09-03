<template>
  <div class="h-screen w-full relative flex flex-col justify-center items-center px-4 font-sans bg-gradient-to-br from-slate-50 via-emerald-50/50 to-slate-100 selection:bg-[#00B775] selection:text-white overflow-hidden">
    <!-- Background Ambient Ornaments & Radial Mesh -->
    <div class="absolute -top-28 -right-28 w-[550px] h-[550px] bg-[#00B775]/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-24 -left-24 w-[450px] h-[450px] bg-teal-300/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#00B775_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.08] pointer-events-none"></div>

    <div class="w-full max-w-md z-10 space-y-4 text-left">
      <!-- 1. Glassmorphism Login Card Form (Single Form Only) -->
      <div class="bg-white/80 backdrop-blur-md border border-white/90 shadow-2xl shadow-emerald-900/10 rounded-3xl p-7 sm:p-8 text-left space-y-5">
        <!-- Title & Brand Header -->
        <div class="text-center space-y-1.5 pb-1">
          <router-link to="/" class="text-3xl font-extrabold tracking-tight text-slate-900 inline-block">
            Clean<span class="text-[#00B775]">Mate</span>
          </router-link>
          <p class="text-slate-600 text-xs sm:text-sm font-medium">
            Masukkan email dan kata sandi Anda untuk mengakses akun.
          </p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Notification / Error alert -->
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-[-6px]"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-[-6px]"
          >
            <div v-if="errorMessage" class="bg-rose-50 border border-rose-200 text-rose-700 px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm font-medium flex items-center space-x-2">
              <AlertCircle class="w-4 h-4 text-rose-500 shrink-0" />
              <span>{{ errorMessage }}</span>
            </div>
          </transition>

          <!-- Email Input Field -->
          <div class="space-y-1.5">
            <label class="block text-slate-800 font-bold text-xs sm:text-sm">
              Email
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <Mail class="w-4 h-4 text-emerald-600/70" />
              </div>
              <input
                v-model="email"
                type="email"
                required
                placeholder="nama@sekolah.sch.id"
                class="w-full pl-10 pr-4 py-2.5 bg-white/90 border border-slate-200 rounded-2xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
              />
            </div>
          </div>

          <!-- Password Input Field -->
          <div class="space-y-1.5">
            <label class="block text-slate-800 font-bold text-xs sm:text-sm">
              Kata Sandi
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <Lock class="w-4 h-4 text-emerald-600/70" />
              </div>
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••"
                class="w-full pl-10 pr-10 py-2.5 bg-white/90 border border-slate-200 rounded-2xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
              >
                <Eye v-if="!showPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Helper link -->
          <div class="flex justify-end items-center text-xs pt-0.5">
            <a href="#" @click.prevent="showAdminHelpAlert" class="text-[#00B775] hover:underline font-semibold">
              Lupa kata sandi?
            </a>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="authStore.loading"
            class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-3.5 rounded-2xl font-bold text-base shadow-lg shadow-[#00B775]/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center disabled:opacity-70 cursor-pointer"
          >
            <span v-if="authStore.loading" class="flex items-center space-x-2">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Memproses...</span>
            </span>
            <span v-else>Masuk ke Akun</span>
          </button>

          <!-- Copyright teks -->
          <div class="text-center text-[11px] text-slate-400 pt-1 font-medium">
            &copy; {{ new Date().getFullYear() }} CleanMate. All rights reserved.
          </div>
        </form>
      </div>

      <!-- Tombol Back -->
      <div class="pt-1 flex justify-start">
        <router-link
          to="/"
          class="inline-flex items-center space-x-2 text-slate-600 hover:text-[#00B775] font-semibold text-xs sm:text-sm transition-all duration-200 hover:-translate-x-1 cursor-pointer"
        >
          <ArrowLeft class="w-4 h-4 text-[#00B775]" />
          <span>Kembali ke Beranda</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Mail, Lock, Eye, EyeOff, ArrowLeft, AlertCircle } from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  errorMessage.value = ''
  
  if (!email.value || !password.value) {
    errorMessage.value = 'Silakan isi email dan password.'
    return
  }

  // 1. Coba login sebagai Siswa terlebih dahulu
  const resSiswa = await authStore.login(
    { email: email.value, password: password.value },
    'siswa'
  )

  if (resSiswa.success && !resSiswa.fallback) {
    const redirectPath = route.query.redirect || '/siswa/dashboard'
    router.push(redirectPath)
    return
  }

  // 2. Jika Siswa gagal, otomatis coba login sebagai Admin
  const resAdmin = await authStore.login(
    { email: email.value, password: password.value },
    'admin'
  )

  if (resAdmin.success && !resAdmin.fallback) {
    const redirectPath = route.query.redirect || '/admin/dashboard'
    router.push(redirectPath)
    return
  }

  // Handle jika backend offline / fallback client-side testing:
  if (resSiswa.fallback || resAdmin.fallback) {
    const isAdminEmail = email.value.toLowerCase().includes('admin') || email.value.toLowerCase().includes('guru')
    const finalRole = isAdminEmail ? 'admin' : 'siswa'
    
    await authStore.login({ email: email.value, password: password.value }, finalRole)
    const redirectPath = route.query.redirect || (finalRole === 'admin' ? '/admin/dashboard' : '/siswa/dashboard')
    router.push(redirectPath)
    return
  }

  // 3. Jika kedua request gagal
  errorMessage.value = 'Email atau password yang Anda masukkan salah.'
}

const showAdminHelpAlert = () => {
  alert('Silakan hubungi Wali Kelas atau Administrator Sekolah untuk mereset kata sandi Anda.')
}
</script>
