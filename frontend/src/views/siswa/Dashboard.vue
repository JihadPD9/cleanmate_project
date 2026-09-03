<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white relative">
    <!-- Custom Toast Notification -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-x-8 scale-95"
      enter-to-class="opacity-100 translate-x-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-x-0 scale-100"
      leave-to-class="opacity-0 translate-x-8 scale-95"
    >
      <div
        v-if="toast.show"
        :class="[
          'fixed top-6 right-6 z-50 px-4 py-3.5 rounded-2xl border shadow-xl flex items-center space-x-3 backdrop-blur-md max-w-md',
          toast.type === 'success'
            ? 'bg-emerald-50/95 border-emerald-200 text-emerald-900'
            : toast.type === 'info'
            ? 'bg-sky-50/95 border-sky-200 text-sky-900'
            : 'bg-rose-50/95 border-rose-200 text-rose-900'
        ]"
      >
        <div :class="[
          'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs',
          toast.type === 'success' ? 'bg-[#00B775] text-white' : toast.type === 'info' ? 'bg-sky-600 text-white' : 'bg-rose-600 text-white'
        ]">
          <CheckCircle2 v-if="toast.type === 'success'" class="w-4 h-4" />
          <Sparkles v-else-if="toast.type === 'info'" class="w-4 h-4" />
          <AlertCircle v-else class="w-4 h-4" />
        </div>
        <div class="text-xs font-bold leading-snug">
          {{ toast.message }}
        </div>
        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 ml-auto p-1 cursor-pointer">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- Siswa Navbar Component with Dropdown Menu -->
    <SiswaNavbar @open-inbox="showInboxModal = true" @open-history="showHistoryModal = true" />

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-4 md:px-8 py-6 w-full space-y-5 flex-1">
      <!-- 1. Real-time Date Header Line -->
      <div class="text-left font-semibold text-sm sm:text-base text-slate-800">
        <span class="text-[#00B775] font-extrabold">{{ currentDayName }}</span>, {{ formattedDateOnly }}
      </div>

      <!-- 2. Welcome Banner Card (Dynamic Student Name) -->
      <div class="bg-white border-2 border-[#00B775]/60 rounded-3xl p-6 sm:p-8 shadow-xs text-left space-y-2">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
          Selamat Datang, <span class="text-[#00B775]">{{ studentName }}</span>
        </h1>
        <p class="text-slate-600 text-sm sm:text-base font-normal">
          Berikut adalah gambaran umum jadwal piket Anda.
        </p>
      </div>

      <!-- 3. Grid Row: Schedule Card & Upload Proof Action Card -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left items-stretch">
        <!-- Left Card: Jadwal Piket (Layout Rata Kiri Estetis) -->
        <div class="bg-white border-2 border-[#00B775]/70 rounded-3xl p-6 sm:p-7 shadow-xs flex flex-col justify-between space-y-5">
          <div class="space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
              <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Jadwal Piket</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                  Hari ini: <span class="text-[#00B775] font-extrabold">{{ currentDayName }}</span>
                </p>
              </div>
              <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0">
                <Calendar class="w-5 h-5" />
              </div>
            </div>

            <div v-if="loadingSchedule && todayDutyOfficers.length === 0" class="py-8 text-center text-slate-400 text-xs font-semibold">
              Memuat data jadwal piket...
            </div>

            <!-- List of Officers for Today -->
            <div v-else class="space-y-3">
              <div
                v-for="(officer, index) in todayDutyOfficers"
                :key="officer.id || index"
                class="w-full bg-white border border-slate-300/80 hover:border-[#00B775] rounded-2xl p-3.5 flex items-center space-x-3 transition-all duration-200 shadow-2xs"
              >
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                  <User class="w-4 h-4 text-[#00B775]" />
                </div>
                <span class="font-bold text-slate-900 text-sm sm:text-base">
                  {{ officer.name || officer.nama_siswa || officer }}
                </span>
                <span v-if="isCurrentStudent(officer)" class="ml-auto text-[10px] font-extrabold uppercase bg-emerald-100 text-[#00B775] px-2.5 py-1 rounded-full border border-emerald-200">
                  Anda
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Card: Upload Bukti Piket Action Card / Status Info Card -->
        <!-- Option 0: Loading State (Mencegah Flashing / Salah Tampil Card Saat Fetching Data) -->
        <div
          v-if="loadingSchedule && todayDutyOfficers.length === 0"
          class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs flex flex-col items-center justify-center text-center py-16 space-y-3"
        >
          <div class="w-9 h-9 border-3 border-[#00B775] border-t-transparent rounded-full animate-spin"></div>
          <p class="text-xs font-extrabold text-slate-400">Memuat status piket...</p>
        </div>

        <!-- Option A: Jika Hari Ini Adalah Jadwal Piket Siswa -->
        <div
          v-else-if="isTodayMyDutyDay"
          class="bg-gradient-to-br from-[#00B775] via-emerald-600 to-teal-600 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-[#00B775]/25 flex flex-col justify-between text-left space-y-6 relative overflow-hidden group"
        >
          <!-- Watermark Icon Background -->
          <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none">
            <CheckCircle2 class="w-64 h-64 text-white" />
          </div>

          <div class="space-y-4 z-10">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                <CheckCircle2 class="w-6 h-6 stroke-[2.5]" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-white bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/30">
                Piket Hari Ini
              </span>
            </div>

            <div class="space-y-2">
              <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug tracking-tight">
                Sudahkah Anda bersih-bersih hari ini?
              </h2>
              <p class="text-emerald-100 text-xs sm:text-sm font-medium leading-relaxed">
                Kirimkan bukti foto kebersihan kelas Anda sekarang untuk menyelesaikan tugas piket harian.
              </p>
            </div>
          </div>

          <!-- Direct CTA Button redirect ke Form Upload -->
          <div class="z-10 pt-2">
            <button
              @click="handleRedirectToUploadForm"
              class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 px-5 rounded-2xl font-bold text-xs sm:text-sm shadow-xl transition-all duration-200 flex items-center justify-between cursor-pointer hover:translate-y-[-1px]"
            >
              <div class="flex items-center space-x-2">
                <Upload class="w-4 h-4 text-[#00B775]" />
                <span>Kirim Bukti Piket</span>
              </div>
              <ArrowRight class="w-4 h-4 text-emerald-400" />
            </button>
          </div>
        </div>

        <!-- Option B: Jika Hari Ini BUKAN Jadwal Piket Siswa (Desain Rata Kiri) -->
        <div
          v-else
          class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs flex flex-col justify-between text-left space-y-6 relative overflow-hidden"
        >
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0">
                <CalendarCheck class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Off-Duty
              </span>
            </div>

            <div class="space-y-2">
              <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Tidak Ada Piket Hari Ini
              </h2>
              <p class="text-slate-600 text-xs sm:text-sm font-medium leading-relaxed">
                Anda tidak memiliki jadwal piket pada hari <span class="font-bold text-[#00B775]">{{ currentDayName }}</span>.
              </p>
            </div>

            <!-- Info Box Jadwal Piket Siswa -->
            <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 space-y-1">
              <p class="text-xs font-bold text-slate-700">Jadwal Piket Anda:</p>
              <p class="text-sm font-extrabold text-[#00B775]">
                {{ myDutyDaysFormatted }}
              </p>
            </div>
          </div>

          <div class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs text-slate-600 font-semibold flex items-center space-x-2">
            <Sparkles class="w-4 h-4 text-[#00B775] shrink-0" />
            <span>Terima kasih sudah menjaga kebersihan kelas!</span>
          </div>
        </div>
      </div>
    </main>

    <!-- Teleport Inbox Modal -->
    <teleport to="body">
      <div
        v-if="showInboxModal"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 text-left font-sans"
      >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
              <Inbox class="w-5 h-5 text-[#00B775]" />
              <h3 class="text-lg font-extrabold text-slate-900">Inbox Notifikasi</h3>
            </div>
            <button @click="showInboxModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-3.5 space-y-1">
              <div class="flex justify-between items-center text-xs font-bold text-[#00B775]">
                <span>Jadwal Piket</span>
                <span class="text-[10px] text-slate-400">Hari ini</span>
              </div>
              <p class="text-xs text-slate-700 font-medium">
                Anda memiliki jadwal piket kelas hari ini. Jangan lupa unggah foto bukti kebersihan!
              </p>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 space-y-1">
              <div class="flex justify-between items-center text-xs font-bold text-slate-800">
                <span>Sistem CleanMate</span>
                <span class="text-[10px] text-slate-400">Kemarin</span>
              </div>
              <p class="text-xs text-slate-600 font-medium">
                Selamat datang di aplikasi Manajemen Piket Kelas CleanMate.
              </p>
            </div>
          </div>

          <button
            @click="showInboxModal = false"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition cursor-pointer"
          >
            Tutup Inbox
          </button>
        </div>
      </div>
    </teleport>

    <!-- Teleport History Modal -->
    <teleport to="body">
      <div
        v-if="showHistoryModal"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 text-left font-sans"
      >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
              <History class="w-5 h-5 text-[#00B775]" />
              <h3 class="text-lg font-extrabold text-slate-900">Histori Bukti Piket</h3>
            </div>
            <button @click="showHistoryModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
            <div
              v-for="(hist, idx) in historyList"
              :key="idx"
              class="bg-white border border-slate-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-2xs"
            >
              <div>
                <p class="text-xs font-bold text-slate-900">{{ hist.tanggal }}</p>
                <p class="text-[11px] text-slate-500 font-medium">Tugas: {{ hist.tugas }}</p>
              </div>
              <span
                :class="[
                  'text-[10px] font-extrabold px-2.5 py-1 rounded-full border',
                  hist.status === 'SETUJU'
                    ? 'bg-emerald-100 text-[#00B775] border-emerald-200'
                    : hist.status === 'TOLAK'
                    ? 'bg-rose-100 text-rose-600 border-rose-200'
                    : 'bg-amber-100 text-amber-700 border-amber-200'
                ]"
              >
                {{ hist.status }}
              </span>
            </div>
          </div>

          <button
            @click="showHistoryModal = false"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition cursor-pointer"
          >
            Tutup Histori
          </button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'
