import { createI18n } from 'vue-i18n'
import ptBR from './locales/pt-BR.json'

const i18n = createI18n({
  legacy: false, // Composition API mode (enables useI18n / t in <script setup>)
  locale: 'pt-BR',
  fallbackLocale: 'pt-BR',
  messages: {
    'pt-BR': ptBR,
  },
})

export default i18n
