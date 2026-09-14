<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-[#00B775] selection:text-white relative">
    <!-- ===== Custom Toast Notification (Fixed Top-Right) ===== -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-x-8 scale-95"
      enter-to-class="opacity-100 translate-x-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-x-0 scale-100"
      leave-to-class="opacity-0 scale-95"
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
        <div :class="[
          'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs',
          toast.type === 'success' ? 'bg-[#00B775] text-white' : 'bg-rose-600 text-white'
        ]">
          <CheckCircle2 v-if="toast.type === 'success'" class="w-4 h-4" />
          <AlertCircle v-else class="w-4 h-4" />
        </div>
        <div class="text-xs font-bold leading-snug flex-1">
          {{ toast.message }}
        </div>
        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 ml-auto p-1 cursor-pointer shrink-0">
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- ===== Admin Navbar Component ===== -->
    <AdminNavbar />

    <!-- ===== Main Content Area ===== -->
    <main class="max-w-6xl mx-auto px-6 md:px-12 py-8 w-full space-y-6">

      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white border border-slate-200/80 px-4 py-2 rounded-full w-fit shadow-xs">
        <router-link to="/admin/dashboard" class="hover:text-[#00B775] transition flex items-center space-x-1">
          <Home class="w-3.5 h-3.5 text-[#00B775]" />
          <span>Dashboard</span>
        </router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-900 font-bold">Manajemen Sanksi</span>
      </nav>

      <!-- Header & Tab Bar -->
      <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
          <div class="space-y-1">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-100/80 border border-amber-200 text-amber-700 text-xs font-semibold">
              <ShieldAlert class="w-3.5 h-3.5" />
              <span>Kelola Sanksi</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
              Manajemen Sanksi
            </h1>
            <p class="text-slate-600 text-sm">
              Kelola master sanksi dan riwayat sanksi siswa secara terpadu.
            </p>
          </div>

          <!-- Add Button (contextual per tab) -->
          <button
            @click="activeTab === 'master' ? openMasterModal() : openSanksiSiswaModal()"
            class="bg-[#00B775] hover:bg-[#009d64] text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-md shadow-[#00B775]/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2 self-start cursor-pointer shrink-0"
          >
            <Plus class="w-5 h-5" />
            <span>{{ activeTab === 'master' ? 'Tambah Master Sanksi' : 'Beri Sanksi Siswa' }}</span>
          </button>
        </div>

        <!-- Tab Switcher -->
        <div class="flex bg-slate-100 p-1 rounded-2xl w-fit">
          <button
            @click="activeTab = 'master'"
            :class="[
              'px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer',
              activeTab === 'master'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-500 hover:text-slate-700'
            ]"
          >
            <span class="flex items-center space-x-1.5">
              <BookOpen class="w-4 h-4" />
              <span>Master Sanksi</span>
            </span>
          </button>
          <button
            @click="activeTab = 'siswa'"
            :class="[
              'px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer',
              activeTab === 'siswa'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-500 hover:text-slate-700'
            ]"
          >
            <span class="flex items-center space-x-1.5">
              <Users class="w-4 h-4" />
              <span>Sanksi Siswa</span>
            </span>
          </button>
        </div>
      </div>

      <!-- ===== TAB 1: MASTER SANKSI ===== -->
      <div v-show="activeTab === 'master'" class="space-y-4">
        <!-- Search Bar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="relative w-full md:w-80">
            <Search class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="masterSearch"
              type="text"
              placeholder="Cari master sanksi..."
              class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            />
          </div>
          <div class="text-xs font-semibold text-slate-500">
            Total Data: <span class="text-[#00B775] font-bold text-sm">{{ filteredMaster.length }}</span>
          </div>
        </div>

        <!-- Master Sanksi Table -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col">
          <div v-if="loadingMaster" class="p-10 text-center text-slate-500 font-medium flex items-center justify-center space-x-2">
            <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memuat data master sanksi...</span>
          </div>

          <div v-else-if="filteredMaster.length === 0" class="p-12 text-center">
            <ShieldAlert class="w-12 h-12 text-slate-300 mx-auto mb-3" />
            <p class="text-slate-500 font-medium text-sm">Belum ada data master sanksi.</p>
          </div>

          <div v-else>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left">
                <thead class="bg-amber-50/80 border-b border-amber-100 text-slate-700 font-bold uppercase text-xs">
                  <tr>
                    <th class="px-6 py-4 w-16">No</th>
                    <th class="px-6 py-4">Nama Sanksi</th>
                    <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4 w-40 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="(item, index) in paginatedMaster"
                    :key="item.id"
                    class="hover:bg-amber-50/30 transition-colors"
                  >
                    <td class="px-6 py-4 font-semibold text-slate-500">
                      {{ (masterPage - 1) * itemsPerPage + index + 1 }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">{{ item.nama_sanksi }}</td>
                    <td class="px-6 py-4 text-slate-600 max-w-xs md:max-w-sm truncate">
                      {{ item.deskripsi || '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                      <div class="flex items-center justify-center space-x-2">
                        <button
                          @click="openMasterModal(item)"
                          title="Edit Master Sanksi"
                          class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer"
                        >
                          <Pencil class="w-4 h-4" />
                        </button>
                        <button
                          @click="openDeleteMasterModal(item)"
                          title="Hapus Master Sanksi"
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

            <!-- Pagination Master -->
            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
              <div>
                Menampilkan <span class="text-slate-900 font-bold">{{ masterStartIdx }}</span> - <span class="text-slate-900 font-bold">{{ masterEndIdx }}</span>
                dari <span class="text-[#00B775] font-extrabold">{{ filteredMaster.length }}</span> Master Sanksi
              </div>
              <div class="flex items-center space-x-1.5">
                <button v-if="masterPage > 1" @click="masterPage--" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                  <ChevronLeft class="w-4 h-4" />
                </button>
                <button
                  v-for="page in masterTotalPages"
                  :key="page"
                  @click="masterPage = page"
                  :class="['w-9 h-9 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center', masterPage === page ? 'bg-[#00B775] border-[#00B775] text-white shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-[#00B775]']"
                >{{ page }}</button>
                <button v-if="masterPage < masterTotalPages" @click="masterPage++" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                  <ChevronRight class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== TAB 2: SANKSI SISWA ===== -->
      <div v-show="activeTab === 'siswa'" class="space-y-4">
        <!-- Search & Filter Bar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="relative w-full md:w-80">
            <Search class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="siswaSearch"
              type="text"
              placeholder="Cari nama siswa atau sanksi..."
              class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
            />
          </div>
          <div class="flex items-center space-x-3">
            <!-- Filter Status -->
            <select
              v-model="statusFilter"
              class="px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#00B775] cursor-pointer shadow-xs"
            >
              <option value="">Semua Status</option>
              <option value="belum">Belum Selesai</option>
              <option value="selesai">Selesai</option>
            </select>
            <div class="text-xs font-semibold text-slate-500">
              Total: <span class="text-[#00B775] font-bold text-sm">{{ filteredSanksiSiswa.length }}</span>
            </div>
          </div>
        </div>

        <!-- Sanksi Siswa Table -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col">
          <div v-if="loadingSiswa" class="p-10 text-center text-slate-500 font-medium flex items-center justify-center space-x-2">
            <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memuat data sanksi siswa...</span>
          </div>

          <div v-else-if="filteredSanksiSiswa.length === 0" class="p-12 text-center">
            <Users class="w-12 h-12 text-slate-300 mx-auto mb-3" />
            <p class="text-slate-500 font-medium text-sm">Belum ada data sanksi siswa.</p>
          </div>

          <div v-else>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left">
                <thead class="bg-rose-50/80 border-b border-rose-100 text-slate-700 font-bold uppercase text-xs">
                  <tr>
                    <th class="px-5 py-4 w-12">No</th>
                    <th class="px-5 py-4">Siswa / Kelompok</th>
                    <th class="px-5 py-4">Jenis Sanksi</th>
                    <th class="px-5 py-4">Tipe</th>
                    <th class="px-5 py-4">Alasan</th>
                    <th class="px-5 py-4 text-center">Status</th>
                    <th class="px-5 py-4 w-36 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="(item, index) in paginatedSiswa"
                    :key="item.id"
                    class="hover:bg-rose-50/20 transition-colors"
                  >
                    <td class="px-5 py-4 font-semibold text-slate-500">
                      {{ (siswaPage - 1) * itemsPerPage + index + 1 }}
                    </td>
                    <td class="px-5 py-4 font-bold text-slate-900">
                      <span v-if="item.tipe === 'individu'">{{ item.siswa?.name || item.user?.name || '-' }}</span>
                      <span v-else class="flex items-center space-x-1.5">
                        <Users class="w-4 h-4 text-indigo-500 shrink-0" />
                        <span>Kelompok {{ item.hari }}</span>
                      </span>
                    </td>
                    <td class="px-5 py-4 text-slate-700 font-medium">
                      {{ item.masterSanksi?.nama_sanksi || item.sanksi?.nama_sanksi || '-' }}
                    </td>
                    <td class="px-5 py-4">
                      <span :class="[
                        'inline-block text-[10px] font-extrabold px-2.5 py-1 rounded-full border uppercase tracking-wide',
                        item.tipe === 'individu'
                          ? 'bg-sky-100 text-sky-700 border-sky-200'
                          : 'bg-indigo-100 text-indigo-700 border-indigo-200'
                      ]">
                        {{ item.tipe }}
                      </span>
                    </td>
                    <td class="px-5 py-4 text-slate-600 max-w-[180px] truncate" :title="item.alasan">
                      {{ item.alasan || '-' }}
                    </td>
                    <td class="px-5 py-4 text-center">
                      <!-- Toggle Status Button -->
                      <button
                        @click="toggleStatus(item)"
                        :disabled="togglingId === item.id"
                        :class="[
                          'inline-flex items-center space-x-1.5 text-[10px] font-extrabold px-3 py-1.5 rounded-full border transition cursor-pointer disabled:opacity-60',
                          item.status_penyelesaian === 'selesai' || item.status === 'selesai'
                            ? 'bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-emerald-200'
                            : 'bg-amber-100 text-amber-700 border-amber-200 hover:bg-amber-200'
                        ]"
                      >
                        <CheckCircle2 v-if="item.status_penyelesaian === 'selesai' || item.status === 'selesai'" class="w-3 h-3" />
                        <Clock v-else class="w-3 h-3" />
                        <span>{{ item.status_penyelesaian === 'selesai' || item.status === 'selesai' ? 'Selesai' : 'Belum' }}</span>
                      </button>
                    </td>
                    <td class="px-5 py-4 text-center">
                      <div class="flex items-center justify-center space-x-2">
                        <button
                          @click="openSanksiSiswaModal(item)"
                          title="Edit Sanksi Siswa"
                          class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer"
                        >
                          <Pencil class="w-4 h-4" />
                        </button>
                        <button
                          @click="openDeleteSiswaModal(item)"
                          title="Hapus Sanksi Siswa"
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

            <!-- Pagination Siswa -->
            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
              <div>
                Menampilkan <span class="text-slate-900 font-bold">{{ siswaStartIdx }}</span> - <span class="text-slate-900 font-bold">{{ siswaEndIdx }}</span>
                dari <span class="text-[#00B775] font-extrabold">{{ filteredSanksiSiswa.length }}</span> Sanksi Siswa
              </div>
              <div class="flex items-center space-x-1.5">
                <button v-if="siswaPage > 1" @click="siswaPage--" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                  <ChevronLeft class="w-4 h-4" />
                </button>
                <button
                  v-for="page in siswaTotalPages"
                  :key="page"
                  @click="siswaPage = page"
                  :class="['w-9 h-9 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center', siswaPage === page ? 'bg-[#00B775] border-[#00B775] text-white shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-[#00B775]']"
                >{{ page }}</button>
                <button v-if="siswaPage < siswaTotalPages" @click="siswaPage++" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-emerald-50 hover:text-[#00B775] transition cursor-pointer shadow-xs">
                  <ChevronRight class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ===== MODAL: Master Sanksi (Tambah / Edit) ===== -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="showMasterModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">
                {{ masterIsEditing ? 'Edit Master Sanksi' : 'Tambah Master Sanksi' }}
              </h3>
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                {{ masterIsEditing ? 'Perbarui data master sanksi yang sudah ada.' : 'Tambahkan jenis sanksi baru ke sistem.' }}
              </p>
            </div>
            <button @click="closeMasterModal" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitMasterForm" class="space-y-4">
            <!-- Nama Sanksi -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">
                Nama Sanksi <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="masterForm.nama_sanksi"
                type="text"
                required
                placeholder="Contoh: Membersihkan Toilet"
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs"
              />
            </div>

            <!-- Deskripsi -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Deskripsi</label>
              <textarea
                v-model="masterForm.deskripsi"
                rows="3"
                placeholder="Jelaskan detail sanksi ini..."
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs resize-none"
              ></textarea>
            </div>

            <!-- Form Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
              <button
                type="button"
                @click="closeMasterModal"
                class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer"
              >Batal</button>
              <button
                type="submit"
                :disabled="submittingMaster"
                class="bg-[#00B775] hover:bg-[#009d64] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
              >
                <svg v-if="submittingMaster" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submittingMaster ? 'Menyimpan...' : (masterIsEditing ? 'Update Sanksi' : 'Simpan Sanksi') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Konfirmasi Hapus Master Sanksi ===== -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
    >
      <div v-if="showDeleteMasterModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
              <AlertTriangle class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Hapus Master Sanksi?</h3>
              <p class="text-xs text-slate-500 font-medium">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
          <p class="text-sm text-slate-600 leading-relaxed font-medium">
            Apakah Anda yakin ingin menghapus sanksi
            <span class="font-bold text-slate-900">"{{ masterToDelete?.nama_sanksi }}"</span>?
            Jika sanksi ini masih aktif digunakan, penghapusan akan gagal.
          </p>
          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button @click="closeDeleteMasterModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer">Batal</button>
            <button
              @click="confirmDeleteMaster"
              :disabled="deletingMaster"
              class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
            >
              <svg v-if="deletingMaster" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ deletingMaster ? 'Menghapus...' : 'Ya, Hapus' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Sanksi Siswa (Tambah / Edit) ===== -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
    >
      <div v-if="showSiswaModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left space-y-5 my-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">
                {{ siswaIsEditing ? 'Edit Sanksi Siswa' : 'Beri Sanksi Siswa' }}
              </h3>
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                {{ siswaIsEditing ? 'Perbarui data sanksi siswa.' : 'Tambahkan sanksi baru untuk siswa atau kelompok.' }}
              </p>
            </div>
            <button @click="closeSiswaModal" class="text-slate-400 hover:text-slate-600 p-1.5 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitSiswaForm" class="space-y-4">
            <!-- Tipe Sanksi: Individu / Kelompok -->
            <div class="space-y-2">
              <label class="block text-slate-800 font-bold text-sm">
                Tipe Sanksi <span class="text-rose-500">*</span>
              </label>
              <div class="flex space-x-3">
                <label
                  v-for="tipe in ['individu', 'kelompok']"
                  :key="tipe"
                  :class="[
                    'flex-1 flex items-center justify-center space-x-2 p-3 rounded-xl border-2 cursor-pointer font-bold text-sm transition-all',
                    siswaForm.tipe === tipe
                      ? 'border-[#00B775] bg-emerald-50 text-[#00B775]'
                      : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                  ]"
                >
                  <input type="radio" v-model="siswaForm.tipe" :value="tipe" class="sr-only" />
                  <User v-if="tipe === 'individu'" class="w-4 h-4" />
                  <Users v-else class="w-4 h-4" />
                  <span class="capitalize">{{ tipe }}</span>
                </label>
              </div>
            </div>

            <!-- Kondisional: Individu → pilih siswa | Kelompok → pilih hari -->
            <transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 translate-y-[-4px]"
              enter-to-class="opacity-100 translate-y-0"
            >
              <div v-if="siswaForm.tipe === 'individu'" class="space-y-1.5">
                <label class="block text-slate-800 font-bold text-sm">
                  Siswa <span class="text-rose-500">*</span>
                </label>
                <select
                  v-model="siswaForm.user_id"
                  required
                  class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs cursor-pointer"
                >
                  <option value="" disabled>-- Pilih Siswa --</option>
                  <option v-for="s in daftarSiswa" :key="s.id" :value="s.id">
                    {{ s.name || s.nama }}
                  </option>
                </select>
              </div>

              <div v-else-if="siswaForm.tipe === 'kelompok'" class="space-y-1.5">
                <label class="block text-slate-800 font-bold text-sm">
                  Hari Piket <span class="text-rose-500">*</span>
                </label>
                <select
                  v-model="siswaForm.hari"
                  required
                  class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs cursor-pointer"
                >
                  <option value="" disabled>-- Pilih Hari --</option>
                  <option v-for="h in hariList" :key="h" :value="h">{{ h }}</option>
                </select>
              </div>
            </transition>

            <!-- Pilih Master Sanksi -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">
                Jenis Sanksi <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="siswaForm.sanksi_id"
                required
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs cursor-pointer"
              >
                <option value="" disabled>-- Pilih Jenis Sanksi --</option>
                <option v-for="ms in masterSanksi" :key="ms.id" :value="ms.id">
                  {{ ms.nama_sanksi }}
                </option>
              </select>
            </div>

            <!-- Alasan -->
            <div class="space-y-1.5">
              <label class="block text-slate-800 font-bold text-sm">Alasan / Keterangan</label>
              <textarea
                v-model="siswaForm.alasan"
                rows="3"
                placeholder="Tuliskan alasan pemberian sanksi..."
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00B775] focus:border-transparent transition-all shadow-xs resize-none"
              ></textarea>
            </div>

            <!-- Form Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
              <button
                type="button"
                @click="closeSiswaModal"
                class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition cursor-pointer"
              >Batal</button>
              <button
                type="submit"
                :disabled="submittingSiswa"
                class="bg-[#00B775] hover:bg-[#009d64] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-[#00B775]/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
              >
                <svg v-if="submittingSiswa" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submittingSiswa ? 'Menyimpan...' : (siswaIsEditing ? 'Update Sanksi' : 'Beri Sanksi') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: Konfirmasi Hapus Sanksi Siswa ===== -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
    >
      <div v-if="showDeleteSiswaModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 max-w-md w-full p-6 sm:p-8 shadow-2xl text-left space-y-5">
          <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
              <AlertTriangle class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-extrabold text-slate-900">Hapus Sanksi Siswa?</h3>
              <p class="text-xs text-slate-500 font-medium">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
          <p class="text-sm text-slate-600 leading-relaxed font-medium">
            Apakah Anda yakin ingin menghapus data sanksi untuk
            <span class="font-bold text-slate-900">
              {{ siswaToDelete?.tipe === 'individu'
                ? (siswaToDelete?.siswa?.name || siswaToDelete?.user?.name || 'siswa ini')
                : `Kelompok ${siswaToDelete?.hari}` }}
            </span>?
          </p>
          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button @click="closeDeleteSiswaModal" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition cursor-pointer">Batal</button>
            <button
              @click="confirmDeleteSiswa"
              :disabled="deletingSiswa"
              class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
            >
              <svg v-if="deletingSiswa" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ deletingSiswa ? 'Menghapus...' : 'Ya, Hapus' }}</span>
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
  ShieldAlert, BookOpen, Users, User, Clock
} from 'lucide-vue-next'

