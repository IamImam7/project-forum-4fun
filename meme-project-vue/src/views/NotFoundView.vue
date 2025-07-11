<template>
  <div class="not-found-container">
    <h1>404</h1>
    <h2>Halaman Tidak Ditemukan</h2>
    <p>Maaf, halaman yang Anda cari tidak ada atau sudah dipindahkan.</p>
    <RouterLink to="/" class="back-link">Kembali ke Halaman Utama</RouterLink>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>

<style scoped>
.not-found-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  height: 80vh; /* Mengambil sebagian besar tinggi layar */
  color: #2c3e50;
}

h1 {
  font-size: 8rem;
  margin: 0;
  font-weight: 900;
  color: #42b983; /* Warna hijau khas Vue */
}

h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 2rem;
}

p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}

.back-link {
  padding: 10px 20px;
  background-color: #42b983;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
  transition: background-color 0.3s;
}

.back-link:hover {
  background-color: #369b70;
}
</style>
