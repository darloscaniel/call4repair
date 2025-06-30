<template>
  <div class="chamados">
    <h2 class="titulo">🛠️ Chamados</h2>

    <EasyDataTable
      v-if="!loading"
      :headers="headers"
      :items="processedChamados"
      theme-color="#2d89ef"
      :loading="loading"
      :search-value="search"
      class="tabela"
    >
      <template #item-employees="{ employees }">
        <ul v-if="employees && Array.isArray(employees)">
          <li v-for="func in employees" :key="func.id">
            {{ func.name }}
          </li>
        </ul>
        <span v-else>-</span>
      </template>
    </EasyDataTable>
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

// Processa os chamados para garantir que employees exista
const processedChamados = computed(() => {
  return chamados.value.map(chamado => ({
    ...chamado,
    employees: chamado.employees || [] // Garante que employees seja array
  }))
})

const headers = [
  { text: '#', value: 'id' },
  { text: 'Cliente', value: 'customer_name' },
  { text: 'Descrição', value: 'description' },
  { text: 'Status', value: 'status' },
  { text: 'Funcionários', value: 'employees' }
]

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
      employees: item.employees || [] // Garante array mesmo se for null/undefined
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
.titulo {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 24px;
  color: #2d3748;
}

.tabela {
  box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
  border-radius: 12px;
  overflow: hidden;
}
</style>
