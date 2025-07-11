<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from "vue-toastification";
import { useAuthStore } from '@/stores/auth';
// Komponen memanggil dirinya sendiri untuk balasan, jadi kita perlu mengimpornya
import CommentItem from './CommentItem.vue';

const props = defineProps({
  comment: { type: Object, required: true },
  postId: { type: [String, Number], required: true }
});

const emit = defineEmits(['reply-submitted']);

const authStore = useAuthStore();
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;

// State lokal untuk setiap item komentar
const score = ref(props.comment.vote_score || 0);
const userVote = ref(props.comment.user_vote?.type || 0); // Asumsi API mengirimkan vote user
const isHidden = ref(props.comment.is_hidden);
const showReplyForm = ref(false);
const newReply = ref('');

// --- LOGIKA VOTING INTERAKTIF ---
const handleVote = async (newVoteType) => {
  const oldVote = userVote.value;
  const oldScore = score.value;
  let scoreModifier = 0;

  if (oldVote === newVoteType) {
    userVote.value = 0;
    scoreModifier = -newVoteType;
  } else if (oldVote !== 0) {
    userVote.value = newVoteType;
    scoreModifier = 2 * newVoteType;
  } else {
    userVote.value = newVoteType;
    scoreModifier = newVoteType;
  }

  score.value += scoreModifier;

  try {
    const type = newVoteType === 1 ? 'up' : 'down';
    await axios.post(`${apiUrl}/api/comments/${props.comment.id}/vote`, { type });
    toast.success("Vote Anda berhasil disimpan!");
  } catch (error) {
    userVote.value = oldVote;
    score.value = oldScore;
    if (error.response && error.response.status === 401) {
      toast.error('Anda harus login untuk bisa vote!');
    } else {
      toast.error('Terjadi kesalahan saat melakukan vote.');
    }
  }
};

// --- LOGIKA BALAS KOMENTAR ---
const submitReply = async () => {
  if (newReply.value.trim() === '') return;
  try {
    const response = await axios.post(`${apiUrl}/api/posts/${props.postId}/comments`, {
      body: newReply.value,
      parent_id: props.comment.id
    });
    emit('reply-submitted', response.data);
    newReply.value = '';
    showReplyForm.value = false;
    toast.success('Balasan berhasil dikirim!');
  } catch (error) {
    toast.error('Gagal mengirim balasan.');
  }
};
</script>

<template>
  <div class="comment-item">
    <div class="comment-content">
      <div class="comment-header">
        <strong class="author-name">{{ comment.user.name }}</strong>
        <span class="vote-section">
          <button @click="handleVote(1)" class="vote-btn" :class="{ 'voted-up': userVote === 1 }">▲</button>
          <span class="score">{{ score }}</span>
          <button @click="handleVote(-1)" class="vote-btn" :class="{ 'voted-down': userVote === -1 }">▼</button>
        </span>
      </div>

      <div v-if="isHidden" class="hidden-comment">
        <p>Komentar ini disembunyikan karena mungkin menyinggung.</p>
        <button @click="isHidden = false" class="show-btn">Tampilkan Komentar</button>
      </div>
      <p v-else class="comment-body">{{ comment.body }}</p>

      <div v-if="!isHidden && authStore.isAuthenticated" class="comment-actions">
        <button @click="showReplyForm = !showReplyForm" class="reply-btn">
          {{ showReplyForm ? 'Batal' : 'Balas' }}
        </button>
      </div>

      <form v-if="showReplyForm" @submit.prevent="submitReply" class="reply-form">
        <textarea v-model="newReply" placeholder="Tulis balasan Anda..."></textarea>
        <div class="reply-form-actions">
          <button type="submit">Kirim</button>
        </div>
      </form>
    </div>

    <div v-if="comment.replies && comment.replies.length > 0" class="replies-container">
      <CommentItem
        v-for="reply in comment.replies"
        :key="reply.id"
        :comment="reply"
        :post-id="postId"
        @reply-submitted="$emit('reply-submitted', $event)"
      />
    </div>
  </div>
</template>

<style scoped>
/* Container & Konten Utama */
.comment-item { margin-top: 1rem; }
.comment-content {
  background-color: #252734; /* Warna kartu sedikit lebih terang */
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #4D4D4D; /* Slate Grey */
}
.comment-header {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 0.9rem; margin-bottom: 0.75rem;
}
.author-name {
  font-weight: 700;
  color: #01CDFE; /* Accent 2: Blue */
}
.comment-body {
  margin: 0;
  color: #EAEAEA; /* Text Primary */
  line-height: 1.6;
}

/* Bagian Vote */
.vote-section { display: flex; align-items: center; gap: 0.5rem; }
.vote-btn { background: none; border: none; cursor: pointer; color: #CFCFCF; font-size: 1.2rem; }
.vote-btn.voted-up { color: #FF71CE; } /* Accent 1: Pink */
.vote-btn.voted-down { color: #01CDFE; } /* Accent 2: Blue */
.score { font-weight: bold; }
.score.score-up { color: #FF71CE; }
.score.score-down { color: #01CDFE; }

/* Aksi & Form Balasan */
.comment-actions { margin-top: 0.75rem; }
.reply-btn {
  background: none; border: none;
  color: #6f97ee; /* Accent 3: Green */
  cursor: pointer; font-weight: 600; padding: 0; font-size: 0.85rem;
}
.reply-form { margin-top: 1rem; }
.reply-form textarea {
  width: 100%; border-radius: 6px; border: 1px solid #4D4D4D;
  padding: 0.75rem; min-height: 70px;
  background-color: #1F1D2B; /* Background */
  color: #EAEAEA;
}
.reply-form textarea:focus {
  outline: none;
  border-color: #01CDFE;
}
.reply-form-actions { text-align: right; }
.reply-form button {
  margin-top: 0.5rem; font-size: 0.8rem;
  background-color: #FF71CE; /* Accent 1: Pink */
  color: #1F1D2B;
  border: none; padding: 8px 16px;
  border-radius: 6px; cursor: pointer; font-weight: bold;
}

/* Komentar Tersembunyi */
.hidden-comment {
  background-color: rgba(77, 77, 77, 0.2);
  border-left: 4px solid #AE00FF; /* Accent: Glitch Purple */
  padding: 1rem; border-radius: 6px;
}
.hidden-comment p { margin: 0 0 0.5rem 0; font-style: italic; color: #CFCFCF; }
.show-btn {
  font-size: 0.8rem; padding: 4px 12px;
  border-radius: 4px; border: 1px solid #CFCFCF;
  background-color: transparent; color: #CFCFCF;
  cursor: pointer;
}
.show-btn:hover { background-color: #4D4D4D; }

/* Kontainer Balasan (Nested) */
.replies-container {
  margin-top: 1rem;
  margin-left: 1.5rem;
  padding-left: 1.5rem;
  border-left: 2px solid #4D4D4D; /* Slate Grey */
}
</style>
