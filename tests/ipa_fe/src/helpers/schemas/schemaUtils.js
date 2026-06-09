/**
 * Normalize URL to absolute format
 * @param {string} url - URL to normalize (relative or absolute)
 * @param {string} baseUrl - Base URL for relative URLs
 * @returns {string|null} Absolute URL or null if invalid
 */
export const normalizeUrl = (url, baseUrl) => {
  if (!url) return null
  if (url.startsWith('http')) return url

  try {
    return new URL(url, baseUrl).toString()
  } catch {
    return url
  }
}

/**
 * Extract social media URLs from footer data
 * Only includes external URLs (http/https), filters out internal links
 * @param {Object} footer - Footer data object
 * @returns {Array<string>} Array of social media URLs
 */
export const extractSocialMediaUrls = (footer) => {
  if (!footer?.socialMediaLinks?.length) return []

  return footer.socialMediaLinks
    .map(link => link.link?.url)
    .filter(Boolean)
    .filter(url => url.startsWith('http'))
}

/**
 * Format date to ISO 8601 string
 * @param {string|Date} date - Date to format
 * @returns {string|null} ISO 8601 formatted date or null
 */
export const formatDateISO = (date) => {
  if (!date) return null

  try {
    const parsedDate = new Date(date)

    // Check if the date is valid
    if (isNaN(parsedDate.getTime())) {
      console.warn('Invalid date format:', date)
      return null
    }

    // Check if the year is reasonable (between 1900 and 2100)
    const year = parsedDate.getFullYear()
    if (year < 1900 || year > 2100) {
      console.warn('Date year out of reasonable range:', date, 'parsed as', parsedDate)
      return null
    }

    return parsedDate.toISOString()
  } catch (error) {
    console.error('Error formatting date:', date, error)
    return null
  }
}

/**
 * Check if a component exists in the components array
 * @param {Object} pageData - Page data object
 * @param {string} componentName - Component name to check
 * @returns {boolean} True if component exists
 */
export const hasComponent = (pageData, componentName) => {
  return pageData.components?.some(
    comp => comp.componentName === componentName
  )
}

/**
 * Get base URL from page data or window location
 * @param {Object} pageData - Page data object
 * @returns {string} Base URL (origin)
 */
export const getBaseUrl = (pageData) => {
  if (pageData.canonicalUrl) {
    try {
      return new URL(pageData.canonicalUrl).origin
    } catch {
      // Fall through to window location
    }
  }

  if (typeof window !== 'undefined') {
    return window.location.origin
  }

  return ''
}
