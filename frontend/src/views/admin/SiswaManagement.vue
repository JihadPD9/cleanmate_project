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
        <div :class="['w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs', toast.type === 'success' ? 'bg-[#00B775] text-white' : 'bg-rose-600 text-white']">
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
        <span class="text-slate-900 font-bold">Data Siswa</span>
      </nav>

      <!-- Header & Action Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs">
        <div class="space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-sky-100/80 border border-sky-200 text-sky-700 text-xs font-semibold">
            <GraduationCap class="w-3.5 h-3.5" />
            <span>Manajemen Siswa</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Data Siswa</h1>
          <p class="text-slate-600 text-sm">Kelola data akun siswa, informasi profil, dan sanksi manual.</p>
        </div>
        <button
          @click="openAddModal"
          class="bg-[#00B775] hover:bg-[#009d64] text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-md shadow-[#00B775]/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2 self-start cursor-pointer shrink-0"
        >
          <Plus class="w-5 h-5" />
          <span>Tambah Siswa</span>
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
          <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ siswaList.length }}</p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
          <p class="text-xs font-semibold text-rose-600 uppercase tracking-wide">Sanksi Aktif</p>
          <p class="text-3xl font-extrabold text-rose-600 mt-1">{{ sanksiAktifCount }}</p>
        </div>
      </div>

      <!-- Search, Filter & Total Bar -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
          <Search class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama, email, atau nomor absen..."
            class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
          />
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto">
          <!-- Filter Hari Piket -->
          <select
            v-model="hariFilter"
            class="flex-1 md:flex-none px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#00B775] cursor-pointer shadow-xs"
          >
            <option value="">Semua Hari Piket</option>
            <option v-for="h in hariList" :key="h" :value="h">{{ h }}</option>
          </select>
          <div class="text-xs font-semibold text-slate-500 shrink-0">
            Total: <span class="text-[#00B775] font-bold text-sm">{{ filteredSiswa.length }}</span>
          </div>
        </div>
      </div>

      <!-- Siswa Data Table -->
      <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col">

        <!-- Loading State -->
        <div v-if="loading" class="p-10 text-center text-slate-500 font-medium flex items-center justify-center space-x-2">
          <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memuat data siswa...</span>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredSiswa.length === 0" class="p-14 text-center">
          <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <GraduationCap class="w-8 h-8 text-slate-400" />
          </div>
          <p class="text-slate-500 font-semibold text-sm">Tidak ada data siswa ditemukan.</p>
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
                  <th class="px-5 py-4">No. Absen</th>
                  <th class="px-5 py-4">Hari Piket</th>
                  <th class="px-5 py-4 text-center">Sanksi Aktif</th>
                  <th class="px-5 py-4 text-center">Status</th>
                  <th class="px-5 py-4 w-36 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="(siswa, index) in paginatedSiswa"
                  :key="siswa.id"
                  class="hover:bg-sky-50/20 transition-colors group"
                >
                  <td class="px-5 py-4 font-semibold text-slate-400 text-xs">
                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                  </td>
                  <td class="px-5 py-4">
                    <div class="flex items-center space-x-3">
                      <!-- Avatar Inisial -->
                      <div :class="['w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-sm text-white shrink-0', avatarColor(siswa.name || siswa.nama)]">
                        {{ getInitials(siswa.name || siswa.nama) }}
                      </div>
                      <div>
                        <p class="font-bold text-slate-900 text-sm">{{ siswa.name || siswa.nama }}</p>
                        <p class="text-xs text-slate-400 font-medium">{{ siswa.email }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4 font-semibold text-slate-700">
                    {{ siswa.no_absen || '-' }}
                  </td>
                  <td class="px-5 py-4">
                    <span v-if="siswa.hari_piket" class="inline-block text-[10px] font-extrabold px-2.5 py-1 rounded-full border bg-emerald-100 text-emerald-700 border-emerald-200 uppercase tracking-wide">
                      {{ siswa.hari_piket }}
                    </span>
                    <span v-else class="text-slate-400 text-xs">-</span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <span v-if="(siswa.sanksi_aktif_count ?? siswa.active_sanctions_count ?? 0) > 0"
                      class="inline-block text-[10px] font-extrabold px-2.5 py-1.5 rounded-full border bg-rose-100 text-rose-700 border-rose-200">
                      {{ siswa.sanksi_aktif_count ?? siswa.active_sanctions_count }} Sanksi
                    </span>
                    <span v-else class="inline-block text-[10px] font-semibold px-2.5 py-1.5 rounded-full border bg-slate-100 text-slate-500 border-slate-200">
                      Bersih
                    </span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <span :class="[
                      'inline-block text-[10px] font-extrabold px-2.5 py-1.5 rounded-full border uppercase tracking-wide',
                      (siswa.is_active ?? true)
                        ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                        : 'bg-slate-100 text-slate-500 border-slate-200'
                    ]">
                      {{ (siswa.is_active ?? true) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                      <!-- Detail -->
                      <button
                        @click="openDetailModal(siswa)"
                        title="Lihat Detail"
                        class="p-2 bg-sky-50 text-sky-600 hover:bg-sky-100 rounded-xl border border-sky-200 transition cursor-pointer"
                      >
                        <Eye class="w-4 h-4" />
                      </button>
                      <!-- Edit -->
                      <button
                        @click="openEditModal(siswa)"
                        title="Edit Siswa"
                        class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer"
                      >
                        <Pencil class="w-4 h-4" />
                      </button>
                      <!-- Hapus -->
                      <button
                        @click="openDeleteModal(siswa)"
                        title="Hapus Siswa"
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

          <!-- Pagination Footer -->
          <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
            <div>
              Menampilkan <span class="text-slate-900 font-bold">{{ startIdx }}</span> - <span class="text-slate-900 font-bold">{{ endIdx }}</span>
              dari <span class="text-[#00B775] font-extrabold">{{ filteredSiswa.length }}</span> Siswa
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

    <!-- ===== MODAL: Tambah / Edit Siswa ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 my-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">
                {{ isEditing ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
              </h3>
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                {{ isEditing ? 'Perbarui informasi siswa.' : 'Tambahkan akun siswa baru ke sistem.' }}
              </p>
            </div>
            <button @click="closeFormModal" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Grid: Nama & No Absen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="block text-slate-800 font-bold text-sm">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Contoh: Jihad Al-Aqsha"
                  class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
                />
              </div>
              <div class="space-y-1.5">
                <label class="block text-slate-800 font-bold text-sm">No. Absen</label>
                <input
                  v-model.number="form.no_absen"
                  type="number"
                  min="1"
                  placeholder="Contoh: 12"
                  class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
                />
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Email <span class="text-rose-500">*</span></label>
              <input
                v-model="form.email"
                type="email"
                required
                placeholder="siswa@sekolah.sch.id"
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
              />
            </div>

            <!-- Password (hanya tampil saat tambah baru, atau jika ingin reset) -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">
                {{ isEditing ? 'Password Baru' : 'Password' }}
                <span v-if="!isEditing" class="text-rose-500">*</span>
                <span v-else class="text-slate-400 font-normal text-xs">(kosongkan jika tidak diubah)</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  :required="!isEditing"
                  placeholder="Min. 8 karakter"
                  class="w-full px-4 py-2.5 pr-10 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                  <Eye v-if="!showPassword" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Hari Piket -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Hari Piket</label>
              <select
                v-model="form.hari_piket"
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs cursor-pointer"
              >
                <option value="">-- Belum Ditentukan --</option>
                <option v-for="h in hariList" :key="h" :value="h">{{ h }}</option>
              </select>
            </div>

            <!-- Status Aktif (hanya saat edit) -->
            <div v-if="isEditing" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
              <div>
                <p class="text-sm font-bold text-slate-900">Status Akun</p>
                <p class="text-xs text-slate-500">Nonaktifkan untuk menangguhkan akses siswa</p>
              </div>
              <button
                type="button"
                @click="form.is_active = !form.is_active"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none cursor-pointer',
                  form.is_active ? 'bg-[#00B775]' : 'bg-slate-300'
                ]"
              >
                <span :class="['inline-block h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200', form.is_active ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>

            <!-- Form Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
              <button type="button" @click="closeFormModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="bg-[#00B775] hover:bg-[#009d64] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
              >
                <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submitting ? 'Menyimpan...' : (isEditing ? 'Update Siswa' : 'Tambah Siswa') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Detail Siswa ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showDetailModal && selectedSiswa" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 my-4">
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-xl font-extrabold text-slate-900">Detail Siswa</h3>
            <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Avatar & Identity -->
          <div class="flex items-center space-x-4">
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center font-extrabold text-2xl text-white shrink-0 shadow-md', avatarColor(selectedSiswa.name || selectedSiswa.nama)]">
              {{ getInitials(selectedSiswa.name || selectedSiswa.nama) }}
            </div>
            <div>
              <h4 class="text-xl font-extrabold text-slate-900">{{ selectedSiswa.name || selectedSiswa.nama }}</h4>
              <p class="text-sm text-slate-500 font-medium">{{ selectedSiswa.email }}</p>
              <span :class="['inline-block text-[10px] font-extrabold px-2.5 py-1 rounded-full border mt-1.5 uppercase', (selectedSiswa.is_active ?? true) ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200']">
                {{ (selectedSiswa.is_active ?? true) ? 'Akun Aktif' : 'Nonaktif' }}
              </span>
            </div>
          </div>

          <!-- Detail Info Grid -->
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-slate-50 rounded-2xl p-4 space-y-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">No. Absen</p>
              <p class="text-lg font-extrabold text-slate-900">{{ selectedSiswa.no_absen || '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4 space-y-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Hari Piket</p>
              <p class="text-lg font-extrabold text-slate-900">{{ selectedSiswa.hari_piket || '-' }}</p>
            </div>
            <div class="bg-rose-50 rounded-2xl p-4 space-y-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-rose-600">Sanksi Aktif</p>
              <p class="text-lg font-extrabold text-rose-700">{{ selectedSiswa.sanksi_aktif_count ?? selectedSiswa.active_sanctions_count ?? 0 }} Sanksi</p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4 space-y-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">ID Siswa</p>
              <p class="text-lg font-extrabold text-slate-900">#{{ selectedSiswa.id }}</p>
            </div>
          </div>

          <!-- Riwayat Sanksi Siswa (jika ada dari relasi) -->
          <div v-if="selectedSiswa.sanksi_siswa && selectedSiswa.sanksi_siswa.length > 0" class="space-y-2">
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">Riwayat Sanksi</p>
            <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
              <div v-for="sanksi in selectedSiswa.sanksi_siswa" :key="sanksi.id" class="flex items-center justify-between bg-slate-50 rounded-xl p-3 border border-slate-200">
                <div>
                  <p class="text-xs font-bold text-slate-800">{{ sanksi.masterSanksi?.nama_sanksi || sanksi.sanksi?.nama_sanksi || 'Sanksi' }}</p>
                  <p class="text-[10px] text-slate-500">{{ sanksi.alasan || '-' }}</p>
                </div>
                <span :class="['text-[10px] font-extrabold px-2 py-0.5 rounded-full border', sanksi.status_penyelesaian === 'selesai' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200']">
                  {{ sanksi.status_penyelesaian === 'selesai' ? 'Selesai' : 'Belum' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer">
              Tutup
            </button>
            <button
              @click="() => { showDetailModal = false; openEditModal(selectedSiswa) }"
              class="bg-[#00B775] hover:bg-[#009d64] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition cursor-pointer flex items-center space-x-1.5"
            >
              <Pencil class="w-4 h-4" />
              <span>Edit</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Konfirmasi Hapus Siswa ===== -->
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
      <div v-if="showDeleteModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
              <AlertTriangle class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Hapus Data Siswa?</h3>
              <p class="text-xs text-slate-500 font-medium">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
          <p class="text-sm text-slate-600 leading-relaxed font-medium">
            Apakah Anda yakin ingin menghapus akun siswa
            <span class="font-bold text-slate-900">"{{ siswaToDelete?.name || siswaToDelete?.nama }}"</span>?
            Semua data terkait siswa ini (sanksi, jadwal, bukti piket) mungkin juga akan ikut terhapus.
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
  Plus, Search, Pencil, Trash2, X, Eye, EyeOff,
  Home, ChevronRight, ChevronLeft,
  CheckCircle2, AlertCircle, AlertTriangle,
  GraduationCap
} from 'lucide-vue-next'

// ========================
// STATE
// ========================
const siswaList = ref([])
const loading = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const searchQuery = ref('')
const hariFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']

const showFormModal = ref(false)
const showDetailModal = ref(false)
const showDeleteModal = ref(false)
const showPassword = ref(false)

const isEditing = ref(false)
const editingId = ref(null)
const selectedSiswa = ref(null)
const siswaToDelete = ref(null)

const form = ref({
  name: '',
  email: '',
  password: '',
  no_absen: '',
  hari_piket: '',
  is_active: true,
})

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

const extractErrorMessage = (err) => {
  if (err.response) {
    const data = err.response.data
    if (data?.message) return data.message
    if (data?.errors) {
      const firstKey = Object.keys(data.errors)[0]
      return data.errors[firstKey]?.[0] || 'Terjadi kesalahan validasi.'
    }
    if (err.response.status === 422) return 'Data tidak valid. Periksa kembali isian form.'
    if (err.response.status === 409) return 'Data masih digunakan dan tidak bisa dihapus.'
    if (err.response.status === 404) return 'Data tidak ditemukan.'
  }
  return 'Gagal terhubung ke server. Periksa koneksi Anda.'
}

// Generate warna avatar dari nama
const avatarColors = [
  'bg-[#00B775]', 'bg-sky-500', 'bg-indigo-500',
  'bg-violet-500', 'bg-amber-500', 'bg-teal-500',
  'bg-rose-500', 'bg-cyan-500'
]
const avatarColor = (name = '') => {
  const idx = (name.charCodeAt(0) || 0) % avatarColors.length
  return avatarColors[idx]
}

const getInitials = (name = '') => {
  return name
    .split(' ')
    .slice(0, 2)
    .map((w) => w[0]?.toUpperCase() ?? '')
    .join('')
}

// ========================
// COMPUTED
// ========================
const filteredSiswa = computed(() => {
  let list = siswaList.value
  if (hariFilter.value) {
    list = list.filter((s) => s.hari_piket === hariFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(
      (s) =>
        (s.name || s.nama)?.toLowerCase().includes(q) ||
        s.email?.toLowerCase().includes(q) ||
        String(s.no_absen ?? '').includes(q)
    )
  }
  return list
})

const totalPages = computed(() => Math.ceil(filteredSiswa.value.length / itemsPerPage) || 1)
const paginatedSiswa = computed(() => {
  const s = (currentPage.value - 1) * itemsPerPage
  return filteredSiswa.value.slice(s, s + itemsPerPage)
})
const startIdx = computed(() => filteredSiswa.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage + 1)
const endIdx = computed(() => Math.min(currentPage.value * itemsPerPage, filteredSiswa.value.length))

const activeCount = computed(() => siswaList.value.filter((s) => s.is_active ?? true).length)
const sanksiAktifCount = computed(() => siswaList.value.reduce((acc, s) => acc + (s.sanksi_aktif_count ?? s.active_sanctions_count ?? 0), 0))

watch([searchQuery, hariFilter], () => { currentPage.value = 1 })

// ========================
// API CALLS
// ========================
const fetchSiswa = async () => {
  loading.value = true
  try {
    const [resSiswa, resSanksi] = await Promise.allSettled([
      api.get('/admin/siswa'),
      api.get('/admin/sanksi-siswa')
    ])

    const rawSiswa = resSiswa.status === 'fulfilled'
      ? (Array.isArray(resSiswa.value.data) ? resSiswa.value.data : (resSiswa.value.data?.data ?? []))
      : []

    const rawSanksi = resSanksi.status === 'fulfilled'
      ? (Array.isArray(resSanksi.value.data) ? resSanksi.value.data : (resSanksi.value.data?.data ?? []))
      : []

    // Hitung jumlah sanksi aktif (status 'belum') per user_id
    const sanksiCountMap = {}
    rawSanksi.forEach((item) => {
      const status = item.status || item.status_penyelesaian
      if (status === 'belum' || status === 'BELUM') {
        const uid = item.user_id || item.user?.id || item.siswa?.id
        if (uid) {
          sanksiCountMap[uid] = (sanksiCountMap[uid] || 0) + 1
        }
      }
    })

    // Petakan sanksi_aktif_count ke data siswa
    siswaList.value = rawSiswa.map((s) => ({
      ...s,
      sanksi_aktif_count: sanksiCountMap[s.id] || s.sanksi_aktif_count || s.active_sanctions_count || 0
    }))
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
    siswaList.value = []
  } finally {
    loading.value = false
  }
}

// ========================
// MODAL HANDLERS
// ========================
const openAddModal = () => {
  isEditing.value = false
  editingId.value = null
  showPassword.value = false
  form.value = { name: '', email: '', password: '', no_absen: '', hari_piket: '', is_active: true }
  showFormModal.value = true
}

const openEditModal = (siswa) => {
  isEditing.value = true
  editingId.value = siswa.id
  showPassword.value = false
  form.value = {
    name: siswa.name || siswa.nama || '',
    email: siswa.email || '',
    password: '',
    no_absen: siswa.no_absen || '',
    hari_piket: siswa.hari_piket || '',
    is_active: siswa.is_active ?? true,
  }
  showFormModal.value = true
}

const closeFormModal = () => { showFormModal.value = false }

const openDetailModal = (siswa) => {
  selectedSiswa.value = siswa
  showDetailModal.value = true
}

const openDeleteModal = (siswa) => {
  siswaToDelete.value = siswa
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  siswaToDelete.value = null
}

// ========================
// SUBMIT FORM (POST / PUT)
// ========================
const submitForm = async () => {
  submitting.value = true

  // Buat payload, hapus password jika kosong saat edit
  const payload = { ...form.value }
  if (isEditing.value && !payload.password) {
    delete payload.password
  }
  if (!payload.no_absen) delete payload.no_absen
  if (!payload.hari_piket) delete payload.hari_piket

  try {
    if (isEditing.value) {
      await api.put(`/admin/siswa/${editingId.value}`, payload)
      showToast('Data siswa berhasil diperbarui!', 'success')
    } else {
      await api.post('/admin/siswa', payload)
      showToast('Siswa baru berhasil ditambahkan!', 'success')
    }
    closeFormModal()
    await fetchSiswa()
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
  if (!siswaToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/admin/siswa/${siswaToDelete.value.id}`)
    closeDeleteModal()
    showToast('Data siswa berhasil dihapus!', 'success')
    await fetchSiswa()
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
onMounted(() => {
  fetchSiswa()
})
</script>
