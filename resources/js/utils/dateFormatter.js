/**
 * Parse date string from ISO format to timestamp
 * @param {string} dateStr - ISO date string (e.g., "2025-10-04 12:30:45" or "2025-10-04T12:30:45.000000Z")
 * @returns {number} - Timestamp in milliseconds
 */
export function parseDateToTimestamp(dateStr) {
  if (!dateStr) return 0
  return new Date(dateStr).getTime()
}

/**
 * Format date to "27, Aug, 2025" format
 * @param {string} dateStr - ISO date string
 * @returns {string} - Formatted date string
 */
export function formatDateShort(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = date.getDate()
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  const month = months[date.getMonth()]
  const year = date.getFullYear()
  return `${day}, ${month}, ${year}`
}

/**
 * Format date with time to "27, Aug, 2025 14:30" format
 * @param {string} dateStr - ISO date string
 * @returns {string} - Formatted date string with time
 */
export function formatDateTime(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = date.getDate()
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  const month = months[date.getMonth()]
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${day}, ${month}, ${year} ${hours}:${minutes}`
}
