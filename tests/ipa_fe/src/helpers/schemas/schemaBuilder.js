import { ORGANIZATION } from './constants'
import { normalizeUrl, extractSocialMediaUrls, formatDateISO, getBaseUrl } from './schemaUtils'

/**
 * Build Organization schema (schema.org/Organization)
 * Always included on every page
 * @param {Object} pageData - Page configuration data
 * @returns {Object} Organization schema object
 */
export const buildOrganizationSchema = (pageData) => {
  const baseUrl = getBaseUrl(pageData)
  const socialUrls = extractSocialMediaUrls(pageData.navigation?.footer)

  // Get logo URL from Umbraco navigation data (ImageDto uses 'src' property)
  const logoSrc = pageData.navigation?.siteLogo?.src
  const logoUrl = logoSrc ? normalizeUrl(logoSrc, baseUrl) : null

  const schema = {
    '@type': 'Organization',
    '@id': `${baseUrl}#organization`,
    name: pageData.siteName || ORGANIZATION.name,
    alternateName: ORGANIZATION.alternateName,
    url: baseUrl,
    description: pageData.ogDescription || ORGANIZATION.description,
    contactPoint: {
      '@type': 'ContactPoint',
      telephone: ORGANIZATION.telephone,
      email: ORGANIZATION.email,
      contactType: 'customer service'
    },
    address: ORGANIZATION.address,
    sameAs: socialUrls.length > 0 ? socialUrls : [],
    potentialAction: {
      '@type': 'SearchAction',
      target: {
        '@type': 'EntryPoint',
        urlTemplate: `${baseUrl}/search?q={search_term_string}`
      },
      'query-input': 'required name=search_term_string'
    }
  }

  // Only include logo if it exists in Umbraco
  if (logoUrl) {
    schema.logo = {
      '@type': 'ImageObject',
      url: logoUrl
    }
  }

  return schema
}

/**
 * Build WebSite schema (schema.org/WebSite)
 * Always included on every page
 * @param {Object} pageData - Page configuration data
 * @returns {Object} WebSite schema object
 */
export const buildWebSiteSchema = (pageData) => {
  const baseUrl = getBaseUrl(pageData)

  return {
    '@type': 'WebSite',
    '@id': `${baseUrl}#website`,
    url: baseUrl,
    name: pageData.siteName || ORGANIZATION.name,
    publisher: {
      '@id': `${baseUrl}#organization`
    }
  }
}

/**
 * Build BreadcrumbList schema (schema.org/BreadcrumbList)
 * Included when breadcrumbs are present
 * @param {Array} breadcrumbs - Array of breadcrumb items
 * @param {string} baseUrl - Base URL for normalizing URLs
 * @returns {Object|null} BreadcrumbList schema object or null
 */
export const buildBreadcrumbSchema = (breadcrumbs, baseUrl) => {
  if (!breadcrumbs?.length) return null

  return {
    '@type': 'BreadcrumbList',
    itemListElement: breadcrumbs.map((crumb, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: crumb.label,
      item: normalizeUrl(crumb.url, baseUrl)
    }))
  }
}

/**
 * Build WebPage schema (schema.org/WebPage)
 * For generic pages
 * @param {Object} pageData - Page configuration data
 * @returns {Object} WebPage schema object
 */
export const buildWebPageSchema = (pageData) => {
  const url = pageData.canonicalUrl || (typeof window !== 'undefined' ? window.location.href : '')
  const baseUrl = getBaseUrl(pageData)

  const schema = {
    '@type': 'WebPage',
    '@id': url,
    url: url,
    name: pageData.pageTitle,
    description: pageData.pageDescription,
    publisher: {
      '@id': `${baseUrl}#organization`
    },
    isPartOf: {
      '@id': `${baseUrl}#website`
    },
    about: {
      '@id': `${baseUrl}#organization`
    }
  }

  // Add dateModified date if available (use CreateDate as fallback)
  const lastModified = pageData.createDate
  if (lastModified) {
    schema.dateModified = formatDateISO(lastModified)
  }

  // Add primary image if available (ogImage is ImageDto with 'src' property)
  if (pageData.ogImage?.src) {
    schema.primaryImageOfPage = {
      '@type': 'ImageObject',
      url: normalizeUrl(pageData.ogImage.src, baseUrl)
    }
  }

  return schema
}

