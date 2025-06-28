<template>
  <div class="chamados">
    <h2 class="titulo">🛠️ Chamados</h2>

    <table class="tabela" v-if="chamados.length > 0">
      <thead class="cabecalho">
        <tr>
          <th class="celula">#</th>
          <th class="celula">Cliente</th>
          <th class="celula">Descrição</th>
          <th class="celula">Status</th>
          <th class="celula">Funcionários</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="chamado in chamados" :key="chamado.id" class="linha">
          <td class="celula">#{{ chamado.id }}</td>
          <td class="celula">{{ chamado.customer_name }}</td>
          <td class="celula">{{ chamado.description }}</td>
          <td class="celula">
            <span class="status">{{ chamado.status }}</span>
          </td>
          <td class="celula">
            <ul>
              <li v-for="func in chamado.employees" :key="func.id">
                {{ func.nome }}
              </li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else>Nenhum chamado encontrado.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const chamados = ref([])

onMounted(async () => {
  try {
    const response = await fetch('http://localhost:8000/calls')
    const data = await response.json()
    chamados.value = data
  } catch (error) {
    console.error('Erro ao carregar chamados:', error)
  }
})
</script>


<style scoped>
.titulo {
  font-size: 1.875rem; /* 3xl = 30px approx */
  font-weight: 700;
  margin-bottom: 24px;
  color: #2d3748; /* text-gray-800 */
}

.tabela {
  width: 100%;
  background-color: white;
  box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
  border: 1px solid #e2e8f0; /* gray-200 */
  border-radius: 12px;
  overflow: hidden;
  border-collapse: collapse;
  min-width: 100%;
}

.cabecalho {
  background-color: #f7fafc; /* gray-100 */
}

.celula {
  text-align: left;
  padding: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.linha:hover {
  background-color: #f9fafb; /* hover:bg-gray-50 */
}

.status {
  display: inline-block;
  padding: 2px 8px;
  font-size: 0.875rem; /* text-sm */
  border-radius: 6px;
  background-color: #fefcbf; /* bg-yellow-100 */
  color: #b7791f; /* text-yellow-700 */
  font-weight: 600;
}
</style>
