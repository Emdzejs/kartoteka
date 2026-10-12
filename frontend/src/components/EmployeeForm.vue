<script setup lang="ts">
import { reactive } from 'vue'
import type { EmployeeInput } from '@/api/employeesApi'

defineProps<{
  isSaving?: boolean
}>()

const emit = defineEmits<{
  submit: [input: EmployeeInput]
}>()

const form = reactive<EmployeeInput>({
  identityNumber: '',
  firstName: '',
  lastName: '',
  gender: '',
  email: '',
  phoneNumber: '',
  countryCode: 'LV',
  postalCode: '',
  city: '',
  street: '',
  streetNumber: '',
  previousEmployments: [],
})

function addPreviousEmployment() {
  form.previousEmployments.push({
    businessRegistrationNumber: '',
    businessName: '',
    role: '',
    startDate: '',
    endDate: '',
    businessCountryCode: 'LV',
    businessPostalCode: '',
    businessCity: '',
    businessStreet: '',
    businessStreetNumber: '',
  })
}

function removePreviousEmployment(index: number) {
  form.previousEmployments.splice(index, 1)
}

function submit() {
  emit('submit', { ...form })
}
</script>

<template>
  <form @submit.prevent="submit">
    <div class="section background-primary text-center">
      <div class="form-grid">
        <div class="form-field">
          <label for="firstName">Vārds</label>
          <input
            id="firstName"
            v-model.trim="form.firstName"
            type="text"
            maxlength="100"
            required
          />
        </div>

        <div class="form-field">
          <label for="lastName">Uzvārds</label>
          <input
            id="lastName"
            v-model.trim="form.lastName"
            type="text"
            maxlength="100"
            required
          />
        </div>

        <div class="form-field">
          <label for="identityNumber">Personas kods</label>
          <input
            id="identityNumber"
            v-model.trim="form.identityNumber"
            type="text"
            placeholder="123456-78901"
            pattern="[0-9]{6}-?[0-9]{5}"
            required
          />
        </div>

        <div class="form-field">
          <label for="gender">Dzimums</label>
          <select id="gender" v-model="form.gender" required>
            <option value="" disabled>Izvēlieties dzimumu</option>
            <option value="male">Vīrietis</option>
            <option value="female">Sieviete</option>
          </select>
        </div>

        <div class="form-field">
          <label for="email">E-pasts</label>
          <input
            id="email"
            v-model.trim="form.email"
            type="email"
            maxlength="254"
            required
          />
        </div>

        <div class="form-field">
          <label for="phoneNumber">Tālruņa numurs</label>
          <input
            id="phoneNumber"
            v-model.trim="form.phoneNumber"
            type="tel"
            placeholder="+37120000000"
            maxlength="16"
            required
          />
        </div>
      </div>

      <hr/>

      <div class="form-grid">
        <div class="form-field">
          <label for="countryCode">Valsts kods</label>
          <input
            id="countryCode"
            v-model.trim="form.countryCode"
            type="text"
            maxlength="2"
            required
          />
        </div>

        <div class="form-field">
          <label for="postalCode">Pasta indekss</label>
          <input
            id="postalCode"
            v-model.trim="form.postalCode"
            type="text"
            placeholder="LV-1010"
            maxlength="20"
            required
          />
        </div>

        <div class="form-field">
          <label for="city">Pilsēta</label>
          <input
            id="city"
            v-model.trim="form.city"
            type="text"
            maxlength="120"
            required
          />
        </div>

        <div class="street-row">
          <div class="form-field">
            <label for="street">Iela</label>
            <input
              id="street"
              v-model.trim="form.street"
              type="text"
              maxlength="255"
              required
            />
          </div>

          <div class="form-field">
            <label for="streetNumber">Numurs</label>
            <input
              id="streetNumber"
              v-model.trim="form.streetNumber"
              type="text"
              maxlength="20"
              required
            />
          </div>
        </div>
      </div>
    </div>

    <h2>Iepriekšējās darbavietas</h2>
    <div
      v-for="(employment, index) in form.previousEmployments"
      :key="index"
      class="section background-secondary text-center"
    >
      <div class="employment-card-header">
        <h3>Darbavieta Nr. {{ index + 1 }}</h3>

        <button
          type="button"
          class="button-danger"
          :disabled="isSaving"
          @click="removePreviousEmployment(index)"
        >
          Noņemt
        </button>
      </div>

      <div class="form-field">
        <label :for="`role-${index}`">Amats</label>
        <input
          :id="`role-${index}`"
          v-model.trim="employment.role"
          type="text"
          required
        />
      </div>
      <div class="form-grid">
        <div class="form-field">
          <label :for="`businessName-${index}`">Uzņēmuma nosaukums</label>
          <input
            :id="`businessName-${index}`"
            v-model.trim="employment.businessName"
            type="text"
            maxlength="100"
            required
          />
        </div>

        <div class="form-field">
          <label :for="`businessRegistrationNumber-${index}`">
            Reģistrācijas numurs
          </label>
          <input
            :id="`businessRegistrationNumber-${index}`"
            v-model.trim="employment.businessRegistrationNumber"
            type="text"
            maxlength="50"
            required
          />
        </div>

        <div class="form-field">
          <label :for="`startDate-${index}`">Darba sākuma datums</label>
          <input
            :id="`startDate-${index}`"
            v-model="employment.startDate"
            type="date"
            required
          />
        </div>

        <div class="form-field">
          <label :for="`endDate-${index}`">Darba beigu datums</label>
          <input
            :id="`endDate-${index}`"
            v-model="employment.endDate"
            type="date"
            :min="employment.startDate || undefined"
            required
          />
        </div>
      </div>

      <hr class="secondary" />

      <div class="form-grid">
        <div class="form-field">
          <label :for="`businessCountryCode-${index}`">Uzņēmuma adreses valsts kods</label>
          <input
            :id="`businessCountryCode-${index}`"
            v-model.trim="employment.businessCountryCode"
            type="text"
            maxlength="2"
            required
          />
        </div>

        <div class="form-field">
          <label :for="`businessPostalCode-${index}`">Uzņēmuma adreses pasta indekss</label>
          <input
            :id="`businessPostalCode-${index}`"
            v-model.trim="employment.businessPostalCode"
            type="text"
            maxlength="20"
            required
          />
        </div>

        <div class="form-field">
          <label :for="`businessCity-${index}`">Uzņēmuma adreses pilsēta</label>
          <input
            :id="`businessCity-${index}`"
            v-model.trim="employment.businessCity"
            type="text"
            max="120"
            required
          />
        </div>

        <div class="street-row">
          <div class="form-field">
            <label :for="`businessStreet-${index}`">Uzņēmuma adreses iela</label>
            <input
              :id="`businessStreet-${index}`"
              v-model.trim="employment.businessStreet"
              type="text"
              max="255"
              required
            />
          </div>

          <div class="form-field">
            <label :for="`businessStreetNumber-${index}`">Numurs</label>
            <input
              :id="`businessStreetNumber-${index}`"
              v-model.trim="employment.businessStreetNumber"
              type="text"
              max="20"
              required
            />
          </div>
        </div>
      </div>
    </div>
    <button
      type="button"
      class="button-outline"
      :disabled="isSaving"
      @click="addPreviousEmployment"
    >
      {{ form.previousEmployments.length ? 'Pievienot vēl darbavietu' : 'Pievienot darbavietu' }}
    </button>

    <div class="text-center">
      <button type="submit" class="button-success" :disabled="isSaving">
        {{ isSaving ? 'Saglabā...' : 'Saglabāt darbinieku' }}
      </button>
    </div>
  </form>
</template>

<style scoped>
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.street-row {
  display: grid;
  grid-template-columns: minmax(0, 3fr) minmax(0, 1fr);
  gap: 8px;
}

.employment-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
