
<script setup lang="ts">
import { ref, nextTick } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import EmployeeForm from '@/components/EmployeeForm.vue'
import { createEmployee, type EmployeeInput } from '@/api/employeesApi'

const router = useRouter()

const saving = ref(false)
const error = ref<string | null>(null)
const errorElement = ref<HTMLElement | null>(null)

async function handleSubmit(input: EmployeeInput) {
  saving.value = true
  error.value = null

  try {
    const id = await createEmployee(input)

    await router.push({
      name: 'employee-details',
      params: { id },
    })
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Neizdevās izveidot darbinieku'

    await nextTick()

    errorElement.value?.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    })
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <main class="container">
    <RouterLink to="/">< Visi Darbinieki</RouterLink>
    <h1>Pievienot darbinieku</h1>
    <p v-if="error" ref="errorElement" class="error">{{ error }}</p>

    <EmployeeForm :isSaving="saving" @submit="handleSubmit" />
  </main>
</template>
