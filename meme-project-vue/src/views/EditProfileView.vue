<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const authStore = useAuthStore();
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;

// State untuk form utama
const profileForm = ref({
  name: '',
  email: '',
});

// State untuk form ganti password
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const errors = ref({});
const isSubmitting = ref(false);

onMounted(() => {
  if (authStore.user) {
    profileForm.value.name = authStore.user.name;
    profileForm.value.email = authStore.user.email;
  }
});

const handleProfileUpdate = async () => {
  isSubmitting.value = true;
  errors.value = {};
  try {
    const response = await axios.put(`${apiUrl}/api/user/profile`, profileForm.value);
    authStore.setUser(response.data);
    toast.success('Profil berhasil diperbarui!');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      toast.error('Terjadi kesalahan saat memperbarui profil.');
    }
  } finally {
    isSubmitting.value = false;
  }
};

const handlePasswordUpdate = async () => {
  isSubmitting.value = true;
  errors.value = {};
  try {
    const payload = { ...profileForm.value, ...passwordForm.value };
    await axios.put(`${apiUrl}/api/user/profile`, payload);
    toast.success('Password berhasil diubah!');
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
  } catch (error) {
    if (error.response && error.response.status === 422) {
      if (error.response.data.message) {
        toast.error(error.response.data.message);
      } else {
        errors.value = error.response.data.errors;
      }
    } else {
      toast.error('Terjadi kesalahan saat mengubah password.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="edit-profile-container">
    <div class="profile-card">
      <h1 class="title">Edit Your Profile</h1>

      <form @submit.prevent="handleProfileUpdate" class="profile-form">
        <div class="form-group">
          <label for="name">Nama</label>
          <input type="text" id="name" v-model="profileForm.name" class="form-input" />
          <div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" v-model="profileForm.email" class="form-input" />
          <div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div>
        </div>
        <button type="submit" class="submit-button" :disabled="isSubmitting">
          {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </form>

      <hr class="divider" />

      <h2 class="subtitle">Ganti Password</h2>
      <form @submit.prevent="handlePasswordUpdate" class="password-form">
        <div class="form-group">
          <label for="current_password">Password Saat Ini</label>
          <input type="password" id="current_password" v-model="passwordForm.current_password" class="form-input" />
          <div v-if="errors.current_password" class="error-message">{{ errors.current_password[0] }}</div>
        </div>
        <div class="form-group">
          <label for="password">Password Baru</label>
          <input type="password" id="password" v-model="passwordForm.password" class="form-input" />
          <div v-if="errors.password" class="error-message">{{ errors.password[0] }}</div>
        </div>
        <div class="form-group">
          <label for="password_confirmation">Konfirmasi Password Baru</label>
          <input type="password" id="password_confirmation" v-model="passwordForm.password_confirmation" class="form-input" />
        </div>
        <button type="submit" class="submit-button secondary" :disabled="isSubmitting">
          {{ isSubmitting ? 'Menyimpan...' : 'Ubah Password' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.edit-profile-container {
  padding: 2rem 1rem;
}
.profile-card {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #252734; /* Warna kartu */
  border-radius: 12px;
  border: 1px solid #4D4D4D; /* Slate Grey */
}
.title {
  font-size: 1.8rem; text-align: center;
  color: #FF71CE; /* Accent 1: Pink */
  margin-bottom: 0.5rem;
}
.subtitle {
  font-size: 1.2rem; font-weight: 500;
  margin-bottom: 1.5rem; color: #EAEAEA;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #4D4D4D;
}
.form-group {
  margin-bottom: 1.5rem;
}
label {
  display: block; margin-bottom: 0.5rem;
  font-weight: 600; color: #CFCFCF; /* Text Soft */
}
.form-input {
  width: 100%; padding: 0.75rem 1rem;
  border: 1px solid #4D4D4D;
  border-radius: 8px; font-size: 1rem;
  background-color: #1F1D2B; /* Background */
  color: #EAEAEA;
  font-family: 'Audiowide', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus {
  outline: none;
  border-color: #01CDFE; /* Accent 2: Blue */
  box-shadow: 0 0 0 3px rgba(1, 205, 254, 0.2);
}
.submit-button {
  width: 100%; padding: 1rem; font-size: 1rem;
  font-weight: 700; color: #1F1D2B;
  background-color: #FF71CE; /* Accent 1: Pink */
  border: none; border-radius: 8px; cursor: pointer;
  transition: background-color 0.2s;
}
.submit-button.secondary {
  background-color: #01CDFE; /* Accent 2: Blue */
}
.submit-button:hover:not(:disabled) {
  filter: brightness(1.1);
}
.submit-button:disabled {
  background-color: #4D4D4D;
  cursor: not-allowed;
}
.divider {
  border: 0;
  height: 1px;
  background-color: #4D4D4D;
  margin: 2.5rem 0;
}
.error-message {
  color: #FF71CE; /* Accent 1: Pink */
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style>
