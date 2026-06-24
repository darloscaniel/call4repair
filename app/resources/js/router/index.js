import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Employees from '../views/Employees.vue'
import Calls from '../views/Calls.vue'
import Login from '../views/Login.vue'
import PublicCallForm from '../views/PublicCallForm.vue'
import { isAuthenticated, can } from '../auth'

const publicPages = ['/login', '/new-call']

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', component: Dashboard },
  { path: '/employees', component: Employees, meta: { permission: 'manage employees' } },
  { path: '/calls', component: Calls },
  { path: '/login', component: Login },
  { path: '/new-call', component: PublicCallForm },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authRequired = !publicPages.includes(to.path)

  if (authRequired && !isAuthenticated()) {
    return next('/login')
  }

  // UI-level permission gate (server still enforces authorization).
  if (to.meta.permission && !can(to.meta.permission)) {
    return next('/dashboard')
  }

  next()
})

export default router