// ========================
// SHARED STATE
// ========================
const activeTab = ref('master')
const itemsPerPage = 10
const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']

const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value.show = false }, 4000)
}

// Extract error message from API response (handles 422 & other errors)
const extractErrorMessage = (err) => {
  if (err.response) {
    const data = err.response.data
    if (data?.message) return data.message
    if (data?.errors) {
      const firstKey = Object.keys(data.errors)[0]
      return data.errors[firstKey]?.[0] || 'Terjadi kesalahan validasi.'
    }
    if (err.response.status === 422) return 'Data tidak valid atau masih digunakan.'
    if (err.response.status === 409) return 'Data masih aktif digunakan dan tidak bisa dihapus.'
  }
  return 'Gagal terhubung ke server. Periksa koneksi Anda.'
}

// ========================
// TAB 1 — MASTER SANKSI (/admin/sanksi-master)
// ========================
const masterSanksi = ref([])
const loadingMaster = ref(false)
const submittingMaster = ref(false)
const deletingMaster = ref(false)
const masterSearch = ref('')
const masterPage = ref(1)

const showMasterModal = ref(false)
const masterIsEditing = ref(false)
const masterEditingId = ref(null)
const masterForm = ref({ nama_sanksi: '', deskripsi: '' })

const showDeleteMasterModal = ref(false)
const masterToDelete = ref(null)

