<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h3>{{ t('callModal.title') }}</h3>

      <div class="field">
        <label>{{ t('callModal.customer') }}</label>
        <input v-model="edited.customer_name" type="text" class="input" />
      </div>

      <div class="field">
        <label>{{ t('callModal.phone') }}</label>
        <input v-model="edited.phone" type="tel" class="input" />
      </div>

      <div class="field">
        <label>{{ t('callModal.description') }}</label>
        <textarea v-model="edited.description" class="textarea" rows="3"></textarea>
      </div>

      <div class="field">
        <label>{{ t('callModal.status') }}</label>
        <select v-model="edited.status" class="select">
          <option v-for="option in statusOptions" :key="option" :value="option">
            {{ t(`status.${option}`) }}
          </option>
        </select>
      </div>

      <div class="field">
        <label>{{ t('callModal.employees') }}</label>
        <div class="scrollbox">
          <label
            class="check"
            v-for="employee in employees"
            :key="employee.id"
          >
            <input
              type="checkbox"
              :value="employee.id"
              v-model="editedEmployeeIds"
            />
            <span>{{ employee.name }}</span>
          </label>
          <p v-if="!employees.length" class="muted">—</p>
        </div>
      </div>

      <div class="modal-actions">
        <button class="btn btn--ghost" @click="$emit('close')">{{ t('callModal.cancel') }}</button>
        <button class="btn btn--success" @click="save">{{ t('callModal.save') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  call: Object,
  employees: Array
})

const emit = defineEmits(['close', 'save'])

const edited = ref({})
const editedEmployeeIds = ref([])

// Allowed status transitions — must mirror the backend CallStatus enum.
const TRANSITIONS = {
  open: ['in_progress', 'rejected'],
  in_progress: ['done', 'rejected'],
  done: [],
  rejected: ['open'],
}

// The current status is always selectable, plus its valid next states.
const statusOptions = computed(() => {
  const current = props.call?.status
  return [current, ...(TRANSITIONS[current] || [])]
})

watch(() => props.call, (newCall) => {
  edited.value = {
    id: newCall.id,
    customer_name: newCall.customer_name,
    phone: newCall.phone,
    description: newCall.description,
    status: newCall.status,
    employees: [...(newCall.employees || [])]
  }
  editedEmployeeIds.value = edited.value.employees.map((e) => Number(e.id))
}, { immediate: true })

const save = () => {
  edited.value.employees = props.employees.filter((employee) =>
    editedEmployeeIds.value.includes(employee.id)
  )
  emit('save', edited.value)
}
</script>

<style scoped>
.scrollbox {
  max-height: 170px;
  overflow-y: auto;
  border: 1px solid var(--c-border-strong);
  border-radius: var(--radius-sm);
  padding: 0.4rem;
  background: var(--c-surface-2);
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.check {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.5rem 0.6rem;
  border-radius: 8px;
  font-size: 0.92rem;
  cursor: pointer;
  transition: background-color .12s ease;
}
.check:hover {
  background: var(--c-primary-50);
}
.check input {
  width: 16px;
  height: 16px;
  accent-color: var(--c-primary);
  cursor: pointer;
}

.muted {
  color: var(--c-text-faint);
  font-style: italic;
  margin: 0.4rem 0.6rem;
}
</style>
