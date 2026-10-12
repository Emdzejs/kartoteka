
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getEmployees, type EmployeeSummary } from '@/api/employeesApi'
import {formatTimeAgo} from "@/utils/dateHelper.ts";
import {formatIdentityNumber} from "@/utils/identityNumberHelper.ts";

const employees = ref<EmployeeSummary[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

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

onMounted(loadEmployees)
</script>

<template>
  <div class="container">
    <div class="header">
      <h1>Darbinieki</h1>
      <div>
        <RouterLink :to="{ name: 'employee-create' }" class="button">
          Pievienot darbinieku
        </RouterLink>
        <button type="button" :disabled="loading" @click="loadEmployees">
          Ielādēt vēlreiz
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
            <th></th>
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
            <td>
              <small class="text-muted">
                Izveidots {{ formatTimeAgo(employee.createdAt) }} <br/>
                <span v-if="employee.createdAt !== employee.updatedAt">
                  Pēdējo reizi atjaunots {{ formatTimeAgo(employee.updatedAt) }}
                </span>
              </small>
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