/**
 * Build Article schema (schema.org/Article)
 * For article/blog pages
 * @param {Object} pageData - Page configuration data
 * @returns {Object} Article schema object
 */
export const buildArticleSchema = (pageData) => {
  const url = pageData.canonicalUrl || (typeof window !== 'undefined' ? window.location.href : '')
  const baseUrl = getBaseUrl(pageData)

  // Use PublicationDate first, fallback to CreateDate (Umbraco's node creation date)
  const publicationDate = pageData.publicationDate || pageData.createDate

  const schema = {
    '@type': 'Article',
    '@id': url,
    headline: pageData.pageTitle,
    description: pageData.pageDescription,
    url: url,
    author: {
      '@id': `${baseUrl}#organization`
    },
    publisher: {
      '@id': `${baseUrl}#organization`
    },
    isPartOf: {
      '@id': `${baseUrl}#website`
    }
  }

  // Add publication dates if available
  if (publicationDate) {
    schema.datePublished = formatDateISO(publicationDate)
    schema.dateModified = formatDateISO(publicationDate)
  }

  // Add image if available (ogImage is ImageDto with 'src' property)
  if (pageData.ogImage?.src) {
    schema.image = {
      '@type': 'ImageObject',
      url: normalizeUrl(pageData.ogImage.src, baseUrl)
    }
  }

  // Add keywords if available
  if (pageData.tags?.length) {
    schema.keywords = pageData.tags.join(', ')
  }

  return schema
}

/**
 * Build Event schema (schema.org/Event)
 * For event pages
 * @param {Object} pageData - Page configuration data
 * @returns {Object} Event schema object
 */
export const buildEventSchema = (pageData) => {
  const url = pageData.canonicalUrl || (typeof window !== 'undefined' ? window.location.href : '')
  const baseUrl = getBaseUrl(pageData)

  // Try to find event data from components
  const eventDetails = pageData.components?.find(
    c => c.componentName === 'eventDetailsBlock'
  )?.componentData

  const eventData = eventDetails?.event || {}

  const schema = {
    '@type': 'Event',
    '@id': url,
    name: pageData.pageTitle || eventData.title,
    description: pageData.pageDescription || eventData.shortDescription,
    url: url,
    organizer: {
      '@id': `${baseUrl}#organization`
    }
  }

  // Add start and end dates if available
  if (eventData.start) {
    schema.startDate = formatDateISO(eventData.start)
  }

  if (eventData.end) {
    schema.endDate = formatDateISO(eventData.end)
  }

  // Add location if available
  if (eventData.state || eventData.country) {
    schema.location = {
      '@type': 'Place',
      name: eventData.state || eventData.country
    }

    if (eventData.state || eventData.country) {
      schema.location.address = {
        '@type': 'PostalAddress',
        addressRegion: eventData.state,
        addressCountry: eventData.country || 'AU'
      }
    }
  }

  // Add pricing if available
  if (eventData.memberPriceFrom || eventData.nonMemberPriceFrom) {
    const price = eventData.memberPriceFrom || eventData.nonMemberPriceFrom
    schema.offers = {
      '@type': 'Offer',
      price: price,
      priceCurrency: 'AUD',
      availability: 'https://schema.org/InStock'
    }
  }

  // Add image if available (ogImage is ImageDto with 'src' property)
  if (pageData.ogImage?.src) {
    schema.image = {
      '@type': 'ImageObject',
      url: normalizeUrl(pageData.ogImage.src, baseUrl)
    }
  }

  return schema
}
