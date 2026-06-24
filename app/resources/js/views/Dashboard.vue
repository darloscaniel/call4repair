<template>
  <div class="dashboard-container">
    <h2 class="title">{{ t('dashboard.title') }}</h2>
    <p class="subtitle">{{ t('dashboard.subtitle') }}</p>

    <div class="cards">
      <RouterLink to="/calls" class="card">
        <div class="card-icon">🛠️</div>
        <div class="card-body">
          <h3>{{ t('dashboard.callsTitle') }}</h3>
          <p class="card-desc">{{ t('dashboard.callsDesc') }}</p>
          <span class="card-count">{{ callsTotal }}</span>
        </div>
      </RouterLink>

      <RouterLink v-if="can('manage employees')" to="/employees" class="card">
        <div class="card-icon">👥</div>
        <div class="card-body">
          <h3>{{ t('dashboard.employeesTitle') }}</h3>
          <p class="card-desc">{{ t('dashboard.employeesDesc') }}</p>
          <span class="card-count">{{ employeesTotal }}</span>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '../api'
import { isAuthenticated, can } from '../auth'

const router = useRouter()
const { t } = useI18n()

const callsTotal = ref('–')
const employeesTotal = ref('–')

// Pull just the pagination total (per_page=1) to show quick counts on the cards.
const loadCounts = async () => {
  try {
    const { data } = await api.get('/calls', { params: { per_page: 1 } })
    callsTotal.value = data.meta.total
  } catch (err) {
    console.error('Error loading call count:', err)
  }

  if (can('manage employees')) {
    try {
      const { data } = await api.get('/employees', { params: { per_page: 1 } })
      employeesTotal.value = data.meta.total
    } catch (err) {
      console.error('Error loading employee count:', err)
    }
  }
}

onMounted(() => {
  if (!isAuthenticated()) {
    router.push('/login')
    return
  }
  loadCounts()
})
</script>

<style scoped>
.dashboard-container {
  padding: 2rem;
  max-width: 1100px;
  margin: 0 auto;
}

.title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #2d3748;
  text-align: center;
}

.subtitle {
  text-align: center;
  color: #718096;
  margin-bottom: 2rem;
}

.cards {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  justify-content: center;
}

.card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  padding: 1.5rem 2rem;
  min-width: 280px;
  text-decoration: none;
  color: inherit;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
}

.card-icon {
  font-size: 2.5rem;
}

.card-body h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #2d3748;
}

.card-desc {
  margin: 0.25rem 0 0.5rem;
  color: #718096;
  font-size: 0.9rem;
}

.card-count {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d89ef;
}
</style>
