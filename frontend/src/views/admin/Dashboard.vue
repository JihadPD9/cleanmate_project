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

    <!-- Admin Navbar Component -->
    <AdminNavbar />

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-6 md:px-12 py-8 w-full space-y-6">
      <!-- Top Info Bar: Real-time WIB Clock & Dashboard Overview -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-slate-200/80 px-6 py-3.5 rounded-2xl shadow-xs">
        <div class="flex items-center space-x-2 text-xs md:text-sm font-bold text-slate-800">
          <Sparkles class="w-4 h-4 text-[#00B775]" />
          <span>Dashboard Overview</span>
        </div>

        <!-- Dynamic Real-time Clock (WIB) -->
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
                <p class="text-2xl font-extrabold text-slate-900 mt-1">
                  <span v-if="siswaLoading" class="text-base text-slate-400">...</span>
                  <span v-else>{{ totalSiswaCount }}</span>
                </p>
              </div>
              <div class="bg-rose-50/70 border border-rose-200/80 rounded-xl p-3">
                <p class="text-xs font-medium text-rose-600">Sanksi Aktif</p>
                <p class="text-2xl font-extrabold text-rose-600 mt-1">
                  <span v-if="sanksiLoading" class="text-base text-slate-400">...</span>
                  <span v-else>{{ activeSanksiCount }}</span>
                </p>
              </div>
            </div>
          </div>

          <button
            @click="showDetailAlert('Data Siswa')"
            class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
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
              <p class="text-slate-500 text-xs mt-0.5">Konfigurasi daftar sanksi dan pelanggaran siswa.</p>
            </div>

            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-slate-600">Sanksi Belum Tuntas</p>
                <p class="text-xs text-slate-400">Status 'belum' dituntaskan</p>
              </div>
              <div class="text-3xl font-extrabold text-amber-600">
                <span v-if="sanksiLoading" class="text-base text-slate-400">...</span>
                <span v-else>{{ activeSanksiCount }}</span>
              </div>
            </div>
          </div>

          <router-link
            to="/admin/sanksi"
            class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs"
          >
            <span>Kelola Data Sanksi</span>
            <ChevronRight class="w-4 h-4" />
          </router-link>
        </div>

        <!-- 4. Data Jadwal Piket Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-5 lg:col-span-2">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 flex items-center justify-center text-teal-600">
                <Calendar class="w-6 h-6" />
              </div>
              <span class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-200">
                Penjadwalan
              </span>
            </div>

            <div>
              <h2 class="text-xl font-extrabold text-slate-900">Data Jadwal Piket</h2>
              <p class="text-slate-500 text-xs mt-0.5">Kelola giliran piket harian dan sanksi kelompok kelas.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-stretch">
              <!-- Officers List (Set to flex flex-col h-full so height always matches right column) -->
              <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex flex-col justify-between space-y-2 h-full">
                <div class="space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">Piket Hari Ini:</span>
                    <span class="text-xs font-extrabold text-[#00B775] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                      {{ currentDayName }}
                    </span>
                  </div>
                  <div v-if="jadwalLoading" class="text-xs text-slate-400 font-medium">Memuat...</div>
                  <div v-else-if="todaySiswaList.length === 0" class="text-xs text-slate-400 font-medium italic">Tidak ada jadwal hari ini</div>
                  <ul v-else class="text-xs text-slate-700 font-semibold space-y-1">
                    <li
                      v-for="item in todaySiswaList.slice(0, 4)"
                      :key="item.id"
                      class="flex items-center space-x-1.5"
                    >
                      <span class="w-1.5 h-1.5 rounded-full bg-[#00B775] inline-block"></span>
                      <span>{{ item.user?.name ?? item.user?.nama ?? item.siswa?.name ?? item.siswa?.nama ?? item.name ?? '-' }}</span>
                    </li>
                    <li v-if="todaySiswaList.length > 4" class="text-slate-400 text-[10px]">
                      +{{ todaySiswaList.length - 4 }} lainnya
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Right Column: 2 Stats Stacked Vertically (Atas-Bawah) -->
              <div class="flex flex-col gap-3 h-full justify-between">
                <div class="bg-teal-50/70 border border-teal-200/80 rounded-2xl p-3.5 flex items-center justify-between flex-1">
                  <div>
                    <p class="text-xs font-bold text-teal-700">Total Jadwal</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Semua hari</p>
                  </div>
                  <p class="text-2xl font-extrabold text-teal-600">
                    <span v-if="jadwalLoading" class="text-base text-slate-400">...</span>
                    <span v-else>{{ totalJadwalCount }}</span>
                  </p>
                </div>

                <div class="bg-[#00B775]/10 border border-[#00B775]/20 rounded-2xl p-3.5 flex items-center justify-between flex-1">
                  <div>
                    <p class="text-xs font-bold text-[#00B775]">Petugas Besok</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Hari {{ tomorrowDayName }}</p>
                  </div>
                  <p class="text-2xl font-extrabold text-[#00B775]">
                    <span v-if="jadwalLoading" class="text-base text-slate-400">...</span>
                    <span v-else>{{ tomorrowSiswaList.length }}</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <router-link
            to="/admin/jadwal"
            class="w-full bg-[#00B775] hover:bg-[#009d64] text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs"
          >
            <span>Kelola Jadwal Piket</span>
            <ChevronRight class="w-4 h-4" />
          </router-link>
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
            <div v-if="buktiLoading" class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 flex justify-center py-6">
              <span class="text-xs text-slate-400 font-medium animate-pulse">Memuat bukti terbaru...</span>
            </div>
            <div v-else-if="!latestBuktiPiket" class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 flex justify-center py-6">
              <span class="text-xs text-slate-400 font-medium italic">Tidak ada bukti pending</span>
            </div>
            <div v-else class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-3">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-xs font-bold text-slate-700">{{ latestBuktiPiket.user?.name || latestBuktiPiket.siswa?.name || 'Siswa' }}</p>
                  <p class="text-[10px] text-slate-500">{{ formatDate(latestBuktiPiket.tanggal || latestBuktiPiket.created_at) }}</p>
                </div>
                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full border bg-amber-100 text-amber-700 border-amber-200">
                  PENDING
                </span>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div
                  v-if="latestBuktiPiket.foto_1"
                  @click="openImageModal(getImageUrl(latestBuktiPiket.foto_1))"
                  class="block aspect-square bg-slate-200/80 rounded-xl overflow-hidden shadow-inner hover:opacity-90 transition cursor-pointer relative group border border-slate-200"
                >
                  <img :src="getImageUrl(latestBuktiPiket.foto_1)" alt="Foto 1" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[11px] font-bold">
                    <ZoomIn class="w-4 h-4 mr-1" />
                    <span>Zoom</span>
                  </div>
                </div>
                <div v-else class="aspect-square bg-slate-100 border-2 border-dashed border-slate-200/90 rounded-xl flex items-center justify-center text-slate-400 font-bold text-sm shadow-xs select-none">
                  -
                </div>

                <div
                  v-if="latestBuktiPiket.foto_2"
                  @click="openImageModal(getImageUrl(latestBuktiPiket.foto_2))"
                  class="block aspect-square bg-slate-200/80 rounded-xl overflow-hidden shadow-inner hover:opacity-90 transition cursor-pointer relative group border border-slate-200"
                >
                  <img :src="getImageUrl(latestBuktiPiket.foto_2)" alt="Foto 2" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[11px] font-bold">
                    <ZoomIn class="w-4 h-4 mr-1" />
                    <span>Zoom</span>
                  </div>
                </div>
                <div v-else class="aspect-square bg-slate-100 border-2 border-dashed border-slate-200/90 rounded-xl flex items-center justify-center text-slate-400 font-bold text-sm shadow-xs select-none">
                  -
                </div>
              </div>

              <!-- Action buttons -->
              <div class="grid grid-cols-2 gap-2 pt-1">
                <button
                  @click="verifyBukti(latestBuktiPiket.id, 'approved')"
                  :disabled="verifyingId === latestBuktiPiket.id"
                  class="bg-[#00B775] hover:bg-[#009d64] text-white py-2 rounded-xl font-bold text-xs shadow-xs transition cursor-pointer disabled:opacity-70 flex justify-center items-center"
                >
                  <span v-if="verifyingId === latestBuktiPiket.id && verifyStatus === 'approved'" class="animate-pulse">...</span>
                  <span v-else>SETUJU</span>
                </button>
                <button
                  @click="openRejectModal(latestBuktiPiket.id)"
                  :disabled="verifyingId === latestBuktiPiket.id"
                  class="bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-xl font-bold text-xs shadow-xs transition cursor-pointer disabled:opacity-70 flex justify-center items-center"
                >
                  <span v-if="verifyingId === latestBuktiPiket.id && verifyStatus === 'rejected'" class="animate-pulse">...</span>
                  <span v-else>TOLAK</span>
                </button>
              </div>
            </div>
          </div>

          <router-link
            to="/admin/bukti-piket"
            class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer"
          >
            <span>Lihat Semua Bukti</span>
            <ChevronRight class="w-4 h-4" />
          </router-link>
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

    <!-- ===== MODAL: Alasan Tolak (Dashboard) ===== -->
    <teleport to="body">
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
                  :disabled="submittingReject || !rejectReason.trim()"
                  class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 transition disabled:opacity-70 cursor-pointer flex items-center space-x-2"
                >
                  <span>{{ submittingReject ? 'Memproses...' : 'Tolak Laporan' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
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
  ChevronRight,
  ZoomIn,
  ZoomOut,
  X,
  CheckCircle2,
  AlertCircle
} from 'lucide-vue-next'

const router = useRouter()

// ===== Toast Notification State & Handler =====
const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value.show = false }, 3500)
}

