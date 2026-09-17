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
        <span class="text-slate-900 font-bold">Inbox</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs text-left">
        <div class="space-y-1">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100/80 border border-emerald-200 text-[#00B775] text-xs font-semibold">
            <Inbox class="w-3.5 h-3.5" />
            <span>Pusat Notifikasi</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Inbox Anda</h1>
          <p class="text-slate-600 text-sm">Lihat pemberitahuan terkait jadwal piket, sanksi, dan info sistem.</p>
        </div>
      </div>

      <!-- Notifications Data List -->
      <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden text-left flex flex-col">
        <!-- Loading -->
        <div v-if="loading" class="p-10 text-center text-slate-500 font-medium flex items-center justify-center space-x-2">
          <svg class="animate-spin h-5 w-5 text-[#00B775]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memuat notifikasi...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="inboxList.length === 0" class="p-14 text-center">
          <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
            <Inbox class="w-8 h-8 text-[#00B775]" />
          </div>
          <p class="text-slate-600 font-bold text-sm">Tidak ada notifikasi baru.</p>
          <p class="text-slate-400 text-xs mt-1">Inbox Anda saat ini kosong.</p>
        </div>

        <!-- List -->
        <div v-else>
          <!-- Ringkasan Status Sanksi Banner jika ada data sanksi -->
          <div v-if="sanksiSummary.total > 0" class="p-4 sm:p-5 bg-slate-900 text-white border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 shrink-0">
                <AlertTriangle class="w-5 h-5" />
              </div>
              <div class="space-y-0.5">
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ringkasan Status Sanksi Anda</p>
                <p class="text-xs sm:text-sm font-bold text-white">
                  <span v-if="sanksiSummary.selesai > 0" class="text-emerald-400">{{ sanksiSummary.selesai }} Sanksi Selesai</span>
                  <span v-if="sanksiSummary.selesai > 0 && sanksiSummary.belum > 0" class="text-slate-400 font-normal"> dan </span>
                  <span v-if="sanksiSummary.belum > 0" class="text-rose-400">{{ sanksiSummary.belum }} Sanksi Belum Selesai</span>
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
              <span v-if="sanksiSummary.selesai > 0" class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-extrabold px-3 py-1 rounded-full">
                {{ sanksiSummary.selesai }} Selesai
              </span>
              <span v-if="sanksiSummary.belum > 0" class="bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[11px] font-extrabold px-3 py-1 rounded-full">
                {{ sanksiSummary.belum }} Belum
              </span>
            </div>
          </div>

          <!-- Action bar -->
          <div v-if="inboxList.some(isUnread)" class="px-5 py-3.5 border-b border-slate-100 flex justify-end bg-slate-50/50">
            <button
              @click="markAllAsRead"
              :disabled="marking"
              class="text-xs font-bold text-[#00B775] hover:text-emerald-700 transition flex items-center space-x-1.5 cursor-pointer disabled:opacity-50"
            >
              <CheckCircle2 class="w-4 h-4" />
              <span>{{ marking ? 'Menandai...' : 'Tandai Semua Dibaca' }}</span>
            </button>
          </div>
          <div class="divide-y divide-slate-100">
            <div
              v-for="(notif, idx) in inboxList"
              :key="notif.id || idx"
              @click="markSingleAsRead(notif)"
              :class="[
                'p-5 transition-all duration-200 group flex flex-col sm:flex-row gap-4 relative cursor-pointer',
                isUnread(notif) ? 'bg-emerald-50/40 hover:bg-emerald-50/60' : 'bg-white hover:bg-slate-50/80'
              ]"
            >
              <!-- Unread dot -->
              <div v-if="isUnread(notif)" class="absolute top-5 left-3 w-2.5 h-2.5 rounded-full bg-[#00B775] ring-4 ring-emerald-100 animate-pulse"></div>
              
              <div class="flex items-start space-x-4 flex-1 pl-3.5 sm:pl-4">
                <div :class="[
                  'w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border shadow-xs',
                  getIconType(notif) === 'sanksi'
                    ? (getStatusSanksi(notif) === 'selesai' ? 'bg-emerald-100 border-emerald-200 text-[#00B775]' : 'bg-rose-100 border-rose-200 text-rose-600')
                    : getIconType(notif) === 'jadwal' ? 'bg-emerald-100 border-emerald-200 text-[#00B775]' : 'bg-slate-100 border-slate-200 text-slate-600'
                ]">
                  <CheckCircle2 v-if="getIconType(notif) === 'sanksi' && getStatusSanksi(notif) === 'selesai'" class="w-5 h-5 stroke-[2.2]" />
                  <AlertTriangle v-else-if="getIconType(notif) === 'sanksi'" class="w-5 h-5 stroke-[2.2]" />
                  <CalendarCheck v-else-if="getIconType(notif) === 'jadwal'" class="w-5 h-5 stroke-[2.2]" />
                  <Info v-else class="w-5 h-5 stroke-[2.2]" />
                </div>
                
                <div class="space-y-2 w-full">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                    <div class="flex items-center space-x-2">
                      <p :class="['font-extrabold text-base tracking-tight', isUnread(notif) ? 'text-slate-900' : 'text-slate-700']">
                        {{ getTitle(notif) }}
                      </p>
                      <span v-if="isUnread(notif)" class="text-[10px] font-extrabold uppercase bg-emerald-100 text-[#00B775] px-2 py-0.5 rounded-md border border-emerald-200">
                        Baru
                      </span>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold whitespace-nowrap">
                      {{ formatDate(notif.created_at || notif.data?.tanggal || notif.tanggal) }}
                    </span>
                  </div>

                  <!-- Message Text -->
                  <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                    {{ getMessage(notif) }}
                  </p>

                  <!-- Extended Sanction Details Box (If Sanction Notification) -->
                  <div v-if="getIconType(notif) === 'sanksi'" :class="[
                    'rounded-2xl p-4 space-y-2 shadow-2xs mt-2 border',
                    getStatusSanksi(notif) === 'selesai' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-white border-rose-200/90'
                  ]">
                    <div :class="['flex items-center justify-between border-b pb-2', getStatusSanksi(notif) === 'selesai' ? 'border-emerald-200' : 'border-rose-100']">
                      <span :class="['text-[10px] font-extrabold uppercase tracking-wider', getStatusSanksi(notif) === 'selesai' ? 'text-emerald-700' : 'text-rose-500']">Status Sanksi:</span>
                      <span v-if="getStatusSanksi(notif) === 'selesai'" class="inline-flex items-center space-x-1 bg-emerald-100 text-[#00B775] border border-emerald-300 px-3 py-1 rounded-full text-xs font-extrabold shadow-2xs">
                        <CheckCircle2 class="w-3.5 h-3.5" />
                        <span>Selesai</span>
                      </span>
                      <span v-else class="inline-flex items-center space-x-1 bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1 rounded-full text-xs font-extrabold shadow-2xs">
                        <AlertCircle class="w-3.5 h-3.5 text-rose-600" />
                        <span>Belum Selesai</span>
                      </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-1">
                      <div v-if="getNamaSanksi(notif)" class="space-y-0.5">
                        <p :class="['text-[10px] font-extrabold uppercase tracking-wider', getStatusSanksi(notif) === 'selesai' ? 'text-emerald-700' : 'text-rose-500']">Nama Master Sanksi:</p>
                        <p class="font-bold text-slate-900">{{ getNamaSanksi(notif) }}</p>
                      </div>
                      <div v-if="getTipeSanksi(notif)" class="space-y-0.5">
                        <p :class="['text-[10px] font-extrabold uppercase tracking-wider', getStatusSanksi(notif) === 'selesai' ? 'text-emerald-700' : 'text-rose-500']">Tipe Sanksi:</p>
                        <p class="font-bold text-slate-900 capitalize">{{ getTipeSanksi(notif) }}</p>
                      </div>
                    </div>
                    <div v-if="getAlasanSanksi(notif)" :class="['pt-2 border-t space-y-0.5', getStatusSanksi(notif) === 'selesai' ? 'border-emerald-200' : 'border-rose-100']">
                      <p :class="['text-[10px] font-extrabold uppercase tracking-wider', getStatusSanksi(notif) === 'selesai' ? 'text-emerald-700' : 'text-rose-500']">Alasan Sanksi:</p>
                      <p class="font-semibold text-slate-700 italic">"{{ getAlasanSanksi(notif) }}"</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import SiswaNavbar from '@/components/SiswaNavbar.vue'
