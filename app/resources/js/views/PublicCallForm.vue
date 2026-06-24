<template>
  <div class="public-container">
    <div class="public-card">
      <div class="public-brand">
        <span class="public-brand__logo">🔧</span>
        <span class="public-brand__name">{{ t('app.title') }}</span>
      </div>
      <h2 class="public-heading">{{ t('publicForm.heading') }}</h2>

      <form @submit.prevent="submitCall">
        <div class="field">
          <label for="name">{{ t('publicForm.name') }}</label>
          <input
            class="input"
            type="text"
            id="name"
            v-model="form.name"
            required
            :placeholder="t('publicForm.namePlaceholder')"
          />
        </div>

        <div class="field">
          <label for="phone">{{ t('publicForm.phone') }}</label>
          <input
            class="input"
            type="text"
            id="phone"
            v-model="form.phone"
            required
            :placeholder="t('publicForm.phonePlaceholder')"
          />
        </div>

        <div class="field">
          <label for="description">{{ t('publicForm.description') }}</label>
          <textarea
            class="textarea"
            id="description"
            v-model="form.description"
            required
            :placeholder="t('publicForm.descriptionPlaceholder')"
            rows="5"
          ></textarea>
        </div>

        <div class="field">
          <label for="status">{{ t('publicForm.status') }}</label>
          <input class="input" type="text" id="status" :value="t('status.open')" readonly />
        </div>

        <button type="submit" class="btn btn--primary btn--block">{{ t('publicForm.submit') }}</button>
        <button type="button" class="btn btn--ghost btn--block back-btn" @click="goToLogin">
          {{ t('publicForm.back') }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '../api'

const { t } = useI18n()
const router = useRouter()

const form = ref({
  name: '',
  phone: '',
  description: '',
})

const goToLogin = () => {
  router.push('/login')
}

const submitCall = async () => {
  try {
    const payload = {
      customer_name: form.value.name,
      phone: form.value.phone,
      description: form.value.description,
      status: 'open',
    }

    await api.post('/calls', payload)

    alert(t('publicForm.success'))
    form.value = { name: '', phone: '', description: '' }
  } catch (error) {
    console.error('Error creating call:', error)
    if (error.response && error.response.status === 429) {
      alert(t('publicForm.rateLimit'))
    } else {
      alert(t('publicForm.error'))
    }
  }
}
</script>

<style scoped>
.public-container {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
  width: 100%;
  background:
    radial-gradient(900px 500px at 100% 0%, rgba(45, 137, 239, .10), transparent 60%),
    var(--c-bg);
  padding: 2.5rem 1.5rem;
  overflow-y: auto;
}

.public-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  width: 100%;
  max-width: 560px;
  padding: 2.25rem;
}

.public-brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 0.4rem;
}
.public-brand__logo {
  font-size: 1.6rem;
}
.public-brand__name {
  font-size: 1.35rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.public-heading {
  margin: 0 0 1.75rem;
  text-align: center;
  font-size: 1.05rem;
  font-weight: 500;
  color: var(--c-text-muted);
}

.back-btn {
  margin-top: 0.7rem;
}
</style>
