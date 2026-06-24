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
        v-model:server-options="serverOptions"
        :server-items-length="totalItems"
        :headers="headers"
        :items="employees"
        :loading="loading"
        theme-color="#2d89ef"
        table-class-name="customize-table"
        header-text-direction="center"
        body-text-direction="center"
        alternating
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
import { ref, watch, onMounted } from 'vue'
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

const totalItems = ref(0)
const serverOptions = ref({ page: 1, rowsPerPage: 25, sortBy: '', sortType: '' })

const headers = [
  { text: t('employees.name'), value: 'name' },
  { text: t('employees.email'), value: 'email' },
  { text: t('employees.phone'), value: 'phone', width: 150 },
  { text: t('employees.actions'), value: 'actions', width: 200 },
]

const loadEmployees = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/employees', {
      params: {
        page: serverOptions.value.page,
        per_page: serverOptions.value.rowsPerPage,
        search: search.value || undefined,
      },
    })
    employees.value = data.data
    totalItems.value = data.meta.total
  } catch (err) {
    console.error('Error loading employees:', err)
  } finally {
    loading.value = false
  }
}

// Refetch when the page/rows-per-page change (server-side pagination).
watch(serverOptions, loadEmployees, { deep: true })

// Server-side search: reset to the first page and refetch.
watch(search, () => {
  serverOptions.value.page = 1
  loadEmployees()
})

onMounted(loadEmployees)

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
      await api.put(`/employees/${employee.id}`, employee)
    } else {
      await api.post('/employees', employee)
    }
    await loadEmployees()
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

const remove = async (id) => {
  if (confirm(t('employees.confirmDelete'))) {
    try {
      await api.delete(`/employees/${id}`)
      await loadEmployees()
    } catch (err) {
      console.error('Error deleting employee:', err)
    }
  }
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
