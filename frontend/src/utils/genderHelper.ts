export function formatGender(gender: string): string {
  switch (gender) {
    case 'male':
      return 'Vīrietis'
    case 'female':
      return 'Sieviete'
    default:
      return gender
  }
}
