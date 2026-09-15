<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white relative">
    
    <!-- ===== Toast Notification ===== -->
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
          'fixed top-6 right-6 z-[60] px-4 py-3.5 rounded-2xl border shadow-xl flex items-center space-x-3 backdrop-blur-md max-w-sm',
          toast.type === 'success'
            ? 'bg-emerald-50/95 border-emerald-200 text-emerald-900'
            : 'bg-rose-50/95 border-rose-200 text-rose-900'
        ]"
      >
        <div :class="['w-8 h-8 rounded-xl flex items-center justify-center shrink-0', toast.type === 'success' ? 'bg-[#00B775] text-white' : 'bg-rose-600 text-white']">
          <CheckCircle2 v-if="toast.type === 'success'" class="w-4 h-4" />
          <AlertCircle v-else class="w-4 h-4" />
        </div>
        <div class="text-xs font-bold leading-snug flex-1">{{ toast.message }}</div>
        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- ===== Admin Navbar ===== -->
    <AdminNavbar />

    <!-- ===== Main Content ===== -->
    <main class="max-w-6xl mx-auto px-6 md:px-12 py-8 w-full space-y-6">

      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200/80 px-4 py-2 rounded-full w-fit shadow-xs">
        <router-link to="/admin/dashboard" class="hover:text-[#00B775] transition flex items-center space-x-1">
          <Home class="w-3.5 h-3.5 text-[#00B775]" />
          <span>Dashboard</span>
        </router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-900 font-bold">Verifikasi Bukti Piket</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs">
        <div class="space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-sky-100/80 border border-sky-200 text-sky-700 text-xs font-semibold">
            <Camera class="w-3.5 h-3.5" />
            <span>Verifikasi Laporan</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Bukti Piket Siswa</h1>
          <p class="text-slate-600 text-sm">Tinjau dan verifikasi laporan kebersihan dari siswa.</p>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
          <Search class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama siswa..."
            class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all shadow-xs"
          />
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
          <select
            v-model="statusFilter"
            class="flex-1 md:flex-none px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 cursor-pointer shadow-xs"
          >
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
          </select>
          <div class="text-xs font-semibold text-slate-500 shrink-0">
            Total: <span class="text-sky-600 font-bold text-sm">{{ filteredBukti.length }}</span>
          </div>
        </div>
      </div>

      <!-- Bukti Data Table -->
      <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col">
        <!-- Loading -->
        <div v-if="loading" class="p-10 text-center text-slate-500 font-medium flex items-center justify-center space-x-2">
          <svg class="animate-spin h-5 w-5 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memuat data bukti piket...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredBukti.length === 0" class="p-14 text-center">
          <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center mx-auto mb-4 border border-sky-100">
            <Camera class="w-8 h-8 text-sky-400" />
          </div>
          <p class="text-slate-600 font-bold text-sm">Tidak ada bukti piket ditemukan.</p>
          <p class="text-slate-400 text-xs mt-1">Coba ubah filter atau kata kunci pencarian.</p>
        </div>

        <!-- Table -->
        <div v-else>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead class="bg-sky-50/80 border-b border-sky-100 text-slate-700 font-bold uppercase text-xs">
                <tr>
                  <th class="px-5 py-4 w-12">No</th>
                  <th class="px-5 py-4">Siswa</th>
                  <th class="px-5 py-4">Tanggal / Waktu</th>
                  <th class="px-5 py-4">Foto Bukti</th>
                  <th class="px-5 py-4 text-center">Status</th>
                  <th class="px-5 py-4 text-center w-36">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="(item, index) in paginatedBukti"
                  :key="item.id"
                  class="hover:bg-sky-50/20 transition-colors group"
                >
                  <td class="px-5 py-4 font-semibold text-slate-400 text-xs">
                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                  </td>
                  <td class="px-5 py-4">
                    <p class="font-bold text-slate-900 text-sm">{{ item.siswa?.name || 'Siswa' }}</p>
                    <p v-if="item.catatan_siswa" class="text-[10px] text-slate-500 italic truncate max-w-[150px]">"{{ item.catatan_siswa }}"</p>
                  </td>
                  <td class="px-5 py-4">
                    <p class="font-semibold text-slate-700">{{ formatDate(item.created_at) }}</p>
                    <p class="text-[10px] text-slate-400">{{ formatTime(item.created_at) }}</p>
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center space-x-2">
                      <a v-if="item.foto_1" :href="getImageUrl(item.foto_1)" target="_blank" class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 hover:opacity-80 transition block bg-slate-100">
                        <img :src="getImageUrl(item.foto_1)" class="w-full h-full object-cover" />
                      </a>
                      <a v-if="item.foto_2" :href="getImageUrl(item.foto_2)" target="_blank" class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 hover:opacity-80 transition block bg-slate-100">
                        <img :src="getImageUrl(item.foto_2)" class="w-full h-full object-cover" />
                      </a>
                    </div>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <span :class="[
                      'inline-block text-[10px] font-extrabold px-2.5 py-1 rounded-full border uppercase tracking-wide',
                      item.status_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' :
                      item.status_approval === 'rejected' ? 'bg-rose-100 text-rose-700 border-rose-200' :
                      'bg-amber-100 text-amber-700 border-amber-200'
                    ]">
                      {{ item.status_approval }}
                    </span>
                    <p v-if="item.catatan_admin && item.status_approval === 'rejected'" class="text-[10px] text-rose-500 mt-1 truncate max-w-[100px]" :title="item.catatan_admin">
                      {{ item.catatan_admin }}
                    </p>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <div v-if="item.status_approval === 'pending'" class="flex items-center justify-center space-x-1.5">
                      <button @click="verifyItem(item.id, 'approved')" title="Setujui" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl border border-emerald-200 transition cursor-pointer">
                        <Check class="w-4 h-4" />
                      </button>
                      <button @click="openRejectModal(item)" title="Tolak" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer">
                        <X class="w-4 h-4" />
                      </button>
                    </div>
                    <div v-else class="text-[10px] text-slate-400 font-semibold italic">
                      Sudah diverifikasi
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
            <div>
              Menampilkan <span class="text-slate-900 font-bold">{{ startIdx }}</span> - <span class="text-slate-900 font-bold">{{ endIdx }}</span>
              dari <span class="text-sky-600 font-extrabold">{{ filteredBukti.length }}</span> Laporan
            </div>
            <div class="flex items-center space-x-1.5">
              <button v-if="currentPage > 1" @click="currentPage--" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-sky-50 hover:text-sky-600 transition cursor-pointer shadow-xs">
                <ChevronLeft class="w-4 h-4" />
              </button>
              <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :class="['w-9 h-9 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center', currentPage === page ? 'bg-sky-500 border-sky-500 text-white shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-sky-50 hover:text-sky-600']"
              >{{ page }}</button>
              <button v-if="currentPage < totalPages" @click="currentPage++" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-sky-50 hover:text-sky-600 transition cursor-pointer shadow-xs">
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ===== MODAL: Alasan Tolak ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showRejectModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Tolak Bukti Piket</h3>
              <p class="text-xs text-slate-500 font-medium">Berikan alasan mengapa laporan ditolak.</p>
            </div>
            <button @click="closeRejectModal" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitReject" class="space-y-4">
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Catatan Admin <span class="text-rose-500">*</span></label>
              <textarea
                v-model="rejectReason"
                rows="3"
                required
                placeholder="Contoh: Foto kurang jelas, atau kelas masih kotor..."
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all shadow-xs resize-none"
              ></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
              <button type="button" @click="closeRejectModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting || !rejectReason.trim()"
                class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
              >
                <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submitting ? 'Memproses...' : 'Tolak Laporan' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import api from '@/utils/api'
