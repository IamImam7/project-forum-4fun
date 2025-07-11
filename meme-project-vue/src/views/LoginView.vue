<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  email: '',
  password: '',
});

const isSubmitting = ref(false);
const errorMessage = ref(''); // State baru untuk pesan error

const handleLogin = async () => {
  isSubmitting.value = true;
  errorMessage.value = ''; // Reset pesan error setiap kali submit

  try {
    await authStore.login(form.value);
    router.push('/');
  } catch (error) {
    // Tampilkan pesan error dari server jika ada, jika tidak, tampilkan pesan umum
    if (error.response && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <div class="card-header">
        <h1 class="title">Selamat Datang Kembali!</h1>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
          <input type="email" v-model="form.email" class="form-input" placeholder="Email" required />
        </div>

        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <input type="password" v-model="form.password" class="form-input" placeholder="Password" required />
        </div>

        <button type="submit" class="submit-button" :disabled="isSubmitting">
          {{ isSubmitting ? 'Loading...' : 'Login' }}
        </button>
      </form>

      <div class="card-footer">
        <p>Belum punya akun? <RouterLink to="/register" class="link">Daftar di sini</RouterLink></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {

  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 64px - 61px);
  padding: 2rem 1rem;
  background-color: #1F1D2B;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background-color: #252734;
  border-radius: 16px;
  border: 1px solid #4D4D4D;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  /* PERBAIKAN 1: Terapkan font ke seluruh kartu */
  font-family: 'Audiowide', sans-serif;
}

.card-header {
  padding: 2rem 2rem 1.5rem;
  text-align: center;
  border-bottom: 1px solid #4D4D4D;
}
.title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #FF71CE;
  text-shadow: 0 0 8px rgba(255, 113, 206, 0.5);
  margin: 0;
}
.subtitle {
  color: #CFCFCF;
  margin: 0.25rem 0 0 0;
  /* Gunakan font yang lebih mudah dibaca untuk subtitle */
  font-family: sans-serif;
}

.login-form {
  padding: 2rem;
}

.error-message {
  background-color: rgba(255, 76, 41, 0.1);
  color: #FF71CE;
  border: 1px solid #FF4C29;
  padding: 0.75rem 1.25rem;
  margin-bottom: 1rem;
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
}

.input-group {
  position: relative;
  margin-bottom: 1.5rem;
}
.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
}
.form-input {
  box-sizing: border-box;
  width: 100%;
  padding: 0.8rem 1rem 0.8rem 3rem;
  border: 1px solid #4D4D4D;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #1F1D2B;
  color: #EAEAEA;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus {
  outline: none;
  border-color: #01CDFE;
  box-shadow: 0 0 0 4px rgba(1, 205, 254, 0.2);
}
.form-input:focus ~ .input-icon {
  color: #01CDFE;
}

.submit-button {
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1F1D2B;
  background-color: #FF71CE;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  /* PERBAIKAN 3: Pastikan tombol mewarisi font */
  font-family: inherit;
  transition: all 0.3s;
}
.submit-button:hover:not(:disabled) {
  filter: brightness(1.1);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 113, 206, 0.2);
}
.submit-button:disabled {
  background-color: #4D4D4D;
  color: #888;
  cursor: not-allowed;
}

.card-footer {
  padding: 1.5rem;
  text-align: center;
  font-size: 0.9rem;
  background-color: #1F1D2B;
  border-top: 1px solid #4D4D4D;
   font-family: 'Audiowide', sans-serif; /* Gunakan font biasa untuk keterbacaan */
}
.link {
  color: #05FFA1;
  font-weight: 600;
  text-decoration: none;
  font-family: inherit; /* Warisi font Audiowide dari body */
}
.link:hover {
  text-decoration: underline;
}
</style>
