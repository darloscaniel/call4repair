import { createRouter, createWebHistory } from 'vue-router'
import Funcionarios from '../views/Funcionarios.vue'
import Chamados from '../views/Chamados.vue'
import Login from '../views/Login.vue'
const routes = [
  { path: '/', redirect: '/login' },
  { path: '/funcionarios', component: Funcionarios },
  { path: '/chamados', component: Chamados },
  {path: '/login', component: Login }


]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
