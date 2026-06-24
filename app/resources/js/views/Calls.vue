<template>
  <div class="page">
    <header class="page__header">
      <h2 class="page__title">{{ t('calls.title') }}</h2>
      <p class="page__subtitle">{{ t('calls.subtitle') }}</p>
    </header>

    <div class="toolbar">
      <span></span>
      <input
        type="text"
        v-model="search"
        :placeholder="t('calls.search')"
        class="input search"
      />
    </div>

    <div class="panel">
      <EasyDataTable
        v-model:server-options="serverOptions"
        :server-items-length="totalItems"
        :headers="headers"
        :items="processedCalls"
        theme-color="#2d89ef"
        table-class-name="customize-table"
        header-text-direction="center"
        body-text-direction="center"
        :rows-items="[10, 25, 50]"
        show-index
        :loading="loading"
        @click-row="openModal"
      >
        <template #item-status="{ status }">
          <div class="cell-center">
            <span :class="['badge', `badge--${status}`]">
              {{ statusText(status) }}
            </span>
          </div>
        </template>

        <template #item-employees="{ employees }">
          <div class="employees-cell">
            <ul v-if="employees && employees.length" class="chips">
              <li v-for="employee in employees" :key="employee.id">
                <span class="chip">{{ employee.name }}</span>
              </li>
            </ul>
            <span v-else class="muted">{{ t('calls.assign') }}</span>
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
import { ref, watch, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import api from '../api'
import CallModal from '@/components/CallModal.vue'
import { isAuthenticated, can } from '../auth'

const router = useRouter()
const { t } = useI18n()
const calls = ref([])
const loading = ref(true)
const search = ref('')
const showModal = ref(false)
const selectedCall = ref({})
const allEmployees = ref([])

const totalItems = ref(0)
const serverOptions = ref({ page: 1, rowsPerPage: 25, sortBy: '', sortType: '' })

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

const statusText = (status) => {
  return t(`status.${status}`)
}

const openModal = (row) => {
  if (!row || typeof row !== 'object') return
  selectedCall.value = { ...row, employees: row.employees || [] }
  showModal.value = true
}

const loadCalls = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/calls', {
      params: {
        page: serverOptions.value.page,
        per_page: serverOptions.value.rowsPerPage,
        search: search.value || undefined,
      },
    })
    calls.value = data.data
    totalItems.value = data.meta.total
  } catch (err) {
    console.error('Error loading calls:', err)
  } finally {
    loading.value = false
  }
}

const handleSave = async (updated) => {
  try {
    const payload = {
      customer_name: updated.customer_name,
      phone: updated.phone,
      description: updated.description,
      status: updated.status,
    }

    // Only users who can manage employees may (re)assign them. Technicians
    // omit the field entirely so the backend keeps the existing assignments.
    if (can('manage employees')) {
      payload.employees = updated.employees.map((e) => e.id)
    }

    await api.put(`/calls/${updated.id}`, payload)
    showModal.value = false
    await loadCalls()
  } catch (err) {
    console.error('Error saving call:', err)
  }
}

// Refetch on page/rows-per-page change (server-side pagination).
watch(serverOptions, loadCalls, { deep: true })

// Server-side search by customer name.
watch(search, () => {
  serverOptions.value.page = 1
  loadCalls()
})

onMounted(async () => {
  if (!isAuthenticated()) {
    router.push('/login')
    return
  }

  // Employee list (for assignment) is only available to users who can manage
  // employees; technicians simply get an empty assignable list.
  if (can('manage employees')) {
    try {
      const { data } = await api.get('/employees', { params: { per_page: 100 } })
      allEmployees.value = data.data
    } catch (err) {
      console.error('Error loading employees:', err)
    }
  }

  await loadCalls()
})
</script>

<style scoped>
.cell-center {
  display: flex;
  justify-content: center;
}

.employees-cell {
  padding: 6px 0;
}

.chips {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  justify-content: center;
}

.chip {
  background-color: var(--c-surface-2);
  border: 1px solid var(--c-border);
  padding: 4px 10px;
  border-radius: var(--radius-pill);
  font-size: 0.8rem;
  color: var(--c-text-muted);
  white-space: nowrap;
}

.muted {
  color: var(--c-text-faint);
  font-style: italic;
  font-size: 0.85rem;
}

.description-cell {
  max-width: 320px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0 auto;
  color: var(--c-text-muted);
}
</style>
