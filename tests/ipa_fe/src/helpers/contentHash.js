/**
 * Generate a stable hash from component data
 * Uses a simple hash function to create a deterministic identifier
 * @param {object} data - The data object to hash
 * @returns {string} A base36-encoded hash string
 */
export const generateDataHash = (data) => {
  try {
    const str = JSON.stringify(data)
    let hash = 0
    for (let i = 0; i < str.length; i++) {
      const char = str.charCodeAt(i)
      hash = ((hash << 5) - hash) + char
      hash = hash & hash // Convert to 32-bit integer
    }
    return Math.abs(hash).toString(36)
  } catch {
    return Math.random().toString(36).substring(2, 9)
  }
}