// ===== Image Zoom Preview Modal State & Handlers =====
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

const currentDayName = ref('')
const formattedDateOnly = ref('')
const formattedTimeOnly = ref('')
const totalTasksCount = ref(0)
const activeSanksiCount = ref(0)
const tasksLoading = ref(false)
const sanksiLoading = ref(false)
let timer = null

const dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

// Update Real-time WIB Date & Time
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

// Fetch Real Tasks Count from API GET /admin/tasks
const fetchTotalTasks = async () => {
  tasksLoading.value = true
  try {
    const res = await api.get('/admin/tasks')
    if (res.data && Array.isArray(res.data)) {
      totalTasksCount.value = res.data.length
    } else if (res.data && res.data.data && Array.isArray(res.data.data)) {
      totalTasksCount.value = res.data.data.length
    } else {
      totalTasksCount.value = 0
    }
  } catch (err) {
    console.error('API /admin/tasks error:', err)
    totalTasksCount.value = 0
  } finally {
    tasksLoading.value = false
  }
}

// Fetch Active Sanctions Count (status 'belum') from API GET /admin/sanksi-siswa
const fetchActiveSanksi = async () => {
  sanksiLoading.value = true
  try {
    const res = await api.get('/admin/sanksi-siswa')
    const list = Array.isArray(res.data) ? res.data : (res.data?.data || [])
    const activeItems = list.filter((item) => {
      const currentStatus = item.status_penyelesaian ?? item.status
      return currentStatus === 'belum' || currentStatus === 'BELUM'
    })
    activeSanksiCount.value = activeItems.length
  } catch (err) {
    console.error('API /admin/sanksi-siswa error:', err)
    activeSanksiCount.value = 0
  } finally {
    sanksiLoading.value = false
  }
}

