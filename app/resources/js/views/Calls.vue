<template>
  <div class="calls-container">
    <h2 class="title">{{ t('calls.title') }}</h2>
    <div class="search-box">
      <input
        type="text"
        v-model="search"
        :placeholder="t('calls.search')"
        class="search-input"
      />
    </div>

    <div class="table-wrapper">
      <EasyDataTable
        v-if="!loading"
        :headers="headers"
        :items="processedCalls"
        theme-color="#2d89ef"
        table-class-name="customize-table"
        header-text-direction="center"
        body-text-direction="center"
        alternating
        :rows-per-page="25"
        show-index
        :loading="loading"
        :search-value="search"
        @click-row="openModal"
      >
        <template #item-status="{ status }">
          <div class="status-container">
            <span :class="['status-badge', statusClass(status)]">
              {{ statusText(status) }}
            </span>
          </div>
        </template>

        <template #item-employees="{ employees }">
          <div class="employees-container">
            <ul v-if="employees && employees.length" class="employees-list">
              <li v-for="employee in employees" :key="employee.id">
                <span class="employee-badge">{{ employee.name }}</span>
              </li>
            </ul>
            <span v-else class="no-employees">{{ t('calls.assign') }}</span>
          </div>
        </template>
        <template #item-description="{ description }">
          <div class="description-cell" :title="description">
            {{ description }}
          </div>
        </template>
      </EasyDataTable>
      <CallModal
        v-if="showModal"
        :call="selectedCall"
        :employees="allEmployees"
        @close="showModal = false"
        @save="handleSave"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import api from '../api'
import CallModal from '@/components/CallModal.vue'

const router = useRouter()
const { t } = useI18n()
const calls = ref([])
const loading = ref(true)
const search = ref('')
const showModal = ref(false)
const selectedCall = ref({})
const allEmployees = ref([])

const processedCalls = computed(() => {
  return calls.value.map((call) => ({
    ...call,
    employees: call.employees || [],
  }))
})

const headers = [
  { text: t('calls.customer'), value: 'customer_name' },
  { text: t('calls.phone'), value: 'phone', width: 150 },
  { text: t('calls.description'), value: 'description' },
  { text: t('calls.status'), value: 'status', width: 150 },
  { text: t('calls.employees'), value: 'employees' },
]

const statusClass = (status) => {
  return `status-${status}`
}

const statusText = (status) => {
  return t(`status.${status}`)
}

const openModal = (row) => {
  if (!row || typeof row !== 'object') return
  selectedCall.value = { ...row, employees: row.employees || [] }
  showModal.value = true
}

const handleSave = async (updated) => {
  try {
    const response = await api.put(`/calls/${updated.id}`, {
      status: updated.status,
      employees: updated.employees.map((e) => e.id),
    })
    const updatedData = response.data

    calls.value = calls.value.map((call) =>
      call.id === updatedData.id ? updatedData : call
    )

    showModal.value = false
  } catch (err) {
    console.error('Error saving call:', err)
  }
}

onMounted(async () => {
  try {
    const token = sessionStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    const { data: employeesData } = await api.get('/employees')
    allEmployees.value = employeesData

    const { data } = await api.get('/calls')
    calls.value = data.map((item) => ({
      ...item,
      employees: item.employees || [],
    }))
  } catch (error) {
    console.error('Error loading calls:', error)
    sessionStorage.removeItem('token')
    router.push('/login')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.calls-container {
  padding: 2rem;
  max-width: 1500px;
  margin: 0 auto;
}

.title {
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
</style>

<style>
.customize-table {
  --easy-table-header-font-size: 14px;
  --easy-table-header-height: 50px;
  --easy-table-header-item-padding: 10px 15px;
  --easy-table-body-row-height: 50px;
  --easy-table-body-item-padding: 10px 15px;
  --easy-table-footer-height: 50px;
  --easy-table-footer-font-size: 14px;
  --easy-table-footer-padding: 0 15px;
}

.employees-list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.employee-badge {
  background-color: #e2e8f0;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.85rem;
  color: #4a5568;
}

.no-employees {
  color: #a0aec0;
  font-style: italic;
}

.status-container {
  padding: 8px 0;
  display: flex;
  justify-content: center;
}

.status-badge {
  padding: 8px 16px;
  min-width: 100px;
  display: inline-block;
  text-align: center;
}

.status-open {
  background-color: #ffa4ff;
  color: #7a018a;
}

.status-in_progress {
  background-color: #feebc8;
  color: #dd8520;
}

.status-done {
  background-color: #c6f6d5;
  color: #38a169;
}

.status-rejected {
  background-color: #f6c6c6;
  color: #7e0000;
}

.easy-data-table__pagination {
  padding: 15px;
  border-top: 1px solid #e2e8f0;
}

.easy-data-table__body tr:nth-child(even) {
  background-color: #f7fafc;
}

.easy-data-table__body tr:hover {
  background-color: #ebf8ff;
}

.clickable-cell {
  cursor: pointer;
  transition: background-color 0.2s;
}

.clickable-cell:hover {
  background-color: #edf2f7;
}

.description-cell {
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
