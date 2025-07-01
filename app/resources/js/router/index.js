import { createRouter, createWebHistory } from 'vue-router'
import Funcionarios from '../views/Funcionarios.vue'
import Chamados from '../views/Chamados.vue'
import Login from '../views/Login.vue'
import Forms from '../views/Formulario.vue'

const requireAuth = (to, from, next) => {
  const token = sessionStorage.getItem('token')
  if (!token) {
    return next('/login')
  }
  next()
}

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/funcionarios', component: Funcionarios, beforeEnter: requireAuth }, // corrigido
  { path: '/chamados', component: Chamados, beforeEnter: requireAuth },         // corrigido
  { path: '/login', component: Login },
  { path: '/formulario', component: Forms}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/formulario']
  const authRequired = !publicPages.includes(to.path)
  const loggedIn = sessionStorage.getItem('token')

  if (authRequired && !loggedIn) {
    return next('/login')
  }

  next()
})


export default router
