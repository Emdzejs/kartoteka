export interface EmployeeSummary {
  id: string
  identityNumber: string
  fullName: string
  email: string
  phoneNumber: string
  createdAt: string
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

