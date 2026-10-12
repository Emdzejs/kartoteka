import { deleteEmployee } from '@/api/employeesApi'

export async function deleteEmployeeWithConfirm(
  id: string,
  name: string,
): Promise<boolean> {
  if (!window.confirm(`Vai tiešām vēlaties dzēst darbinieku ${name}?`)) {
    return false
  }

  await deleteEmployee(id)

  return true
}
