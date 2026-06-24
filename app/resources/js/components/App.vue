<template>
  <div class="layout">
    <Sidebar v-if="!['/login', '/new-call'].includes(route.path)" />

    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue'
import { RouterView, useRoute } from 'vue-router'

const route = useRoute()
</script>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
  width: 100%;
}

.content {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
  background-color: #f0f2f5;
}
</style>