import { Inbox, Home, ChevronRight, AlertTriangle, CalendarCheck, Info, CheckCircle2, AlertCircle, X } from 'lucide-vue-next'
import api from '@/utils/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const inboxList = ref([])
const mySanksiList = ref([])
const loading = ref(false)
const marking = ref(false)

const toast = ref({ show: false, message: '', type: 'success' })
const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3500)
}

const sanksiSummary = computed(() => {
  const list = mySanksiList.value
  const selesai = list.filter(s => (s.status_penyelesaian || s.status || '').toLowerCase() === 'selesai').length
  const belum = list.filter(s => (s.status_penyelesaian || s.status || '').toLowerCase() !== 'selesai').length
  return { selesai, belum, total: list.length }
})

const isUnread = (notif) => {
  if (!notif) return false
  if (notif.is_read === true) return false
  if (notif.read_at !== null && notif.read_at !== undefined) return false
  return true
}

const getTitle = (notif) => {
  return notif.data?.judul || notif.data?.nama_sanksi || notif.data?.title || notif.judul || notif.title || 'Pemberitahuan Sistem'
}

const getMessage = (notif) => {
  return notif.data?.pesan || notif.data?.message || notif.pesan || notif.message || '-'
}

const getNamaSanksi = (notif) => {
  return notif.data?.nama_sanksi || notif.nama_sanksi || null
}

