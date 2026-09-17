<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white relative">
    
    <!-- ===== Siswa Navbar ===== -->
    <SiswaNavbar />

    <!-- ===== Main Content ===== -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 md:px-12 py-8 w-full space-y-6">
      <!-- Breadcrumb / Tombol Kembali -->
      <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200/80 px-4 py-2 rounded-full w-fit shadow-xs">
        <router-link to="/siswa/dashboard" class="hover:text-[#00B775] transition flex items-center space-x-1">
          <Home class="w-3.5 h-3.5 text-[#00B775]" />
          <span>Dashboard</span>
        </router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-900 font-bold">Histori Bukti Piket</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs text-left">
        <div class="space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100/80 border border-emerald-200 text-[#00B775] text-xs font-semibold">
            <History class="w-3.5 h-3.5" />
            <span>Histori Upload</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Histori Bukti Piket</h1>
          <p class="text-slate-600 text-sm">Lihat kembali seluruh foto bukti, status verifikasi admin, dan catatan tugas Anda.</p>
        </div>
      </div>

      <!-- History Data List -->
      <div class="space-y-4">
        <!-- Loading State -->
        <div v-if="loading" class="bg-white border border-slate-200/80 rounded-3xl p-12 text-center text-slate-500 font-medium flex items-center justify-center space-x-2 shadow-xs">
          <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memuat riwayat Anda...</span>
        </div>

        <!-- Empty State -->
        <div v-else-if="historyList.length === 0" class="bg-white border border-slate-200/80 rounded-3xl p-14 text-center shadow-xs">
          <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
            <History class="w-8 h-8 text-[#00B775]" />
          </div>
          <p class="text-slate-600 font-bold text-sm">Belum Ada Histori Bukti Piket</p>
          <p class="text-slate-400 text-xs mt-1">Anda belum pernah mengunggah foto bukti piket ke dalam sistem.</p>
        </div>

        <!-- List Cards -->
        <div v-else class="space-y-4">
          <div
            v-for="item in historyList"
            :key="item.id"
            class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs space-y-4 text-left hover:border-emerald-300 transition-all duration-200"
          >
            <!-- Card Header: Date, Time & Status Badge -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
              <div class="space-y-0.5">
                <div class="flex items-center space-x-2 text-sm font-bold text-slate-900">
                  <Calendar class="w-4 h-4 text-[#00B775]" />
                  <span>{{ formatDateWithDay(item.tanggal || item.created_at) }}</span>
                </div>
                <div class="flex items-center space-x-1.5 text-xs font-semibold text-slate-400 pl-6">
                  <Clock class="w-3.5 h-3.5 text-slate-400" />
                  <span>Upload: {{ formatTime(item.created_at) }}</span>
                </div>
              </div>

              <!-- Status Badge (Same style as Admin) -->
              <span
                :class="[
                  'self-start sm:self-center text-[11px] font-extrabold px-3 py-1 rounded-full border uppercase tracking-wider',
                  item.status_approval === 'approved' || item.status_approval === 'APPROVED' || item.status_approval === 'setuju'
                    ? 'bg-emerald-100 text-[#00B775] border-emerald-200'
                    : item.status_approval === 'rejected' || item.status_approval === 'REJECTED' || item.status_approval === 'tolak'
                    ? 'bg-rose-100 text-rose-600 border-rose-200'
                    : 'bg-amber-100 text-amber-700 border-amber-200'
                ]"
              >
                {{ (item.status_approval || 'PENDING').toUpperCase() }}
              </span>
            </div>

            <!-- Content Grid: Details & Photo Preview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
              <!-- Left 2 columns: Task List & Catatan / Alasan Upload -->
              <div class="md:col-span-2 space-y-3">
                <!-- Tasks list badges -->
                <div class="space-y-1">
                  <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Tugas Dikerjakan:</p>
                  <div v-if="item.tasks && item.tasks.length > 0" class="flex flex-wrap gap-1.5">
                    <span
                      v-for="t in item.tasks"
                      :key="t.id"
                      class="bg-emerald-50 text-[#00B775] border border-emerald-200 px-2.5 py-1 rounded-xl text-xs font-bold"
                    >
                      {{ t.nama_tugas }}
                    </span>
                  </div>
                  <p v-else class="text-xs font-semibold text-slate-700">Piket Kelas</p>
                </div>

                <!-- Alasan / Catatan Upload Siswa -->
                <div class="space-y-1">
                  <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Alasan / Catatan Upload:</p>
                  <p class="text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl p-3 leading-relaxed italic">
                    "{{ item.deskripsi || item.catatan || 'Tidak ada catatan tambahan.' }}"
                  </p>
                </div>

                <!-- Alasan Reject dari Admin (Jika Ditolak) -->
                <div v-if="(item.status_approval === 'rejected' || item.status_approval === 'REJECTED' || item.status_approval === 'tolak') && (item.catatan_admin || item.alasan_reject)" class="space-y-1">
                  <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500 flex items-center space-x-1">
                    <AlertCircle class="w-3.5 h-3.5" />
                    <span>Alasan Penolakan Admin:</span>
                  </p>
                  <p class="text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3 leading-relaxed">
                    "{{ item.catatan_admin || item.alasan_reject }}"
                  </p>
                </div>
              </div>

              <!-- Right column: 2 Foto Bukti Thumbnails with Zoom -->
              <div class="space-y-1.5">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Foto Bukti:</p>
                <div class="grid grid-cols-2 gap-2">
                  <div
                    v-if="item.foto_1"
                    @click="openImageModal(getImageUrl(item.foto_1))"
                    class="aspect-square bg-slate-200 rounded-2xl overflow-hidden border border-slate-200 hover:border-[#00B775] hover:opacity-90 transition cursor-pointer relative group shadow-xs"
                    title="Klik untuk Zoom"
                  >
                    <img :src="getImageUrl(item.foto_1)" alt="Foto 1" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                      <ZoomIn class="w-4 h-4" />
                    </div>
                  </div>
                  <div v-else class="aspect-square bg-slate-100 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 font-bold text-sm select-none">
                    -
                  </div>

                  <div
                    v-if="item.foto_2"
                    @click="openImageModal(getImageUrl(item.foto_2))"
                    class="aspect-square bg-slate-200 rounded-2xl overflow-hidden border border-slate-200 hover:border-[#00B775] hover:opacity-90 transition cursor-pointer relative group shadow-xs"
                    title="Klik untuk Zoom"
                  >
                    <img :src="getImageUrl(item.foto_2)" alt="Foto 2" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                      <ZoomIn class="w-4 h-4" />
                    </div>
                  </div>
                  <div v-else class="aspect-square bg-slate-100 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 font-bold text-sm select-none">
                    -
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ===== Teleport Image Preview Modal (Zoom In / Out) ===== -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="previewModal.show"
          class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-[100] flex flex-col items-center justify-center p-4 font-sans select-none"
          @click.self="closeImageModal"
        >
          <!-- Floating Toolbar Controls -->
          <div class="fixed top-6 bg-slate-900/90 border border-slate-700/80 rounded-2xl px-4 py-2 flex items-center space-x-3 text-white shadow-2xl backdrop-blur-lg z-[110]">
            <button
              type="button"
              @click="zoomOut"
              class="p-2 hover:bg-slate-800 rounded-xl transition cursor-pointer text-slate-300 hover:text-white"
              title="Zoom Out (-)"
            >
              <ZoomOut class="w-5 h-5" />
            </button>
            <span class="text-xs font-mono font-bold w-12 text-center text-[#00B775]">
              {{ Math.round(previewModal.zoom * 100) }}%
            </span>
            <button
              type="button"
              @click="zoomIn"
              class="p-2 hover:bg-slate-800 rounded-xl transition cursor-pointer text-slate-300 hover:text-white"
              title="Zoom In (+)"
            >
              <ZoomIn class="w-5 h-5" />
            </button>
            <button
              type="button"
              @click="resetZoom"
              class="p-2 hover:bg-slate-800 rounded-xl transition cursor-pointer text-slate-300 hover:text-white text-xs font-semibold px-2.5"
              title="Reset Zoom"
            >
              Reset
            </button>
            <div class="w-px h-5 bg-slate-700"></div>
            <button
              type="button"
              @click="closeImageModal"
              class="p-2 bg-rose-600/80 hover:bg-rose-600 rounded-xl transition cursor-pointer text-white"
              title="Tutup Modal"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Image Display Area -->
          <div class="w-full h-full flex items-center justify-center overflow-auto p-8 max-w-5xl max-h-[85vh]">
            <img
              :src="previewModal.url"
              alt="Bukti Piket Large Preview"
              class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl transition-transform duration-200 ease-out"
              :style="{ transform: `scale(${previewModal.zoom})` }"
            />
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'
import SiswaNavbar from '@/components/SiswaNavbar.vue'
import {
  History,
  Home,
  ChevronRight,
  Calendar,
  Clock,
  AlertCircle,
  ZoomIn,
  ZoomOut,
  X
} from 'lucide-vue-next'

