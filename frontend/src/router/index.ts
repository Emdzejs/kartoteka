import { createRouter, createWebHistory } from 'vue-router'
import EmployeesView from '@/views/EmployeesView.vue'
import EmployeeDetailsView from "@/views/EmployeeDetailsView.vue";
import CreateEmployeeView from "@/views/CreateEmployeeView.vue";
import EditEmployeeView from "@/views/EditEmployeeView.vue";

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
    {
      path: '/employees/new',
      name: 'employee-create',
      component: CreateEmployeeView,
    },
    {
      path: '/employees/:id/edit',
      name: 'employee-edit',
      component: EditEmployeeView,
    },
  ],
})

export default router