const getTipeSanksi = (notif) => {
  if (notif.data?.tipe_sanksi) return notif.data.tipe_sanksi
  const pesan = notif.data?.pesan || ''
  if (pesan.toLowerCase().includes('(individu)')) return 'Individu'
  if (pesan.toLowerCase().includes('(kelompok)')) return 'Kelompok'
  return null
}

const getAlasanSanksi = (notif) => {
  if (notif.data?.alasan) return notif.data.alasan
  const pesan = notif.data?.pesan || ''
  if (pesan.includes('karena: ')) {
    return pesan.split('karena: ')[1].replace(/\.$/, '')
  }
  return null
}

const getStatusSanksi = (notif) => {
  const notifAlasan = (getAlasanSanksi(notif) || '').toLowerCase().trim()
  const notifNama = (getNamaSanksi(notif) || '').toLowerCase().trim()

  // Match dengan real data sanksi siswa
  if (mySanksiList.value.length > 0) {
    const matched = mySanksiList.value.find(s => {
      const sAlasan = (s.alasan || '').toLowerCase().trim()
      const sNama = (s.masterSanksi?.nama_sanksi || s.sanksi?.nama_sanksi || '').toLowerCase().trim()
      
      if (notifAlasan && sAlasan && (notifAlasan === sAlasan || sAlasan.includes(notifAlasan) || notifAlasan.includes(sAlasan))) {
        return true
      }
      if (notifNama && sNama && (notifNama === sNama || sNama.includes(notifNama))) {
        return true
      }
      return false
    })

    if (matched) {
      const st = (matched.status_penyelesaian || matched.status || '').toLowerCase()
      return st === 'selesai' ? 'selesai' : 'belum_selesai'
    }
  }

  // Fallback match ke data payload notifikasi jika backend menyertakannya
  const statusRaw = (notif.data?.status_penyelesaian || notif.data?.status_sanksi || notif.data?.status || notif.status_sanksi || '').toLowerCase()
  if (['selesai', 'completed', 'approved', 'disetujui'].includes(statusRaw)) return 'selesai'

  const textCombined = (getTitle(notif) + ' ' + getMessage(notif)).toLowerCase()
  if (textCombined.includes('selesai') || textCombined.includes('diselesaikan') || textCombined.includes('bebas sanksi') || textCombined.includes('tuntas') || textCombined.includes('disetujui')) {
    return 'selesai'
  }

  return 'belum_selesai'
}

