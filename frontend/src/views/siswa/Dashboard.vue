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
    <SiswaNavbar />

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

      <!-- Off-Duty Info Banner (Tampil KHUSUS jika Piket Disetujui & Siswa sedang Off-Duty) -->
      <div
        v-if="showOffDutyBannerOnApproved"
        class="bg-emerald-50/90 border-2 border-emerald-300 rounded-3xl p-4 sm:p-5 flex items-center justify-between text-left shadow-xs animate-in fade-in duration-200"
      >
        <div class="flex items-center space-x-3.5">
          <div class="w-10 h-10 rounded-2xl bg-[#00B775] text-white flex items-center justify-center shrink-0 shadow-xs">
            <CalendarCheck class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Status Piket Anda:</span>
              <span class="text-[10px] font-extrabold uppercase bg-emerald-200 text-emerald-800 px-2.5 py-0.5 rounded-full border border-emerald-300">Off-Duty</span>
            </div>
            <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-0.5">
              Hari ini Anda libur piket (Piket Kelas Selesai). Jadwal piket Anda: <span class="font-extrabold text-[#00B775]">{{ myDutyDaysFormatted }}</span>.
            </p>
          </div>
        </div>
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
                  {{ officer.user?.name || officer.siswa?.name || officer.user?.nama || officer.name || officer.nama || officer }}
                </span>
                <span v-if="isCurrentStudent(officer)" class="ml-auto text-[10px] font-extrabold uppercase bg-emerald-100 text-[#00B775] px-2.5 py-1 rounded-full border border-emerald-200">
                  Anda
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Card: Upload Bukti Piket Action Card / Status Info Card -->
        <!-- Option 0: Loading State -->
        <div
          v-if="loadingSchedule && todayDutyOfficers.length === 0"
          class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs flex flex-col items-center justify-center text-center py-16 space-y-3"
        >
          <div class="w-9 h-9 border-3 border-[#00B775] border-t-transparent rounded-full animate-spin"></div>
          <p class="text-xs font-extrabold text-slate-400">Memuat status piket...</p>
        </div>

        <!-- Option A: Jika Bukti Piket Kelompok Hari Ini Sudah Di-upload (has_uploaded === true) -->
        <!-- a. Status Pending (Menunggu Verifikasi) -->
        <div
          v-else-if="todayStatus.has_uploaded && (todayStatus.status_approval === 'pending' || todayStatus.status_approval === 'PENDING')"
          class="bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-600 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-sky-500/25 flex flex-col justify-between text-left space-y-6 relative overflow-hidden group"
        >
          <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none">
            <Hourglass class="w-64 h-64 text-white" />
          </div>

          <div class="space-y-4 z-10">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                <Hourglass class="w-6 h-6 stroke-[2.5]" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-white bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/30">
                Menunggu Verifikasi
              </span>
            </div>

            <div class="space-y-2">
              <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug tracking-tight">
                Bukti Piket Dikirim
              </h2>
              <p class="text-sky-100 text-xs sm:text-sm font-medium leading-relaxed">
                Bukti Piket Kelompok Hari Ini Telah Dikirim oleh
                <span class="font-extrabold text-white underline decoration-sky-300 underline-offset-4">{{ todayStatus.uploader_name || todayStatus.uploader?.name || 'Anggota Kelompok' }}</span>
              </p>
            </div>
          </div>

          <div class="z-10 pt-2">
            <button
              disabled
              class="w-full bg-white/20 backdrop-blur-md text-white py-3.5 px-5 rounded-2xl font-bold text-xs sm:text-sm border border-white/30 flex items-center justify-center space-x-2 cursor-not-allowed opacity-90"
            >
              <Clock class="w-4 h-4 text-sky-200" />
              <span>Sudah Dikirim (Menunggu Admin)</span>
            </button>
          </div>
        </div>

        <!-- b. Status Approved (Disetujui Admin) -->
        <div
          v-else-if="todayStatus.has_uploaded && (todayStatus.status_approval === 'approved' || todayStatus.status_approval === 'APPROVED' || todayStatus.status_approval === 'setuju' || todayStatus.status_approval === 'SETUJU')"
          class="bg-gradient-to-br from-emerald-500 via-[#00B775] to-teal-700 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-emerald-500/25 flex flex-col justify-between text-left space-y-6 relative overflow-hidden group"
        >
          <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none">
            <CheckCircle2 class="w-64 h-64 text-white" />
          </div>

          <div class="space-y-4 z-10">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                <CheckCircle2 class="w-6 h-6 stroke-[2.5]" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-white bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/30">
                Piket Selesai
              </span>
            </div>

            <div class="space-y-2">
              <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug tracking-tight">
                Piket Disetujui
              </h2>
              <p class="text-emerald-100 text-xs sm:text-sm font-medium leading-relaxed">
                Piket Kelompok Hari Ini Selesai & Disetujui Admin. Terima kasih sudah menjaga kebersihan kelas!
              </p>
            </div>
          </div>

          <div class="z-10 pt-2">
            <button
              disabled
              class="w-full bg-white/20 backdrop-blur-md text-white py-3.5 px-5 rounded-2xl font-bold text-xs sm:text-sm border border-white/30 flex items-center justify-center space-x-2 cursor-not-allowed opacity-90"
            >
              <CheckCircle2 class="w-4 h-4 text-emerald-200" />
              <span>Tugas Piket Tuntas</span>
            </button>
          </div>
        </div>

        <!-- c. Status Rejected (Ditolak Admin) -->
        <div
          v-else-if="todayStatus.has_uploaded && (todayStatus.status_approval === 'rejected' || todayStatus.status_approval === 'REJECTED' || todayStatus.status_approval === 'tolak' || todayStatus.status_approval === 'TOLAK')"
          class="bg-gradient-to-br from-rose-500 via-rose-600 to-red-700 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-rose-500/25 flex flex-col justify-between text-left space-y-6 relative overflow-hidden group"
        >
          <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none">
            <AlertCircle class="w-64 h-64 text-white" />
          </div>

          <div class="space-y-4 z-10">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                <AlertCircle class="w-6 h-6 stroke-[2.5]" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-white bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/30">
                Perlu Perbaikan
              </span>
            </div>

            <div class="space-y-2">
              <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug tracking-tight">
                Bukti Piket Ditolak
              </h2>
              <div class="bg-white/15 border border-white/20 rounded-2xl p-3.5 backdrop-blur-xs space-y-1">
                <p class="text-[11px] font-bold uppercase tracking-wider text-rose-100">Catatan Admin:</p>
                <p class="text-xs sm:text-sm font-semibold text-white italic">
                  "{{ todayStatus.catatan_admin || todayStatus.catatan || 'Foto kurang jelas atau tugas belum selesai sepenuhnya.' }}"
                </p>
              </div>
            </div>
          </div>

          <div class="z-10 pt-2">
            <button
              @click="handleRedirectToUploadForm"
              class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 px-5 rounded-2xl font-bold text-xs sm:text-sm shadow-xl transition-all duration-200 flex items-center justify-between cursor-pointer hover:translate-y-[-1px]"
            >
              <div class="flex items-center space-x-2">
                <Upload class="w-4 h-4 text-rose-400" />
                <span>Kirim Ulang Bukti Piket</span>
              </div>
              <ArrowRight class="w-4 h-4 text-rose-300" />
            </button>
          </div>
        </div>

        <!-- d. Status Belum Upload & Hari Ini Piket Siswa -->
        <div
          v-else-if="isTodayMyDutyDay"
          class="bg-gradient-to-br from-[#00B775] via-emerald-600 to-teal-600 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-[#00B775]/25 flex flex-col justify-between text-left space-y-6 relative overflow-hidden group"
        >
          <div class="absolute -right-8 -bottom-8 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none">
            <Camera class="w-64 h-64 text-white" />
          </div>

          <div class="space-y-4 z-10">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-inner">
                <Camera class="w-6 h-6 stroke-[2.5]" />
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

        <!-- e. Jika Hari Ini BUKAN Jadwal Piket Siswa -->
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
  Sparkles,
  Hourglass,
  Clock,
  Camera
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
const allClassSchedules = ref([])
const todayStatus = ref({
  has_uploaded: false,
  status_approval: null,
  uploader_name: '',
  catatan_admin: ''
})