import { useAuthStore } from '@/stores/auth'
import SiswaNavbar from '@/components/SiswaNavbar.vue'
import {
  User,
  CheckCircle2,
  Upload,
  Inbox,
  History,
  X,
  AlertCircle,
  CalendarCheck,
  Calendar,
  ArrowRight,
  Sparkles
} from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()

const currentDayName = ref('')
const formattedDateOnly = ref('')
const loadingSchedule = ref(false)

const showInboxModal = ref(false)
const showHistoryModal = ref(false)

const todayDutyOfficers = ref([])
const historyList = ref([])

const toast = ref({
  show: false,
  message: '',
  type: 'success',
})

const dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

// 1. Dynamic Student Name dari Auth Store (Ilman Abidullah)
const studentName = computed(() => {
  const user = authStore.user
  if (user) {
    if (user.name && user.name !== 'Siswa Piket' && user.name !== 'Siswa') {
      return user.name
    }
    if (user.email) {
      if (user.email.toLowerCase().includes('ilman')) return 'Ilman Abidullah'
      if (user.email.toLowerCase().includes('jihad')) return 'Jihad'
      
      const prefix = user.email.split('@')[0]
      return prefix
        .replace(/[._-]/g, ' ')
        .split(' ')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ')
    }
  }
  return 'Ilman Abidullah'
})

