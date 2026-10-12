
const relativeFormatter = new Intl.RelativeTimeFormat('lv-LV', {
  numeric: 'always',
})

const SECONDS_PER_DAY = 86400
const MAX_RELATIVE_DAYS = 11

export function formatTimeAgo(value: string): string {
  const date = new Date(value)
  const seconds = Math.floor((Date.now() - date.getTime()) / 1000)

  if (seconds < 60) {
    return 'Tikko'
  }

  if (seconds < 3600) {
    return relativeFormatter.format(-Math.floor(seconds / 60), 'minute')
  }

  if (seconds < SECONDS_PER_DAY) {
    return relativeFormatter.format(-Math.floor(seconds / 3600), 'hour')
  }

  if (seconds < MAX_RELATIVE_DAYS * SECONDS_PER_DAY) {
    return relativeFormatter.format(-Math.floor(seconds / SECONDS_PER_DAY), 'day')
  }

  return formatAbsoluteDate(date)
}

function formatAbsoluteDate(date: Date): string {
  const pad = (value: number): string => String(value).padStart(2, '0')

  const day = date.getDate()
  const month = pad(date.getMonth() + 1)
  const year = date.getFullYear()
  const hours = pad(date.getHours())
  const minutes = pad(date.getMinutes())

  return `${day}/${month}/${year} ${hours}:${minutes}`
}
