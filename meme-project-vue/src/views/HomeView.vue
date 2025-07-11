<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MemeCard from '../components/MemeCard.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const posts = ref([]);
const pagination = ref({});
const isLoading = ref(true);
const activeSort = ref('new');
const apiUrl = import.meta.env.VITE_API_URL;

const fetchPosts = async (url, sortType = 'new') => {
  isLoading.value = true;
  activeSort.value = sortType;

  const requestUrl = new URL(url);
  requestUrl.searchParams.set('sort', sortType);

  try {
    const response = await axios.get(requestUrl.href);
    posts.value = response.data.data;
    const { data, ...meta } = response.data;
    pagination.value = meta;
  } catch (error) {
    console.error("Gagal mengambil data postingan:", error);
    posts.value = [];
  } finally {
    isLoading.value = false;
  }
};

const changeSort = (sortType) => {
  fetchPosts(`${apiUrl}/api/posts`, sortType);
};

onMounted(() => {
  changeSort('new');
});
</script>

<template>
  <div class="home-container">
    <div class="sort-controls">
  <button @click="changeSort('new')" :class="{ active: activeSort === 'new' }">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/></svg>
    <span>New</span>
  </button>
  <button @click="changeSort('hot')" :class="{ active: activeSort === 'hot' }">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4.5c.3-.3.8-.3 1 0l5 5c.3.3.3.8 0 1s-.8.3-1 0l-5-5c-.3-.2-.3-.7 0-1z"/><path d="M14.5 4.5c.3.3.2.8 0 1l-5 5c-.3.3-.8.3-1 0s-.3-.8 0-1l5-5c.2-.3.7-.3 1 0z"/><path d="M3 14.5c.3.3.8.3 1 0l5 5c.3.3.3.8 0 1s-.8.3-1 0l-5-5c-.3-.2-.3-.7 0-1z"/><path d="M3 14.5c.3-.3.2-.8 0-1l-5-5c-.3-.3-.8-.3-1 0s-.3.8 0 1l5 5c.2.3.7.3 1 0z"/></svg>
    <span>Hot</span>
  </button>
  <button @click="changeSort('top')" :class="{ active: activeSort === 'top' }">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
    <span>Top</span>
  </button>
</div>

  <div v-if="isLoading" class="state-info loading-background-animated">
      <LoadingSpinner />
    </div>

    <div v-else-if="posts && posts.length > 0">
      <div class="posts-list">
        <MemeCard v-for="post in posts" :key="post.id" :post="post" />
      </div>
      <div class="pagination-controls">
  <button @click="fetchPosts(pagination.prev_page_url, activeSort)" :disabled="!pagination.prev_page_url">
    &laquo; Sebelumnya
  </button>
  <span class="page-info">
    Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
  </span>
  <button @click="fetchPosts(pagination.next_page_url, activeSort)" :disabled="!pagination.next_page_url">
    Selanjutnya &raquo;
  </button>
</div>
    </div>

    <div v-else class="state-info">
      <p>Yah, belum ada meme di sini. Mungkin Anda bisa jadi yang pertama?</p>
    </div>
  </div>
</template>

<style scoped>
.home-container { max-width: 760px; margin: 0 auto; padding: 2rem 1.5rem; }
.sort-controls {
  display: flex;
  justify-content: center; /* Tambahkan ini untuk menengahkan tombol */
  gap: 0.75rem; /* Sedikit perlebar jarak antar tombol */
  margin-bottom: 2rem;
}

.sort-controls button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  /* flex-grow: 1; <-- HAPUS BARIS INI */
  padding: 0.5rem 1.25rem; /* Perkecil padding agar tombol tidak terlalu tinggi & lebar */
  border: 1px solid #4D4D4D;
  background-color: #3a3a42;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  color: #E0E0E0;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.sort-controls button:hover { border-color: #FF4C29; color: #ffffff; }
.sort-controls button.active {
  background-color: #FF4C29; color: #1E1E24;
  border-color: #FF4C29;
  box-shadow: 0 4px 12px rgba(255, 76, 41, 0.2);
}
.sort-controls button.active svg { stroke: #1E1E24; }
.state-info { text-align: center; padding: 4rem 2rem; background-color: #4D4D4D; border-radius: 8px; border: 1px solid #5a5a5a; }
.state-info p { font-size: 1.1rem; color: #b0b0b0; font-weight: 500; }
.posts-list { display: flex; flex-direction: column; gap: 1.5rem; }
.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2.5rem;
}
.page-info {
  font-family: 'Audiowide', sans-serif;
  font-size: 0.9rem;
  color: #CFCFCF;
  font-weight: 500;
}
.pagination-controls button {
  padding: 0.6rem 1.2rem;
  background-color: transparent;
  color: #CFCFCF;
  border: 2px solid #4D4D4D; /* Slate Grey */
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
  font-family: 'Audiowide', sans-serif;
  transition: all .2s;
}
.pagination-controls button:hover:not(:disabled) {
  background-color: #FF71CE; /* Accent 1: Pink */
  color: #1F1D2B;
  border-color: #FF71CE;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(255, 113, 206, 0.2);
}
.pagination-controls button:disabled {
  border-color: #3a3a42;
  color: #666;
  cursor: not-allowed;
  opacity: 0.5;
}

.loading-background-animated {
  /* Gradien dari warna-warna tema kita */
  background: linear-gradient(-45deg, #1F1D2B, #252734, #FF71CE, #01CDFE);
  background-size: 400% 400%;
  animation: gradientWave 10s ease infinite;
  border: none; /* Hilangkan border agar terlihat lebih mulus */
}

/* Animasi yang menggerakkan posisi gradien */
@keyframes gradientWave {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>