const toast = ref({
  show: false,
  message: '',
  type: 'success',
})

const dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

// Get Current Logged-In User ID
const currentUserId = computed(() => {
  const user = authStore.user
  if (user && user.id) return user.id
  
  // Fallback ke localStorage jika diset secara terpisah
  try {
    const localUser = JSON.parse(localStorage.getItem('user') || '{}')
    if (localUser && localUser.id) return localUser.id
  } catch (e) {
    // Ignore error
  }
  return null
})

// 1. Dynamic Student Name dari Auth Store
const studentName = computed(() => {
  const user = authStore.user
  if (user && user.name) {
    return user.name
  }
  return 'Siswa Piket'
})

const isCurrentStudent = (officer) => {
  if (!officer) return false
  
  // Cek pencocokan ID jika ID tersedia
  const officerId = officer.user_id || officer.siswa_id || officer.user?.id || officer.siswa?.id || officer.id
  if (currentUserId.value && officerId) {
    return Number(currentUserId.value) === Number(officerId)
  }
  
  // Fallback ke pencocokan nama / email
  const name = typeof officer === 'string'
    ? officer
    : officer.user?.name || officer.siswa?.name || officer.name || officer.nama_siswa || ''
  if (name && studentName.value) {
    return name.toLowerCase().includes(studentName.value.toLowerCase())
  }
  return false
}

