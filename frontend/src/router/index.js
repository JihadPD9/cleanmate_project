import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import LandingPage from '@/views/LandingPage.vue'
import LoginPage from '@/views/LoginPage.vue'
import AdminDashboard from '@/views/admin/Dashboard.vue'
import TasksManagement from '@/views/admin/TasksManagement.vue'
import SiswaDashboard from '@/views/siswa/Dashboard.vue'
import SanksiManagement from '@/views/admin/SanksiManagement.vue'
import SiswaManagement from '@/views/admin/SiswaManagement.vue'
import JadwalManagement from '@/views/admin/JadwalManagement.vue'
import BuktiPiketManagement from '@/views/admin/BuktiPiketManagement.vue'
import UploadBuktiPage from '@/views/siswa/UploadBuktiPage.vue'

const routes = [
  {
    path: '/',
    name: 'LandingPage',
    component: LandingPage,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
  },
  {
    path: '/siswa/dashboard',
    name: 'SiswaDashboard',
    component: SiswaDashboard,
    meta: { requiresAuth: true, role: 'siswa' },
  },
  {
    path: '/siswa/upload-bukti',
    name: 'UploadBukti',
    component: UploadBuktiPage,
    meta: { requiresAuth: true, role: 'siswa' },
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/tasks',
    name: 'AdminTasks',
    component: TasksManagement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/sanksi',
    name: 'AdminSanksi',
    component: SanksiManagement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/siswa',
    name: 'AdminSiswa',
    component: SiswaManagement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/jadwal',
    name: 'AdminJadwal',
    component: JadwalManagement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/bukti-piket',
    name: 'AdminBuktiPiket',
    component: BuktiPiketManagement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    // Catch-all fallback route
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation Guard (Vue Router 4 Return Value Syntax)
router.beforeEach((to) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return { name: 'Login', query: { redirect: to.fullPath } }
    }

    if (to.meta.role && authStore.role !== to.meta.role) {
      // Redirect ke dashboard sesuai rolenya jika mencoba akses area berlawanan
      return authStore.role === 'admin' 
        ? { name: 'AdminDashboard' } 
        : { name: 'SiswaDashboard' }
    }
  }

  return true
})

export default router
