<template>
  <aside class="sidebar">
    <div class="brand">
      <span class="brand__logo">🔧</span>
      <span class="brand__name">{{ t('app.title') }}</span>
    </div>

    <nav class="menu">
      <RouterLink
        to="/dashboard"
        class="menu-link"
        :class="{ active: $route.path === '/dashboard' }"
      >{{ t('nav.dashboard') }}</RouterLink>

      <RouterLink
        v-if="can('manage employees')"
        to="/employees"
        class="menu-link"
        :class="{ active: $route.path === '/employees' }"
      >{{ t('nav.employees') }}</RouterLink>

      <RouterLink
        to="/calls"
        class="menu-link"
        :class="{ active: $route.path === '/calls' }"
      >{{ t('nav.calls') }}</RouterLink>
    </nav>

    <div class="logout-container">
      <button class="logout-button" @click="logout">
        {{ t('nav.logout') }}
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRouter, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '../api'
import { can, clearAuth } from '../auth'

const router = useRouter()
const { t } = useI18n()

const logout = async () => {
  try {
    // Invalidate the token server-side (blacklist) and clear auth cookies.
    await api.post('/logout')
  } catch {
    // Ignore network/credential errors; we still clear local state below.
  } finally {
    clearAuth()
    router.push('/login')
  }
}
</script>

<style scoped>
.sidebar {
  width: 250px;
  flex-shrink: 0;
  background: var(--c-sidebar);
  color: var(--c-sidebar-text);
  height: 100vh;
  padding: 22px 16px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 10px 22px;
  margin-bottom: 14px;
  border-bottom: 1px solid var(--c-sidebar-2);
}

.brand__logo {
  font-size: 1.5rem;
}

.brand__name {
  font-size: 1.2rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.01em;
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.menu-link {
  display: block;
  padding: 11px 14px;
  border-radius: var(--radius-sm);
  color: var(--c-sidebar-text);
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.menu-link:hover {
  background-color: var(--c-sidebar-2);
  color: #fff;
}

.menu-link.active {
  background-color: var(--c-primary);
  color: #fff;
  font-weight: 600;
  box-shadow: var(--shadow-sm);
}

.logout-container {
  margin-top: auto;
  padding-top: 16px;
}

.logout-button {
  width: 100%;
  padding: 11px 16px;
  background-color: transparent;
  color: #fca5a5;
  border: 1px solid var(--c-sidebar-2);
  border-radius: var(--radius-sm);
  font-family: inherit;
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.logout-button:hover {
  background-color: rgba(220, 38, 38, 0.15);
  border-color: rgba(220, 38, 38, 0.4);
  color: #fecaca;
}
</style>
