<template>
  <aside class="sidebar">
    <h1 class="title">Call4Repair</h1>

    <nav class="menu">
      <RouterLink
        to="/funcionarios"
        class="menu-link"
        :class="{ active: $route.path === '/funcionarios' }"
      >👥 Funcionários</RouterLink>

      <RouterLink
        to="/chamados"
        class="menu-link"
        :class="{ active: $route.path === '/chamados' }"
      >🛠️ Chamados</RouterLink>
    </nav>

    <div class="logout-container">
      <button class="logout-button" @click="logout">
        🔒 Sair
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { RouterLink } from 'vue-router'

const router = useRouter()

const logout = () => {
  sessionStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  width: 17%;
  background-color: #1a202c;
  color: white;
  height: 100vh;
  padding: 24px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: space-between; 
}

.title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 32px;
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.menu-link {
  display: block;
  padding: 8px 16px;
  border-radius: 6px;
  color: white;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.menu-link:hover {
  background-color: #4a5568;
}

.menu-link.active {
  background-color: #2d3748;
  font-weight: bold;
}

.logout-container {
  margin-top: auto;
}

.logout-button {
  background-color: #e53e3e;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  width: 100%;
  font-weight: 600;
  transition: background-color 0.2s ease;
}

.logout-button:hover {
  background-color: #c53030;
}
</style>
