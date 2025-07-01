<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h3>{{ isEdicao ? 'Editar Funcionário' : 'Cadastrar Funcionário' }}</h3>

      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="nome">Nome:</label>
          <input
            id="nome"
            v-model="form.name"
            type="text"
            placeholder="Digite o nome"
            required
          />
        </div>

        <div class="form-group">
          <label for="idade">Idade:</label>
          <input
            id="idade"
            v-model.number="form.age"
            type="number"
            min="18"
            max="100"
            placeholder="Digite a idade"
            required
          />
        </div>

        <div class="form-group">
          <label for="telefone">Telefone:</label>
          <input
            id="telefone"
            v-model="form.phone"
            type="tel"
            placeholder="Digite o telefone"
            required
            pattern="^\+?[0-9\s\-]{8,15}$"
          />
        </div>

        <div class="form-group">
          <label for="email">Email:</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Digite o email"
            required
          />
        </div>

        <div class="modal-actions">
          <button type="submit" class="btn-save">Salvar</button>
          <button type="button" class="btn-cancel" @click="$emit('close')">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'

const props = defineProps({
  funcionario: {
    type: Object,
    default: () => ({ name: '', age: '', phone: '', email: '' })
  }
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
  id: null,
  name: '',
  age: null,
  phone: '',
  email: ''
})

const isEdicao = computed(() => !!form.id)

watch(() => props.funcionario, (novo) => {
  form.id = novo.id || null
  form.name = novo.name || ''
  form.age = novo.age || null
  form.phone = novo.phone || ''
  form.email = novo.email || ''
}, { immediate: true })

const submitForm = () => {
  emit('save', { ...form })
}
</script>


<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  width: 400px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

.modal h3 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1.25rem;
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-group input {
  padding: 0.5rem;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-save {
  background-color: #38a169;
  color: white;
  padding: 0.5rem 1.25rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s ease;
}

.btn-save:hover {
  background-color: #2f855a;
}

.btn-cancel {
  background-color: #e53e3e;
  color: white;
  padding: 0.5rem 1.25rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s ease;
}

.btn-cancel:hover {
  background-color: #c53030;
}
</style>
