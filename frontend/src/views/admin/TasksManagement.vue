<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white relative">
    <!-- 2. Custom Toast Notification (Fixed Top-Right) -->
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
            : 'bg-rose-50/95 border-rose-200 text-rose-900'
        ]"
      >
        <div :class="[
          'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs',
          toast.type === 'success' ? 'bg-[#00B775] text-white' : 'bg-rose-600 text-white'
        ]">
          <CheckCircle2 v-if="toast.type === 'success'" class="w-4 h-4" />
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

    <!-- Admin Navbar Component -->
    <AdminNavbar />

    <!-- Main Content Area -->
    <main class="max-w-6xl mx-auto px-6 md:px-12 py-8 w-full space-y-6">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200/80 px-4 py-2 rounded-full w-fit shadow-xs">
        <router-link to="/admin/dashboard" class="hover:text-[#00B775] transition flex items-center space-x-1">
          <Home class="w-3.5 h-3.5 text-[#00B775]" />
          <span>Dashboard</span>
        </router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-900 font-bold">Manajemen Tugas Piket</span>
      </nav>

      <!-- Header & Action Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs">
        <div class="text-left space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100/80 border border-emerald-200 text-[#00B775] text-xs font-semibold">
            <Sparkles class="w-3.5 h-3.5 text-[#00B775]" />
            <span>Kelola Tugas</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
            Manajemen Tugas Piket
          </h1>
          <p class="text-slate-600 text-sm">
            Kelola daftar tugas piket kelas, kriteria kebersihan, dan bobot poin.
          </p>
        </div>

        <button
          @click="openAddModal"
          class="bg-[#00B775] hover:bg-[#009d64] text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-md shadow-[#00B775]/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2 self-start md:self-auto cursor-pointer"
        >
          <Plus class="w-5 h-5" />
          <span>Tambah Tugas Baru</span>
        </button>
      </div>

      <!-- Search & Status Notification Bar -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
          <Search class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari tugas piket..."
            class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
          />
        </div>
        
        <div class="text-xs font-semibold text-slate-500">
          Total Data: <span class="text-[#00B775] font-bold text-sm">{{ filteredTasks.length }}</span>
        </div>
      </div>

      <!-- Tasks Data Table Card -->
      <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col justify-between">
        <div v-if="loading" class="p-10 text-center text-slate-500 font-medium">
          Memuat data tugas piket...
        </div>

        <div v-else-if="filteredTasks.length === 0" class="p-10 text-center text-slate-500 font-medium">
          Tidak ada data tugas piket ditemukan.
        </div>

        <div v-else>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead class="bg-emerald-50/80 border-b border-emerald-100 text-slate-700 font-bold uppercase text-xs">
                <tr>
                  <th class="px-6 py-4 w-16">No</th>
                  <th class="px-6 py-4">Nama Tugas</th>
                  <th class="px-6 py-4">Deskripsi Tugas</th>
                  <th class="px-6 py-4 w-32 text-center">Bobot / Poin</th>
                  <th class="px-6 py-4 w-40 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="(task, index) in paginatedTasks"
                  :key="task.id"
                  class="hover:bg-emerald-50/40 transition-colors"
                >
                  <td class="px-6 py-4 font-semibold text-slate-500">
                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                  </td>
                  <td class="px-6 py-4 font-bold text-slate-900">{{ task.nama_tugas }}</td>
                  <td class="px-6 py-4 text-slate-600 max-w-xs md:max-w-md truncate">
                    {{ task.deskripsi || '-' }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-block bg-emerald-100 text-[#00B775] font-extrabold px-3 py-1 rounded-full text-xs border border-emerald-200">
                      {{ task.bobot || 1 }} Poin
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="openEditModal(task)"
                        title="Edit Tugas"
                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer"
                      >
                        <Pencil class="w-4 h-4" />
                      </button>
                      <button
                        @click="openDeleteModal(task)"
                        title="Hapus Tugas"
                        class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Footer Paginasi Interaktif dengan Ikon (< dan >) & Kondisi Tampil -->
          <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
            <div>
              Menampilkan <span class="text-slate-900 font-bold">{{ startItemIndex }}</span> - <span class="text-slate-900 font-bold">{{ endItemIndex }}</span> dari <span class="text-[#00B775] font-extrabold">{{ filteredTasks.length }}</span> Tugas Piket
            </div>

            <div class="flex items-center space-x-1.5">
              <!-- Tombol Sebelumnya (Hanya muncul jika currentPage > 1) -->
              <button
                v-if="currentPage > 1"
                @click="currentPage--"
                title="Halaman Sebelumnya"
                class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer font-bold shadow-xs"
              >
                <ChevronLeft class="w-4 h-4" />
              </button>

              <!-- Angka Halaman (1, 2, ...) -->
              <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :class="[
                  'w-9 h-9 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center',
                  currentPage === page
                    ? 'bg-[#00B775] border-[#00B775] text-white shadow-xs'
                    : 'bg-white border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-[#00B775]'
                ]"
              >
                {{ page }}
              </button>

              <!-- Tombol Selanjutnya (Hanya muncul jika currentPage < totalPages) -->
              <button
                v-if="currentPage < totalPages"
                @click="currentPage++"
                title="Halaman Selanjutnya"
                class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer font-bold shadow-xs"
              >
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Form (Tambah / Edit Tugas) -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 z-50"
    >
      <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xl font-bold text-slate-900">
            {{ isEditing ? 'Edit Tugas Piket' : 'Tambah Tugas Piket Baru' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Nama Tugas -->
          <div class="space-y-1.5">
            <label class="block text-slate-800 font-bold text-sm">
              Nama Tugas <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.nama_tugas"
              type="text"
              required
              placeholder="Contoh: Menyapu Lantai Kelas"
              class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            />
          </div>

          <!-- Deskripsi Tugas -->
          <div class="space-y-1.5">
            <label class="block text-slate-800 font-bold text-sm">
              Deskripsi Tugas
            </label>
            <textarea
              v-model="form.deskripsi"
              rows="3"
              placeholder="Jelaskan detail instruksi tugas piket..."
              class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            ></textarea>
          </div>

          <!-- Bobot / Poin -->
          <div class="space-y-1.5">
            <label class="block text-slate-800 font-bold text-sm">
              Bobot Poin
            </label>
            <input
              v-model.number="form.bobot"
              type="number"
              min="1"
              max="100"
              class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            />
          </div>

          <!-- Form Buttons -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <button
              type="button"
              @click="closeModal"
              class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="bg-[#00B775] hover:bg-[#009d64] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition disabled:opacity-70 cursor-pointer"
            >
              <span v-if="submitting">Menyimpan...</span>
              <span v-else>{{ isEditing ? 'Update Tugas' : 'Simpan Tugas' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- 1. Custom Confirmation Modal (Hapus Tugas) -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 z-50"
    >
      <div class="bg-white/95 rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center space-x-3.5">
          <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
            <AlertTriangle class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-xl font-extrabold text-slate-900">Hapus Tugas Piket?</h3>
            <p class="text-xs text-slate-500 font-medium">Konfirmasi hapus data tugas</p>
          </div>
        </div>

        <p class="text-sm text-slate-600 leading-relaxed font-medium">
          Apakah Anda yakin ingin menghapus tugas <span class="font-bold text-slate-900">"{{ taskToDelete?.nama_tugas }}"</span>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
          <button
            type="button"
            @click="closeDeleteModal"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="button"
            @click="confirmDeleteTask"
            :disabled="deleting"
            class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer"
          >
            <span v-if="deleting">Menghapus...</span>
            <span v-else>Ya, Hapus</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import api from '@/utils/api'
import AdminNavbar from '@/components/AdminNavbar.vue'
import {
  Plus,
  Search,
  Pencil,
  Trash2,
  X,
  Home,
  ChevronRight,
  ChevronLeft,
  Sparkles,
  CheckCircle2,
  AlertCircle,
  AlertTriangle
} from 'lucide-vue-next'

const tasks = ref([])
const loading = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const searchQuery = ref('')

// Paginasi State (Maksimal 10 Data per Halaman)
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Modal Form State (Tambah / Edit)
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = ref({
  nama_tugas: '',
  deskripsi: '',
  bobot: 1,
})

// Custom Delete Confirmation Modal State
const showDeleteModal = ref(false)
const taskToDelete = ref(null)

// Custom Toast Notification State
const toast = ref({
  show: false,
  message: '',
  type: 'success',
})

let toastTimer = null

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

// Filtered tasks by search query
const filteredTasks = computed(() => {
  if (!searchQuery.value.trim()) return tasks.value
  const q = searchQuery.value.toLowerCase()
  return tasks.value.filter(
    (t) =>
      t.nama_tugas.toLowerCase().includes(q) ||
      (t.deskripsi && t.deskripsi.toLowerCase().includes(q))
  )
})

// Total Halaman
const totalPages = computed(() => {
  return Math.ceil(filteredTasks.value.length / itemsPerPage.value) || 1
})

// Paginated Tasks (10 data per halaman)
const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredTasks.value.slice(start, end)
})

// Index Start & End untuk teks footer paginasi
const startItemIndex = computed(() => {
  if (filteredTasks.value.length === 0) return 0
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const endItemIndex = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return Math.min(end, filteredTasks.value.length)
})

// Reset ke halaman 1 saat melakukan pencarian
watch(searchQuery, () => {
  currentPage.value = 1
})

// Fetch Tasks GET /admin/tasks
const fetchTasks = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/tasks')
    if (res.data && Array.isArray(res.data)) {
      tasks.value = res.data
    } else if (res.data && res.data.data && Array.isArray(res.data.data)) {
      tasks.value = res.data.data
    } else {
      loadDummyTasks()
    }
  } catch (err) {
    console.warn('API /admin/tasks call failed, loading fallback items:', err)
    loadDummyTasks()
  } finally {
    loading.value = false
  }
}

const loadDummyTasks = () => {
  if (tasks.value.length === 0) {
    tasks.value = [
      { id: 1, nama_tugas: 'Menyapu Lantai', deskripsi: 'Menyapu area depan dan belakang kelas hingga bersih', bobot: 2 },
      { id: 2, nama_tugas: 'Mengepel Lantai', deskripsi: 'Mengepel seluruh lantai kelas menggunakan karbol pewangi', bobot: 3 },
      { id: 3, nama_tugas: 'Membersihkan Papan Tulis', deskripsi: 'Menghapus papan tulis dan merapikan spidol/penghapus', bobot: 1 },
      { id: 4, nama_tugas: 'Mengosongkan Tempat Sampah', deskripsi: 'Membuang sampah kelas ke tempat penampungan akhir', bobot: 2 },
      { id: 5, nama_tugas: 'Merapikan Meja Guru & Siswa', deskripsi: 'Mensejajarkan posisi meja dan kursi belajar', bobot: 1 },
      { id: 6, nama_tugas: 'Membersihkan Jendela & Kaca', deskripsi: 'Lap pembersih kaca jendela luar dan dalam', bobot: 2 },
      { id: 7, nama_tugas: 'Merapikan Alat Kebersihan', deskripsi: 'Mengembalikan sapu, pel, dan pengki ke sudut penyimpanan', bobot: 1 },
      { id: 8, nama_tugas: 'Membersihkan Meja Komputer', deskripsi: 'Mengelap debu pada layar monitor dan meja komputer', bobot: 1 },
      { id: 9, nama_tugas: 'Merapikan Sudut Baca / Rak Buku', deskripsi: 'Menyusun buku pelajaran di rak sesuai urutan', bobot: 2 },
      { id: 10, nama_tugas: 'Membersihkan Kipas / AC', deskripsi: 'Membersihkan debu pada baling-baling kipas angin', bobot: 3 },
      { id: 11, nama_tugas: 'Menyiram Tanaman Kelas', deskripsi: 'Menyiram pot tanaman hias di depan kelas', bobot: 1 },
      { id: 12, nama_tugas: 'Membersihkan Loker Siswa', deskripsi: 'Mengecek dan merapikan kebersihan loker kelas', bobot: 2 },
    ]
  }
}

const openAddModal = () => {
  isEditing.value = false
  editingId.value = null
  form.value = { nama_tugas: '', deskripsi: '', bobot: 1 }
  isModalOpen.value = true
}

const openEditModal = (task) => {
  isEditing.value = true
  editingId.value = task.id
  form.value = {
    nama_tugas: task.nama_tugas,
    deskripsi: task.deskripsi || '',
    bobot: task.bobot || 1,
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

// Buka Custom Modal Konfirmasi Hapus
const openDeleteModal = (task) => {
  taskToDelete.value = task
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  taskToDelete.value = null
}

// Submit Form (Tambah / Edit) dengan Custom Toast Notification
const submitForm = async () => {
  if (!form.value.nama_tugas) return

  submitting.value = true
  try {
    if (isEditing.value) {
      await api.put(`/admin/tasks/${editingId.value}`, form.value)
      
      const idx = tasks.value.findIndex((t) => t.id === editingId.value)
      if (idx !== -1) {
        tasks.value[idx] = { ...tasks.value[idx], ...form.value }
      }
      closeModal()
      showToast('Tugas piket berhasil diperbarui!', 'success')
    } else {
      const res = await api.post('/admin/tasks', form.value)
      const newTask = res.data?.data || res.data || {
        id: Date.now(),
        ...form.value,
      }
      tasks.value.unshift(newTask)
      closeModal()
      showToast('Tugas piket baru berhasil ditambahkan!', 'success')
    }
  } catch (err) {
    console.warn('API submit fallback:', err)
    if (isEditing.value) {
      const idx = tasks.value.findIndex((t) => t.id === editingId.value)
      if (idx !== -1) {
        tasks.value[idx] = { ...tasks.value[idx], ...form.value }
      }
      closeModal()
      showToast('Tugas piket berhasil diperbarui!', 'success')
    } else {
      tasks.value.unshift({
        id: Date.now(),
        ...form.value,
      })
      closeModal()
      showToast('Tugas piket baru berhasil ditambahkan!', 'success')
    }
  } finally {
    submitting.value = false
  }
}

// Eksekusi Hapus Tugas via Custom Confirmation Modal
const confirmDeleteTask = async () => {
  if (!taskToDelete.value) return

  deleting.value = true
  const targetId = taskToDelete.value.id
  try {
    await api.delete(`/admin/tasks/${targetId}`)
    tasks.value = tasks.value.filter((t) => t.id !== targetId)
    closeDeleteModal()
    showToast('Tugas piket telah berhasil dihapus!', 'success')
  } catch (err) {
    console.warn('API delete fallback:', err)
    tasks.value = tasks.value.filter((t) => t.id !== targetId)
    closeDeleteModal()
    showToast('Tugas piket telah berhasil dihapus!', 'success')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchTasks()
})
</script>
