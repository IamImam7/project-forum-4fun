<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const isOpen = ref(false);
const dropdownRef = ref(null);

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};

// Fungsi untuk menutup dropdown saat klik di luar area dropdown
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
  <div class="profile-dropdown" ref="dropdownRef">
    <button @click="isOpen = !isOpen" class="dropdown-toggle">
      <span class="user-name">Halo, {{ authStore.user?.name }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>

    <transition name="fade">
      <div v-if="isOpen" class="dropdown-menu">
        <RouterLink to="/profile/edit" class="dropdown-item" @click="isOpen = false">
          Edit Profil
        </RouterLink>
        <a href="#" @click.prevent="handleLogout" class="dropdown-item logout">
          Logout
        </a>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.profile-dropdown {
  position: relative;
}
.dropdown-toggle {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #f1f1f1;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 600;
}
.chevron-down {
  transition: transform 0.2s;
}
.profile-dropdown[aria-expanded="true"] .chevron-down {
  transform: rotate(180deg);
}
.dropdown-menu {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  min-width: 180px;
  padding: 0.5rem 0;
  z-index: 100;
  border: 1px solid #eee;
}
.dropdown-item {
  display: block;
  padding: 0.75rem 1.5rem;
  color: #333;
  text-decoration: none;
  font-size: 0.9rem;
}
.dropdown-item:hover {
  background-color: #f7f7f7;
}
.dropdown-item.logout {
  color: #e53e3e;
}
/* Transisi Fade */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
