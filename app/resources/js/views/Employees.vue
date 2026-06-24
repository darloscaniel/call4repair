<template>
  <div class="employees-container">
    <h2 class="title">{{ t('employees.title') }}</h2>

    <div class="actions-top">
      <button class="btn-create" @click="createEmployee">
        <i class="fas fa-plus"></i> {{ t('employees.create') }}
      </button>
    </div>
    <div class="search-box">
      <input
        type="text"
        v-model="search"
        :placeholder="t('employees.search')"
        class="search-input"
      />
    </div>
    <EmployeeFormModal
      v-if="showCreateModal || showEditModal"
      :employee="selectedEmployee"
      @close="closeModal"
      @save="handleSave"
    />

    <div class="table-wrapper">
      <EasyDataTable
        :headers="headers"
        :items="employees"
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
            <button class="btn-edit" @click="edit(id)">
              <i class="fas fa-edit"></i> {{ t('employees.edit') }}
            </button>
            <button class="btn-delete" @click="remove(id)">
              <i class="fas fa-trash"></i> {{ t('employees.delete') }}
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
import { useI18n } from 'vue-i18n'
import api from '../api'
import EmployeeFormModal from '@/components/EmployeeFormModal.vue'

const { t } = useI18n()

const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedEmployee = ref(null)
const employees = ref([])
const loading = ref(true)
const search = ref('')
const error = ref(null)
const rowsPerPage = ref(25)

const headers = [
  { text: t('employees.name'), value: 'name' },
  { text: t('employees.email'), value: 'email' },
  { text: t('employees.phone'), value: 'phone', width: 150 },
  { text: t('employees.actions'), value: 'actions', width: 200 },
]

const createEmployee = () => {
  selectedEmployee.value = { name: '', age: '', phone: '', email: '' }
  showCreateModal.value = true
}

const edit = (id) => {
  const employee = employees.value.find((e) => e.id === id)
  if (employee) {
    selectedEmployee.value = { ...employee }
    showEditModal.value = true
  }
}

const handleSave = async (employee) => {
  try {
    if (employee.id) {
      const response = await api.put(`/employees/${employee.id}`, employee)
      const updated = response.data
      employees.value = employees.value.map((e) => (e.id === updated.id ? updated : e))
    } else {
      const response = await api.post('/employees', employee)
      employees.value.push(response.data)
    }
  } catch (err) {
    console.error('Error saving employee:', err)
  } finally {
    closeModal()
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
}

onMounted(async () => {
  try {
    const { data } = await api.get('/employees')
    employees.value = data
  } catch (err) {
    error.value = 'Error loading employees.'
    console.error(err)
  } finally {
    loading.value = false
  }
})

const remove = async (id) => {
  if (confirm(t('employees.confirmDelete'))) {
    try {
      await api.delete(`/employees/${id}`)
      employees.value = employees.value.filter((e) => e.id !== id)
    } catch (err) {
      console.error('Error deleting employee:', err)
    }
  }
}

const updateRowsPerPage = (value) => {
  rowsPerPage.value = value
}
</script>

<style scoped>
.employees-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.title {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #2d3748;
  text-align: center;
}

.actions-top {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 1rem;
}

.btn-create {
  background-color: #38a169;
  color: white;
  padding: 8px 14px;
  border: none;
  border-radius: 20px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background-color 0.2s ease;
}

.btn-create:hover {
  background-color: #2f855a;
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

.btn-edit, .btn-delete {
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

.btn-edit {
  background-color: #4299e1;
  color: white;
}

.btn-edit:hover {
  background-color: #3182ce;
}

.btn-delete {
  background-color: #f56565;
  color: white;
}

.btn-delete:hover {
  background-color: #e53e3e;
}

.search-box {
  margin-bottom: 1rem;
  display: flex;
  justify-content: flex-end;
}

.search-input {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 0.95rem;
  width: 250px;
}
</style>
