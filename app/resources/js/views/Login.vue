<template>
  <div class="login-container">
    <form @submit.prevent="login" class="login-card">
      <div class="login-brand">
        <span class="login-brand__logo">🔧</span>
        <span class="login-brand__name">Call4Repair</span>
      </div>
      <h2 class="login-title">{{ t('login.title') }}</h2>

      <div class="field">
        <label for="email">{{ t('login.email') }}</label>
        <input class="input" v-model="email" type="email" id="email" required />
      </div>

      <div class="field">
        <label for="password">{{ t('login.password') }}</label>
        <input class="input" v-model="password" type="password" id="password" required />
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>

      <button type="submit" class="btn btn--primary btn--block">{{ t('login.submit') }}</button>
      <button type="button" @click="goToPublicForm" class="btn btn--ghost btn--block public-btn">
        {{ t('login.openCall') }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import api from '../api'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { setAuth, clearAuth } from '../auth'

const email = ref('')
const password = ref('')
const error = ref(null)
const router = useRouter()
const { t } = useI18n()

const goToPublicForm = () => {
  router.push('/new-call')
}

onBeforeMount(() => {
  clearAuth()
})

const login = async () => {
  error.value = null
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    })

    setAuth(response.data)
    router.push('/dashboard')
  } catch (err) {
    if (err.response && err.response.status === 401) {
      error.value = t('login.invalidCredentials')
    } else {
      error.value = t('login.genericError')
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  background:
    radial-gradient(900px 500px at 100% 0%, rgba(45, 137, 239, .10), transparent 60%),
    radial-gradient(700px 500px at 0% 100%, rgba(109, 40, 217, .08), transparent 55%),
    var(--c-bg);
  padding: 1.5rem;
}

.login-card {
  background: var(--c-surface);
  padding: 2.25rem;
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  width: 100%;
  max-width: 400px;
}

.login-brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 0.5rem;
}
.login-brand__logo {
  font-size: 1.6rem;
}
.login-brand__name {
  font-size: 1.35rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.login-title {
  margin: 0 0 1.75rem;
  text-align: center;
  font-size: 1.05rem;
  font-weight: 500;
  color: var(--c-text-muted);
}

.error-message {
  background: var(--c-danger-bg);
  color: var(--c-danger);
  border-radius: var(--radius-sm);
  padding: 0.6rem 0.8rem;
  margin-bottom: 1rem;
  font-size: 0.85rem;
  text-align: center;
}

.public-btn {
  margin-top: 0.7rem;
}
</style>