const totalSiswaCount = ref(0)
const siswaLoading = ref(false)
const jadwalLoading = ref(false)
const totalJadwalCount = ref(0)
const todaySiswaList = ref([])
const tomorrowSiswaList = ref([])
const tomorrowDayName = ref('')

// Fetch Real Total Siswa Count from API GET /admin/siswa
const fetchTotalSiswa = async () => {
  siswaLoading.value = true
  try {
    const res = await api.get('/admin/siswa')
    const list = Array.isArray(res.data) ? res.data : (res.data?.data || [])
    totalSiswaCount.value = list.length
  } catch (err) {
    console.error('API /admin/siswa error:', err)
    totalSiswaCount.value = 0
  } finally {
    siswaLoading.value = false
  }
}

// Fetch Jadwal Piket — untuk card dashboard
const fetchJadwalDashboard = async () => {
  jadwalLoading.value = true
  try {
    const res = await api.get('/admin/jadwal-piket')
    const list = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    totalJadwalCount.value = list.length
    const dayNamesForFilter = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
    const now = new Date()
    const today = dayNamesForFilter[now.getDay()]
    const tomorrowIdx = (now.getDay() + 1) % 7
    const tomorrow = dayNamesForFilter[tomorrowIdx]
    
    tomorrowDayName.value = tomorrow
    todaySiswaList.value = list.filter(j => (j.hari_piket ?? j.hari) === today)
    tomorrowSiswaList.value = list.filter(j => (j.hari_piket ?? j.hari) === tomorrow)
  } catch (err) {
    console.error('API /admin/jadwal-piket error:', err)
    totalJadwalCount.value = 0
    todaySiswaList.value = []
    tomorrowSiswaList.value = []
  } finally {
    jadwalLoading.value = false
  }
}

