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
        <span class="text-slate-900 font-bold">Jadwal Piket</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs">
        <div class="space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100/80 border border-emerald-200 text-[#00B775] text-xs font-semibold">
            <CalendarDays class="w-3.5 h-3.5" />
            <span>Penjadwalan</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Jadwal Piket</h1>
          <p class="text-slate-600 text-sm">Kelola jadwal piket harian siswa per hari dalam seminggu.</p>
        </div>
        <button
          @click="openAddModal"
          class="bg-[#00B775] hover:bg-[#009d64] text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-md shadow-[#00B775]/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2 self-start cursor-pointer shrink-0"
        >
          <Plus class="w-5 h-5" />
          <span>Tambah Jadwal</span>
        </button>
      </div>

      <!-- Simpler Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div class="space-y-1">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Jadwal Terdaftar</p>
            <p class="text-3xl font-extrabold text-slate-900">{{ jadwalList.length }}</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#00B775]">
            <CalendarDays class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
          <div class="space-y-1">
            <div class="flex items-center space-x-2">
              <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Piket Hari Ini</p>
              <span class="text-[10px] font-extrabold text-[#00B775] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 uppercase">
                {{ todayName }}
              </span>
            </div>
            <p class="text-3xl font-extrabold text-[#00B775]">{{ countPerHari(todayName) }} <span class="text-xs font-bold text-slate-400">Siswa</span></p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#00B775]">
            <Users class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Weekly Jadwal View + Tab Header Hari -->
      <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden">
        <!-- Tab Header Hari -->
        <div class="flex bg-slate-50 border-b border-slate-200/80 overflow-x-auto">
          <button
            @click="activeHariFilter = ''"
            :class="[
              'flex-1 min-w-[90px] px-4 py-3.5 text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap cursor-pointer',
              activeHariFilter === ''
                ? 'border-[#00B775] text-[#00B775] bg-white'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-white/60'
            ]"
          >Semua</button>
          <button
            v-for="hari in hariList"
            :key="hari"
            @click="activeHariFilter = hari"
            :class="[
              'flex-1 min-w-[90px] px-4 py-3.5 text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap cursor-pointer flex items-center justify-center space-x-1.5',
              activeHariFilter === hari
                ? 'border-[#00B775] text-[#00B775] bg-white'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-white/60'
            ]"
          >
            <span>{{ hari }}</span>
            <span v-if="countPerHari(hari) > 0" :class="['text-[10px] font-extrabold px-1.5 py-0.5 rounded-full', activeHariFilter === hari ? 'bg-emerald-100 text-[#00B775]' : 'bg-slate-200 text-slate-600']">
              {{ countPerHari(hari) }}
            </span>
          </button>
        </div>
        <!-- Search & Controls Bar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="relative w-full sm:w-72">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama siswa..."
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all"
            />
          </div>
          <div class="text-xs font-semibold text-slate-500 shrink-0">
            Menampilkan <span class="text-[#00B775] font-bold">{{ filteredJadwal.length }}</span> jadwal
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="p-12 text-center flex items-center justify-center space-x-2 text-slate-500 font-medium">
          <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memuat jadwal piket...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredJadwal.length === 0" class="p-14 text-center">
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
            <CalendarDays class="w-7 h-7 text-[#00B775]" />
          </div>
          <p class="text-slate-600 font-bold text-sm">Belum ada jadwal piket.</p>
          <p class="text-slate-400 text-xs mt-1">Klik tombol "Tambah Jadwal" untuk mulai mengisi.</p>
        </div>

        <!-- Grid View per Hari (saat "Semua" dipilih) -->
        <div v-else-if="activeHariFilter === ''" class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
          <div v-for="hari in hariList" :key="hari" class="space-y-2">
            <!-- Hari Header -->
            <div :class="['flex items-center justify-between px-3 py-2 rounded-xl border font-bold text-xs', todayName === hari ? 'bg-[#00B775] text-white border-[#00B775]' : 'bg-emerald-50 text-emerald-800 border-emerald-200']">
              <span>{{ hari }}</span>
              <span v-if="todayName === hari" class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded-md">Hari Ini</span>
            </div>
            <!-- Siswa per hari -->
            <div
              v-for="item in jadwalPerHari(hari)"
              :key="item.id"
              class="group bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center justify-between hover:border-emerald-300 hover:bg-emerald-50/30 transition-all"
            >
              <div class="flex items-center space-x-2 min-w-0">
                <div :class="['w-7 h-7 rounded-lg flex items-center justify-center font-extrabold text-[11px] text-white shrink-0', avatarColor(getSiswaName(item))]">
                  {{ getInitials(getSiswaName(item)) }}
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-800 truncate">{{ getSiswaName(item) }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                <button @click="openEditModal(item)" class="p-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg border border-amber-200 transition cursor-pointer">
                  <Pencil class="w-3 h-3" />
                </button>
                <button @click="openDeleteModal(item)" class="p-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg border border-rose-200 transition cursor-pointer">
                  <Trash2 class="w-3 h-3" />
                </button>
              </div>
            </div>
            <!-- Empty hari placeholder -->
            <div v-if="jadwalPerHari(hari).length === 0" class="text-center py-3 text-[10px] text-slate-400 border border-dashed border-slate-200 rounded-xl">
              Belum ada siswa
            </div>
          </div>
        </div>

        <!-- List View (saat filter hari tertentu aktif) -->
        <div v-else>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead class="bg-emerald-50/80 border-b border-emerald-100 text-slate-700 font-bold uppercase text-xs">
                <tr>
                  <th class="px-5 py-4 w-12">No</th>
                  <th class="px-5 py-4">Siswa</th>
                  <th class="px-5 py-4 w-28 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="(item, index) in paginatedJadwal"
                  :key="item.id"
                  class="hover:bg-emerald-50/20 transition-colors group"
                >
                  <td class="px-5 py-4 font-semibold text-slate-400 text-xs">
                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center space-x-3">
                      <div :class="['w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-sm text-white shrink-0', avatarColor(getSiswaName(item))]">
                        {{ getInitials(getSiswaName(item)) }}
                      </div>
                      <div>
                        <p class="font-bold text-slate-900 text-sm">{{ getSiswaName(item) }}</p>
                        <p class="text-xs text-slate-400">{{ item.siswa?.email ?? item.email ?? '' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                      <button @click="openEditModal(item)" title="Edit" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer">
                        <Pencil class="w-4 h-4" />
                      </button>
                      <button @click="openDeleteModal(item)" title="Hapus" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer">
                        <Trash2 class="w-4 h-4" />
                      </button>
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
              dari <span class="text-[#00B775] font-extrabold">{{ filteredJadwal.length }}</span> Jadwal
            </div>
            <div class="flex items-center space-x-1.5">
              <button v-if="currentPage > 1" @click="currentPage--" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                <ChevronLeft class="w-4 h-4" />
              </button>
              <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :class="['w-9 h-9 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center', currentPage === page ? 'bg-[#00B775] border-[#00B775] text-white shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-[#00B775]']"
              >{{ page }}</button>
              <button v-if="currentPage < totalPages" @click="currentPage++" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ===== MODAL: Tambah / Edit Jadwal ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 my-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">
                {{ isEditing ? 'Edit Jadwal Piket' : 'Tambah Jadwal Piket' }}
              </h3>
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                {{ isEditing ? 'Ubah data jadwal siswa ini.' : 'Tetapkan jadwal piket untuk seorang siswa.' }}
              </p>
            </div>
            <button @click="closeFormModal" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Pilih Siswa (Mode Multi-select untuk Tambah, Single untuk Edit) -->
            <div v-if="!isEditing" class="space-y-2">
              <div class="flex items-center justify-between">
                <label class="block text-slate-800 font-bold text-sm">
                  Pilih Siswa <span class="text-rose-500">*</span>
                  <span v-if="form.user_ids.length > 0" class="ml-2 text-xs text-[#00B775] font-extrabold">
                    ({{ form.user_ids.length }} dipilih)
                  </span>
                </label>
                <button
                  type="button"
                  @click="toggleSelectAllSiswa"
                  class="text-xs font-bold text-[#00B775] hover:underline cursor-pointer"
                >
                  {{ isAllSiswaSelected ? 'Batal Semua' : 'Pilih Semua' }}
                </button>
              </div>

              <!-- Search Siswa di Modal -->
              <div class="relative">
                <Search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input
                  v-model="modalSiswaSearch"
                  type="text"
                  placeholder="Cari nama siswa..."
                  class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all"
                />
              </div>

              <!-- List Checkbox Siswa -->
              <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-2xl p-2 space-y-1 bg-slate-50/50">
                <label
                  v-for="s in filteredModalSiswa"
                  :key="s.id"
                  :class="[
                    'flex items-center space-x-2.5 p-2 rounded-xl border transition-all cursor-pointer text-xs font-semibold',
                    form.user_ids.includes(s.id)
                      ? 'bg-emerald-50 border-[#00B775] text-[#00B775] font-bold'
                      : 'bg-white border-slate-200 text-slate-700 hover:border-emerald-300'
                  ]"
                >
                  <input
                    type="checkbox"
                    :value="s.id"
                    v-model="form.user_ids"
                    class="w-4 h-4 rounded text-[#00B775] focus:ring-[#00B775] accent-[#00B775]"
                  />
                  <span class="truncate">{{ s.name || s.nama }} {{ s.no_absen ? `(No. ${s.no_absen})` : '' }}</span>
                </label>
                <div v-if="filteredModalSiswa.length === 0" class="text-center py-4 text-slate-400 text-xs font-medium">
                  Siswa tidak ditemukan.
                </div>
              </div>
            </div>

            <!-- Single Select (Mode Edit) -->
            <div v-else class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">
                Siswa <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.user_id"
                required
                disabled
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 cursor-not-allowed"
              >
                <option v-for="s in daftarSiswa" :key="s.id" :value="s.id">
                  {{ s.name || s.nama }} {{ s.no_absen ? `(No. ${s.no_absen})` : '' }}
                </option>
              </select>
              <p class="text-[10px] text-slate-400">* Siswa tidak bisa diubah saat Mode Edit.</p>
            </div>

            <!-- Hari Piket -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">
                Hari Piket <span class="text-rose-500">*</span>
              </label>
              <div class="grid grid-cols-5 gap-2">
                <button
                  v-for="hari in hariList"
                  :key="hari"
                  type="button"
                  @click="form.hari_piket = hari"
                  :class="[
                    'py-2 rounded-xl border-2 text-xs font-bold transition-all cursor-pointer',
                    form.hari_piket === hari
                      ? 'border-[#00B775] bg-emerald-50 text-[#00B775]'
                      : 'border-slate-200 text-slate-500 hover:border-emerald-300 hover:bg-emerald-50/40'
                  ]"
                >{{ hari.slice(0, 3) }}</button>
              </div>
              <p v-if="!form.hari_piket" class="text-[10px] text-rose-400 font-medium">Pilih hari piket</p>
            </div>

            <!-- Catatan (Opsional) -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Catatan <span class="text-slate-400 font-normal text-xs">(opsional)</span></label>
              <textarea
                v-model="form.catatan"
                rows="2"
                placeholder="Catatan tambahan untuk jadwal ini..."
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs resize-none"
              ></textarea>
            </div>

            <!-- Form Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
              <button type="button" @click="closeFormModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting || !form.hari_piket || (!isEditing && form.user_ids.length === 0) || (isEditing && !form.user_id)"
                class="bg-[#00B775] hover:bg-[#009d64] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition disabled:opacity-60 cursor-pointer flex items-center space-x-2"
              >
                <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submitting ? 'Menyimpan...' : (isEditing ? 'Update Jadwal' : `Simpan ${form.user_ids.length > 1 ? `(${form.user_ids.length} Siswa)` : 'Jadwal'}`) }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Konfirmasi Hapus ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showDeleteModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
              <AlertTriangle class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Hapus Jadwal Piket?</h3>
              <p class="text-xs text-slate-500 font-medium">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
          <p class="text-sm text-slate-600 leading-relaxed font-medium">
            Hapus jadwal piket hari <span class="font-bold text-slate-900">{{ itemToDelete?.hari_piket ?? itemToDelete?.hari }}</span>
            untuk siswa <span class="font-bold text-slate-900">{{ getSiswaName(itemToDelete) }}</span>?
          </p>
          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button @click="closeDeleteModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer">Batal</button>
            <button
              @click="confirmDelete"
              :disabled="deleting"
              class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
            >
              <svg v-if="deleting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ deleting ? 'Menghapus...' : 'Ya, Hapus' }}</span>
            </button>
          </div>
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
  Plus, Search, Pencil, Trash2, X,
  Home, ChevronRight, ChevronLeft,
  CheckCircle2, AlertCircle, AlertTriangle,
  CalendarDays, Users
} from 'lucide-vue-next'

