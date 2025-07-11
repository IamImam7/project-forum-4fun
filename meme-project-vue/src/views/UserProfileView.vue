<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import MemeCard from '../components/MemeCard.vue';

const route = useRoute();
const posts = ref([]);
const profileUser = ref(null); // State untuk menyimpan data user dari profil yg dilihat
const pagination = ref({});
const isLoading = ref(true);
const apiUrl = import.meta.env.VITE_API_URL;

// Fungsi untuk memformat tanggal
const joinDate = computed(() => {
  if (!profileUser.value?.created_at) return '';
  const date = new Date(profileUser.value.created_at);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
});

const fetchUserPosts = async (username) => {
  isLoading.value = true;
  const requestUrl = `${apiUrl}/api/users/${username}/posts`;

  try {
    const response = await axios.get(requestUrl);
    // Ambil data user dan posts dari respons baru
    profileUser.value = response.data.user;
    posts.value = response.data.posts.data;
    const { data, ...meta } = response.data.posts;
    pagination.value = meta;
  } catch (error) {
    console.error("Gagal mengambil data postingan user:", error);
    posts.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchUserPosts(route.params.username);
});

// Awasi perubahan username di URL
watch(() => route.params.username, (newUsername) => {
  if (newUsername) {
    fetchUserPosts(newUsername);
  }
});
</script>

<template>
  <div class="user-profile-container">
    <header v-if="profileUser" class="page-header">
      <div class="avatar-container">
        <svg class="avatar-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="50" fill="#4D4D4D"/>
          <path d="M50,15 C63.807,15 75,26.193 75,40 C75,53.807 63.807,65 50,65 C36.193,65 25,53.807 25,40 C25,26.193 36.193,15 50,15 Z M20,85 L80,85 C80,75 60,70 50,70 C40,70 20,75 20,85 Z" fill="#CFCFCF"></path>
        </svg>
      </div>
      <div class="profile-info">
        <h1 class="username">{{ profileUser.name }}</h1>
        <div class="profile-stats">
          <span class="stat-item">
            <strong>{{ pagination.total || 0 }}</strong> Postingan
          </span>
          <span class="separator">|</span>
          <span class="stat-item">
            Bergabung sejak <strong>{{ joinDate }}</strong>
          </span>
        </div>
      </div>
    </header>

   <div v-if="isLoading" class="state-info loading-background-animated">
       <LoadingSpinner />
    </div>

    <div v-else-if="posts && posts.length > 0">
      <div class="posts-list">
        <MemeCard v-for="post in posts" :key="post.id" :post="post" />
        <div class="pagination-controls">
  <button @click="fetchUserPosts(profileUser.name, pagination.prev_page_url)" :disabled="!pagination.prev_page_url">
    &laquo; Sebelumnya
  </button>
  <span class="page-info">
    Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
  </span>
  <button @click="fetchUserPosts(profileUser.name, pagination.next_page_url)" :disabled="!pagination.next_page_url">
    Selanjutnya &raquo;
  </button>
</div>
      </div>
      </div>

    <div v-else class="state-info">
      <p>Pengguna ini belum membuat postingan.</p>
    </div>
  </div>
</template>

<style scoped>
.user-profile-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

/* Header Profil Baru */
.page-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
  padding: 1.5rem;
  background-color: #252734;
  border-radius: 12px;
  border: 1px solid #4D4D4D;
}
.avatar-container {
  flex-shrink: 0;
}
.avatar-svg {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid #01CDFE; /* Accent 2: Blue */
}
.profile-info {
  display: flex;
  flex-direction: column;
  font-family: 'Audiowide', sans-serif;
}
.username {
  font-size: 2.2rem;
  font-weight: 700;
  color: #EAEAEA;
  margin: 0;
}
.profile-stats {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 0.5rem;
  color: #CFCFCF;
 font-family: 'Audiowide', sans-serif;
  font-size: 0.9rem;
}
.stat-item strong {
  color: #05FFA1; /* Accent 3: Green */
  font-weight: 700;
}
.separator {
  color: #4D4D4D;
}
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
  background-color: #4D4D4D;
  color: #EAEAEA;
  border: 1px solid #5a5a5a;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-family: 'Audiowide', sans-serif;
  transition: all .2s;
}
.pagination-controls button:hover:not(:disabled) {
  background-color: #FF71CE;
  color: #1F1D2B;
  transform: translateY(-2px);
  border-color: #FF71CE;
}
.pagination-controls button:disabled {
  background-color: #3a3a42;
  color: #888;
  cursor: not-allowed;
  opacity: 0.5;
}
/* State Info (Loading/Kosong) */
.state-info { text-align: center; padding: 4rem 2rem; background-color: #252734; border-radius: 8px; border: 1px solid #4D4D4D; }
.state-info p { font-size: 1.1rem; color: #CFCFCF; font-weight: 500; margin-bottom: 1rem; }

/* Daftar Post */
.posts-list { display: flex; flex-direction: column; gap: 1.5rem; }
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
