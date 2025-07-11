<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const router = useRouter();
const toast = useToast();
const apiUrl = import.meta.env.VITE_API_URL;

// --- STATE ---
const form = ref({
  title: '',
  categories: [], // Tetap sebagai array untuk menampung ID
  is_nsfw: false,
  image: null,
});

const allCategories = ref([]);
const errors = ref({});
const imagePreviewUrl = ref(null);
const isSubmitting = ref(false);
const fileInputRef = ref(null);
const showCategoryModal = ref(false); // State untuk kontrol modal

// --- DATA KATEGORI ---
onMounted(async () => {
  allCategories.value = [
    { id: 1, name: 'Random' }, { id: 2, name: 'Trending' }, { id: 3, name: 'Relatable' },
    { id: 4, name: 'Dark Humor' }, { id: 5, name: 'Wholesome' }, { id: 6, name: 'Satire & Ironi' },
    { id: 7, name: 'Shitpost' }, { id: 8, name: 'Dank Meme' }, { id: 9, name: 'Classic Meme' },
    { id: 10, name: 'Template Remix' }, { id: 11, name: 'AI Generated' }, { id: 12, name: 'Anime & Manga' },
    { id: 13, name: 'Game' }, { id: 14, name: 'Movie & Series' }, { id: 15, name: 'K-Pop & J-Pop' },
    { id: 16, name: 'Western Pop Culture' }, { id: 17, name: 'Programmer Meme' }, { id: 18, name: 'Student Life' },
    { id: 19, name: 'Meme Sejarah' }, { id: 20, name: 'Science & Math Meme' }, { id: 21, name: 'Literature / Sastra Meme' }
  ];
});

// --- COMPUTED PROPERTY ---
const selectedCategoriesText = computed(() => {
  if (form.value.categories.length === 0) return 'Pilih Kategori...';
  return allCategories.value
    .filter(cat => form.value.categories.includes(cat.id))
    .map(cat => cat.name)
    .join(', ');
});

// --- FUNGSI-FUNGSI ---
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.value.image = file;
    imagePreviewUrl.value = URL.createObjectURL(file);
  }
};

const triggerFileInput = () => { fileInputRef.value.click(); };

const removeImage = () => {
  form.value.image = null;
  imagePreviewUrl.value = null;
  fileInputRef.value.value = '';
};

