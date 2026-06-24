<template>
  <div class="page">
    <header class="page__header">
      <h2 class="page__title">{{ t('dashboard.title') }}</h2>
      <p class="page__subtitle">{{ t('dashboard.subtitle') }}</p>
    </header>

    <!-- Stat tiles -->
    <div class="stats-row">
      <div class="stat">
        <span class="stat__icon stat__icon--calls">🛠️</span>
        <div>
          <div class="stat__value">{{ stats.totals.calls }}</div>
          <div class="stat__label">{{ t('dashboard.statCalls') }}</div>
        </div>
      </div>
      <div class="stat">
        <span class="stat__icon stat__icon--open">📂</span>
        <div>
          <div class="stat__value">{{ stats.totals.open }}</div>
          <div class="stat__label">{{ t('dashboard.statOpen') }}</div>
        </div>
      </div>
      <div v-if="stats.totals.employees !== null" class="stat">
        <span class="stat__icon stat__icon--employees">👥</span>
        <div>
          <div class="stat__value">{{ stats.totals.employees }}</div>
          <div class="stat__label">{{ t('dashboard.statEmployees') }}</div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
      <div class="chart-card chart-card--status">
        <h3 class="chart-card__title">{{ t('dashboard.chartStatus') }}</h3>
        <div class="chart-canvas">
          <Doughnut v-if="hasCalls" :data="statusChart" :options="doughnutOptions" />
          <p v-else class="chart-empty">{{ t('dashboard.empty') }}</p>
        </div>
      </div>

      <div class="chart-card chart-card--perday">
        <h3 class="chart-card__title">{{ t('dashboard.chartPerDay') }}</h3>
        <div class="chart-canvas">
          <Bar :data="perDayChart" :options="barOptions" />
        </div>
      </div>

      <div v-if="showPerEmployee" class="chart-card chart-card--wide">
        <h3 class="chart-card__title">{{ t('dashboard.chartPerEmployee') }}</h3>
        <div class="chart-canvas">
          <Bar :data="perEmployeeChart" :options="horizontalBarOptions" />
        </div>
      </div>
    </div>

    <!-- Quick access -->
    <h3 class="section-title">{{ t('dashboard.quickAccess') }}</h3>
    <div class="quick">
      <RouterLink to="/calls" class="quick-card">
        <span class="quick-card__icon quick-card__icon--calls">🛠️</span>
        <div>
          <span class="quick-card__title">{{ t('dashboard.callsTitle') }}</span>
          <span class="quick-card__desc">{{ t('dashboard.callsDesc') }}</span>
        </div>
      </RouterLink>

      <RouterLink v-if="can('manage employees')" to="/employees" class="quick-card">
        <span class="quick-card__icon quick-card__icon--employees">👥</span>
        <div>
          <span class="quick-card__title">{{ t('dashboard.employeesTitle') }}</span>
          <span class="quick-card__desc">{{ t('dashboard.employeesDesc') }}</span>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { Doughnut, Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
} from 'chart.js'
import api from '../api'
import { isAuthenticated, can } from '../auth'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement)

// Match the app typography and muted text colour used across the design system.
ChartJS.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif"
ChartJS.defaults.font.size = 12
ChartJS.defaults.color = '#6b7280'

const router = useRouter()
const { t } = useI18n()

// Status colours aligned with the badge tokens (info/warning/success/danger).
const STATUS_COLORS = {
  open: '#7c3aed',
  in_progress: '#d97706',
  done: '#16a34a',
  rejected: '#dc2626',
}

// Subtle grid lines matching the theme border colour.
const GRID = '#eef1f6'

const stats = ref({
  totals: { calls: 0, open: 0, employees: null },
  by_status: { open: 0, in_progress: 0, done: 0, rejected: 0 },
  per_day: [],
  per_employee: [],
  can_view_all: false,
})

const hasCalls = computed(() => stats.value.totals.calls > 0)
const showPerEmployee = computed(
  () => stats.value.can_view_all && stats.value.per_employee.length > 0
)