const fetchRealSanksiSiswa = async () => {
  try {
    // Gunakan endpoint khusus siswa /api/siswa/sanksi-siswa (bukan /admin/ yang kena 403)
    const res = await api.get('/siswa/sanksi-siswa')
    const data = res.data?.data || (Array.isArray(res.data) ? res.data : [])
    mySanksiList.value = data
  } catch (err) {
    try {
      const resAlt = await api.get('/admin/sanksi-siswa')
      const all = resAlt.data?.data || (Array.isArray(resAlt.data) ? resAlt.data : [])
      const uid = authStore.user?.id
      const sName = authStore.user?.name?.toLowerCase()
      mySanksiList.value = all.filter(item => {
        const itemUid = item.user_id || item.siswa_id || item.user?.id || item.siswa?.id || item.id
        if (uid && itemUid && Number(uid) === Number(itemUid)) return true
        const itemNama = (item.siswa?.name || item.user?.name || item.name || '').toLowerCase()
        if (sName && itemNama && itemNama.includes(sName)) return true
        return false
      })
    } catch (errAlt) {
      console.warn('Gagal memuat sanksi-siswa real:', err)
      mySanksiList.value = []
    }
  }
}

const loadNotifications = async () => {
  loading.value = true
  try {
    await fetchRealSanksiSiswa()
    const res = await api.get('/siswa/notifications')
    const historyData = res.data?.data || (Array.isArray(res.data) ? res.data : [])
    
    // Deduplikasi notifikasi sanksi ganda (karena backend mengirim notifikasi ulang saat status berubah)
    const uniqueNotifications = []
    const seenSanksiKeys = new Set()

    historyData.forEach((notif) => {
      if (getIconType(notif) === 'sanksi') {
        const key = notif.data?.sanksi_siswa_id || notif.data?.sanksi_id || `${getNamaSanksi(notif)}_${getAlasanSanksi(notif)}`
        if (key && !seenSanksiKeys.has(key)) {
          seenSanksiKeys.add(key)
          uniqueNotifications.push(notif)
        } else if (!key) {
          uniqueNotifications.push(notif)
        }
      } else {
        uniqueNotifications.push(notif)
      }
    })

    inboxList.value = uniqueNotifications

    // Tampilkan notifikasi ringkasan jika sanksi berubah / ada data
    if (sanksiSummary.value.total > 0) {
      const { selesai, belum } = sanksiSummary.value
      setTimeout(() => {
        if (selesai > 0 && belum > 0) {
          showToast(`Status Sanksi: ${selesai} sanksi selesai dan ${belum} sanksi belum selesai.`, 'info')
        } else if (selesai > 0 && belum === 0) {
          showToast(`Selamat! Semua (${selesai}) sanksi Anda telah diselesaikan oleh Admin.`, 'success')
        } else if (belum > 0) {
          showToast(`Perhatian: Anda memiliki ${belum} sanksi yang belum diselesaikan.`, 'error')
        }
      }, 500)
    }
  } catch (err) {
    console.error('Gagal mengambil notifikasi', err)
  } finally {
    loading.value = false
  }
}

const markSingleAsRead = async (notif) => {
  if (!isUnread(notif)) return
  try {
    await api.patch(`/siswa/notifications/${notif.id}/read`)
    notif.read_at = new Date().toISOString()
    notif.is_read = true
    window.dispatchEvent(new CustomEvent('notifications-updated'))
  } catch (err) {
    console.error('Gagal menandai notifikasi dibaca:', err)
  }
}

const markAllAsRead = async () => {
  const unreadItems = inboxList.value.filter(isUnread)
  if (unreadItems.length === 0) return

  marking.value = true
  try {
    await api.post('/siswa/notifications/read-all')
    const nowIso = new Date().toISOString()
    inboxList.value.forEach(n => {
      n.read_at = nowIso
      n.is_read = true
    })
    window.dispatchEvent(new CustomEvent('notifications-updated'))
    showToast('Semua notifikasi ditandai sudah dibaca', 'success')
  } catch (err) {
    console.error('Gagal menandai dibaca', err)
    showToast('Gagal menandai notifikasi.', 'error')
  } finally {
    marking.value = false
  }
}

const getIconType = (notif) => {
  const typeStr = (notif.type || '').toLowerCase()
  const titleStr = (getTitle(notif) || '').toLowerCase()
  if (typeStr.includes('sanksi') || titleStr.includes('sanksi')) return 'sanksi'
  if (typeStr.includes('jadwal') || titleStr.includes('jadwal') || titleStr.includes('piket')) return 'jadwal'
  return 'system'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return dateString
  return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(date) + ' WIB'
}

onMounted(() => {
  loadNotifications()
})
</script>
