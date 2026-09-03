<template>
  <div class="h-screen w-full relative font-sans text-slate-800 selection:bg-[#00B775] selection:text-white">
    <!-- 1. Header Sticky / Fixed Across Sections -->
    <header
      class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-white/70 border-b border-slate-200/50 px-6 md:px-12 lg:px-20 py-3.5 transition-all duration-300"
    >
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Pure Text Logo -->
        <router-link to="/" class="text-2xl font-extrabold tracking-tight text-slate-900">
          Clean<span class="text-[#00B775]">Mate</span>
        </router-link>

        <!-- Button Login Header: Tampil hanya saat scroll ke Section 2 -->
        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 translate-y-[-6px] scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 translate-y-0 scale-100"
          leave-to-class="opacity-0 translate-y-[-6px] scale-95"
        >
          <router-link
            v-if="isScrolled"
            to="/login"
            class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#00B775] to-emerald-600 hover:from-[#009d64] hover:to-emerald-700 text-white font-semibold px-5 py-2 rounded-full shadow-md shadow-[#00B775]/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 text-sm"
          >
            <span>Masuk ke Aplikasi</span>
            <ArrowRight class="w-4 h-4" />
          </router-link>
        </transition>
      </div>
    </header>

    <!-- Main Full-Screen Scroll Snap Container -->
    <div
      ref="scrollContainer"
      @scroll="handleScroll"
      class="h-screen w-full overflow-y-auto snap-y snap-mandatory scroll-smooth bg-slate-50 overflow-x-hidden"
    >
      <!-- Section 1: Hero Section (Full Screen Snap) -->
      <section class="h-screen w-full snap-start snap-always relative flex flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 px-6 md:px-12 lg:px-20 pt-20 sm:pt-24 pb-4">
        <!-- Background Ambient Ornaments & Mesh -->
        <div class="absolute -top-28 -right-28 w-[550px] h-[550px] bg-[#00B775]/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute -bottom-24 -left-24 w-[450px] h-[450px] bg-teal-300/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#00B775_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.08] pointer-events-none"></div>

        <!-- Hero Main Content (Left Aligned) -->
        <div class="w-full max-w-7xl mx-auto flex-1 flex flex-col justify-center text-left z-10 my-auto">
          <div class="max-w-3xl space-y-4">
            <!-- Tagline Mini Uppercase Spasi Wide (Top spacing disesuaikan) -->
            <p class="text-xs font-semibold tracking-widest text-[#00B775] uppercase">
              Platform Kebersihan Kelas Modern
            </p>

            <!-- Hero Headline -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight leading-[1.12]">
              Jagalah kebersihan <br class="hidden sm:inline" />
              <span class="bg-gradient-to-r from-[#00B775] via-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Kelas Anda.
              </span>
            </h1>

            <!-- Subtitle / Deskripsi -->
            <p class="text-slate-600 text-base sm:text-lg md:text-xl leading-relaxed max-w-2xl font-normal">
              Kelas bersih, belajar lebih fokus. Kelola jadwal piket, bukti tugas, hingga sanksi secara praktis dan transparan dalam satu platform.
            </p>

            <!-- Primary CTA Button -->
            <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
              <router-link
                to="/login"
                class="inline-flex items-center justify-center space-x-2.5 bg-[#00B775] hover:bg-[#009d64] text-white font-bold text-base px-8 py-3.5 rounded-2xl shadow-xl shadow-[#00B775]/30 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
              >
                <span>Masuk ke Aplikasi</span>
                <ArrowRight class="w-5 h-5" />
              </router-link>
            </div>

            <!-- Quick Highlight Feature Badges -->
            <div class="pt-5 grid grid-cols-1 sm:grid-cols-3 gap-3.5 border-t border-slate-200/80">
              <div class="flex items-center space-x-3 text-slate-700">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0 shadow-xs">
                  <CheckCircle2 class="w-4 h-4" />
                </div>
                <span class="text-sm font-semibold">Fokus Belajar Maksimal</span>
              </div>
              <div class="flex items-center space-x-3 text-slate-700">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0 shadow-xs">
                  <ShieldCheck class="w-4 h-4" />
                </div>
                <span class="text-sm font-semibold">Lingkungan Sehat</span>
              </div>
              <div class="flex items-center space-x-3 text-slate-700">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 border border-emerald-200 flex items-center justify-center text-[#00B775] shrink-0 shadow-xs">
                  <Zap class="w-4 h-4" />
                </div>
                <span class="text-sm font-semibold">Budaya Tanggung Jawab</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="w-full max-w-7xl mx-auto pb-2 flex justify-between items-center text-slate-500 text-xs z-10 font-medium">
          <span>&copy; {{ new Date().getFullYear() }} CleanMate App</span>
          <div class="flex items-center space-x-1.5 animate-bounce">
            <span>Scroll ke bawah</span>
            <svg class="w-4 h-4 text-[#00B775]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
          </div>
        </div>
      </section>

      <!-- Section 2: Features Section / Interactive Carousel (Full Screen Snap) -->
      <section class="h-screen w-full snap-start snap-always relative flex flex-col justify-center items-center overflow-hidden bg-gradient-to-br from-emerald-50/60 via-slate-50 to-emerald-100/40 px-6 md:px-12 lg:px-20 pt-16 pb-6">
        <!-- Background Ambient Blobs & Mesh -->
        <div class="absolute top-10 -left-20 w-[450px] h-[450px] bg-teal-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-20 w-[500px] h-[500px] bg-[#00B775]/20 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#00B775_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.06] pointer-events-none"></div>

        <div class="w-full max-w-4xl mx-auto z-10 flex flex-col justify-center items-center">
          <!-- Section Header -->
          <div class="text-center space-y-2 mb-6 max-w-2xl">
            <p class="text-xs font-semibold tracking-widest text-[#00B775] uppercase">
              MANFAAT UTAMA
            </p>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
              Mengapa Kebersihan Itu Penting
            </h2>
            <p class="text-slate-600 text-base md:text-lg leading-relaxed">
              Lingkungan higienis adalah pilar utama kesehatan, produktivitas, dan integritas profesional.
            </p>
          </div>

          <!-- Glassmorphism Card Container -->
          <div class="w-full relative overflow-hidden backdrop-blur-md bg-white/75 border border-white/80 shadow-xl shadow-emerald-900/5 rounded-3xl p-8 md:p-10 transition-all duration-500 text-left flex flex-col justify-center group mb-6">
            <!-- Background Watermark Icon -->
            <div class="absolute -right-8 -bottom-8 opacity-[0.07] group-hover:opacity-15 transition-opacity duration-500 pointer-events-none">
              <component :is="carouselItems[activeIndex].icon" class="w-64 h-64 text-[#00B775]" />
            </div>

            <div class="flex items-center space-x-4 mb-4 z-10">
              <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#00B775] to-emerald-400 flex items-center justify-center shadow-md shadow-[#00B775]/25 text-white shrink-0">
                <component :is="carouselItems[activeIndex].icon" class="w-6 h-6" />
              </div>
              <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#00B775]">Point 0{{ activeIndex + 1 }}</span>
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                  {{ carouselItems[activeIndex].title }}
                </h3>
              </div>
            </div>

            <p class="text-slate-700 leading-relaxed text-base md:text-lg pl-0 sm:pl-16 z-10 font-normal">
              {{ carouselItems[activeIndex].description }}
            </p>
          </div>

          <!-- Carousel Pagination Dots (Bulat-bulat Interaktif) -->
          <div class="flex justify-center items-center space-x-3">
            <button
              v-for="(item, index) in carouselItems"
              :key="index"
              @click="activeIndex = index"
              :class="[
                'rounded-full transition-all duration-300 focus:outline-none cursor-pointer',
                activeIndex === index
                  ? 'w-10 h-3.5 bg-[#00B775] shadow-md shadow-[#00B775]/40'
                  : 'w-3.5 h-3.5 bg-emerald-200 hover:bg-emerald-400'
              ]"
              :aria-label="'Go to slide ' + (index + 1)"
            ></button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ShieldCheck, CheckCircle2, ArrowRight, Zap, HeartPulse, Award } from 'lucide-vue-next'

const activeIndex = ref(0)
const scrollContainer = ref(null)
const isScrolled = ref(false)

const handleScroll = () => {
  if (scrollContainer.value) {
    isScrolled.value = scrollContainer.value.scrollTop > 200
  }
}

const carouselItems = [
  {
    title: 'Peningkatan Produktivitas',
    description: 'Lingkungan kelas yang bersih dan tertata membantu siswa lebih fokus, mengurangi gangguan, serta meningkatkan efisiensi dan produktivitas secara keseluruhan.',
    icon: Zap
  },
  {
    title: 'Kesehatan Terjaga',
    description: 'Bebas dari debu dan kuman mencegah penyebaran penyakit di lingkungan sekolah, membuat suasana belajar tetap nyaman dan aman.',
    icon: HeartPulse
  },
  {
    title: 'Kedisiplinan & Tanggung Jawab',
    description: 'Menumbuhkan rasa kepemilikan dan tanggung jawab bersama terhadap kebersihan fasilitas kelas yang digunakan setiap hari.',
    icon: Award
  }
]

let timer = null

onMounted(() => {
  timer = setInterval(() => {
    activeIndex.value = (activeIndex.value + 1) % carouselItems.length
  }, 5000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
