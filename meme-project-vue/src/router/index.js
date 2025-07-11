import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import HomeView from '../views/HomeView.vue';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/post/:id', // URL dinamis dengan parameter id
      name: 'post.detail',
      component: () => import('../views/PostDetailView.vue')
    },
    {
    path: '/my-posts',
    name: 'my-posts',
    component: () => import('../views/MyPostsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile/edit',
    name: 'profile.edit',
    component: () => import('../views/EditProfileView.vue'),
    meta: { requiresAuth: true }
  },
    {
      path: '/submit',
      name: 'submit',
      // Menggunakan lazy loading untuk efisiensi
      component: () => import('../views/SubmitView.vue'),
      // Memberi penanda bahwa route ini memerlukan otentikasi
      meta: { requiresAuth: true }
    },
    {
      path: '/category/:slug',
      name: 'category.posts',
      component: () => import('../views/CategoryPostsView.vue')
    },
     {
      path: '/user/:username',
      name: 'user.posts',
      component: () => import('../views/UserProfileView.vue')
    },
    {
      path: '/search',
      name: 'search',
      component: () => import('../views/SearchView.vue')
    },
    {
      // (Opsional) Rute fallback untuk halaman yang tidak ditemukan
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('../views/NotFoundView.vue') // Anda perlu membuat file NotFoundView.vue
    }
  ]
});

// Navigation Guard
// Kode ini akan berjalan sebelum setiap navigasi halaman
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // Periksa apakah halaman yang dituju memerlukan login dan pengguna belum login
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Jika ya, alihkan ke halaman login
    next({ name: 'login' });
  } else {
    // Jika tidak, biarkan pengguna melanjutkan navigasi
    next();
  }
});

export default router;