const filteredMaster = computed(() => {
  if (!masterSearch.value.trim()) return masterSanksi.value
  const q = masterSearch.value.toLowerCase()
  return masterSanksi.value.filter(
    (m) => m.nama_sanksi?.toLowerCase().includes(q) || m.deskripsi?.toLowerCase().includes(q)
  )
})

const masterTotalPages = computed(() => Math.ceil(filteredMaster.value.length / itemsPerPage) || 1)
const paginatedMaster = computed(() => {
  const s = (masterPage.value - 1) * itemsPerPage
  return filteredMaster.value.slice(s, s + itemsPerPage)
})
const masterStartIdx = computed(() => filteredMaster.value.length === 0 ? 0 : (masterPage.value - 1) * itemsPerPage + 1)
const masterEndIdx = computed(() => Math.min(masterPage.value * itemsPerPage, filteredMaster.value.length))

watch(masterSearch, () => { masterPage.value = 1 })

const fetchMasterSanksi = async () => {
  loadingMaster.value = true
  try {
    const res = await api.get('/admin/sanksi-master')
    masterSanksi.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
    masterSanksi.value = []
  } finally {
    loadingMaster.value = false
  }
}

const openMasterModal = (item = null) => {
  masterIsEditing.value = !!item
  masterEditingId.value = item?.id ?? null
  masterForm.value = { nama_sanksi: item?.nama_sanksi ?? '', deskripsi: item?.deskripsi ?? '' }
  showMasterModal.value = true
}

