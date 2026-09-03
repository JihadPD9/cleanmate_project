<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white">
    <!-- Admin Navbar Component -->
    <AdminNavbar />

    <!-- Main Content Container (Single page flow, single window scrollbar) -->
    <main class="max-w-7xl mx-auto px-6 md:px-12 py-8 w-full space-y-6">
      <!-- Top Info Bar: Real-time WIB Clock & Dashboard Overview -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-slate-200/80 px-6 py-3.5 rounded-2xl shadow-xs">
        <div class="flex items-center space-x-2 text-xs md:text-sm font-bold text-slate-800">
          <Sparkles class="w-4 h-4 text-[#00B775]" />
          <span>Dashboard Overview</span>
        </div>

        <!-- Dynamic Real-time Clock (WIB) dengan nama hari warna hijau (#00B775) -->
        <div class="flex items-center space-x-2 text-xs md:text-sm font-semibold text-slate-700">
          <Clock class="w-4 h-4 text-[#00B775]" />
          <span>
            <span class="text-[#00B775] font-extrabold">{{ currentDayName }}</span>, {{ formattedDateOnly }} | {{ formattedTimeOnly }} WIB
          </span>
        </div>
      </div>

      <!-- Welcome Banner Card -->
      <div class="bg-white border border-slate-200/80 rounded-3xl p-6 md:p-8 shadow-xs text-left space-y-1.5">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
          Selamat Datang, <span class="bg-gradient-to-r from-[#00B775] to-emerald-600 bg-clip-text text-transparent">Admin!</span>
        </h1>
        <p class="text-slate-600 text-sm md:text-base font-normal max-w-xl">
          Pantau dan kelola aktivitas piket serta sanksi kelas hari ini secara praktis dan terintegrasi.
        </p>
      </div>

      <!-- SaaS Grid Layout: Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
        <!-- 1. Data Siswa Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#00B775]">
                <Users class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                Siswa
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Siswa</h2>
              <p class="text-slate-500 text-xs mt-0.5">Mengelola data siswa & sanksi manual</p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
              <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                <p class="text-xs font-medium text-slate-500">Total Siswa</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1">26</p>
              </div>
              <div class="bg-rose-50/70 border border-rose-200/80 rounded-xl p-3">
                <p class="text-xs font-medium text-rose-600">Sanksi Aktif</p>
                <p class="text-2xl font-extrabold text-rose-600 mt-1">2</p>
              </div>
            </div>
          </div>

          <button
            @click="showDetailAlert('Data Siswa')"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
          >
            <span>Lihat Detail</span>
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>

        <!-- 2. Data Tugas Piket Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 flex items-center justify-center text-teal-600">
                <ClipboardList class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-200">
                Tugas Piket
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Tugas Piket</h2>
              <p class="text-slate-500 text-xs mt-0.5 leading-relaxed">
                Konfigurasi daftar tugas utama dan kriteria kebersihan.
              </p>
            </div>

            <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-4 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-600">Total Tugas Piket</p>
                <p class="text-xs text-slate-400">Terdaftar di sistem</p>
              </div>
              <div class="text-3xl font-extrabold text-[#00B775]">
                <span v-if="tasksLoading" class="text-base text-slate-400">...</span>
                <span v-else>{{ totalTasksCount }}</span>
              </div>
            </div>
          </div>

          <router-link
            to="/admin/tasks"
            class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs"
          >
            <span>Kelola Tugas Piket</span>
            <ChevronRight class="w-4 h-4" />
          </router-link>
        </div>

        <!-- 3. Data Sanksi Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                <AlertTriangle class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                Kedisiplinan
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Sanksi</h2>
              <p class="text-slate-500 text-xs mt-0.5">Konfigurasi daftar sanksi dan poin pelanggaran.</p>
            </div>

            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-600">Total Kategori Sanksi</p>
                <p class="text-xs text-slate-400">Jenis sanksi aktif</p>
              </div>
              <div class="text-3xl font-extrabold text-amber-600">
                7
              </div>
            </div>
          </div>

          <button
            @click="showDetailAlert('Data Sanksi')"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
          >
            <span>Lihat Detail</span>
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>

        <!-- 4. Data Jadwal Piket Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5 lg:col-span-2">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600">
                <Calendar class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200">
                Penjadwalan
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Jadwal Piket</h2>
              <p class="text-slate-500 text-xs mt-0.5">Kelola giliran piket harian dan sanksi kelompok kelas.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Officers List -->
              <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-slate-700">Piket Hari Ini:</span>
                  <span class="text-xs font-extrabold text-[#00B775] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    {{ currentDayName }}
                  </span>
                </div>
                <ul class="text-xs text-slate-700 font-semibold space-y-1 list-disc list-inside">
                  <li>Jihad</li>
                  <li>Ilman</li>
                </ul>
              </div>

              <!-- Group Sanction -->
              <div class="bg-rose-50/70 border border-rose-200/80 rounded-xl p-4 flex flex-col justify-between">
                <p class="text-xs font-semibold text-rose-700">Sanksi Kelompok Aktif</p>
                <div class="flex items-baseline justify-between pt-2">
                  <span class="text-xs text-slate-500">Total Kelompok</span>
                  <span class="text-3xl font-extrabold text-rose-600">2</span>
                </div>
              </div>
            </div>
          </div>

          <button
            @click="showDetailAlert('Data Jadwal Piket')"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
          >
            <span>Lihat Detail Jadwal</span>
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>

        <!-- 5. Data Bukti Piket Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600">
                <Camera class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-200">
                Verifikasi
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Bukti Piket</h2>
              <p class="text-slate-500 text-xs mt-0.5">Verifikasi foto hasil kebersihan siswa.</p>
            </div>

            <!-- Proof Item -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-3">
              <p class="text-xs font-bold text-slate-700 flex items-center justify-between">
                <span>Bukti Terbaru:</span>
                <span class="text-[#00B775]">Jumat, 04-12-2026</span>
              </p>

              <div class="grid grid-cols-2 gap-2">
                <div class="bg-slate-200/80 rounded-xl h-20 flex items-center justify-center text-slate-500 text-xs font-bold shadow-inner">
                  Foto 1
                </div>
                <div class="bg-slate-200/80 rounded-xl h-20 flex items-center justify-center text-slate-500 text-xs font-bold shadow-inner">
                  Foto 2
                </div>
              </div>

              <!-- Action buttons -->
              <div class="grid grid-cols-2 gap-2 pt-1">
                <button
                  @click="approveProof"
                  class="bg-[#00B775] hover:bg-[#009d64] text-white py-2 rounded-xl font-bold text-xs shadow-xs transition cursor-pointer"
                >
                  SETUJU
                </button>
                <button
                  @click="rejectProof"
                  class="bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-xl font-bold text-xs shadow-xs transition cursor-pointer"
                >
                  TOLAK
                </button>
              </div>
            </div>
          </div>

          <button
            @click="showDetailAlert('Data Bukti Piket')"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
          >
            <span>Lihat Semua Bukti</span>
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/utils/api'
import AdminNavbar from '@/components/AdminNavbar.vue'
import {
  Sparkles,
  Clock,
  ClipboardList,
  Users,
  AlertTriangle,
  Calendar,
  Camera,
  ChevronRight
} from 'lucide-vue-next'

const currentDayName = ref('')
const formattedDateOnly = ref('')
const formattedTimeOnly = ref('')
const totalTasksCount = ref(0)
const tasksLoading = ref(false)
let timer = null

const dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

// Update Real-time WIB Date & Time (Hari warna hijau)
const updateWibClock = () => {
  const now = new Date()
  const dayName = dayNamesIndo[now.getDay()]
  currentDayName.value = dayName

  const dd = String(now.getDate()).padStart(2, '0')
  const mm = String(now.getMonth() + 1).padStart(2, '0')
  const yyyy = now.getFullYear()

  const hh = String(now.getHours()).padStart(2, '0')
  const min = String(now.getMinutes()).padStart(2, '0')
  const ss = String(now.getSeconds()).padStart(2, '0')

  formattedDateOnly.value = `${dd}-${mm}-${yyyy}`
  formattedTimeOnly.value = `${hh}:${min}:${ss}`
}

// Fetch Real Tasks Count from API GET /api/admin/tasks
const fetchTotalTasks = async () => {
  tasksLoading.value = true
  try {
    const res = await api.get('/admin/tasks')
    if (res.data && Array.isArray(res.data)) {
      totalTasksCount.value = res.data.length
    } else if (res.data && res.data.data && Array.isArray(res.data.data)) {
      totalTasksCount.value = res.data.data.length
    } else {
      totalTasksCount.value = 7
    }
  } catch (err) {
    console.warn('API /admin/tasks call failed, falling back to count 7:', err)
    totalTasksCount.value = 7
  } finally {
    tasksLoading.value = false
  }
}

const showDetailAlert = (sectionName) => {
  alert(`Halaman detail ${sectionName} sedang dalam pengembangan.`)
}

const approveProof = () => {
  alert('Bukti piket telah DISETUJU!')
}

const rejectProof = () => {
  alert('Bukti piket telah DITOLAK!')
}

onMounted(() => {
  updateWibClock()
  timer = setInterval(updateWibClock, 1000)
  fetchTotalTasks()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