// Format hari piket siswa (diambil dari data real API allClassSchedules)
const myDutyDaysFormatted = computed(() => {
  const days = []
  const uid = currentUserId.value
  const sName = studentName.value?.toLowerCase()

  allClassSchedules.value.forEach((item) => {
    const itemDay = item.hari_piket || item.hari
    if (!itemDay) return

    const officerId = item.user_id || item.siswa_id || item.user?.id || item.siswa?.id || item.id
    const officerName = (item.user?.name || item.siswa?.name || item.name || item.nama_siswa || '').toLowerCase()

    const isMatch = (uid && officerId && Number(uid) === Number(officerId)) ||
      (sName && officerName && officerName.includes(sName))

    if (isMatch && !days.includes(itemDay)) {
      days.push(itemDay)
    }
  })

  if (days.length === 0) return 'Belum ditentukan'
  if (days.length === 1) return days[0]
  if (days.length === 2) return `${days[0]} & ${days[1]}`
  return days.slice(0, -1).join(', ') + ', & ' + days[days.length - 1]
})

const isTodayMyDutyDay = computed(() => {
  if (todayDutyOfficers.value.length === 0) return false
  return todayDutyOfficers.value.some((officer) => isCurrentStudent(officer))
})

const isApprovedPiket = computed(() => {
  if (!todayStatus.value.has_uploaded) return false
  const status = (todayStatus.value.status_approval || '').toLowerCase()
  return status === 'approved' || status === 'setuju'
})