// ========================
// CONSTANTS
// ========================
const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']
const dayMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const todayName = dayMap[new Date().getDay()]
const itemsPerPage = 10

// ========================
// STATE
// ========================
const jadwalList = ref([])
const daftarSiswa = ref([])
const loading = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const searchQuery = ref('')
const activeHariFilter = ref('')
const currentPage = ref(1)

const showFormModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const itemToDelete = ref(null)

const modalSiswaSearch = ref('')
const form = ref({ user_id: '', user_ids: [], hari_piket: '', catatan: '' })
const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

// ========================
// HELPERS & MULTI-SELECT COMPUTED
// ========================
const filteredModalSiswa = computed(() => {
  if (!modalSiswaSearch.value.trim()) return daftarSiswa.value
  const q = modalSiswaSearch.value.toLowerCase()
  return daftarSiswa.value.filter(s => (s.name || s.nama || '').toLowerCase().includes(q))
})

const isAllSiswaSelected = computed(() => {
  if (daftarSiswa.value.length === 0) return false
  return form.value.user_ids.length === daftarSiswa.value.length
})

const toggleSelectAllSiswa = () => {
  if (isAllSiswaSelected.value) {
    form.value.user_ids = []
  } else {
    form.value.user_ids = daftarSiswa.value.map(s => s.id)
  }
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value.show = false }, 4000)
}

