<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h3>Editar Chamado</h3>

      <div class="form-group">
        <label><strong>Descrição:</strong></label>
        <p class="descricao">{{ chamado.description }}</p>
      </div>

      <div class="form-group">
        <label>Status:</label>
        <select v-model="edited.status" class="input-select">
          <option value="aberto">Aberto</option>
          <option value="em_andamento">Em Andamento</option>
          <option value="concluido">Concluído</option>
          <option value="recusado">Recusado</option>
        </select>
      </div>

      <div class="form-group">
        <label>Funcionários:</label>
        <div class="scrollbox">
          <div
            class="checkbox-item"
            v-for="func in funcionarios"
            :key="func.id"
          >
            <label>
              <input
                type="checkbox"
                :value="func.id"
                v-model="editedEmployeeIds"
              />
              {{ func.name }}
            </label>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button class="btn-save" @click="save">Salvar</button>
        <button class="btn-cancel" @click="$emit('close')">Cancelar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  chamado: Object,
  funcionarios: Array
})

const emit = defineEmits(['close', 'save'])

const edited = ref({})
const editedEmployeeIds = ref([])

watch(() => props.chamado, (novo) => {
  edited.value = {
    id: novo.id,
    status: novo.status,
    employees: [...(novo.employees || [])]
  }
  editedEmployeeIds.value = edited.value.employees.map(e => Number(e.id))
}, { immediate: true })

const save = () => {
  edited.value.employees = props.funcionarios.filter(func =>
    editedEmployeeIds.value.includes(func.id)
  )
  emit('save', edited.value)
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
  width: 450px;
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
  margin-bottom: 1.5rem;
}

.input-select {
  width: 100%;
  padding: 0.5rem;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.scrollbox {
  max-height: 160px;
  overflow-y: auto;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 0.5rem;
  background-color: #fafafa;
}

.checkbox-item {
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.checkbox-item input {
  margin-right: 0.5rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-save {
  background-color: #38a169;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-cancel {
  background-color: #e53e3e;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.descricao {
  background: #f9f9f9;
  padding: 0.75rem;
  border-radius: 8px;
  max-height: 150px;
  overflow-y: auto;
  font-size: 0.95rem;
  margin-top: 0.5rem;
  white-space: pre-wrap;
}
</style>
