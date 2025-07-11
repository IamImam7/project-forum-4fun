<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import MemeCard from '../components/MemeCard.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const route = useRoute();
const posts = ref([]);
const pagination = ref({});
const isLoading = ref(true);
const searchTerm = ref('');
const apiUrl = import.meta.env.VITE_API_URL;

const fetchSearchResults = async (query, url = null) => {
  if (!query) {
    posts.value = [];
    return;
  }
  isLoading.value = true;
  searchTerm.value = query;

  const requestUrl = url || `${apiUrl}/api/search`;

  try {
    const response = await axios.get(requestUrl, { params: { q: query } });
    posts.value = response.data.data;
    const { data, ...meta } = response.data;
    pagination.value = meta;
  } catch (error) {
    console.error("Gagal melakukan pencarian:", error);
    posts.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchSearchResults(route.query.q);
});

// Awasi perubahan pada query URL
watch(() => route.query.q, (newQuery) => {
  fetchSearchResults(newQuery);
});
</script>

<template>
  <div class="search-container">
    <header class="page-header">
      <div class="header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </div>
      <div class="header-info">
        <h1 class="page-title">
          Hasil Pencarian untuk:
          <span class="search-term">"{{ searchTerm }}"</span>
        </h1>
        <p class="page-subtitle">{{ pagination.total || 0 }} hasil ditemukan</p>
      </div>
    </header>

   <div v-if="isLoading" class="state-info loading-background-animated">
  <LoadingSpinner />
</div>

    <div v-else-if="posts && posts.length > 0">
      <div class="posts-list">
        <MemeCard v-for="post in posts" :key="post.id" :post="post" />
      </div>

      <div class="pagination-controls">
  <button @click="fetchSearchResults(searchTerm, pagination.prev_page_url)" :disabled="!pagination.prev_page_url">
    &laquo; Sebelumnya
  </button>
  <span class="page-info">
    Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
  </span>
  <button @click="fetchSearchResults(searchTerm, pagination.next_page_url)" :disabled="!pagination.next_page_url">
    Selanjutnya &raquo;
  </button>
</div>
    </div>

    <div v-else class="state-info">
      <p>Tidak ada hasil yang ditemukan untuk pencarian Anda.</p>
    </div>
  </div>
</template>

<style scoped>
.search-container {
  max-width: 760px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

/* Header Halaman Baru */
.page-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #4D4D4D; /* Slate Grey */
}
.header-icon {
  flex-shrink: 0;
  color: #05FFA1; /* Accent 3: Green */
}
.header-info {
  display: flex;
  flex-direction: column;
}
.page-title {
  font-size: 2.2rem;
  font-weight: 700;
  color: #EAEAEA; /* Text Primary */
  margin: 0;
  word-break: break-all;
}
.search-term {
  color: #01CDFE; /* Accent 2: Blue */
}
.page-subtitle {
  font-size: 0.9rem;
  color: #CFCFCF; /* Text Soft */
  margin-top: 0.25rem;
  font-family: 'Audiowide', sans-serif;
}

/* State Info (Loading/Kosong) */
.state-info { text-align: center; padding: 4rem 2rem; background-color: #252734; border-radius: 8px; border: 1px solid #4D4D4D; }
.state-info p { font-size: 1.1rem; color: #CFCFCF; font-weight: 500; }

/* Daftar Post & Pagination */
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
  background: linear-gradient(-45deg, #1F1D2B, #252734, #FF71CE, #710172);
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