const handleSubmit = async () => {
  if (!form.value.image) {
    toast.error('Silakan pilih gambar terlebih dahulu.');
    return;
  }
  if (form.value.categories.length === 0) {
    toast.error('Silakan pilih minimal satu kategori.');
    return;
  }

  isSubmitting.value = true;
  errors.value = {};

  const formData = new FormData();
  formData.append('title', form.value.title);
  formData.append('is_nsfw', form.value.is_nsfw ? 1 : 0);
  formData.append('image', form.value.image);
  form.value.categories.forEach(categoryId => {
    formData.append('categories[]', categoryId);
  });

  try {
    await axios.post(`${apiUrl}/api/posts`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    toast.success('Meme berhasil diunggah!');
    router.push('/');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
      toast.error('Gagal validasi. Periksa kembali isian Anda.');
    } else {
      toast.error('Terjadi kesalahan saat mengunggah.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="submit-page-container">
    <div class="submit-card">
      <div class="card-header">
        <h1 class="title">Submit Your Meme</h1>
        <p class="subtitle">Show the world what you've got!</p>
      </div>

      <form @submit.prevent="handleSubmit" class="submit-form">
        <div class="form-grid">
          <div class="form-fields">
            <div class="form-group">
              <label for="title">Judul Meme</label>
              <input type="text" id="title" v-model="form.title" class="form-input" placeholder="Buat judul yang menggelitik..." />
              <div v-if="errors.title" class="error-message">{{ errors.title[0] }}</div>
            </div>
            <div class="form-group">
              <label>Kategori</label>
              <button type="button" @click="showCategoryModal = true" class="category-selector-btn">
                <span class="selected-categories">{{ selectedCategoriesText }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
              </button>
              <div v-if="errors.categories" class="error-message">{{ errors.categories[0] }}</div>
            </div>
          </div>
          <div class="file-upload-section">
            <div class="form-group">
              <label>Gambar Meme</label>
              <div v-if="!imagePreviewUrl" class="file-input-area" @click="triggerFileInput">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="upload-icon"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <span>Pilih file</span>
              </div>
              <div v-else class="image-preview-container">
                <img :src="imagePreviewUrl" alt="Image Preview" class="image-preview" />
                <button type="button" @click="removeImage" class="remove-image-btn">&times;</button>
              </div>
              <input type="file" ref="fileInputRef" @change="handleFileChange" accept="image/*" class="hidden-input" />
              <div v-if="errors.image" class="error-message">{{ errors.image[0] }}</div>
            </div>
          </div>
        </div>
        <div class="form-group checkbox-group">
          <label class="switch">
            <input type="checkbox" v-model="form.is_nsfw">
            <span class="slider round"></span>
          </label>
          <span>Tandai sebagai konten NSFW (Dewasa)</span>
        </div>
        <button type="submit" class="submit-button" :disabled="isSubmitting">
          {{ isSubmitting ? 'Mengunggah...' : 'Kirim Meme' }}
        </button>
      </form>
    </div>
  </div>

  <teleport to="body">
    <transition name="modal-fade">
      <div v-if="showCategoryModal" class="modal-backdrop" @click.self="showCategoryModal = false">
        <div class="modal-content">
          <header class="modal-header">
            <h3>Pilih Kategori (Maks. 3)</h3>
            <button @click="showCategoryModal = false" class="close-btn">&times;</button>
          </header>
          <section class="modal-body">
            <div class="category-grid">
              <label v-for="cat in allCategories" :key="cat.id" class="category-label">
                <input
                  type="checkbox"
                  :value="cat.id"
                  v-model="form.categories"
                  :disabled="form.categories.length >= 3 && !form.categories.includes(cat.id)"
                  class="hidden-checkbox">
                <span class="category-pill">{{ cat.name }}</span>
              </label>
            </div>
          </section>
          <footer class="modal-footer">
            <button @click="showCategoryModal = false" class="btn-done">Selesai</button>
          </footer>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<style scoped>
/* Latar Belakang & Container Utama */
.submit-page-container { padding: 2rem 1rem; background: #1F1D2B; }
.submit-card { max-width: 800px; margin: 0 auto; background-color: #252734; border: 1px solid #4D4D4D; border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); overflow: hidden; font-family: 'Audiowide', sans-serif; }
.card-header { padding: 1.5rem 2rem; background-color: #1F1D2B; border-bottom: 1px solid #4D4D4D; }
.title { color: #FF71CE; text-shadow: 0 0 8px rgba(255,113,206,0.5); font-size: 1.8rem; font-weight: 700; margin: 0; }
.subtitle { color: #CFCFCF; margin: 0.25rem 0 0 0; }
.submit-form { padding: 2rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 1.5rem; }
.form-group { margin-bottom: 1.5rem; }
.form-group:last-child { margin-bottom: 0; }
label { display: block; margin-bottom: 0.75rem; font-weight: 600; color: #CFCFCF; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
.form-input { width: 100%; padding: 0.8rem 1rem; border: 1px solid #4D4D4D; border-radius: 8px; font-size: 1rem; background-color: #1F1D2B; color: #EAEAEA; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; }
.form-input:focus { outline: none; border-color: #01CDFE; box-shadow: 0 0 0 4px rgba(1, 205, 254, 0.2); }
.error-message { color: #FF71CE; font-size: 0.875rem; margin-top: 0.25rem; }
.file-input-area { border: 2px dashed #4D4D4D; border-radius: 8px; padding: 1.5rem; text-align: center; color: #CFCFCF; cursor: pointer; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.file-input-area:hover { border-color: #01CDFE; background-color: #1F1D2B; }
.upload-icon { color: #888; transition: color 0.3s; }
.file-input-area:hover .upload-icon { color: #01CDFE; }
.hidden-input { display: none; }
.image-preview-container { position: relative; height: 100%; }
.image-preview { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #4D4D4D; }
.remove-image-btn { position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; border: none; background-color: rgba(31, 29, 43, 0.8); color: white; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; }
.checkbox-group { display: flex; align-items: center; gap: 0.75rem; color: #CFCFCF; }
.switch { position: relative; display: inline-block; width: 50px; height: 28px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #4D4D4D; transition: .4s; border-radius: 28px; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #05FFA1; }
input:checked + .slider:before { transform: translateX(22px); }
.submit-button { width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 700; color: #1F1D2B; background-image: linear-gradient(to right, #FF71CE, #ff8eda); border: none; border-radius: 8px; cursor: pointer; font-family: inherit; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255, 113, 206, 0.2); }
.submit-button:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 113, 206, 0.3); }
.submit-button:disabled { background-image: none; background-color: #4D4D4D; cursor: not-allowed; box-shadow: none; color: #888; }
.category-selector-btn { width: 100%; padding: 0.8rem 1rem; border: 1px solid #4D4D4D; border-radius: 8px; background-color: #1F1D2B; color: #CFCFCF; font-family: inherit; font-size: 1rem; text-align: left; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
.selected-categories { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.modal-backdrop { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 2000; background-color: rgba(31, 29, 43, 0.7); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; }
.modal-content { background-color: #252734; border: 1px solid #4D4D4D; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5); width: 90%; max-width: 600px; display: flex; flex-direction: column; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #4D4D4D; }
.modal-header h3 { color: #FF71CE; font-size: 1.25rem; margin: 0; }
.close-btn { background: none; border: none; font-size: 2rem; color: #888; cursor: pointer; }
.modal-body { padding: 1.5rem; max-height: 60vh; overflow-y: auto; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #4D4D4D; text-align: right; }
.btn-done { padding: 0.6rem 1.5rem; background-color: #FF71CE; color: #1F1D2B; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-family: 'Audiowide', sans-serif; }
.category-grid { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hidden-checkbox { display: none; }
.category-pill { display: block; padding: 0.5rem 1rem; border: 1px solid #4D4D4D; border-radius: 20px; cursor: pointer; transition: all 0.2s ease-in-out; color: #CFCFCF; }
.hidden-checkbox:hover + .category-pill { border-color: #01CDFE; color: #01CDFE; }
.hidden-checkbox:checked + .category-pill { background-color: #FF71CE; color: #1F1D2B; border-color: #FF71CE; font-weight: bold; }
.hidden-checkbox:disabled + .category-pill { cursor: not-allowed; background-color: #4D4D4D; color: #888; border-color: #4D4D4D; }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }

@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
  .submit-card { padding: 1rem; }
  .card-header, .submit-form { padding: 1.5rem; }
}
</style>