const showOffDutyBannerOnApproved = computed(() => {
  return isApprovedPiket.value && !isTodayMyDutyDay.value
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

// 2. Fetch Real Data Jadwal Piket Harian & Status Bukti Piket Hari Ini dari Backend Laravel
const fetchDashboardData = async () => {
  loadingSchedule.value = true
  try {
    // 1) Ambil status bukti piket kelompok hari ini dari GET /api/siswa/bukti-piket/today
    try {
      const resStatus = await api.get('/siswa/bukti-piket/today')
      const payload = resStatus.data
      const buktiRecord = payload?.data || null
      const hasUploaded = payload?.has_uploaded ?? (buktiRecord ? true : false)

      todayStatus.value = {
        has_uploaded: hasUploaded,
        status_approval: buktiRecord?.status_approval || buktiRecord?.status || null,
        uploader_name: buktiRecord?.user?.name || buktiRecord?.uploader_name || '',
        catatan_admin: buktiRecord?.catatan_admin || buktiRecord?.catatan || ''
      }
    } catch (errStatus) {
      console.warn('Gagal memuat status bukti piket hari ini:', errStatus)
    }

    // 2) Ambil jadwal kelas real dari GET /api/siswa/jadwal-piket/kelas
    let classSchedules = []
    try {
      const resJadwal = await api.get('/siswa/jadwal-piket/kelas')
      classSchedules = Array.isArray(resJadwal.data)
        ? resJadwal.data
        : (resJadwal.data?.data || [])
    } catch (errJadwal) {
      try {
        const resJadwalAlt = await api.get('/siswa/jadwal-piket')
        classSchedules = Array.isArray(resJadwalAlt.data)
          ? resJadwalAlt.data
          : (resJadwalAlt.data?.data || [])
      } catch (errAlt) {
        console.warn('Gagal memuat jadwal piket kelas dari API:', errAlt)
      }
    }

    allClassSchedules.value = classSchedules

    // 3) Filter petugas piket hari ini berdasarkan nama hari saat ini
    const today = currentDayName.value
    if (today && classSchedules.length > 0) {
      todayDutyOfficers.value = classSchedules.filter((j) => {
        const itemHari = j.hari_piket || j.hari
        return itemHari === today
      })
    } else {
      todayDutyOfficers.value = []
    }

    // 4) Ambil histori bukti piket milik siswa dari GET /api/siswa/bukti-piket
    try {
      const resHistory = await api.get('/siswa/bukti-piket')
      const historyData = resHistory.data?.data || resHistory.data
      if (Array.isArray(historyData) && historyData.length > 0) {
        historyList.value = historyData.map((item) => ({
          tanggal: item.tanggal,
          tugas: item.tasks?.map((t) => t.nama_tugas).join(', ') || item.deskripsi || 'Piket Kelas',
          status: (item.status_approval || 'PENDING').toUpperCase()
        }))
      } else {
        loadFallbackHistory()
      }
    } catch (errHist) {
      loadFallbackHistory()
    }
  } catch (err) {
    console.error('API Error fetchDashboardData:', err)
    todayDutyOfficers.value = []
    loadFallbackHistory()
  } finally {
    loadingSchedule.value = false
  }

  // 5) Cek Notifikasi Sanksi/Inbox & Status Sanksi Real
  try {
    const [resNotif, resSanksi] = await Promise.allSettled([
      api.get('/siswa/notifications'),
      api.get('/siswa/sanksi-siswa')
    ])

    const notifs = resNotif.status === 'fulfilled'
      ? (Array.isArray(resNotif.value.data) ? resNotif.value.data : (resNotif.value.data?.data || []))
      : []

    const mySanksi = resSanksi.status === 'fulfilled'
      ? (Array.isArray(resSanksi.value.data) ? resSanksi.value.data : (resSanksi.value.data?.data || []))
      : []

    const unreadNotifs = notifs.filter(n => n.read_at === null || (!n.read_at && !n.is_read))

    if (mySanksi.length > 0) {
      const selesaiCount = mySanksi.filter(s => (s.status_penyelesaian || s.status || '').toLowerCase() === 'selesai').length
      const belumCount = mySanksi.filter(s => (s.status_penyelesaian || s.status || '').toLowerCase() !== 'selesai').length

      setTimeout(() => {
        if (selesaiCount > 0 && belumCount > 0) {
          showToast(`Info Sanksi: ${selesaiCount} Sanksi Selesai dan ${belumCount} Sanksi Belum Selesai. Cek Inbox!`, 'info')
        } else if (selesaiCount > 0 && belumCount === 0) {
          showToast(`Semua Sanksi (${selesaiCount}) Anda telah diselesaikan oleh Admin!`, 'success')
        } else if (belumCount > 0) {
          showToast(`Perhatian: Anda memiliki ${belumCount} Sanksi yang belum diselesaikan. Cek Inbox!`, 'error')
        }
      }, 1000)
    } else if (unreadNotifs.length > 0) {
      setTimeout(() => {
        showToast(`Anda memiliki ${unreadNotifs.length} notifikasi belum dibaca. Cek menu Inbox Anda!`, 'info')
      }, 1000)
    }
  } catch (e) {
    console.warn('Gagal cek notifikasi & status sanksi', e)
  }
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
  fetchDashboardData()
})
</script>