const statusChart = computed(() => {
  const keys = Object.keys(stats.value.by_status)
  return {
    labels: keys.map((k) => t(`status.${k}`)),
    datasets: [
      {
        data: keys.map((k) => stats.value.by_status[k]),
        backgroundColor: keys.map((k) => STATUS_COLORS[k]),
        borderWidth: 0,
        hoverOffset: 6,
      },
    ],
  }
})

const perDayChart = computed(() => ({
  labels: stats.value.per_day.map((d) => formatDay(d.date)),
  datasets: [
    {
      label: t('dashboard.statCalls'),
      data: stats.value.per_day.map((d) => d.count),
      backgroundColor: '#2d89ef',
      borderRadius: 6,
      maxBarThickness: 26,
    },
  ],
}))

const perEmployeeChart = computed(() => ({
  labels: stats.value.per_employee.map((e) => e.name),
  datasets: [
    {
      label: t('dashboard.statCalls'),
      data: stats.value.per_employee.map((e) => e.count),
      backgroundColor: '#6d28d9',
      borderRadius: 6,
      maxBarThickness: 22,
    },
  ],
}))

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '62%',
  plugins: {
    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
  },
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false }, border: { display: false } },
    y: {
      beginAtZero: true,
      ticks: { precision: 0 },
      grid: { color: GRID },
      border: { display: false },
    },
  },
}

const horizontalBarOptions = {
  indexAxis: 'y',
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: {
      beginAtZero: true,
      ticks: { precision: 0 },
      grid: { color: GRID },
      border: { display: false },
    },
    y: { grid: { display: false }, border: { display: false } },
  },
}

const formatDay = (iso) => {
  const [, m, d] = iso.split('-')
  return `${d}/${m}`
}

const loadStats = async () => {
  try {
    const { data } = await api.get('/stats')
    stats.value = data
  } catch (err) {
    console.error('Error loading stats:', err)
  }
}

onMounted(() => {
  if (!isAuthenticated()) {
    router.push('/login')
    return
  }
  loadStats()
})
</script>

<style scoped>
/* Stat tiles */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.1rem 1.4rem;
}

.stat__icon {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  border-radius: var(--radius-sm);
  font-size: 1.4rem;
  flex-shrink: 0;
}
.stat__icon--calls { background: var(--c-primary-50); }
.stat__icon--open { background: var(--c-info-bg); }
.stat__icon--employees { background: var(--c-warning-bg); }

.stat__value {
  font-size: 1.7rem;
  font-weight: 800;
  line-height: 1;
  font-variant-numeric: tabular-nums;
}
.stat__label {
  margin-top: 0.25rem;
  color: var(--c-text-muted);
  font-size: 0.85rem;
}

/* Charts */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
  margin-bottom: 1.75rem;
}

.chart-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.25rem 1.4rem;
}

/* Asymmetric layout: compact doughnut beside a wider time series,
   full-width workload bar underneath. */
.chart-card--status { grid-column: span 1; }
.chart-card--perday { grid-column: span 2; }
.chart-card--wide   { grid-column: 1 / -1; }

@media (max-width: 980px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
  .chart-card--status,
  .chart-card--perday,
  .chart-card--wide {
    grid-column: auto;
  }
}

.chart-card__title {
  margin: 0 0 1rem;
  font-size: 1rem;
  font-weight: 700;
}

.chart-canvas {
  position: relative;
  height: 260px;
}

.chart-empty {
  display: grid;
  place-items: center;
  height: 100%;
  color: var(--c-text-faint);
  font-style: italic;
}

/* Quick access */
.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin: 0 0 1rem;
}

.quick {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
  max-width: 760px;
}

.quick-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.1rem 1.3rem;
  color: inherit;
  text-decoration: none;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.quick-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: var(--c-border-strong);
}

.quick-card__icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-sm);
  font-size: 1.35rem;
  flex-shrink: 0;
}
.quick-card__icon--calls { background: var(--c-primary-50); }
.quick-card__icon--employees { background: var(--c-info-bg); }

.quick-card__title {
  display: block;
  font-weight: 700;
}
.quick-card__desc {
  display: block;
  margin-top: 0.2rem;
  color: var(--c-text-muted);
  font-size: 0.85rem;
}
</style>
