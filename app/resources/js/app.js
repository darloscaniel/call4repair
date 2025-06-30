import { createApp } from 'vue'
import '../css/app.css'
import App from './components/App.vue'
import router from './router'
import '@fortawesome/fontawesome-free/css/all.css'

createApp(App).use(router).mount('#app')