const isCurrentStudent = (officer) => {
  const name = typeof officer === 'string' ? officer : officer.name || officer.nama_siswa
  if (!name) return false
  return name.toLowerCase().includes(studentName.value.toLowerCase())
}

// 3. Logika: Jadwal piket siswa (Mencari hari-hari di mana siswa ini dijadwalkan)
const scheduleMapData = {
  Senin: [{ name: 'Jihad' }, { name: 'Ilman Abidullah' }],
  Selasa: [{ name: 'Riki' }, { name: 'Candra' }],
  Rabu: [{ name: 'Ilman Abidullah' }, { name: 'Riki' }],
  Kamis: [{ name: 'Jihad' }, { name: 'Candra' }],
  Jumat: [{ name: 'Ilman Abidullah' }, { name: 'Jihad' }, { name: 'Riki' }],
}

const myDutyDaysFormatted = computed(() => {
  const currentName = studentName.value.toLowerCase()
  const days = []
  for (const [day, officers] of Object.entries(scheduleMapData)) {
    if (officers.some((o) => o.name.toLowerCase().includes(currentName))) {
      days.push(day)
    }
  }
  if (days.length === 0) return 'Belum ditentukan'
  if (days.length === 1) return days[0]
  if (days.length === 2) return `${days[0]} & ${days[1]}`
  return days.slice(0, -1).join(', ') + ', & ' + days[days.length - 1]
})

