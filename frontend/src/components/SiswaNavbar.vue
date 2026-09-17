<template>
  <header class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200/50 py-3.5 px-6 md:px-12 flex items-center justify-between shadow-xs sticky top-0 z-40 transition-all duration-300">
    <!-- Brand Logo Text Only -->
    <div class="flex items-center">
      <router-link to="/siswa/dashboard" class="text-2xl font-extrabold tracking-tight text-slate-900">
        Clean<span class="text-[#00B775]">Mate</span>
      </router-link>
    </div>

    <!-- Right Side: Green Dropdown Menu -->
    <div class="relative" ref="dropdownRef">
      <button
        @click="isMenuOpen = !isMenuOpen"
        class="relative bg-[#00B775] hover:bg-[#009d64] text-white px-4 py-2 rounded-xl font-bold text-sm flex items-center space-x-2.5 shadow-md shadow-[#00B775]/20 transition-all duration-200 cursor-pointer"
      >
        <span class="tracking-wide font-extrabold text-xs sm:text-sm">MENU</span>
        <Menu v-if="!isMenuOpen" class="w-4 h-4 stroke-[2.5]" />
        <X v-else class="w-4 h-4 stroke-[2.5]" />
        
        <!-- Red Dot Unread Badge -->
        <span v-if="unreadCount > 0" class="absolute -top-1.5 -right-1.5 flex h-3.5 w-3.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500 border-2 border-white"></span>
        </span>
      </button>

      <!-- Menu Dropdown Overlay Panel -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-[-6px]"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-[-6px]"
      >
        <div
          v-if="isMenuOpen"
          class="absolute right-0 mt-2 w-48 bg-[#00B775] text-white rounded-2xl shadow-2xl py-2 z-50 border border-emerald-400/40 text-left overflow-hidden"
        >
          <router-link
            to="/siswa/inbox"
            @click="isMenuOpen = false"
            class="flex items-center space-x-2.5 px-4 py-2.5 text-xs font-semibold hover:bg-[#009d64] transition relative"
          >
            <Inbox class="w-4 h-4" />
            <span class="flex-1">Inbox</span>
            <span v-if="unreadCount > 0" class="bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-xs">
              {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
          </router-link>

          <!-- Navigate directly to /siswa/history instead of emitting -->
          <router-link
            to="/siswa/history"
            @click="isMenuOpen = false"
            class="flex items-center space-x-2.5 px-4 py-2.5 text-xs font-semibold hover:bg-[#009d64] transition"
          >
            <History class="w-4 h-4" />
            <span>Histori</span>
          </router-link>

          <div class="my-1 border-t border-emerald-400/30"></div>

          <button
            @click="promptLogout"
            class="w-full flex items-center space-x-2.5 px-4 py-2.5 text-xs font-semibold hover:bg-rose-600 transition text-left cursor-pointer"
          >
            <LogOut class="w-4 h-4" />
            <span>Log out</span>
          </button>
        </div>
      </transition>
    </div>

    <!-- Teleport Modal ke <body> agar Backdrop Blur Menutupi Seluruh Layar -->
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
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Menu, X, Inbox, History, LogOut } from 'lucide-vue-next'
import api from '@/utils/api'

const router = useRouter()
const authStore = useAuthStore()
const isMenuOpen = ref(false)
const showLogoutModal = ref(false)
const dropdownRef = ref(null)
const unreadCount = ref(0)

const fetchUnreadCount = async () => {
  try {
    const res = await api.get('/siswa/notifications')
    if (res.data && typeof res.data.unread_count === 'number') {
      unreadCount.value = res.data.unread_count
    } else {
      const notifs = Array.isArray(res.data) ? res.data : (res.data?.data || [])
      unreadCount.value = notifs.filter(n => n.read_at === null || (!n.read_at && !n.is_read)).length
    }
  } catch (err) {
    console.error('Failed to fetch unread notifications', err)
  }
}

const promptLogout = () => {
  isMenuOpen.value = false
  showLogoutModal.value = true
}

const confirmLogout = () => {
  showLogoutModal.value = false
  authStore.logout()
  router.push('/login')
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('notifications-updated', fetchUnreadCount)
  fetchUnreadCount()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('notifications-updated', fetchUnreadCount)
})
</script>
