<script setup lang="ts">
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import {getEmployee, type EmployeeDetails} from '@/api/employeesApi'
import { formatIdentityNumber } from '@/utils/identityNumberHelper'
import {formatDate, formatTimeAgo} from '@/utils/dateHelper'
import {formatGender} from "@/utils/genderHelper.ts";
import router from "@/router";
import {deleteEmployeeWithConfirm} from "@/utils/deleteHelper.ts";

const route = useRoute()
const employee = ref<EmployeeDetails | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)
const isDeleting = ref(false)

async function loadEmployee(id: string) {
  loading.value = true
  error.value = null
  employee.value = null

  try {
    employee.value = await getEmployee(id)
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Radās kļūda saņemot datus'
  } finally {
    loading.value = false
  }
}
async function handleDelete() {
  if (!employee.value || isDeleting.value) return

  isDeleting.value = true
  error.value = null

  try {
    const deleted = await deleteEmployeeWithConfirm(
      employee.value.id,
      employee.value.fullName,
    )

    if (deleted) {
      await router.push({ name: 'employees' })
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Neizdevās dzēst darbinieku'
  } finally {
    isDeleting.value = false
  }
}

function printEmployee() {
  window.print()
}

watch(
  () => route.params.id,
  (id) => loadEmployee(String(id)),
  { immediate: true },
)
</script>

<template>
  <div class="container">
    <RouterLink to="/" class="no-print">< Visi darbinieki</RouterLink>
    <template v-if="employee">
      <div class="heading text-center">
        <div></div>

        <div>
          <h1>{{ employee.fullName }}</h1>
        </div>

        <div>
          <div class="action-buttons no-print">
            <RouterLink
              :to="{ name: 'employee-edit', params: { id: employee.id } }"
              class="button button-warning"
              aria-label="Rediģēt darbinieku"
              title="Rediģēt"
            >
              <span class="material-symbols-outlined" aria-hidden="true">edit</span>
            </RouterLink>

            <button
              type="button"
              class="button button-danger"
              aria-label="Dzēst darbinieku"
              title="Dzēst darbinieku"
              :disabled="isDeleting"
              @click="handleDelete"
            >
              <span class="material-symbols-outlined" aria-hidden="true">delete</span>
            </button>

            <button type="button" class="button" @click="printEmployee">
              <span class="material-symbols-outlined" aria-hidden="true">print</span>
            </button>
          </div>
          <small class="text-muted">Pēdējo reizi atjaunots {{ formatTimeAgo(employee.updatedAt) }}</small>
        </div>
      </div>
    </template>
    <p v-if="loading">Ielādē datus...</p>

    <div v-else-if="error">
      <p class="text-danger">{{ error }}</p>
    </div>

    <template v-else-if="employee">
      <div class="section background-primary text-center">
        <dl class="personal-details-grid">
          <div>
            <dt>Vārds</dt>
            <dd>{{ employee.firstName }}</dd>
          </div>

          <div>
            <dt>Uzvārds</dt>
            <dd>{{ employee.lastName }}</dd>
          </div>

          <div>
            <dt>Dzimums</dt>
            <dd>{{ formatGender(employee.gender) }}</dd>
          </div>

          <div>
            <dt>Personas kods</dt>
            <dd>{{ formatIdentityNumber(employee.identityNumber) }}</dd>
          </div>

          <div>
            <dt>E-Pasts</dt>
            <dd>{{ employee.email }}</dd>
          </div>

          <div>
            <dt>Telefona Nr.</dt>
            <dd>{{ employee.phoneNumber }}</dd>
          </div>
        </dl>
        <hr/>
        <dl class="personal-details-grid">
          <div>
            <dt>Valsts</dt>
            <dd>{{ employee.countryCode }}</dd>
          </div>
          <div>
            <dt>Pilsēta</dt>
            <dd>{{ employee.city }}</dd>
          </div>
          <div>
            <dt>Pasta indekss</dt>
            <dd>{{ employee.postalCode }}</dd>
          </div>
          <div>
            <dt>Iela</dt>
            <dd>{{ employee.street }} {{ employee.streetNumber }}</dd>
          </div>
        </dl>
      </div>

      <h3>
        Iepriekšējās darba vietas
        {{ employee.previousEmployments.length ? '(' + employee.previousEmployments.length + ')' : '- Nav' }}</h3>

      <template v-if="employee.previousEmployments.length !== 0">
        <div
          v-for="employment in employee.previousEmployments"
          :key="employment.id"
          class="section background-secondary text-center"
        >
          <div class="employment-list">
            <h3>{{ employment.role }} <span class="text-muted">({{ formatDate(employment.startDate) }} - {{ formatDate(employment.endDate) }})</span></h3>
            <dl class="details-grid">
              <div>
                <dt>Uzņēmuma nosaukums</dt>
                <dd>{{ employment.businessName }}</dd>
              </div>
              <div>
                <dt>Reģistrācijas numurs</dt>
                <dd>{{ employment.businessRegistrationNumber }}</dd>
              </div>
              <div>
                <dt>Uzņēmuma adrese</dt>
                <dd>
                  {{ employment.businessCountryCode }},
                  {{ employment.businessCity }},
                  {{ employment.businessPostalCode }},
                  {{ employment.businessStreet }},
                  {{ employment.businessStreetNumber }}
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </template>
    </template>
  </div>
</template>

<style scoped>
.heading {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
}

.heading > div:last-child {
  justify-self: end;
  align-self: center;
}

.action-buttons {
  margin-bottom: 6px;
}

.personal-details-grid, .details-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 24px;
  margin: 0;
}

.details-grid {
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

dt {
  margin-bottom: 6px;
  font-size: 13px;
  color: var(--color-muted);
}

dd {
  margin: 0;
  overflow-wrap: anywhere;
  font-weight: 500;
}

.employment-list h3 {
  margin-bottom: 16px;
}

@media (max-width: 640px) {
  .details-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}

@media print {
  .no-print {
    display: none !important;
  }

  .section {
    break-inside: avoid;
  }
}
</style>