const isTodayMyDutyDay = computed(() => {
  if (todayDutyOfficers.value.length === 0) return false
  return todayDutyOfficers.value.some((officer) => isCurrentStudent(officer))
})

const updateDate = () => {
  const now = new Date()
  const dayName = dayNamesIndo[now.getDay()]
  currentDayName.value = dayName

  const dd = String(now.getDate()).padStart(2, '0')
  const mm = String(now.getMonth() + 1).padStart(2, '0')
  const yyyy = now.getFullYear()

  formattedDateOnly.value = `${dd}-${mm}-${yyyy}`
}

// 2. Load Data Jadwal Harian
const fetchDashboardData = async () => {
  loadingSchedule.value = true
  try {
    const res = await api.get('/siswa/dashboard')
    const data = res.data?.data || res.data

    if (data && data.today_officers && Array.isArray(data.today_officers)) {
      todayDutyOfficers.value = data.today_officers
    } else if (data && data.jadwal && Array.isArray(data.jadwal)) {
      todayDutyOfficers.value = data.jadwal
    } else {
      loadFallbackOfficers()
    }

    if (data && data.history && Array.isArray(data.history)) {
      historyList.value = data.history
    } else {
      loadFallbackHistory()
    }
  } catch (err) {
    console.warn('API /siswa/dashboard call failed, loading fallback schedule:', err)
    loadFallbackOfficers()
    loadFallbackHistory()
  } finally {
    loadingSchedule.value = false
  }
}

const loadFallbackOfficers = () => {
  const day = currentDayName.value || 'Kamis'
  todayDutyOfficers.value = scheduleMapData[day] || [
    { id: 1, name: 'Jihad' },
    { id: 2, name: 'Candra' }
  ]
}

const loadFallbackHistory = () => {
  historyList.value = [
    { tanggal: 'Rabu, 02-12-2026', tugas: 'Menyapu & Mengepel', status: 'SETUJU' },
    { tanggal: 'Rabu, 25-11-2026', tugas: 'Papan Tulis & Sampah', status: 'SETUJU' },
    { tanggal: 'Rabu, 18-11-2026', tugas: 'Merapikan Meja', status: 'PENDING' },
  ]
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3500)
}

// 4. Redirect ke Form Upload Bukti Piket (/siswa/upload-bukti)
const handleRedirectToUploadForm = () => {
  router.push('/siswa/upload-bukti')
}

onMounted(() => {
  updateDate()
  loadFallbackOfficers() // Pre-fill synchronously on mount to eliminate loading flash!
  fetchDashboardData()
})
</script>
