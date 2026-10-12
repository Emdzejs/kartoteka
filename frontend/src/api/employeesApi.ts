export interface EmployeeSummary {
  id: string
  identityNumber: string
  fullName: string
  email: string
  phoneNumber: string
  updatedAt: string
}

export async function getEmployees(): Promise<EmployeeSummary[]> {
  const response = await fetch('/api/employees')

  if (!response.ok) {
    throw new Error('Radās kļūda ielādējot darbiniekus')
  }

  return (await response.json()) as EmployeeSummary[]
}

export interface PreviousEmploymentDetails {
  id: string
  businessRegistrationNumber: string
  businessName: string
  role: string
  startDate: string
  endDate: string
  businessCountryCode: string
  businessPostalCode: string
  businessCity: string
  businessStreet: string
  businessStreetNumber: string
}

export interface EmployeeDetails {
  id: string
  identityNumber: string
  fullName: string
  firstName: string
  lastName: string
  email: string
  gender: string
  phoneNumber: string
  countryCode: string
  postalCode: string
  city: string
  street: string
  streetNumber: string
  createdAt: string
  updatedAt: string
  previousEmployments: PreviousEmploymentDetails[]
}

export async function getEmployee(id: string): Promise<EmployeeDetails> {
  const response = await fetch(`/api/employees/${encodeURIComponent(id)}`)

  if (response.status === 404) {
    throw new Error('Darbinieks netika atrasts')
  }

  if (!response.ok) {
    throw new Error('Radās kļūda ielādējot darbinieku')
  }

  return (await response.json()) as EmployeeDetails
}

export interface PreviousEmploymentInput {
  id?: string
  businessRegistrationNumber: string
  businessName: string
  role: string
  startDate: string
  endDate: string
  businessCountryCode: string
  businessPostalCode: string
  businessCity: string
  businessStreet: string
  businessStreetNumber: string
}

export interface EmployeeInput {
  identityNumber: string
  firstName: string
  lastName: string
  gender: string
  email: string
  phoneNumber: string
  countryCode: string
  postalCode: string
  city: string
  street: string
  streetNumber: string
  previousEmployments: PreviousEmploymentInput[]
}

export async function createEmployee(input: EmployeeInput): Promise<string> {
  const response = await fetch('/api/employees', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  })

  if (!response.ok) {
    const data: { error?: string } | null = await response
      .json()
      .catch(() => null)

    throw new Error(data?.error ?? 'Neizdevās izveidot darbinieku')
  }

  const data = (await response.json()) as { id: string }

  return data.id
}

export async function updateEmployee(
  id: string,
  input: EmployeeInput,
): Promise<void> {
  const response = await fetch(`/api/employees/${encodeURIComponent(id)}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  })

  if (!response.ok) {
    if (response.status === 409) {
      throw new Error('Darbinieks ar šādu personas kodu jau eksistē')
    }

    throw new Error('Neizdevās rediģēt darbinieku')
  }
}

export async function deleteEmployee(id: string): Promise<void> {
  const response = await fetch(`/api/employees/${encodeURIComponent(id)}`, {
    method: 'DELETE',
  })

  if (!response.ok) {
    throw new Error('Neizdevās dzēst darbinieku')
  }
}