const closeMasterModal = () => { showMasterModal.value = false }

const submitMasterForm = async () => {
  submittingMaster.value = true
  try {
    if (masterIsEditing.value) {
      await api.put(`/admin/sanksi-master/${masterEditingId.value}`, masterForm.value)
      showToast('Master sanksi berhasil diperbarui!', 'success')
    } else {
      await api.post('/admin/sanksi-master', masterForm.value)
      showToast('Master sanksi baru berhasil ditambahkan!', 'success')
    }
    closeMasterModal()
    await fetchMasterSanksi()
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
  } finally {
    submittingMaster.value = false
  }
}

const openDeleteMasterModal = (item) => {
  masterToDelete.value = item
  showDeleteMasterModal.value = true
}

const closeDeleteMasterModal = () => {
  showDeleteMasterModal.value = false
  masterToDelete.value = null
}

const confirmDeleteMaster = async () => {
  if (!masterToDelete.value) return
  deletingMaster.value = true
  try {
    await api.delete(`/admin/sanksi-master/${masterToDelete.value.id}`)
    closeDeleteMasterModal()
    showToast('Master sanksi berhasil dihapus!', 'success')
    await fetchMasterSanksi()
  } catch (err) {
    closeDeleteMasterModal()
    showToast(extractErrorMessage(err), 'error')
  } finally {
    deletingMaster.value = false
  }
}

