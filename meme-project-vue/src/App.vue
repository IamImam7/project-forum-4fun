<script setup>
import { ref } from 'vue';
import { RouterView, RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import ProfileDropdown from '@/components/ProfileDropdown.vue';

const authStore = useAuthStore();
const router = useRouter();
const searchQuery = ref('');

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
  }
};
</script>

<template>
  <div id="app-layout">
    <header class="main-header">
      <nav class="navbar-container">

        <div class="nav-section nav-left">
          <RouterLink to="/" class="brand">4Fun</RouterLink>
        </div>

        <div class="nav-section nav-center">
          <form @submit.prevent="handleSearch" class="search-form">
            <input type="search" v-model="searchQuery" placeholder="Cari judul meme..." class="search-input">
            <button type="submit" class="search-button">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
          </form>
        </div>

        <div class="nav-section nav-right">
          <RouterLink to="/" class="nav-link">Home</RouterLink>
          <template v-if="authStore.isAuthenticated">
            <RouterLink to="/my-posts" class="nav-link">Postingan Saya</RouterLink>
            <RouterLink to="/submit" class="nav-link cta-link">Submit</RouterLink>
            <ProfileDropdown />
          </template>
          <template v-else>
            <RouterLink to="/login" class="nav-link">Login</RouterLink>
            <RouterLink to="/register" class="nav-link">Register</RouterLink>
          </template>
        </div>

      </nav>
    </header>

    <main class="main-content">
      <RouterView />
    </main>

    <footer class="main-footer">
      <p>&copy; {{ new Date().getFullYear() }} 4Fun</p>
    </footer>
  </div>
</template>

<style scoped>
#app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  /* PERBAIKAN: Tambahkan warna latar utama di sini */
  background-color: #1F1D2B;
}

.main-header {
  background-color: #252734;
  border-bottom: 1px solid #4D4D4D;
  padding: 0 2rem;
  position: sticky; top: 0; z-index: 1000;
}

.navbar-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

.nav-section { display: flex; align-items: center; }
.nav-left { flex-shrink: 0; }
.nav-center { flex-grow: 1; padding: 0 2rem; }
.nav-right { flex-shrink: 0; gap: 0.75rem; }

.brand {
  font-family: 'Audiowide', sans-serif;
  font-size: 1.6rem;
  font-weight: 700;
  text-decoration: none;
  color: #FF71CE;
  text-shadow: 0 0 5px rgba(255, 113, 206, 0.7);
}

.search-form { position: relative; width: 100%; max-width: 500px; margin: 0 auto; }
.search-input {
  width: 100%;
  padding: 0.6rem 2.5rem 0.6rem 1.2rem;
  border-radius: 20px;
  border: 1px solid #4D4D4D;
  background-color: #1F1D2B;
  color: #EAEAEA;
  font-family: 'Audiowide', sans-serif;
}
.search-input:focus {
  outline: none;
  border-color: #01CDFE;
  box-shadow: 0 0 0 3px rgba(1, 205, 254, 0.2);
}
.search-button {
  position: absolute; right: 5px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; padding: 0.5rem;
  cursor: pointer; color: #888;
}

.nav-link {
  font-family: 'Audiowide', sans-serif;
  color: #CFCFCF;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  white-space: nowrap;
  transition: all 0.2s;
}
.nav-link:hover {
  color: #EAEAEA;
  background-color: #4D4D4D;
}
.nav-link.router-link-exact-active {
  color: #4dd9fc;
}
.cta-link {
  background-color: #970062;
  color: #ffffff;
  font-weight: 700;
}
.cta-link:hover {
  background-color: #bd5098;
  color: #1F1D2B !important;
}

.main-content {
  flex-grow: 1;
  width: 100%;
}

.main-footer {
  text-align: center;
  padding: 1.5rem;
  background-color: #1a1a1f;
  color: #888;
}
</style>
