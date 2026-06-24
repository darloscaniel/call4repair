<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h3>{{ isEditing ? t('employeeModal.editTitle') : t('employeeModal.createTitle') }}</h3>

      <form @submit.prevent="submitForm">
        <div class="field">
          <label for="name">{{ t('employeeModal.name') }}</label>
          <input
            id="name"
            class="input"
            v-model="form.name"
            type="text"
            :placeholder="t('employeeModal.namePlaceholder')"
            required
          />
        </div>

        <div class="field">
          <label for="age">{{ t('employeeModal.age') }}</label>
          <input
            id="age"
            class="input"
            v-model.number="form.age"
            type="number"
            min="18"
            max="100"
            :placeholder="t('employeeModal.agePlaceholder')"
            required
          />
        </div>

        <div class="field">
          <label for="phone">{{ t('employeeModal.phone') }}</label>
          <input
            id="phone"
            class="input"
            v-model="form.phone"
            type="tel"
            :placeholder="t('employeeModal.phonePlaceholder')"
            required
            pattern="^\+?[0-9\s\-]{8,15}$"
          />
        </div>

        <div class="field">
          <label for="email">{{ t('employeeModal.email') }}</label>
          <input
            id="email"
            class="input"
            v-model="form.email"
            type="email"
            :placeholder="t('employeeModal.emailPlaceholder')"
            required
          />
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn--ghost" @click="$emit('close')">
            {{ t('employeeModal.cancel') }}
          </button>
          <button type="submit" class="btn btn--success">{{ t('employeeModal.save') }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  employee: {
    type: Object,
    default: () => ({ name: '', age: '', phone: '', email: '' })
  }
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
  id: null,
  name: '',
  age: null,
  phone: '',
  email: ''
})

const isEditing = computed(() => !!form.id)

watch(() => props.employee, (newEmployee) => {
  form.id = newEmployee.id || null
  form.name = newEmployee.name || ''
  form.age = newEmployee.age || null
  form.phone = newEmployee.phone || ''
  form.email = newEmployee.email || ''
}, { immediate: true })

const submitForm = () => {
  emit('save', { ...form })
}
</script>
