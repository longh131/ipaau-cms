import { SCHEMA_TYPES } from './constants'
import { hasComponent } from './schemaUtils'

/**
 * Detect the type of page based on components and data
 * @param {Object} pageData - Page configuration data
 * @returns {string} Schema type (article, event, or webpage)
 */
export const detectPageType = (pageData) => {
  // Check for event indicators first (more specific)
  if (
    hasComponent(pageData, 'eventHeader') ||
    hasComponent(pageData, 'eventDetailsBlock')
  ) {
    return SCHEMA_TYPES.EVENT
  }

  // Check for article indicators
  // 1. Check document type alias first (most reliable)
  // 2. Check for article-specific components
  // 3. Check for publicationDate field (specific to ArticlePage)
  if (
    pageData.alias === 'articlePage' ||
    hasComponent(pageData, 'articleHeader') ||
    hasComponent(pageData, 'articleContainer') ||
    pageData.publicationDate
  ) {
    return SCHEMA_TYPES.ARTICLE
  }

  // Default to generic webpage
  return SCHEMA_TYPES.WEBPAGE
}
