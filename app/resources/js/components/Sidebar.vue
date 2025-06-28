<template>
  <aside class="sidebar">
    <h1 class="title">🔧 Call4Repair</h1>
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
  </aside>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.sidebar {
  width: 256px; /* w-64 é 16rem = 256px */
  background-color: #1a202c; /* bg-gray-900 */
  color: white;
  height: 100vh;
  padding: 24px; /* p-6 = 1.5rem = 24px */
  box-sizing: border-box;
}

.title {
  font-size: 1.5rem; /* text-2xl */
  font-weight: bold;
  margin-bottom: 32px; /* mb-8 */
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 16px; /* space-y-4 */
}

.menu-link {
  display: block;
  padding: 8px 16px; /* px-4 py-2 */
  border-radius: 6px; /* rounded */
  color: white;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.menu-link:hover {
  background-color: #4a5568; /* hover:bg-gray-700 */
}

.menu-link.active {
  background-color: #2d3748; /* bg-gray-800 */
  font-weight: bold;
}
</style>
