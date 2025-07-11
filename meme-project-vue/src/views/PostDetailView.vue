<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import MemeCard from '../components/MemeCard.vue';
import CommentItem from '../components/CommentItem.vue';
import ConfirmationModal from '../components/ConfirmationModal.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

// Inisialisasi state dan hook
const showConfirmModal = ref(false);
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;

const post = ref(null);
const comments = ref([]);
const newComment = ref('');
const isLoading = ref(true);

// Fungsi untuk mengambil data post dan komentar dari API
const fetchPostAndComments = async () => {
  const postId = route.params.id;
  isLoading.value = true;
  try {
    const postResponse = await axios.get(`${apiUrl}/api/posts/${postId}`);
    post.value = postResponse.data;

    const commentsResponse = await axios.get(`${apiUrl}/api/posts/${postId}/comments`);
    comments.value = commentsResponse.data;
  } catch (error) {
    console.error("Gagal mengambil data:", error);
    router.push({ name: 'NotFound' });
  } finally {
    isLoading.value = false;
  }
};

// Panggil fungsi fetch saat komponen dimuat
onMounted(fetchPostAndComments);

// Fungsi untuk mengirim komentar utama
const handleCommentSubmit = async () => {
  if (newComment.value.trim() === '') return;
  try {
    const response = await axios.post(`${apiUrl}/api/posts/${post.value.id}/comments`, {
      body: newComment.value,
      parent_id: null
    });
    comments.value.unshift(response.data);
    newComment.value = '';
    toast.success('Komentar berhasil dikirim!');
  } catch (error) {
    toast.error('Gagal mengirim komentar.');
  }
};

// Fungsi untuk menangani balasan baru dari komponen anak (CommentItem)
const handleNewReply = (reply) => {
  const findAndPushReply = (commentList, newReply) => {
    for (const comment of commentList) {
      if (comment.id === newReply.parent_id) {
        if (!comment.replies) {
          comment.replies = [];
        }
        comment.replies.unshift(newReply);
        return true;
      }
      if (comment.replies && findAndPushReply(comment.replies, newReply)) {
        return true;
      }
    }
    return false;
  };
  findAndPushReply(comments.value, reply);
};

// Fungsi untuk meminta konfirmasi hapus (membuka modal)
const handleDeleteRequest = () => {
  showConfirmModal.value = true;
};

// Fungsi untuk menjalankan aksi hapus setelah dikonfirmasi dari modal
const confirmDeletion = async () => {
  showConfirmModal.value = false; // Tutup modal
  try {
    await axios.delete(`${apiUrl}/api/posts/${post.value.id}`);
    toast.success('Meme berhasil dihapus.');
    router.push('/'); // Arahkan kembali ke homepage
  } catch (error) {
    toast.error('Gagal menghapus post. Anda mungkin tidak memiliki izin.');
    console.error("Gagal menghapus post:", error);
  }
};
</script>

<template>
  <main class="detail-container">
   <div v-if="isLoading" class="state-info loading-background-animated">
      <LoadingSpinner />
    </div>

    <div v-else-if="post">
      <div v-if="authStore.isAuthenticated && authStore.user?.id === post.user_id" class="post-actions">
    <button @click="handleDeleteRequest" class="delete-button">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
      <span>Hapus Postingan</span>
    </button>
  </div>
      <MemeCard :post="post" :is-detail-page="true" />

      <div class="comment-section">
        <h3 class="comment-title">Beri Komentar</h3>

        <form v-if="authStore.isAuthenticated" @submit.prevent="handleCommentSubmit" class="comment-form">
          <textarea v-model="newComment" placeholder="Tulis komentar Anda di sini..."></textarea>
          <button type="submit">Kirim Komentar</button>
        </form>
        <div v-else class="login-prompt">
          <p><RouterLink to="/login" class="link">Login</RouterLink> untuk bisa berkomentar.</p>
        </div>

        <hr class="divider" />

        <div class="comments-list">
          <CommentItem
            v-for="comment in comments"
            :key="comment.id"
            :comment="comment"
            :post-id="post.id"
            @reply-submitted="handleNewReply"
          />
          <div v-if="comments.length === 0" class="state-info-small">
            <p>Jadilah yang pertama berkomentar!</p>
          </div>
        </div>
      </div>
    </div>
    <ConfirmationModal
      v-if="showConfirmModal"
      title="Konfirmasi Penghapusan"
      message="Sekali Dihapus gabisa balik lhoo..."
      confirmText="Ya, Hapus"
      @confirm="confirmDeletion"
      @close="showConfirmModal = false"
    />
  </main>
</template>

<style scoped>
.detail-container {
  max-width: 800px; /* Sedikit lebih lebar untuk menampung kartu dan komentar */
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

.comment-section {
  margin-top: 2rem;
  background-color: #252734; /* Warna kartu */
  padding: 1.5rem 2rem;
  border-radius: 12px;
  font-family: 'Audiowide', sans-serif;
  border: 1px solid #4D4D4D; /* Slate Grey */
}

.comment-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #EAEAEA;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #4D4D4D;
}

.comment-form textarea {
  width: 100%;
  min-height: 100px;
  padding: 1rem;
  border: 1px solid #4D4D4D;
  border-radius: 8px;
  background-color: #1F1D2B; /* Background */
  color: #EAEAEA;
  font-family: 'Audiowide', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.comment-form textarea:focus {
  outline: none;
  border-color: #01CDFE; /* Accent 2: Blue */
  box-shadow: 0 0 0 3px rgba(1, 205, 254, 0.2);
}

.comment-form button {
  display: block;
  margin-top: 1rem;
  margin-left: auto; /* Posisikan ke kanan */
  padding: 0.75rem 1.5rem;
  background-color: #FF71CE; /* Accent 1: Pink */
  color: #1F1D2B;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
  font-family: 'Audiowide', sans-serif;
  transition: background-color 0.2s;
}
.comment-form button:hover {
  background-color: #ff8eda;
}

.login-prompt {
  text-align: center;
  padding: 1rem;
  background-color: rgba(77, 77, 77, 0.2);
  border-radius: 8px;
}
.login-prompt .link {
  color: #05FFA1; /* Accent 3: Green */
  font-weight: 700;
  text-decoration: none;
}

.divider {
  border: 0;
  height: 1px;
  background-color: #4D4D4D;
  margin: 2rem 0;
}

.state-info, .state-info-small {
  text-align: center;
  padding: 3rem 1rem;
}
.state-info p, .state-info-small p {
  font-size: 1.1rem;
  color: #CFCFCF;
}

.post-actions {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 1rem;
}

.delete-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #ff0000; /* Slate Grey */
  color: #080005; /* Accent 1: Pink */
  border: 1px solid #a73a3a;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 1000;
  font-family: 'Audiowide', sans-serif;
  transition: all 0.2s;
}

.delete-button:hover {
  background-color: hsl(348, 72%, 21%);
  border: 1px solid #f10202;
  color: #fefefe;
}

.delete-button:hover svg {
  stroke: #ffffff;
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
