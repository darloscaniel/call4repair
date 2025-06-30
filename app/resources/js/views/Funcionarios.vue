<template>
  <div class="funcionarios-container">
    <h2 class="titulo">👥 Funcionários</h2>

    <div class="table-wrapper">
      <EasyDataTable
        :headers="headers"
        :items="funcionarios"
        :loading="loading"
        :search-value="search"
        theme-color="#2d89ef"
        table-class-name="customize-table"
        header-text-direction="center"
        body-text-direction="center"
        alternating
        :rows-per-page="rowsPerPage"
        @update:rows-per-page="updateRowsPerPage"
        show-index
      >
        <template #item-actions="{ id }">
          <div class="actions-container">
            <button class="btn-editar" @click="editar(id)">
              <i class="fas fa-edit"></i> Editar
            </button>
            <button class="btn-excluir" @click="excluir(id)">
              <i class="fas fa-trash"></i> Excluir
            </button>
          </div>
        </template>

        <template #item-phone="{ phone }">
          <span class="phone-number">
            {{ phone || '-' }}
          </span>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import '@fortawesome/fontawesome-free/css/all.css'
import api from '../api'

const funcionarios = ref([])
const loading = ref(true)
const search = ref('')
const error = ref(null)
const rowsPerPage = ref(25)

const headers = [
  { text: 'ID', value: 'id', width: 80 },
  { text: 'Nome', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Telefone', value: 'phone', width: 150 },
  { text: 'Ações', value: 'actions', width: 200 }
]

const updateRowsPerPage = (value) => {
  rowsPerPage.value = value
}

onMounted(async () => {
  try {
    const { data } = await api.get('/employees')
    funcionarios.value = data
  } catch (err) {
    error.value = 'Erro ao carregar funcionários.'
    console.error(err)
  } finally {
    loading.value = false
  }
})

const editar = (id) => {
  console.log('Editar', id)
}

const excluir = (id) => {
  console.log('Excluir', id)
}
</script>

<style scoped>
.funcionarios-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.titulo {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #2d3748;
  text-align: center;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.actions-container {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.btn-editar, .btn-excluir {
  padding: 8px 12px;
  border: none;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-editar {
  background-color: #4299e1;
  color: white;
}

.btn-editar:hover {
  background-color: #3182ce;
}

.btn-excluir {
  background-color: #f56565;
  color: white;
}

.btn-excluir:hover {
  background-color: #e53e3e;
}
</style>

<style>
/* Estilos globais da tabela */
.customize-table {
  --easy-table-header-font-size: 14px;
  --easy-table-header-height: 50px;
  --easy-table-header-item-padding: 10px 15px;
  --easy-table-body-row-height: 60px;
  --easy-table-body-item-padding: 10px 15px;
  --easy-table-footer-height: 50px;
  --easy-table-footer-font-size: 14px;
  --easy-table-footer-padding: 0 15px;
}

.phone-number {
  font-family: monospace;
  font-size: 0.95rem;
}

.easy-data-table__pagination {
  padding: 15px;
  border-top: 1px solid #e2e8f0;
  position: relative;
  z-index: 1;
}

.easy-data-table__body tr:nth-child(even) {
  background-color: #f7fafc;
}

.easy-data-table__body tr:hover {
  background-color: #ebf8ff;
}

/* Estilos específicos para o dropdown "Rows per page" */
.easy-data-table__rows-per-page {
  position: relative;
  display: inline-block;
  margin-right: 10px;
}

.easy-data-table__rows-per-page-select {
  position: relative;
  z-index: 2;
}

.easy-data-table__rows-per-page-select select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  padding: 6px 30px 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background-color: white;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 16px;
  cursor: pointer;
  font-size: 14px;
  min-width: 80px;
}

.easy-data-table__rows-per-page-select select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 1px #4299e1;
}
</style>