const historyList = ref([])
const loading = ref(false)

// Image Zoom Modal State
const previewModal = ref({
  show: false,
  url: '',
  zoom: 1
})

const openImageModal = (url) => {
  if (!url) return
  previewModal.value = { show: true, url, zoom: 1 }
}

const closeImageModal = () => {
  previewModal.value.show = false
}

const zoomIn = () => {
  if (previewModal.value.zoom < 3) {
    previewModal.value.zoom = Number((previewModal.value.zoom + 0.25).toFixed(2))
  }
}

const zoomOut = () => {
  if (previewModal.value.zoom > 0.5) {
    previewModal.value.zoom = Number((previewModal.value.zoom - 0.25).toFixed(2))
  }
}

const resetZoom = () => {
  previewModal.value.zoom = 1
}

const getImageUrl = (path) => {
  if (!path) return ''
  return `http://127.0.0.1:8000/storage/${path}`
}

const formatDateWithDay = (dateStr) => {
  if (!dateStr) return '-'
  const date = typeof dateStr === 'string' && dateStr.length === 10 ? new Date(`${dateStr}T00:00:00`) : new Date(dateStr)
  if (isNaN(date.getTime())) return dateStr
  return new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }).format(date)
}

const formatTime = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  if (isNaN(date.getTime())) return '-'
  return new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(date) + ' WIB'
}

const fetchHistory = async () => {
  loading.value = true
  try {
    const res = await api.get('/siswa/bukti-piket')
    const historyData = res.data?.data || res.data
    if (Array.isArray(historyData)) {
      historyList.value = historyData
    } else {
      historyList.value = []
    }
  } catch (err) {
    console.error('Failed to load history from API:', err)
    historyList.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchHistory()
})
</script>
