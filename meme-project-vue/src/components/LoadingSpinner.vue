<template>
  <div class="spinner-container">
    <div class="spinner"></div>
    <p class="loading-text">Memuat...</p>
  </div>
</template>

<style scoped>
.spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 5px solid #4D4D4D; /* Warna dasar spinner */
  border-top-color: #FF71CE; /* Warna aksen pink */
  animation: spin 1s linear infinite;
}

.loading-text {
  font-family: 'Audiowide', sans-serif;
  color: #CFCFCF;
  font-size: 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
