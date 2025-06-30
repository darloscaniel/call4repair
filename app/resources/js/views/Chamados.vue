<template>
  <div class="chamados-container">
    <h2 class="titulo">🛠️ Chamados</h2>

    <div class="table-wrapper">
      <EasyDataTable
        v-if="!loading"
        :headers="headers"
        :items="processedChamados"
        theme-color="#2d89ef"
        table-class-name="customize-table"
        header-text-direction="center"
        body-text-direction="center"
        alternating
        :rows-per-page="25"
        show-index
        :loading="loading"
        :search-value="search"
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
              <li v-for="func in employees" :key="func.id">
                <span class="employee-badge">{{ func.name }}</span>
              </li>
            </ul>
            <span v-else class="no-employees">-</span>
          </div>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import api from '../api'

const router = useRouter()
const chamados = ref([])
const loading = ref(true)
const search = ref('')

// Processa os chamados para garantir que employees exista
const processedChamados = computed(() => {
  return chamados.value.map(chamado => ({
    ...chamado,
    employees: chamado.employees || []
  }))
})

const headers = [
  { text: 'Cliente', value: 'customer_name' },
  { text: 'Descrição', value: 'description' },
  { text: 'Status', value: 'status', width: 150 },
  { text: 'Funcionários', value: 'employees' }
]

const statusClass = (status) => {
  return `status-${status.toLowerCase()}`
}

const statusText = (status) => {
  const map = {
    'aberto': 'Aberto',
    'em_andamento': 'Em Andamento',
    'concluido': 'Concluído'
  }
  return map[status] || status
}

onMounted(async () => {
  try {
    const token = sessionStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    const { data } = await api.get('/calls')
    chamados.value = data.map(item => ({
      ...item,
      employees: item.employees || []
    }))
  } catch (error) {
    console.error('Erro ao carregar chamados:', error)
    sessionStorage.removeItem('token')
    router.push('/login')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.chamados-container {
  padding: 2rem;
  max-width: 1500px;
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
  padding: 8px 0; /* Espaço vertical aumentado */
  display: flex;
  justify-content: center;
}

.status-badge {
  padding: 8px 16px; /* Aumente o padding horizontal */
  min-width: 100px; /* Largura mínima para consistência */
  display: inline-block;
  text-align: center;
}


.status-aberto {
  background-color: #fed7d7;
  color: #e53e3e;
}

.status-em_andamento {
  background-color: #feebc8;
  color: #dd6b20;
}

.status-concluido {
  background-color: #c6f6d5;
  color: #38a169;
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
</style>