const extractErrorMessage = (err) => {
  if (err.response) {
    const data = err.response.data
    if (data?.message) return data.message
    if (data?.errors) {
      const key = Object.keys(data.errors)[0]
      return data.errors[key]?.[0] || 'Validasi gagal.'
    }
    if (err.response.status === 422) return 'Data tidak valid atau jadwal sudah ada untuk siswa ini.'
    if (err.response.status === 409) return 'Siswa ini sudah memiliki jadwal di hari yang sama.'
  }
  return 'Gagal terhubung ke server.'
}

const avatarColors = ['bg-indigo-500', 'bg-[#00B775]', 'bg-sky-500', 'bg-violet-500', 'bg-amber-500', 'bg-teal-500', 'bg-rose-500', 'bg-cyan-500']
const avatarColor = (name = '') => avatarColors[(name.charCodeAt(0) || 0) % avatarColors.length]
const getInitials = (name = '') => name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() ?? '').join('')

// Ambil nama siswa dari berbagai kemungkinan struktur response API
const getSiswaName = (item) => {
  if (!item) return '-'
  return item.siswa?.name ?? item.siswa?.nama ?? item.user?.name ?? item.nama_siswa ?? item.name ?? '-'
}

// ========================
// COMPUTED
// ========================
const filteredJadwal = computed(() => {
  let list = jadwalList.value
  if (activeHariFilter.value) {
    list = list.filter(j => (j.hari_piket ?? j.hari) === activeHariFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(j => getSiswaName(j).toLowerCase().includes(q))
  }
  return list
})

const totalPages = computed(() => Math.ceil(filteredJadwal.value.length / itemsPerPage) || 1)
const paginatedJadwal = computed(() => {
  const s = (currentPage.value - 1) * itemsPerPage
  return filteredJadwal.value.slice(s, s + itemsPerPage)
})
const startIdx = computed(() => filteredJadwal.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage + 1)
const endIdx = computed(() => Math.min(currentPage.value * itemsPerPage, filteredJadwal.value.length))

const countPerHari = (hari) => jadwalList.value.filter(j => (j.hari_piket ?? j.hari) === hari).length
const jadwalPerHari = (hari) => {
  let list = jadwalList.value.filter(j => (j.hari_piket ?? j.hari) === hari)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(j => getSiswaName(j).toLowerCase().includes(q))
  }
  return list
}

