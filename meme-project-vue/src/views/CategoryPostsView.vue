<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import MemeCard from '../components/MemeCard.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const route = useRoute();
const posts = ref([]);
const category = ref(null); // <-- State untuk menyimpan detail kategori
const pagination = ref({});
const isLoading = ref(true);
const apiUrl = import.meta.env.VITE_API_URL;

const fetchCategoryPosts = async (slug, url = null) => {
  isLoading.value = true;
  const requestUrl = url || `${apiUrl}/api/categories/${slug}/posts`;

  try {
    const response = await axios.get(requestUrl);
    // Ambil data kategori dan postingan dari respons baru
    category.value = response.data.category;
    posts.value = response.data.posts.data;
    const { data, ...meta } = response.data.posts;
    pagination.value = meta;
  } catch (error) {
    console.error("Gagal mengambil data postingan kategori:", error);
    posts.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchCategoryPosts(route.params.slug);
});

watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    fetchCategoryPosts(newSlug);
  }
});
</script>

<template>
  <div class="category-posts-container">
    <header v-if="category" class="page-header">
      <div class="header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
      </div>
      <div class="header-info">
        <h1 class="page-title">{{ category.name }}</h1>
        <p class="page-subtitle">{{ pagination.total || 0 }} postingan dalam kategori ini</p>
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
  <button @click="fetchCategoryPosts(category.slug, pagination.prev_page_url)" :disabled="!pagination.prev_page_url">
    &laquo; Sebelumnya
  </button>
  <span class="page-info">
    Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
  </span>
  <button @click="fetchCategoryPosts(category.slug, pagination.next_page_url)" :disabled="!pagination.next_page_url">
    Selanjutnya &raquo;
  </button>
</div>
    </div>

    <div v-else class="state-info">
      <p>Sepertinya belum ada meme di kategori ini.</p>
    </div>
  </div>
</template>

<style scoped>
.category-posts-container {
  max-width: 760px;
  margin: 0 auto;
  font-family: 'Audiowide', sans-serif;
  padding: 2rem 1.5rem;
}

/* Header Halaman Baru */
.page-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
  padding: 1.5rem;
  background-color: #252734; /* Warna kartu */
  border-radius: 12px;
  font-family: 'Audiowide', sans-serif;
  border: 1px solid #4D4D4D; /* Slate Grey */
}
.header-icon {
  flex-shrink: 0;
  padding: 1rem;
  background-color: rgba(5, 255, 161, 0.1); /* Latar ikon dari Accent 3: Green */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.header-icon svg {
  color: #05FFA1; /* Accent 3: Green */
}
.header-info {
  display: flex;
  flex-direction: column;
}
.page-title {
  font-size: 2.2rem;
  font-weight: 700;
  color: #EAEAEA;
  margin: 0;
  font-family: 'Audiowide', sans-serif;
}
.page-subtitle {

  font-size: 0.9rem;
  color: #CFCFCF; /* Text Soft */
  margin-top: 0.25rem;
  font-family: 'Audiowide', sans-serif;
}

/* Sisanya sama seperti halaman MyPostsView */
.state-info { text-align: center; padding: 4rem 2rem; background-color: #252734; border-radius: 8px; border: 1px solid #4D4D4D; }
.state-info p { font-size: 1.1rem; color: #CFCFCF; font-weight: 500; }
.posts-list { display: flex; flex-direction: column; gap: 1.5rem; font-family: 'Audiowide', sans-serif;}
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
