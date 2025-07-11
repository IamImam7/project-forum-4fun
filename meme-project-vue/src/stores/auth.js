// src/stores/auth.js
import { defineStore } from 'pinia';
import axios from 'axios';

const apiUrl = import.meta.env.VITE_API_URL;

export const useAuthStore = defineStore('auth', {
  state: () => ({
     user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(credentials) {
      try {
        const response = await axios.post(`${apiUrl}/api/login`, credentials);
        const { access_token, user } = response.data;

        this.token = access_token;
        this.user = user;
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));

        // Atur header default Axios untuk request selanjutnya
        axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;

      } catch (error) {
        // Tangani error, misalnya tampilkan notifikasi
        console.error('Login failed:', error);
        throw error;
      }
    },
    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    },
     setUser(user) {
    this.user = user;
    localStorage.setItem('user', JSON.stringify(user));
  },
  },
});