watch([searchQuery, activeHariFilter], () => { currentPage.value = 1 })

// ========================
// API CALLS
// ========================
const fetchJadwal = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/jadwal-piket')
    jadwalList.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
    jadwalList.value = []
  } finally {
    loading.value = false
  }
}

const fetchDaftarSiswa = async () => {
  try {
    const res = await api.get('/admin/siswa')
    daftarSiswa.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    console.warn('Gagal memuat daftar siswa:', err)
    daftarSiswa.value = []
  }
}

// ========================
// MODAL HANDLERS
// ========================
const openAddModal = () => {
  isEditing.value = false
  editingId.value = null
  modalSiswaSearch.value = ''
  form.value = { user_id: '', user_ids: [], hari_piket: activeHariFilter.value || '', catatan: '' }
  showFormModal.value = true
}

const openEditModal = (item) => {
  isEditing.value = true
  editingId.value = item.id
  modalSiswaSearch.value = ''
  const uId = item.user_id ?? item.siswa?.id ?? item.user?.id ?? ''
  form.value = {
    user_id: uId,
    user_ids: uId ? [uId] : [],
    hari_piket: item.hari_piket ?? item.hari ?? '',
    catatan: item.catatan ?? '',
  }
  showFormModal.value = true
}

const closeFormModal = () => { showFormModal.value = false }

