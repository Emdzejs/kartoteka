
<script setup lang="ts">
import {nextTick, onMounted, ref} from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import EmployeeForm from '@/components/EmployeeForm.vue'
import {
  getEmployee,
  updateEmployee,
  type EmployeeDetails,
  type EmployeeInput,
} from '@/api/employeesApi'

const route = useRoute()
const router = useRouter()
const id = String(route.params.id)

const employee = ref<EmployeeDetails | null>(null)
const loading = ref(true)
const isSaving = ref(false)
const error = ref<string | null>(null)
const errorElement = ref<HTMLElement | null>(null)

onMounted(async () => {
  try {
    employee.value = await getEmployee(id)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Radās kļūda saņemot datus'
  } finally {
    loading.value = false
  }
})

async function handleSubmit(input: EmployeeInput) {
  isSaving.value = true
  error.value = null

  try {
    await updateEmployee(id, input)

    await router.push({
      name: 'employee-details',
      params: { id },
    })
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Neizdevās saglabāt darbinieku'

    await nextTick()

    errorElement.value?.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    })
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <main class="container">
    <RouterLink :to="{ name: 'employee-details', params: { id } }">
      < Darbinieka informācija
    </RouterLink>

    <h1>Rediģēt darbinieku</h1>

    <p v-if="loading">Ielādē darbinieku...</p>

    <p v-if="error" ref="errorElement" class="error">{{ error }}</p>

    <EmployeeForm
      v-if="employee"
      :initial-data="employee"
      :is-saving="isSaving"
      @submit="handleSubmit"
    />
  </main>
</template>