// ========================
// TAB 2 — SANKSI SISWA (/admin/sanksi-siswa)
// ========================
const sanksiSiswa = ref([])
const daftarSiswa = ref([])
const loadingSiswa = ref(false)
const submittingSiswa = ref(false)
const deletingSiswa = ref(false)
const togglingId = ref(null)
const siswaSearch = ref('')
const statusFilter = ref('')
const siswaPage = ref(1)

const showSiswaModal = ref(false)
const siswaIsEditing = ref(false)
const siswaEditingId = ref(null)
const siswaForm = ref({ tipe: 'individu', user_id: '', hari: '', sanksi_id: '', alasan: '' })

const showDeleteSiswaModal = ref(false)
const siswaToDelete = ref(null)

const filteredSanksiSiswa = computed(() => {
  let list = sanksiSiswa.value
  if (statusFilter.value) {
    list = list.filter((s) => s.status_penyelesaian === statusFilter.value || s.status === statusFilter.value)
  }
  if (siswaSearch.value.trim()) {
    const q = siswaSearch.value.toLowerCase()
    list = list.filter(
      (s) =>
        s.siswa?.name?.toLowerCase().includes(q) ||
        s.user?.name?.toLowerCase().includes(q) ||
        s.masterSanksi?.nama_sanksi?.toLowerCase().includes(q) ||
        s.sanksi?.nama_sanksi?.toLowerCase().includes(q) ||
        s.hari?.toLowerCase().includes(q) ||
        s.alasan?.toLowerCase().includes(q)
    )
  }
  return list
})

