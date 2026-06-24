<template>
  <h2 class="title">{{ t('publicForm.title') }}</h2>
  <div class="form-container">

    <h2>{{ t('publicForm.heading') }}</h2>
    <form @submit.prevent="submitCall">
      <div class="form-group">
        <label for="name">{{ t('publicForm.name') }}</label>
        <input
          type="text"
          id="name"
          v-model="form.name"
          required
          :placeholder="t('publicForm.namePlaceholder')"
        />
      </div>

      <div class="form-group">
        <label for="phone">{{ t('publicForm.phone') }}</label>
        <input
          type="text"
          id="phone"
          v-model="form.phone"
          required
          :placeholder="t('publicForm.phonePlaceholder')"
        />
      </div>

      <div class="form-group">
        <label for="description">{{ t('publicForm.description') }}</label>
        <textarea
          id="description"
          v-model="form.description"
          required
          :placeholder="t('publicForm.descriptionPlaceholder')"
          rows="5"
        ></textarea>
      </div>

      <div class="form-group">
        <label for="status">{{ t('publicForm.status') }}</label>
        <input
          type="text"
          id="status"
          :value="t('status.open')"
          readonly
        />
      </div>

      <button type="submit" class="submit-btn">{{ t('publicForm.submit') }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '../api'

const { t } = useI18n()

const form = ref({
  name: '',
  phone: '',
  description: '',
})

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
.form-container {
  max-width: 800px;
  margin: 20px auto;
  padding: 30px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 28px;
}

.form-group {
  margin-bottom: 25px;
  width: 95%;
}

label {
  display: block;
  margin-bottom: 10px;
  font-weight: bold;
  font-size: 18px;
  color: #444;
}

input,
textarea {
  width: 100%;
  padding: 15px;
  border: 2px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  transition: border 0.3s;
}

input:focus,
textarea:focus {
  border-color: #4CAF50;
  outline: none;
}

textarea {
  min-height: 150px;
}

input[readonly] {
  background-color: #f9f9f9;
  cursor: not-allowed;
}

.submit-btn {
  width: 100%;
  padding: 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 10px;
}

.submit-btn:hover {
  background-color: #45a049;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>
