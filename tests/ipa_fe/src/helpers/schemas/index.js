import { detectPageType } from './schemaDetector'
import { SCHEMA_TYPES } from './constants'
import { getBaseUrl } from './schemaUtils'
import {
  buildOrganizationSchema,
  buildWebSiteSchema,
  buildBreadcrumbSchema,
  buildWebPageSchema,
  buildArticleSchema,
  buildEventSchema
} from './schemaBuilder'

/**
 * Generate complete JSON-LD schemas for a page
 * Returns a schema.org graph with all applicable schemas
 * @param {Object} pageData - Page configuration data from PageConfigurationDto
 * @returns {Object|null} JSON-LD schema object with @graph array
 */
export const generateSchemas = (pageData) => {
  if (!pageData) return null

  const baseUrl = getBaseUrl(pageData)
  const pageType = detectPageType(pageData)

  // Build schema graph array
  const graph = []

  // 1. Organization schema (always included on all pages)
  graph.push(buildOrganizationSchema(pageData))

  // 2. WebSite schema (always included on all pages)
  graph.push(buildWebSiteSchema(pageData))

  // 3. Breadcrumb schema (conditional - only when breadcrumbs exist)
  const breadcrumbSchema = buildBreadcrumbSchema(pageData.breadcrumbs, baseUrl)
  if (breadcrumbSchema) {
    graph.push(breadcrumbSchema)
  }

  // 4. Page-specific schema based on detected type
  switch (pageType) {
    case SCHEMA_TYPES.ARTICLE:
      graph.push(buildArticleSchema(pageData))
      break

    case SCHEMA_TYPES.EVENT:
      graph.push(buildEventSchema(pageData))
      break

    case SCHEMA_TYPES.WEBPAGE:
    default:
      graph.push(buildWebPageSchema(pageData))
      break
  }

  // Return complete schema object with @context and @graph
  return {
    '@context': 'https://schema.org',
    '@graph': graph
  }
}

// Export helper functions for testing or advanced usage
export { detectPageType } from './schemaDetector'
export * from './constants'
