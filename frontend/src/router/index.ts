import { createRouter, createWebHistory } from 'vue-router'
import EmployeesView from '@/views/EmployeesView.vue'
import EmployeeDetailsView from "@/views/EmployeeDetailsView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'employees',
      component: EmployeesView,
    },
    {
      path: '/employees/:id',
      name: 'employee-details',
      component: EmployeeDetailsView,
    },
  ],
})

export default router