import AdminNavbar from '@/components/AdminNavbar.vue'
import {
  Camera, Search, Check, X,
  Home, ChevronRight, ChevronLeft,
  CheckCircle2, AlertCircle
} from 'lucide-vue-next'

// ========================
// STATE
// ========================
const buktiList = ref([])
const loading = ref(false)
const submitting = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const showRejectModal = ref(false)
const rejectingId = ref(null)
const rejectReason = ref('')

const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

// ========================
// HELPERS
// ========================
const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value.show = false }, 4000)
}

const getImageUrl = (path) => {
  if (!path) return ''
  return `http://127.0.0.1:8000/storage/${path}`
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(date)
}

const formatTime = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(date) + ' WIB'
}

// ========================
// COMPUTED
// ========================
const filteredBukti = computed(() => {
  let list = buktiList.value
  if (statusFilter.value) {
    list = list.filter(b => b.status_approval === statusFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(b => (b.siswa?.name || '').toLowerCase().includes(q))
  }
  return list
})

const totalPages = computed(() => Math.ceil(filteredBukti.value.length / itemsPerPage) || 1)
const paginatedBukti = computed(() => {
  const s = (currentPage.value - 1) * itemsPerPage
  return filteredBukti.value.slice(s, s + itemsPerPage)
})
const startIdx = computed(() => filteredBukti.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage + 1)
const endIdx = computed(() => Math.min(currentPage.value * itemsPerPage, filteredBukti.value.length))

watch([searchQuery, statusFilter], () => { currentPage.value = 1 })

// ========================
// API CALLS
// ========================
const fetchBukti = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/bukti-piket')
    buktiList.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    console.error('API /admin/bukti-piket error:', err)
    showToast('Gagal mengambil data bukti piket', 'error')
  } finally {
    loading.value = false
  }
}

const verifyItem = async (id, status, catatan = '') => {
  try {
    await api.patch(`/admin/bukti-piket/${id}/status`, { 
      status_approval: status,
      catatan_admin: catatan
    })
    showToast(`Bukti piket berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}.`, 'success')
    await fetchBukti()
  } catch (err) {
    console.error('Gagal memverifikasi bukti:', err)
    showToast('Terjadi kesalahan saat memverifikasi.', 'error')
  }
}

// ========================
// MODAL HANDLERS
// ========================
const openRejectModal = (item) => {
  rejectingId.value = item.id
  rejectReason.value = ''
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectingId.value = null
  rejectReason.value = ''
}

const submitReject = async () => {
  if (!rejectingId.value || !rejectReason.value.trim()) return
  submitting.value = true
  await verifyItem(rejectingId.value, 'rejected', rejectReason.value)
  submitting.value = false
  closeRejectModal()
}

// ========================
// INIT
// ========================
onMounted(() => {
  fetchBukti()
})
</script>
