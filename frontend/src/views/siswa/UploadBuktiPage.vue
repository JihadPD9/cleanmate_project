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

    <!-- Siswa Navbar Component -->
    <SiswaNavbar @open-inbox="showInboxModal = true" @open-history="showHistoryModal = true" />

    <!-- Main Content Container -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-6 w-full space-y-6 flex-1 text-left">
      <!-- Top Action Bar: Back Button -->
      <div class="flex items-center justify-between">
        <router-link
          to="/siswa/dashboard"
          class="inline-flex items-center space-x-2 text-slate-600 hover:text-[#00B775] font-bold text-sm transition-colors duration-200 group"
        >
          <ChevronLeft class="w-5 h-5 text-slate-400 group-hover:text-[#00B775] transition-colors" />
          <span>Kembali ke Dashboard</span>
        </router-link>

        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
          Form Bukti Piket
        </span>
      </div>

      <!-- Main Form Container Card -->
      <div class="bg-white border-2 border-slate-200/90 rounded-3xl p-6 sm:p-10 shadow-xs space-y-7">
        <!-- Form Header Title & Date -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-5">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              Upload Bukti Piket
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">
              Dokumentasikan tugas-tugas yang telah diselesaikan.
            </p>
          </div>
          <div class="text-xs sm:text-sm font-extrabold text-[#00B775] bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-xl w-fit">
            {{ formattedDate }}
          </div>
        </div>

        <form @submit.prevent="handleFormSubmit" class="space-y-6">
          <!-- 1. Nama Siswa (Read-only / Locked) -->
          <div class="space-y-2">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Nama Siswa
            </label>
            <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-2xl p-4 flex items-center space-x-3.5">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0">
                <User class="w-5 h-5" />
              </div>
              <div>
                <p class="font-extrabold text-slate-900 text-sm sm:text-base">{{ studentName }}</p>
                <p class="text-[11px] text-slate-400 font-semibold">Terkunci (Read-Only)</p>
              </div>
              <span class="ml-auto text-[10px] font-extrabold uppercase bg-emerald-100 text-[#00B775] px-2.5 py-1 rounded-full border border-emerald-200">
                Aktif
              </span>
            </div>
          </div>

          <!-- 2. Jadwal Piket (Read-only / Locked) -->
          <div class="space-y-2">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Jadwal Piket
            </label>
            <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-2xl p-4 flex items-center space-x-3.5">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0">
                <Calendar class="w-5 h-5" />
              </div>
              <div>
                <p class="font-extrabold text-slate-900 text-sm sm:text-base">{{ currentDayName }}</p>
                <p class="text-[11px] text-slate-400 font-semibold">Terkunci (Read-Only)</p>
              </div>
            </div>
          </div>

          <!-- 3. Tugas (Diambil dari database / API tasks) -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                Tugas Piket <span class="text-rose-500">*</span>
              </label>
              <span class="text-[11px] text-slate-400 font-semibold">Pilih tugas yang diselesaikan</span>
            </div>

            <div v-if="loadingTasks" class="p-4 text-center text-xs text-slate-400 font-semibold">
              Memuat data tugas...
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label
                v-for="task in availableTasks"
                :key="task.id"
                :class="[
                  'p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center space-x-3 text-xs sm:text-sm font-bold',
                  selectedTaskIds.includes(task.id)
                    ? 'bg-emerald-50 border-[#00B775] text-[#00B775] shadow-xs'
                    : 'bg-white border-slate-200 text-slate-700 hover:border-emerald-300'
                ]"
              >
                <input
                  type="checkbox"
                  :value="task.id"
                  v-model="selectedTaskIds"
                  class="w-4 h-4 rounded text-[#00B775] focus:ring-[#00B775] accent-[#00B775]"
                />
                <span class="truncate">{{ task.nama_tugas }}</span>
              </label>
            </div>
          </div>

          <!-- 4. Bukti Piket (Maksimal 2 Foto) -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                Bukti Piket (Maks. 2 Foto) <span class="text-rose-500">*</span>
              </label>
              <span class="text-[11px] font-bold text-slate-400">{{ uploadedFiles.length }} / 2 Foto</span>
            </div>

            <!-- Upload Dropzone & Thumbnail Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Uploaded Previews -->
              <div
                v-for="(fileObj, idx) in uploadedFiles"
                :key="idx"
                class="relative rounded-2xl overflow-hidden border-2 border-emerald-200 bg-slate-100 aspect-video group"
              >
                <img :src="fileObj.preview" alt="Preview" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="removeFile(idx)"
                  class="absolute top-2 right-2 bg-rose-600 hover:bg-rose-700 text-white p-1.5 rounded-xl shadow-md transition cursor-pointer"
                  title="Hapus Foto"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
                <div class="absolute bottom-2 left-2 bg-slate-950/70 text-white text-[10px] font-bold px-2 py-0.5 rounded-md backdrop-blur-xs">
                  Foto #{{ idx + 1 }}
                </div>
              </div>

              <!-- Upload Button Box (if less than 2 files) -->
              <div
                v-if="uploadedFiles.length < 2"
                @click="triggerFileInput"
                class="border-2 border-dashed border-slate-300 hover:border-[#00B775] bg-slate-50/80 hover:bg-emerald-50/40 rounded-2xl p-6 transition-all duration-200 flex flex-col items-center justify-center space-y-2 cursor-pointer aspect-video group"
              >
                <input
                  ref="fileInputRef"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileChange"
                />
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 group-hover:border-emerald-300 flex items-center justify-center text-slate-500 group-hover:text-[#00B775] transition-colors shadow-xs">
                  <ImagePlus class="w-6 h-6" />
                </div>
                <div class="text-center">
                  <p class="text-xs font-bold text-slate-800 group-hover:text-[#00B775]">Upload Foto Bukti</p>
                  <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG hingga 5MB</p>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. Catatan Opsional -->
          <div class="space-y-2">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
              Catatan <span class="text-slate-400 font-normal lowercase">(opsional)</span>
            </label>
            <textarea
              v-model="catatan"
              rows="3"
              placeholder="Catatan tambahan (opsional, contoh: sebutkan jika ada anggota piket yang tidak hadir)..."
              class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            ></textarea>
            <p class="text-[11px] text-slate-400 font-medium">
              Gunakan catatan ini jika ada kendala atau laporan anggota piket yang tidak bertugas.
            </p>
          </div>

          <!-- Submit Button (Disaat ini belum bisa dibikin submit ke backend) -->
          <div class="pt-4 border-t border-slate-100">
            <button
              type="submit"
              class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-[#00B775]/25 hover:shadow-emerald-500/35 transition-all duration-200 flex items-center justify-center space-x-2 cursor-pointer"
            >
              <Send class="w-4 h-4" />
              <span>Kirim Bukti Piket</span>
            </button>
          </div>
        </form>
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
                Anda memiliki jadwal piket kelas hari ini.
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
            <div class="bg-white border border-slate-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-2xs">
              <div>
                <p class="text-xs font-bold text-slate-900">Rabu, 02-12-2026</p>
                <p class="text-[11px] text-slate-500 font-medium">Tugas: Menyapu & Mengepel</p>
              </div>
              <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full border bg-emerald-100 text-[#00B775] border-emerald-200">
                SETUJU
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
import api from '@/utils/api'
import { useAuthStore } from '@/stores/auth'
import SiswaNavbar from '@/components/SiswaNavbar.vue'
import {
  ChevronLeft,
  User,
  Calendar,
  ImagePlus,
  Trash2,
  Send,
  CheckCircle2,
  Sparkles,
  AlertCircle,
  X,
  Inbox,
  History
} from 'lucide-vue-next'

const authStore = useAuthStore()

const currentDayName = ref('Kamis')
const formattedDate = ref('')
const loadingTasks = ref(false)

const showInboxModal = ref(false)
const showHistoryModal = ref(false)

const availableTasks = ref([])
const selectedTaskIds = ref([1, 2])
const uploadedFiles = ref([])
const catatan = ref('')

const fileInputRef = ref(null)

const toast = ref({
  show: false,
  message: '',
  type: 'info',
})

const dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

// Dynamic Student Name
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

const updateDateInfo = () => {
  const now = new Date()
  currentDayName.value = dayNamesIndo[now.getDay()]

  const dd = String(now.getDate()).padStart(2, '0')
  const mm = String(now.getMonth() + 1).padStart(2, '0')
  const yyyy = now.getFullYear()

  formattedDate.value = `${dd}-${mm}-${yyyy}`
}

// Fetch Tasks from API / Database
const fetchTasks = async () => {
  loadingTasks.value = true
  try {
    const res = await api.get('/admin/tasks')
    if (res.data && Array.isArray(res.data)) {
      availableTasks.value = res.data
    } else if (res.data && res.data.data && Array.isArray(res.data.data)) {
      availableTasks.value = res.data.data
    } else {
      loadFallbackTasks()
    }
  } catch (err) {
    console.warn('API fetch tasks fallback:', err)
    loadFallbackTasks()
  } finally {
    loadingTasks.value = false
  }
}

const loadFallbackTasks = () => {
  availableTasks.value = [
    { id: 1, nama_tugas: 'Menyapu Lantai' },
    { id: 2, nama_tugas: 'Mengepel Lantai' },
    { id: 3, nama_tugas: 'Membersihkan Papan Tulis' },
    { id: 4, nama_tugas: 'Mengosongkan Tempat Sampah' },
    { id: 5, nama_tugas: 'Merapikan Meja Guru & Siswa' },
    { id: 6, nama_tugas: 'Membersihkan Kaca Jendela' },
  ]
}

const triggerFileInput = () => {
  if (uploadedFiles.value.length >= 2) {
    showToast('Maksimal 2 foto bukti piket.', 'info')
    return
  }
  if (fileInputRef.value) {
    fileInputRef.value.click()
  }
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (uploadedFiles.value.length >= 2) {
      showToast('Maksimal 2 foto bukti piket.', 'info')
      return
    }
    const preview = URL.createObjectURL(file)
    uploadedFiles.value.push({ file, preview })
    // Reset file input value
    event.target.value = ''
  }
}

const removeFile = (index) => {
  uploadedFiles.value.splice(index, 1)
}

const showToast = (message, type = 'info') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 4000)
}

const handleFormSubmit = () => {
  showToast('Fitur submit belum diaktifkan (Form UI Peninjauan).', 'info')
}

onMounted(() => {
  updateDateInfo()
  fetchTasks()
})
</script>
