<template>
  <div class="login-container">
    <form @submit.prevent="login" class="login-form">
      <h2 class="title">🔐 Login</h2>

      <div class="form-group">
        <label for="email">Email</label>
        <input v-model="email" type="email" id="email" required />
      </div>

      <div class="form-group">
        <label for="password">Senha</label>
        <input v-model="password" type="password" id="password" required />
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>

      <button type="submit">Entrar</button> 
      <button type="button" @click="goToFormulario" class="formulario-btn">
      Abrir Chamado
      </button> 
    </form>
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref(null)
const router = useRouter()

const goToFormulario = () => {
  router.push('/formulario')
}

onBeforeMount(() => {
  sessionStorage.removeItem('token')
})

const login = async () => {
  error.value = null
  try {
    const response = await axios.post('http://localhost:8080/api/login', {
      email: email.value,
      password: password.value
    })

    sessionStorage.setItem('token', response.data.token)
    router.push('/funcionarios')
  } catch (err) {
    if (err.response && err.response.status === 401) {
      error.value = 'Credenciais inválidas.'
    } else {
      error.value = 'Erro ao tentar logar. Tente novamente.'
    }
  }
}
</script>



<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: #f5f7fa;
}

.login-form {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}

.title {
  margin-bottom: 1.5rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 0.25rem;
}

input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #2d89ef;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

button:hover {
  background-color: #2267c5;
}

.error-message {
  color: red;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  text-align: center;
}

.formulario-btn {
  width: 100%;
  padding: 0.75rem;
  background-color: #05a100;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  cursor: pointer;
  margin-top: 2%;
}

.formulario-btn:hover {
  background-color: #3e8e41;
}

</style>
