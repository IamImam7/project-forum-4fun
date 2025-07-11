import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// 1. Impor Toast dan CSS-nya
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import './assets/main.css'; 

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
// 2. Gunakan Toast dengan beberapa opsi default
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 5,
  newestOnTop: true
});
// --- BAGIAN PENTING ADA DI SINI ---
import { useAuthStore } from './stores/auth'
const authStore = useAuthStore()

// Periksa jika token ada di state (yang diambil dari localStorage)
if (authStore.token) {
  // Atur header otentikasi untuk SEMUA permintaan Axios
  axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
}
// --------------------------------

app.mount('#app')
