<template>
  <header class="w-full bg-white/70 backdrop-blur-md border-b border-slate-200/50 py-3.5 px-6 md:px-12 flex items-center justify-between shadow-xs sticky top-0 z-40 transition-all duration-300">
    <!-- Brand Logo Text Only -->
    <div class="flex items-center">
      <router-link to="/admin/dashboard" class="text-2xl font-extrabold tracking-tight text-slate-900">
        Clean<span class="text-[#00B775]">Mate</span>
      </router-link>
    </div>

    <!-- Action Area: Logout Button -->
    <div class="flex items-center space-x-4">
      <button
        @click="showLogoutModal = true"
        class="inline-flex items-center space-x-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white px-5 py-2 rounded-xl font-semibold text-sm transition duration-200 shadow-md shadow-rose-500/20 hover:shadow-rose-500/35 hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
      >
        <LogOut class="w-4 h-4" />
        <span>Logout</span>
      </button>
    </div>

    <!-- Teleport Modal ke <body> agar Backdrop Blur Menutupi Seluruh Layar (100% Sama Rata) -->
    <teleport to="body">
      <div
        v-if="showLogoutModal"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 text-left font-sans"
      >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
          <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
              <LogOut class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Keluar dari Akun?</h3>
              <p class="text-xs text-slate-500 font-medium">Konfirmasi sesi keluar</p>
            </div>
          </div>

          <p class="text-sm text-slate-600 leading-relaxed font-medium">
            Apakah Anda yakin ingin keluar dari sistem <span class="font-bold text-slate-900">CleanMate</span>?
          </p>

          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button
              type="button"
              @click="showLogoutModal = false"
              class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer"
            >
              Batal
            </button>
            <button
              type="button"
              @click="confirmLogout"
              class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition cursor-pointer"
            >
              Ya, Keluar
            </button>
          </div>
        </div>
      </div>
    </teleport>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { LogOut } from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()
const showLogoutModal = ref(false)

const confirmLogout = () => {
  showLogoutModal.value = false
  authStore.logout()
  router.push('/login')
}
</script>
