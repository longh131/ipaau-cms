import { createContext, useContext, useState, useEffect, useMemo, useCallback } from 'react'
import PropTypes from 'prop-types'
import { textToSnakeCase } from '../../../helpers/text'

/**
 * Context for sharing search results state and URL parameters across filter components.
 */
const SearchResultsContext = createContext({
  urlParams: {},
  searchTerm: '',
  setSearchTerm: () => {},
  getParam: () => null,
  getParamArray: () => [],
  hasParam: () => false,
  updateUrl: () => {},
  getFilterIdentifier: () => '',
  matchIdentifierToValue: () => null,
  getSelectedFiltersFromUrl: () => [],
})

/**
 * Parses all URL search parameters into a structured object.
 * Handles both single and multiple values for each parameter.
 * @param {URLSearchParams} searchParams
 * @returns {Object} Parsed parameters object
 */
const parseUrlParams = (searchParams) => {
  const params = {}

  searchParams.forEach((value, key) => {
    if (params[key]) {
      // If the key already exists, convert to array or push to existing array
      if (Array.isArray(params[key])) {
        params[key].push(value)
      } else {
        params[key] = [params[key], value]
      }
    } else {
      params[key] = value
    }
  })

  return params
}

/**
 * Provider component that parses URL parameters and makes them available to all child components.
 */
export const SearchResultsProvider = ({ children }) => {
  const [urlParams, setUrlParams] = useState({})
  const [searchTerm, setSearchTerm] = useState('')

  // Parse URL parameters on mount and when URL changes
  useEffect(() => {
    const updateFromUrl = () => {
      const url = new URL(window.location.href)
      const searchParams = new URLSearchParams(url.search)
      const parsed = parseUrlParams(searchParams)

      setUrlParams(parsed)
      setSearchTerm(parsed.q ? decodeURIComponent(parsed.q) : '')
    }

    // Initial parse
    updateFromUrl()

    // Listen for popstate events (browser back/forward)
    window.addEventListener('popstate', updateFromUrl)

    return () => {
      window.removeEventListener('popstate', updateFromUrl)
    }
  }, [])

  /**
   * Get a single parameter value.
   * If the parameter has multiple values, returns the first one.
   */
  const getParam = useCallback(
    (key) => {
      const value = urlParams[key]
      if (Array.isArray(value)) {
        return value[0]
      }
      return value || null
    },
    [urlParams]
  )

  /**
   * Get a parameter as an array (useful for multi-value params like topics, locations, etc.)
   */
  const getParamArray = useCallback(
    (key) => {
      const value = urlParams[key]
      if (!value) return []
      if (Array.isArray(value)) return value
      return [value]
    },
    [urlParams]
  )

  /**
   * Check if a parameter exists.
   */
  const hasParam = useCallback(
    (key) => {
      return key in urlParams
    },
    [urlParams]
  )

  /**
   * Update the URL with new parameters without triggering a page reload.
   * @param {Object} newParams - Parameters to add/update
   * @param {Array} removeKeys - Parameter keys to remove
   */
  const updateUrl = useCallback((newParams = {}, removeKeys = []) => {
    const url = new URL(window.location.href)

    // Remove specified keys
    removeKeys.forEach((key) => {
      url.searchParams.delete(key)
    })

    // Add/update new params
    Object.entries(newParams).forEach(([key, value]) => {
      url.searchParams.delete(key) // Clear existing values first

      if (Array.isArray(value)) {
        value.forEach((v) => url.searchParams.append(key, v))
      } else if (value !== null && value !== undefined && value !== '') {
        url.searchParams.set(key, value)
      }
    })

    // Update browser URL without reload
    window.history.pushState({}, '', url.toString())

    // Update local state
    const parsed = parseUrlParams(url.searchParams)
    setUrlParams(parsed)
    setSearchTerm(parsed.q ? decodeURIComponent(parsed.q) : '')
  }, [])

  /**
   * Generate a filter identifier from a display value.
   * Used to create URL-friendly identifiers like "ethics_and_professional_standards" from "Ethics and professional standards".
   * @param {string} displayValue - The display name
   * @returns {string} Snake_case identifier
   */
  const getFilterIdentifier = useCallback((displayValue) => {
    return textToSnakeCase(displayValue)
  }, [])

  /**
   * Match a snake_case identifier from URL params to a value from a list of options.
   * @param {string} identifier - The snake_case identifier from URL (e.g., "nsw", "ethics_and_professional_standards")
   * @param {Array} options - Array of possible values to match against (can be strings or objects with name property)
   * @param {string} [nameKey='name'] - Key to use for object options
   * @returns {string|null} The matching display value or null if not found
   */
  const matchIdentifierToValue = useCallback((identifier, options, nameKey = 'name') => {
    if (!identifier || !options) return null

    const normalizedIdentifier = identifier.toLowerCase()

    for (const option of options) {
      const displayValue = typeof option === 'string' ? option : option[nameKey]
      if (textToSnakeCase(displayValue) === normalizedIdentifier) {
        return displayValue
      }
    }
    return null
  }, [])

  /**
   * Get selected filter values from URL params by matching identifiers to a list of options.
   * @param {string} paramKey - URL parameter key (e.g., "location", "topic", "event")
   * @param {Array} options - Array of possible values to match against
   * @param {string} [nameKey='name'] - Key to use for object options
   * @returns {Array} Array of matched display values
   */
  const getSelectedFiltersFromUrl = useCallback(
    (paramKey, options, nameKey = 'name') => {
      const identifiers = getParamArray(paramKey)
      if (!identifiers.length || !options) return []

      const matched = []
      for (const identifier of identifiers) {
        const matchedValue = matchIdentifierToValue(identifier, options, nameKey)
        if (matchedValue) {
          matched.push(matchedValue)
        } else {
          // If no match found, keep the raw identifier (useful for codes like event categories)
          matched.push(identifier)
        }
      }
      return matched
    },
    [getParamArray, matchIdentifierToValue]
  )

  const contextValue = useMemo(
    () => ({
      urlParams,
      searchTerm,
      setSearchTerm,
      getParam,
      getParamArray,
      hasParam,
      updateUrl,
      getFilterIdentifier,
      matchIdentifierToValue,
      getSelectedFiltersFromUrl,
    }),
    [urlParams, searchTerm, getParam, getParamArray, hasParam, updateUrl, getFilterIdentifier, matchIdentifierToValue, getSelectedFiltersFromUrl]
  )

  return <SearchResultsContext.Provider value={contextValue}>{children}</SearchResultsContext.Provider>
}

SearchResultsProvider.propTypes = {
  children: PropTypes.node.isRequired,
}

/**
 * Hook to access the search results context.
 * @returns {Object} Context value with URL params and helper functions
 */
export const useSearchResults = () => {
  const context = useContext(SearchResultsContext)
  if (!context) {
    throw new Error('useSearchResults must be used within a SearchResultsProvider')
  }
  return context
}

export default SearchResultsContext

