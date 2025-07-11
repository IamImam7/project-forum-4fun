<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useToast } from "vue-toastification";
import axios from 'axios';

// Di dalam <script setup> di MemeCard.vue
const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
  // TAMBAHKAN PROP BARU INI
  isDetailPage: {
    type: Boolean,
    default: false, // Nilai defaultnya false
  },
});
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;
const score = ref(parseInt(props.post.vote_score || 0));
const userVote = ref(props.post.user_vote?.type || 0);
const isBlurred = ref(props.post.is_nsfw);

function revealImage() {
  if (isBlurred.value) isBlurred.value = false;
}

const handleVote = async (newVoteType) => {
  const oldVote = userVote.value;
  const oldScore = score.value;
  let scoreModifier = 0;
  if (oldVote === newVoteType) { userVote.value = 0; scoreModifier = -newVoteType; }
  else if (oldVote !== 0) { userVote.value = newVoteType; scoreModifier = 2 * newVoteType; }
  else { userVote.value = newVoteType; scoreModifier = newVoteType; }
  score.value += scoreModifier;
  try {
    const type = newVoteType === 1 ? 'up' : 'down';
    await axios.post(`${apiUrl}/api/posts/${props.post.id}/vote`, { type });
    toast.success("Vote Anda berhasil disimpan!");
  } catch (error) {
    userVote.value = oldVote; score.value = oldScore;
    toast.error("Gagal vote. Anda harus login.");
  }
};


</script>

<template>
  <div class="meme-card">
    <div class="vote-section">
      <button @click.stop="handleVote(1)" class="vote-btn" :class="{ 'voted-up': userVote === 1 }">▲</button>
<span class="score" :class="{ 'score-up': userVote === 1, 'score-down': userVote === -1 }">{{ score }}</span>
<button @click.stop="handleVote(-1)" class="vote-btn" :class="{ 'voted-down': userVote === -1 }">▼</button>
    </div>
    <div class="content-section">
      <div class="card-header">
      <div class="meta-left">
  <RouterLink
    v-for="category in post.categories"
    :key="category.id"
    :to="{ name: 'category.posts', params: { slug: category.slug } }"
    class="category-link"
  >
    {{ category.name }}
  </RouterLink>

  <span v-if="post.is_nsfw" class="nsfw-tag">NSFW</span>
</div>
        <span class="separator">&bull;</span>
        <span class="author">
          Diposting oleh
          <RouterLink :to="{ name: 'user.posts', params: { username: post.user.name } }" class="author-link" @click.stop>{{ post.user.name }}</RouterLink>
        </span>
      </div>
      <RouterLink :to="{ name: 'post.detail', params: { id: post.id } }" class="title-link">
        <h3 class="title">{{ post.title }}</h3>
      </RouterLink>
      <div class="image-container" :class="{'is-clickable': isBlurred}" @click="revealImage">
        <img :src="`${apiUrl}/storage/${post.image_path}`" :alt="post.title" :class="{ blurred: isBlurred }"/>
        <div v-if="isBlurred" class="image-overlay">
          <span class="overlay-button">Lihat Gambar NSFW</span>
        </div>
      </div>
      <div v-if="post.top_comment" class="top-comment-preview">
        <div class="top-comment-body">
          <strong class="top-comment-author">{{ post.top_comment.user.name }}:</strong>
          <span class="top-comment-text">"{{ post.top_comment.body }}"</span>
        </div>
      </div>
      <div class="card-footer">
  <RouterLink
    v-if="!isDetailPage"
    :to="{ name: 'post.detail', params: { id: post.id } }"
    class="comment-link"
  >
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    <span>Lihat Komentar</span>
  </RouterLink>
</div>
    </div>
  </div>
</template>

<style scoped>
.meme-card {
  font-family: 'Audiowide', sans-serif; /* <-- TAMBAHKAN BARIS INI */
  display: flex;
  background-color: #252734;
  border: 1px solid #4D4D4D;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  transition: border-color 0.3s, box-shadow 0.3s;
  overflow: hidden;
}
.meme-card:hover {
  border-color: #FF71CE; /* Accent 1: Pink */
  box-shadow: 0 0 15px rgba(255, 113, 206, 0.1);
}

/* Kolom Voting */
.vote-section {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 8px;
  background-color: #1F1D2B;
  border-right: 1px solid #4D4D4D;
}
.vote-btn {
  border: none; background: none; cursor: pointer;
  font-size: 1.6rem; color: #CFCFCF;
}
.vote-btn.voted-up {
  color: #FF71CE; /* Warna upvote dari tema Anda */
}
.vote-btn.voted-down {
  color: #01CDFE; /* Warna downvote dari tema Anda */
}
.score.score-up { color: #FF71CE; }
.score.score-down { color: #01CDFE; }

/* Kolom Konten */
.content-section {
  flex: 1;
  padding: 12px;
  min-width: 0; /* Trik penting agar flexbox tidak overflow */
}
.card-header {
  display: flex; flex-wrap: wrap; align-items: center;
  gap: 0.75rem; font-size: 0.8rem;
  color: #CFCFCF;
  margin-bottom: 8px;
}
.category { color: #EAEAEA; font-weight: 600; }
.category-link {
  text-decoration: none;
  color: #EAEAEA;
  background-color: #3a3a42;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  transition: background-color 0.2s;
}
.category-link:hover {
  background-color: #4D4D4D;
}
.title-link,
.comment-link {
  text-decoration: none;
}
.nsfw-tag {
  background-color: #FF71CE;
  color: #1F1D2B; font-weight: bold;
  padding: 2px 6px; border-radius: 4px;
}
.author a { color: #05FFA1; text-decoration: none; }
.separator { display: none; }
.title { font-size: 1.4rem; margin: 0 0 12px; color: #ffffff; }

/* === PERBAIKAN UTAMA PADA GAMBAR === */
.image-container {
  position: relative;
  border-radius: 6px;
  overflow: hidden; /* Pastikan overlay tidak keluar dari batas */
  background-color: #1F1D2B;
}
.image-container img {
  display: block;
  /* Pastikan gambar tidak lebih lebar dari containernya */
  max-width: 100%;
  /* Biarkan tinggi menyesuaikan agar tidak terdistorsi */
  height: auto;
  margin: 0 auto; /* Pusatkan gambar jika lebih kecil dari container */
}
img.blurred {
  filter: blur(20px);
}
.image-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  display: flex; justify-content: center; align-items: center;
  background-color: rgba(30, 30, 36, 0.7);
  cursor: pointer;
}
.overlay-button {
  background-color: #FF71CE;
  color: #1F1D2B; font-weight: bold;
  padding: 10px 20px;
  border-radius: 8px; border: none;
}
/* ================================== */

/* Top Comment & Footer */
.top-comment-preview {
  margin-top: 12px; background-color: #1F1D2B;
  border: 1px solid #4D4D4D; border-radius: 6px;
  padding: 12px; font-size: 0.9rem;
}
.top-comment-author { color: #01CDFE; }
.top-comment-text { color: #CFCFCF; }
.card-footer { margin-top: 12px; border-top: 1px solid #4D4D4D; padding-top: 8px; }
.comment-link { color: #CFCFCF; }
.comment-link:hover { color: #05FFA1; }
</style>
