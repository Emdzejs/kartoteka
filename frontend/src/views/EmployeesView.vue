
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getEmployees, type EmployeeSummary } from '@/api/employeesApi'
import {formatIdentityNumber} from "@/utils/identityNumberHelper.ts";
import {deleteEmployeeWithConfirm} from "@/utils/deleteHelper.ts";

const employees = ref<EmployeeSummary[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const deletingId = ref<string | null>(null)

async function loadEmployees() {
  loading.value = true
  error.value = null

  try {
    employees.value = await getEmployees()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Radās kļūda saņemot datus'
  } finally {
    loading.value = false
  }
}

async function handleDelete(employee: EmployeeSummary) {
  if (deletingId.value !== null) return

  deletingId.value = employee.id
  error.value = null

  try {
    const deleted = await deleteEmployeeWithConfirm(employee.id, employee.fullName)

    if (deleted) {
      await loadEmployees()
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Neizdevās dzēst darbinieku'
  } finally {
    deletingId.value = null
  }
}

onMounted(loadEmployees)
</script>

<template>
  <div class="container">
    <div class="header">
      <h1>Darbinieki</h1>
      <div class="action-buttons">
        <RouterLink
          :to="{ name: 'employee-create' }"
          class="button button-success"
          aria-label="Pievienot darbinieku"
          title="Pievienot darbinieku"
        >
          <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
        </RouterLink>
        <button
          type="button"
          class="button button-outline"
          :disabled="loading"
          aria-label="Atjaunot darbinieku sarakstu"
          title="Atjaunot darbinieku sarakstu"
          @click="loadEmployees"
        >
          <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
        </button>
      </div>
    </div>

    <div class="text-center">
      <p v-if="loading">Ielādē datus...</p>

      <div v-else-if="error">
        <p class="text-danger">{{ error }}</p>
        <button type="button" @click="loadEmployees">Mēģināt vēlreiz</button>
      </div>

      <p v-else-if="employees.length === 0">
        Pašreiz nav izveidots neviens darbinieks
      </p>

      <div v-else class="table-wrapper">
        <table>
          <thead>
          <tr>
            <th>Vārds</th>
            <th>Personas kods</th>
            <th>Kontakti</th>
            <th/>
          </tr>
          </thead>

          <tbody>
          <tr v-for="employee in employees" :key="employee.id">
            <td>
              <RouterLink :to="{ name: 'employee-details', params: { id: employee.id } }">
                {{ employee.fullName }}
              </RouterLink>
            </td>
            <td>{{ formatIdentityNumber(employee.identityNumber) }}</td>
            <td>
              {{employee.phoneNumber}} <br/>
              <small class="text-muted">{{ employee.email }}</small>
            </td>
            <td class="action-buttons">
              <RouterLink
                :to="{ name: 'employee-details', params: { id: employee.id } }"
                class="button"
                aria-label="Apskatīt darbinieku"
                title="Apskatīt darbinieku"
              >
                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
              </RouterLink>

              <RouterLink
                :to="{ name: 'employee-edit', params: { id: employee.id } }"
                class="button button-warning"
                aria-label="Rediģēt darbinieku"
                title="Rediģēt darbinieku"
              >
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
              </RouterLink>
              <button
                type="button"
                class="button-danger"
                aria-label="Dzēst darbinieku"
                title="Dzēst darbinieku"
                :disabled="deletingId !== null"
                @click.stop="handleDelete(employee)"
              >
                  <span class="material-symbols-outlined" aria-hidden="true">delete</span>
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
}
</style>
