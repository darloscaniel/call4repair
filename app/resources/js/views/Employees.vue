<template>
  <div class="page">
    <header class="page__header">
      <h2 class="page__title">{{ t('employees.title') }}</h2>
      <p class="page__subtitle">{{ t('employees.subtitle') }}</p>
    </header>

    <div class="toolbar">
      <button class="btn btn--primary" @click="createEmployee">
        <i class="fas fa-plus"></i> {{ t('employees.create') }}
      </button>
      <input
        type="text"
        v-model="search"
        :placeholder="t('employees.search')"
        class="input search"
      />
    </div>

    <EmployeeFormModal
      v-if="showCreateModal || showEditModal"
      :employee="selectedEmployee"
      @close="closeModal"
      @save="handleSave"
    />

    <div class="panel">
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
        :rows-items="[10, 25, 50]"
        show-index
      >
        <template #item-actions="{ id }">
          <div class="actions-cell">
            <button class="btn btn--ghost btn--sm" @click="edit(id)">
              <i class="fas fa-edit"></i> {{ t('employees.edit') }}
            </button>
            <button class="btn btn--danger btn--sm" @click="remove(id)">
              <i class="fas fa-trash"></i> {{ t('employees.delete') }}
            </button>
          </div>
        </template>

        <template #item-phone="{ phone }">
          <span>{{ phone || '–' }}</span>
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
  { text: t('employees.actions'), value: 'actions', width: 220 },
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
.actions-cell {
  display: flex;
  gap: 8px;
  justify-content: center;
}
</style>
