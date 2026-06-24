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
  overflow-y: auto;
  background-color: var(--c-bg);
}
</style>
