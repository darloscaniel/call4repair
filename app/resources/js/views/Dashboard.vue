<template>
  <div class="page">
    <header class="page__header">
      <h2 class="page__title">{{ t('dashboard.title') }}</h2>
      <p class="page__subtitle">{{ t('dashboard.subtitle') }}</p>
    </header>

    <div class="cards">
      <RouterLink to="/calls" class="card">
        <div class="card__icon card__icon--calls">🛠️</div>
        <div class="card__body">
          <h3 class="card__title">{{ t('dashboard.callsTitle') }}</h3>
          <p class="card__desc">{{ t('dashboard.callsDesc') }}</p>
        </div>
        <span class="card__count">{{ callsTotal }}</span>
      </RouterLink>

      <RouterLink v-if="can('manage employees')" to="/employees" class="card">
        <div class="card__icon card__icon--employees">👥</div>
        <div class="card__body">
          <h3 class="card__title">{{ t('dashboard.employeesTitle') }}</h3>
          <p class="card__desc">{{ t('dashboard.employeesDesc') }}</p>
        </div>
        <span class="card__count">{{ employeesTotal }}</span>
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
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.25rem;
  max-width: 760px;
}

.card {
  display: flex;
  align-items: center;
  gap: 1.1rem;
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.4rem 1.6rem;
  color: inherit;
  text-decoration: none;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
  border-color: var(--c-border-strong);
}

.card__icon {
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  border-radius: var(--radius-sm);
  font-size: 1.6rem;
  flex-shrink: 0;
}
.card__icon--calls { background: var(--c-primary-50); }
.card__icon--employees { background: var(--c-info-bg); }

.card__body {
  flex: 1;
}

.card__title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
}

.card__desc {
  margin: 0.25rem 0 0;
  color: var(--c-text-muted);
  font-size: 0.88rem;
  line-height: 1.4;
}

.card__count {
  font-size: 1.9rem;
  font-weight: 800;
  color: var(--c-primary);
  font-variant-numeric: tabular-nums;
}
</style>
