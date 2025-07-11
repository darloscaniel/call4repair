<template>
  <div class="funcionarios-container">
    <h2 class="titulo">👥 Funcionários</h2>


    <div class="actions-top">
      <button class="btn-criar" @click="criarFuncionario">
        <i class="fas fa-plus"></i> Criar Funcionário
      </button>
    </div>
<div class="search-box">
  <input
    type="text"
    v-model="search"
    placeholder="🔍 Pesquisar funcionário..."
    class="input-pesquisa"
  />
</div>
 <ModalFuncionario
  v-if="showModalCriar || showModalEditar"
  :funcionario="funcionarioSelecionado"
  @close="fecharModal"
  @save="handleSalvarOuAtualizar"
/>

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
import ModalFuncionario from '@/components/CriarFuncionarioModal.vue'

const showModalCriar = ref(false)
const showModalEditar = ref(false)
const funcionarioSelecionado = ref(null)
const funcionarios = ref([])
const loading = ref(true)
const search = ref('')
const error = ref(null)
const rowsPerPage = ref(25)

const headers = [
  { text: 'Nome', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Telefone', value: 'phone', width: 150 },
  { text: 'Ações', value: 'actions', width: 200 }
]

const criarFuncionario = () => {
  funcionarioSelecionado.value = { name: '', age: '', phone: '', email: '' }
  showModalCriar.value = true
}

const editar = (id) => {
  const func = funcionarios.value.find(f => f.id === id)
  if (func) {
    funcionarioSelecionado.value = { ...func }
    showModalEditar.value = true
  }
}

const handleSalvarOuAtualizar = async (func) => {
  try {
    if (func.id) {

      const response = await api.put(`/employees/${func.id}`, func)
      const atualizado = response.data
      funcionarios.value = funcionarios.value.map(f => f.id === atualizado.id ? atualizado : f)
    } else {
   
      const response = await api.post('/employees', func)
      funcionarios.value.push(response.data)
    }
  } catch (error) {
    console.error('Erro ao salvar funcionário:', error)
  } finally {
    fecharModal()
  }
}

const fecharModal = () => {
  showModalCriar.value = false
  showModalEditar.value = false
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

const excluir = async (id) => {
  if (confirm('Tem certeza que deseja excluir este funcionário?')) {
    try {
      await api.delete(`/employees/${id}`)
      funcionarios.value = funcionarios.value.filter(f => f.id !== id)
    } catch (error) {
      console.error('Erro ao excluir funcionário:', error)
    }
  }
}

const updateRowsPerPage = (value) => {
  rowsPerPage.value = value
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

.actions-top {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 1rem;
}

.btn-criar {
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

.btn-criar:hover {
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

.search-box {
  margin-bottom: 1rem;
  display: flex;
  justify-content: flex-end;
}

.input-pesquisa {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 0.95rem;
  width: 250px;
}

</style>
