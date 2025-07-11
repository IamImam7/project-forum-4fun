<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const router = useRouter();
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const errors = ref({});
const isSubmitting = ref(false);

const handleRegister = async () => {
  isSubmitting.value = true;
  errors.value = {};
  try {
    await axios.post(`${apiUrl}/api/register`, form.value);
    toast.success('Registrasi berhasil! Silakan login.');
    router.push('/login');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
      toast.error('Gagal validasi, periksa kembali isian Anda.');
    } else {
      toast.error('Terjadi kesalahan. Silakan coba lagi.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="register-page">
    <div class="register-card">
      <div class="card-header">
        <h1 class="title">Bergabung dengan Forum</h1>
        
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          <input type="text" v-model="form.name" class="form-input" placeholder="Nama" required />
        </div>
        <div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div>

        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
          <input type="email" v-model="form.email" class="form-input" placeholder="Email" required />
        </div>
        <div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div>

        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <input type="password" v-model="form.password" class="form-input" placeholder="Password Baru" required />
        </div>
        <div v-if="errors.password" class="error-message">{{ errors.password[0] }}</div>

        <div class="input-group">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <input type="password" v-model="form.password_confirmation" class="form-input" placeholder="Konfirmasi Password" required />
        </div>

        <button type="submit" class="submit-button" :disabled="isSubmitting">
          {{ isSubmitting ? 'Mendaftar...' : 'Register' }}
        </button>
      </form>

      <div class="card-footer">
        <p>Sudah punya akun? <RouterLink to="/login" class="link">Login di sini</RouterLink></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 64px - 61px);
  padding: 2rem 1rem;
  background-color: #1F1D2B; /* Background */
}
.register-card {
  width: 100%;
  max-width: 480px; /* Sedikit lebih lebar untuk form yang lebih panjang */
  background-color: #252734; /* Warna kartu */
  border-radius: 16px;
  border: 1px solid #4D4D4D; /* Slate Grey */
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  overflow: hidden;
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
  color: #FF71CE; /* Accent 1: Pink */
  text-shadow: 0 0 8px rgba(255, 113, 206, 0.5);
  margin: 0;
}
.subtitle {
  color: #CFCFCF; /* Text Soft */
  margin: 0.25rem 0 0 0;
  font-family: sans-serif; /* Font biasa untuk keterbacaan */
}
.register-form {
  padding: 2rem;
}
.error-message {
  color: #FF71CE; /* Accent 1: Pink */
  font-size: 0.875rem;
  margin-top: -1rem; /* Tarik ke atas agar lebih dekat dengan input */
  margin-bottom: 1rem;
  padding-left: 0.5rem;
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
  border-color: #01CDFE; /* Accent 2: Blue */
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
  background-color: #FF71CE; /* Accent 1: Pink */
  border: none;
  border-radius: 8px;
  cursor: pointer;
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
}
.link {
  color: #05FFA1; /* Accent 3: Green */
  font-weight: 600;
  text-decoration: none;
}
.link:hover {
  text-decoration: underline;
}
</style>