const latestBuktiPiket = ref(null)
const buktiLoading = ref(false)
const verifyingId = ref(null)
const verifyStatus = ref('')

const getImageUrl = (path) => {
  if (!path) return ''
  return `http://127.0.0.1:8000/storage/${path}`
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = typeof dateString === 'string' && dateString.length === 10 ? new Date(`${dateString}T00:00:00`) : new Date(dateString)
  if (isNaN(date.getTime())) return dateString
  return new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }).format(date)
}

const fetchBuktiTerbaru = async () => {
  buktiLoading.value = true
  try {
    const res = await api.get('/admin/bukti-piket')
    const list = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    // Ambil bukti yang statusnya 'pending'
    const pendingList = list.filter(b => b.status_approval === 'pending')
    // Asumsi list terurut dari terbaru, ambil index 0
    latestBuktiPiket.value = pendingList.length > 0 ? pendingList[0] : null
  } catch (err) {
    console.error('API /admin/bukti-piket error:', err)
    latestBuktiPiket.value = null
  } finally {
    buktiLoading.value = false
  }
}

const showRejectModal = ref(false)
const rejectingId = ref(null)
const rejectReason = ref('')
const submittingReject = ref(false)

const openRejectModal = (id) => {
  rejectingId.value = id
  rejectReason.value = ''
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectingId.value = null
  rejectReason.value = ''
}

const verifyBukti = async (id, status, catatan = '') => {
  verifyingId.value = id
  verifyStatus.value = status
  try {
    await api.patch(`/admin/bukti-piket/${id}/status`, {
      status_approval: status,
      ...(catatan ? { catatan_admin: catatan } : {})
    })
    showToast(`Bukti piket berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}!`, 'success')
    await fetchBuktiTerbaru()
  } catch (err) {
    console.error('Gagal memverifikasi bukti:', err)
    showToast('Terjadi kesalahan saat memverifikasi bukti piket.', 'error')
  } finally {
    verifyingId.value = null
    verifyStatus.value = ''
  }
}

const submitReject = async () => {
  if (!rejectingId.value || !rejectReason.value.trim()) return
  submittingReject.value = true
  await verifyBukti(rejectingId.value, 'rejected', rejectReason.value)
  submittingReject.value = false
  closeRejectModal()
}


const showDetailAlert = (sectionName) => {
  if (sectionName === 'Data Sanksi') {
    router.push('/admin/sanksi')
  } else if (sectionName === 'Data Siswa') {
    router.push('/admin/siswa')
  } else {
    console.log(`Navigating to ${sectionName}`)
  }
}

onMounted(() => {
  updateWibClock()
  timer = setInterval(updateWibClock, 1000)
  fetchTotalTasks()
  fetchActiveSanksi()
  fetchTotalSiswa()
  fetchJadwalDashboard()
  fetchBuktiTerbaru()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
