<template>
  <div>
    <h2 class="titulo">👥 Funcionários</h2>

    <EasyDataTable
      :headers="headers"
      :items="funcionarios"
      :loading="loading"
      :search-value="search"
      theme-color="#2d89ef"
      class="tabela"
    >
      <template #item-actions="{ item }">
        <button class="btn-editar" @click="editar(item.id)">Editar</button>
        <button class="btn-excluir" @click="excluir(item.id)">Excluir</button>
      </template>
    </EasyDataTable>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import api from '../api' // se estiver usando axios customizado

const funcionarios = ref([])
const loading = ref(true)
const search = ref('')
const error = ref(null)

const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Nome', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Telefone', value: 'phone' },
  { text: 'Ações', value: 'actions' },
]

onMounted(async () => {
  try {
    const { data } = await api.get('/employees') // ou fetch
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
.titulo {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 24px;
  color: #2d3748;
}

.tabela {
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  border-radius: 12px;
  overflow: hidden;
}

.btn-editar, .btn-excluir {
  padding: 5px 10px;
  margin: 0 5px;
  cursor: pointer;
  border: none;
  border-radius: 4px;
}

.btn-editar {
  background-color: #4299e1;
  color: white;
}

.btn-excluir {
  background-color: #f56565;
  color: white;
}
</style>
