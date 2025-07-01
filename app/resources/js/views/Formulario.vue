<template>
<h2 class="titulo">🔧 Call4Repair 🔧</h2>
  <div class="form-container">
    
    <h2>Criar Novo Chamado</h2>
    <form @submit.prevent="submitChamado">
      <div class="form-group">
        <label for="nome">Nome:</label>
        <input
          type="text"
          id="nome"
          v-model="form.nome"
          required
          placeholder="Seu nome"
        />
      </div>

        <div class="form-group">
        <label for="nome">Telefone Para Contato:</label>
        <input
          type="text"
          id="telefone"
          v-model="form.telefone"
          required
          placeholder="Ex: (11) 91234-5678"
        />
      </div>

      <div class="form-group">
        <label for="descricao">Descrição do Problema:</label>
        <textarea
          id="descricao"
          v-model="form.descricao"
          required
          placeholder="Descreva o problema..."
          rows="5"
        ></textarea>
      </div>

      <div class="form-group">
        <label for="status">Status:</label>
        <input
          type="text"
          id="status"
          v-model="form.status"
          readonly
          value="Aberto"
        />
      </div>

      <button type="submit" class="submit-btn">Enviar Chamado</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({
  nome: '',
  telefone: '',
  descricao: '',
  status: 'Aberto',
})

const submitChamado = async () => {
  try {
    const payload = {
      customer_name: form.value.nome,
      phone: form.value.telefone,
      description: form.value.descricao,
      status: form.value.status,
    }

    const response = await fetch('http://localhost:8080/api/calls', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    })

    if (!response.ok) throw new Error('Erro ao criar chamado')

    alert('Chamado criado com sucesso!')
    form.value = { nome: '', descricao: '', status: 'Aberto' }
  } catch (error) {
    console.error('Erro ao criar chamado:', error)
    alert('Erro ao criar chamado. Tente novamente.')
  }
}
</script>

<style scoped>
.form-container {
  max-width: 800px; /* Largura maior para o container */
  margin: 20px auto;
  padding: 30px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 28px;
}

.form-group {
  margin-bottom: 25px;
  width: 95%;
}

label {
  display: block;
  margin-bottom: 10px;
  font-weight: bold;
  font-size: 18px;
  color: #444;
}

input,
textarea {
  width: 100%; /* Ocupa toda a largura do form-group */
  padding: 15px;
  border: 2px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  transition: border 0.3s;
}

input:focus,
textarea:focus {
  border-color: #4CAF50;
  outline: none;
}

textarea {
  min-height: 150px; /* Altura maior para a descrição */
}

input[readonly] {
  background-color: #f9f9f9;
  cursor: not-allowed;
}

.submit-btn {
  width: 100%;
  padding: 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 10px;
}

.submit-btn:hover {
  background-color: #45a049;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>