const openDeleteModal = (item) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  itemToDelete.value = null
}

// ========================
// SUBMIT (POST / PUT)
// ========================
const submitForm = async () => {
  if (!form.value.hari_piket) return
  submitting.value = true

  try {
    if (isEditing.value) {
      const payload = {
        user_id: form.value.user_id,
        hari: form.value.hari_piket,
        hari_piket: form.value.hari_piket,
        ...(form.value.catatan ? { catatan: form.value.catatan } : {}),
      }
      await api.put(`/admin/jadwal-piket/${editingId.value}`, payload)
      showToast('Jadwal piket berhasil diperbarui!', 'success')
    } else {
      const targetUserIds = form.value.user_ids.length > 0
        ? form.value.user_ids
        : (form.value.user_id ? [form.value.user_id] : [])

      if (targetUserIds.length === 0) {
        showToast('Pilih setidaknya satu siswa.', 'error')
        submitting.value = false
        return
      }

      let successCount = 0
      let failMessages = []

      for (const uid of targetUserIds) {
        try {
          const payload = {
            user_id: uid,
            hari: form.value.hari_piket,
            hari_piket: form.value.hari_piket,
            ...(form.value.catatan ? { catatan: form.value.catatan } : {}),
          }
          await api.post('/admin/jadwal-piket', payload)
          successCount++
        } catch (err) {
          const sObj = daftarSiswa.value.find(s => Number(s.id) === Number(uid))
          const sName = sObj ? (sObj.name || sObj.nama) : `Siswa ID ${uid}`
          failMessages.push(`${sName}: ${extractErrorMessage(err)}`)
        }
      }

      if (successCount > 0 && failMessages.length === 0) {
        showToast(`${successCount} jadwal piket berhasil ditambahkan!`, 'success')
      } else if (successCount > 0 && failMessages.length > 0) {
        showToast(`${successCount} jadwal berhasil ditambahkan. (${failMessages.join(' | ')})`, 'success')
      } else {
        showToast(failMessages.join(' | ') || 'Gagal menambahkan jadwal piket.', 'error')
      }
    }
    closeFormModal()
    await fetchJadwal()
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
  } finally {
    submitting.value = false
  }
}

// ========================
// DELETE
// ========================
const confirmDelete = async () => {
  if (!itemToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/admin/jadwal-piket/${itemToDelete.value.id}`)
    closeDeleteModal()
    showToast('Jadwal piket berhasil dihapus!', 'success')
    await fetchJadwal()
  } catch (err) {
    closeDeleteModal()
    showToast(extractErrorMessage(err), 'error')
  } finally {
    deleting.value = false
  }
}

// ========================
// INIT
// ========================
onMounted(async () => {
  await Promise.all([fetchJadwal(), fetchDaftarSiswa()])
  // Auto-filter ke hari ini jika ada data
  if (todayName !== 'Minggu' && todayName !== 'Sabtu') {
    // Tidak auto-filter, biar user lihat semua dulu
  }
})
</script>