const siswaTotalPages = computed(() => Math.ceil(filteredSanksiSiswa.value.length / itemsPerPage) || 1)
const paginatedSiswa = computed(() => {
  const s = (siswaPage.value - 1) * itemsPerPage
  return filteredSanksiSiswa.value.slice(s, s + itemsPerPage)
})
const siswaStartIdx = computed(() => filteredSanksiSiswa.value.length === 0 ? 0 : (siswaPage.value - 1) * itemsPerPage + 1)
const siswaEndIdx = computed(() => Math.min(siswaPage.value * itemsPerPage, filteredSanksiSiswa.value.length))

watch([siswaSearch, statusFilter], () => { siswaPage.value = 1 })

const fetchSanksiSiswa = async () => {
  loadingSiswa.value = true
  try {
    const res = await api.get('/admin/sanksi-siswa')
    sanksiSiswa.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
    sanksiSiswa.value = []
  } finally {
    loadingSiswa.value = false
  }
}

const fetchDaftarSiswa = async () => {
  try {
    const res = await api.get('/admin/jadwal-piket')
    const rawData = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    // Extract user objects from schedule items, ensuring unique students
    const studentMap = new Map()
    rawData.forEach((item) => {
      if (item.user && item.user.id) {
        studentMap.set(item.user.id, item.user)
      }
    })
    daftarSiswa.value = Array.from(studentMap.values())
  } catch (err) {
    console.warn('Gagal memuat daftar siswa dari jadwal piket:', err)
    daftarSiswa.value = []
  }
}

