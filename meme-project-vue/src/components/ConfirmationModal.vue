<script setup>
defineProps({
  title: {
    type: String,
    default: 'Konfirmasi'
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Ya, Lanjutkan'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  }
});

const emit = defineEmits(['confirm', 'close']);
</script>

<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div class="modal-backdrop" @click.self="$emit('close')">
        <div class="modal-content">
          <header class="modal-header">
            <h3>{{ title }}</h3>
            <button @click="$emit('close')" class="close-btn">&times;</button>
          </header>
          <section class="modal-body">
            <p>{{ message }}</p>
          </section>
          <footer class="modal-footer">
            <button @click="$emit('close')" class="btn btn-secondary">{{ cancelText }}</button>
            <button @click="$emit('confirm')" class="btn btn-danger">{{ confirmText }}</button>
          </footer>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 2000;
  background-color: rgba(31, 29, 43, 0.7); /* Background dari tema */
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-content {
  background-color: #252734; /* Warna kartu */
  border: 1px solid #4D4D4D;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
  width: 90%;
  max-width: 450px;
  display: flex;
  flex-direction: column;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #4D4D4D;
}
.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #FF71CE; /* Accent 1: Pink */
}
.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  color: #888;
  cursor: pointer;
}
.modal-body {
  padding: 1.5rem;
  color: #EAEAEA;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background-color: #1F1D2B;
  border-top: 1px solid #4D4D4D;
}
.btn {
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  border: none;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Audiowide', sans-serif;
  transition: filter 0.2s;
}
.btn:hover {
  filter: brightness(1.1);
}
.btn-secondary {
  background-color: #4D4D4D;
  color: #EAEAEA;
}
.btn-danger {
  background-color: #FF4C29; /* Blood Orange untuk aksi bahaya */
  color: #1F1D2B;
}

/* Transisi */
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
</style>
