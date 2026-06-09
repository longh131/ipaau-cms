// Organization details for JSON-LD schema
export const ORGANIZATION = {
  name: 'Institute of Public Accountants',
  alternateName: 'IPA',
  url: 'https://www.publicaccountants.org.au',
  description: 'The Institute of Public Accountants is the voice of small business advisors and accountants.',

  // Contact information - UPDATE THESE VALUES
  telephone: '+61-3-8665-3100',
  email: 'info@publicaccountants.org.au',

  // Physical address - UPDATE THESE VALUES
  address: {
    '@type': 'PostalAddress',
    streetAddress: 'Level 6, 555 Lonsdale Street',
    addressLocality: 'Melbourne',
    addressRegion: 'VIC',
    postalCode: '3000',
    addressCountry: 'AU'
  }
}

// Schema types for page type detection
export const SCHEMA_TYPES = {
  ARTICLE: 'article',
  EVENT: 'event',
  WEBPAGE: 'webpage'
}
