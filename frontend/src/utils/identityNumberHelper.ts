export function formatIdentityNumber(value: string): string {
  if (value.length !== 11) {
    return value
  }

  return `${value.slice(0, 6)}-${value.slice(6)}`
}
