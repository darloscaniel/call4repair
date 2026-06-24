import { createRouter, createWebHistory } from 'vue-router'
import Employees from '../views/Employees.vue'
import Calls from '../views/Calls.vue'
import Login from '../views/Login.vue'
import PublicCallForm from '../views/PublicCallForm.vue'

const publicPages = ['/login', '/new-call']

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/employees', component: Employees },
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
  const loggedIn = sessionStorage.getItem('token')

  if (authRequired && !loggedIn) {
    return next('/login')
  }

  next()
})

export default router