const openSanksiSiswaModal = (item = null) => {
  siswaIsEditing.value = !!item
  siswaEditingId.value = item?.id ?? null
  siswaForm.value = {
    tipe: item?.tipe ?? 'individu',
    user_id: item?.user_id ?? item?.siswa?.id ?? '',
    hari: item?.hari ?? '',
    sanksi_id: item?.sanksi_id ?? item?.masterSanksi?.id ?? item?.sanksi?.id ?? '',
    alasan: item?.alasan ?? '',
  }
  showSiswaModal.value = true
}

const closeSiswaModal = () => { showSiswaModal.value = false }

const submitSiswaForm = async () => {
  submittingSiswa.value = true
  const payload = {
    tipe: siswaForm.value.tipe,
    sanksi_id: siswaForm.value.sanksi_id,
    alasan: siswaForm.value.alasan,
    ...(siswaForm.value.tipe === 'individu'
      ? { user_id: siswaForm.value.user_id }
      : { hari: siswaForm.value.hari })
  }

  try {
    if (siswaIsEditing.value) {
      await api.put(`/admin/sanksi-siswa/${siswaEditingId.value}`, payload)
      showToast('Sanksi siswa berhasil diperbarui!', 'success')
    } else {
      await api.post('/admin/sanksi-siswa', payload)
      showToast('Sanksi berhasil diberikan!', 'success')
    }
    closeSiswaModal()
    await fetchSanksiSiswa()
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
  } finally {
    submittingSiswa.value = false
  }
}

const toggleStatus = async (item) => {
  togglingId.value = item.id
  const currentStatus = item.status_penyelesaian || item.status
  const newStatus = currentStatus === 'selesai' ? 'belum' : 'selesai'
  try {
    await api.patch(`/admin/sanksi-siswa/${item.id}/status`, { status: newStatus, status_penyelesaian: newStatus })
    item.status_penyelesaian = newStatus
    item.status = newStatus
    showToast(
      newStatus === 'selesai' ? 'Sanksi ditandai selesai!' : 'Status dikembalikan ke belum selesai.',
      'success'
    )
  } catch (err) {
    showToast(extractErrorMessage(err), 'error')
  } finally {
    togglingId.value = null
  }
}

const openDeleteSiswaModal = (item) => {
  siswaToDelete.value = item
  showDeleteSiswaModal.value = true
}

const closeDeleteSiswaModal = () => {
  showDeleteSiswaModal.value = false
  siswaToDelete.value = null
}

const confirmDeleteSiswa = async () => {
  if (!siswaToDelete.value) return
  deletingSiswa.value = true
  try {
    await api.delete(`/admin/sanksi-siswa/${siswaToDelete.value.id}`)
    closeDeleteSiswaModal()
    showToast('Data sanksi siswa berhasil dihapus!', 'success')
    await fetchSanksiSiswa()
  } catch (err) {
    closeDeleteSiswaModal()
    showToast(extractErrorMessage(err), 'error')
  } finally {
    deletingSiswa.value = false
  }
}

// ========================
// INIT
// ========================
onMounted(async () => {
  await Promise.all([fetchMasterSanksi(), fetchSanksiSiswa(), fetchDaftarSiswa()])
})
</script>
