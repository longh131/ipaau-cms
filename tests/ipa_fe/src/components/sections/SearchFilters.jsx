import { useState, useEffect, useRef } from 'react'
import { createPortal } from 'react-dom'
import PropTypes from 'prop-types'
import Loader from '../helpers/Loader'
import { XMarkIcon } from '@heroicons/react/24/solid'
import { dataLayerPush } from '../../helpers/thirdparty'
import { DateFilters, TopicFilters, LocationFilters, EventFilters, PageFilters } from '../helpers/search/filters'
import { FilterIcon } from '../helpers/search/filters/Icons'
import { useScrollLock } from '../../helpers/scrollLock'
import { useSearchResults } from '../helpers/search/SearchResultsContext'
import constants from '../../helpers/constants'

const PRE_FILTER_EVENTS = constants.search.preFilters.events
const PRE_FILTER_NEWS = constants.search.preFilters.news
const PRE_FILTER_CONTENT = constants.search.preFilters.content

/**
 * Helper: Parse location identifiers from URL and match to taxonomy values
 */
const parseLocationFilters = (locationIdentifiers, taxonomies, matchIdentifierToValue) => {
  return locationIdentifiers
    .map((id) => matchIdentifierToValue(id, taxonomies.locations))
    .filter(Boolean)
}

/**
 * Helper: Parse topic identifiers from URL and match to taxonomy values
 */
const parseTopicFilters = (topicIdentifiers, taxonomies, matchIdentifierToValue) => {
  return topicIdentifiers
    .map((id) => matchIdentifierToValue(id, taxonomies.topics, 'name'))
    .filter(Boolean)
}

/**
 * Helper: Parse event identifiers from URL - can be category codes or display names
 */
const parseEventFilters = (eventIdentifiers, taxonomies, matchIdentifierToValue) => {
  const eventCategories = taxonomies.eventCategories || {}
  const eventCategoryCodes = Object.keys(eventCategories)
  const eventCategoryNames = Object.values(eventCategories)

  let matchedEvents = eventIdentifiers
    .map((id) => {
      // First check if it's a direct category code
      if (eventCategoryCodes.includes(id)) {
        return id
      }
      // Check if it matches "events" or "all_events" for the ALL_EVENTS option
      if (id === 'events' || id === 'all_events') {
        return 'ALL_EVENTS'
      }
      // Try to match by display name to get the code
      const matchedName = matchIdentifierToValue(id, eventCategoryNames)
      if (matchedName) {
        // Find the code for this display name
        const entry = Object.entries(eventCategories).find(([, name]) => name === matchedName)
        return entry ? entry[0] : null
      }
      return null
    })
    .filter(Boolean)

  // If ALL_EVENTS is selected, expand to include all sub-categories
  if (matchedEvents.includes('ALL_EVENTS')) {
    matchedEvents = ['ALL_EVENTS', ...eventCategoryCodes.filter((code) => !matchedEvents.includes(code)), ...matchedEvents.filter((e) => e !== 'ALL_EVENTS')]
    // Deduplicate
    matchedEvents = [...new Set(matchedEvents)]
  }

  return matchedEvents
}

/**
 * Helper: Parse page type identifiers from URL
 */
const parsePageFilters = (pageIdentifiers) => {
  return pageIdentifiers
    .map((id) => {
      if (id === 'page' || id === 'content') {
        return 'page'
      }
      return null
    })
    .filter(Boolean)
}

const SearchFilters = ({ onFiltersChange, onPendingFiltersChange, appliedFilters = {}, hasUnappliedFilters = false, preFilter }) => {
  const onFiltersChangeRef = useRef(onFiltersChange)
  const onPendingFiltersChangeRef = useRef(onPendingFiltersChange)
  const lastSyncedFiltersRef = useRef(null)
  const manuallyOpenedRef = useRef(false) // Track if user manually opened the filters
  const resizeTimeoutRef = useRef(null)
  const previousWidthRef = useRef(null) // Track previous window width
  const { lockScroll, unlockScroll } = useScrollLock()
  const { getParam, getParamArray, matchIdentifierToValue, updateUrl, getFilterIdentifier } = useSearchResults()
  const initializedFromUrlRef = useRef(false)
  // Initialize based on screen size: open on desktop (>= 1024px), closed on mobile
  const [open, setOpen] = useState(() => {
    if (typeof window !== 'undefined') {
      return { 'filters-accordion': window.innerWidth >= 1024 }
    }
    return { 'filters-accordion': false } // Default to closed for SSR
  })
  // Keep refs up to date
  useEffect(() => {
    onFiltersChangeRef.current = onFiltersChange
  }, [onFiltersChange])
  useEffect(() => {
    onPendingFiltersChangeRef.current = onPendingFiltersChange
  }, [onPendingFiltersChange])
  const [taxonomies, setTaxonomies] = useState(null)
  const [selectedTypes, setSelectedTypes] = useState([])
  const [selectedLocations, setSelectedLocations] = useState([])
  const [selectedTopics, setSelectedTopics] = useState([])
  const [dateFrom, setDateFrom] = useState('')
  const [dateTo, setDateTo] = useState('')
  // Track which filter sections should be initially expanded (from URL params)
  const [initialOpenSections, setInitialOpenSections] = useState({
    locations: false,
    topics: false,
  })

  // Fetch taxonomies on mount
  useEffect(() => {
    const fetchTaxonomies = async () => {
      try {
        const response = await fetch(`${window.location.origin}/api/SearchApi/Taxonomies`)
        const data = await response.json()

        if (data.success) {
          setTaxonomies(data.result)
        }
      } catch (error) {
        console.error('Error fetching taxonomies:', error)
      }
    }
    fetchTaxonomies()
  }, [])

  // Initialize filters from URL params (or preFilter CMS setting) when taxonomies are loaded (only once)
  useEffect(() => {
    if (!taxonomies || initializedFromUrlRef.current) return

    // Mark as initialized to prevent re-running
    initializedFromUrlRef.current = true

    const eventCategories = taxonomies.eventCategories || {}
    const eventCategoryCodes = Object.keys(eventCategories)

    // Get location/topic/date params from URL (these apply in all modes)
    const locationIdentifiers = getParamArray('location')
    const topicIdentifiers = getParamArray('topic')
    const urlDateFrom = getParam('dateFrom')
    const urlDateTo = getParam('dateTo')

    const matchedLocations = parseLocationFilters(locationIdentifiers, taxonomies, matchIdentifierToValue)
    const matchedTopics = parseTopicFilters(topicIdentifiers, taxonomies, matchIdentifierToValue)

    if (preFilter === PRE_FILTER_EVENTS) {
      // Lock to all events — ignore URL event/page type params
      const allEventTypesSelected = ['ALL_EVENTS', ...eventCategoryCodes]
      setSelectedTypes(allEventTypesSelected)
      if (matchedLocations.length > 0) setSelectedLocations(matchedLocations)
      if (matchedTopics.length > 0) setSelectedTopics(matchedTopics)
      if (urlDateFrom) setDateFrom(urlDateFrom)
      if (urlDateTo) setDateTo(urlDateTo)
      setInitialOpenSections({
        locations: matchedLocations.length > 0,
        topics: matchedTopics.length > 0,
      })
      onFiltersChangeRef.current({
        types: allEventTypesSelected,
        locations: matchedLocations,
        topics: matchedTopics,
        dateFrom: urlDateFrom || '',
        dateTo: urlDateTo || '',
      })
    } else if (preFilter === PRE_FILTER_NEWS) {
      // Lock to news articles — ignore URL event/page type params
      setSelectedTypes(['news'])
      if (matchedLocations.length > 0) setSelectedLocations(matchedLocations)
      if (matchedTopics.length > 0) setSelectedTopics(matchedTopics)
      if (urlDateFrom) setDateFrom(urlDateFrom)
      if (urlDateTo) setDateTo(urlDateTo)
      setInitialOpenSections({
        locations: matchedLocations.length > 0,
        topics: matchedTopics.length > 0,
      })
      onFiltersChangeRef.current({
        types: ['news'],
        locations: matchedLocations,
        topics: matchedTopics,
        dateFrom: urlDateFrom || '',
        dateTo: urlDateTo || '',
      })
    } else if (preFilter === PRE_FILTER_CONTENT) {
      // Lock to content pages only — ignore URL event/page type params
      setSelectedTypes(['content'])
      if (matchedLocations.length > 0) setSelectedLocations(matchedLocations)
      if (matchedTopics.length > 0) setSelectedTopics(matchedTopics)
      if (urlDateFrom) setDateFrom(urlDateFrom)
      if (urlDateTo) setDateTo(urlDateTo)
      setInitialOpenSections({
        locations: matchedLocations.length > 0,
        topics: matchedTopics.length > 0,
      })
      onFiltersChangeRef.current({
        types: ['content'],
        locations: matchedLocations,
        topics: matchedTopics,
        dateFrom: urlDateFrom || '',
        dateTo: urlDateTo || '',
      })
    } else {
      // No preFilter — use URL params as before
      const eventIdentifiers = getParamArray('event')
      const pageIdentifiers = getParamArray('page')

      const matchedEvents = parseEventFilters(eventIdentifiers, taxonomies, matchIdentifierToValue)
      const matchedPageTypes = parsePageFilters(pageIdentifiers)

      // Combine event types and page types
      const allTypes = [...matchedEvents, ...matchedPageTypes]

      // Only update state if we have values from URL
      const hasUrlFilters =
        matchedLocations.length > 0 ||
        matchedTopics.length > 0 ||
        allTypes.length > 0 ||
        urlDateFrom ||
        urlDateTo

      if (hasUrlFilters) {
        if (matchedLocations.length > 0) setSelectedLocations(matchedLocations)
        if (matchedTopics.length > 0) setSelectedTopics(matchedTopics)
        if (allTypes.length > 0) setSelectedTypes(allTypes)
        if (urlDateFrom) setDateFrom(urlDateFrom)
        if (urlDateTo) setDateTo(urlDateTo)

        // Set which filter sections should be initially expanded
        setInitialOpenSections({
          locations: matchedLocations.length > 0,
          topics: matchedTopics.length > 0,
        })

        // Auto-apply filters from URL
        onFiltersChangeRef.current({
          types: allTypes,
          locations: matchedLocations,
          topics: matchedTopics,
          dateFrom: urlDateFrom || '',
          dateTo: urlDateTo || '',
        })
      }
    }
  }, [taxonomies, preFilter, getParamArray, getParam, matchIdentifierToValue])

  // Sync internal state with applied filters when they change
  // Only sync when appliedFilters actually changes (not on every render)
  useEffect(() => {
    if (!appliedFilters) return

    const currentAppliedKey = JSON.stringify({
      types: appliedFilters.types || [],
      locations: appliedFilters.locations || [],
      topics: appliedFilters.topics || [],
      dateFrom: appliedFilters.dateFrom || '',
      dateTo: appliedFilters.dateTo || '',
    })

    const lastSyncedKey = lastSyncedFiltersRef.current
    if (currentAppliedKey === lastSyncedKey) return

    // Only sync if appliedFilters actually changed
    setSelectedTypes(appliedFilters.types || [])
    setSelectedLocations(appliedFilters.locations || [])
    setSelectedTopics(appliedFilters.topics || [])
    setDateFrom(appliedFilters.dateFrom || '')
    setDateTo(appliedFilters.dateTo || '')
    lastSyncedFiltersRef.current = currentAppliedKey
  }, [appliedFilters])

  // Track pending filter changes and notify parent
  useEffect(() => {
    if (!onPendingFiltersChangeRef.current) return

    const pendingFilters = {
      types: selectedTypes,
      locations: selectedLocations,
      topics: selectedTopics,
      dateFrom,
      dateTo,
    }

    onPendingFiltersChangeRef.current(pendingFilters)
  }, [selectedTypes, selectedLocations, selectedTopics, dateFrom, dateTo])

  // Lock/unlock scroll when mobile filter panel opens/closes
  useEffect(() => {
    // Only lock scroll on mobile (when window width is less than 1024px)
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
      if (open['filters-accordion']) {
        lockScroll()
      } else {
        unlockScroll()
      }
    }

    // Cleanup on unmount
    return () => {
      if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        unlockScroll()
      }
    }
  }, [open['filters-accordion'], lockScroll, unlockScroll])

  const handleAccordionToggle = (accordionId) => {
    setOpen((prev) => {
      const newState = !prev[accordionId]
      // Track manual opens (when user opens on mobile)
      if (newState && typeof window !== 'undefined' && window.innerWidth < 1024) {
        manuallyOpenedRef.current = true
      }
      return { ...prev, [accordionId]: newState }
    })
  }

  // Initialize previous width on mount
  useEffect(() => {
    if (typeof window !== 'undefined') {
      previousWidthRef.current = window.innerWidth
    }
  }, [])

  // Handle window resize with debouncing
  useEffect(() => {
    if (typeof window === 'undefined') return

    const handleResize = () => {
      // Clear existing timeout
      if (resizeTimeoutRef.current) {
        clearTimeout(resizeTimeoutRef.current)
      }

      // Debounce resize handling
      resizeTimeoutRef.current = setTimeout(() => {
        const currentWidth = window.innerWidth
        const previousWidth = previousWidthRef.current
        const isDesktop = currentWidth >= 1024
        const isMobile = currentWidth < 1024
        const wasDesktop = previousWidth !== null && previousWidth >= 1024
        const wasMobile = previousWidth !== null && previousWidth < 1024

        // If transitioning from desktop to mobile
        if (wasDesktop && isMobile) {
          // Only close if user hasn't manually opened it
          if (!manuallyOpenedRef.current && open['filters-accordion']) {
            setOpen((prev) => ({ ...prev, 'filters-accordion': false }))
          }
        }
        // If transitioning from mobile to desktop
        else if (wasMobile && isDesktop) {
          // Open filters on desktop (desktop default)
          if (!open['filters-accordion']) {
            setOpen((prev) => ({ ...prev, 'filters-accordion': true }))
          }
          // Reset manual open flag when on desktop
          manuallyOpenedRef.current = false
        }

        // Update previous width
        previousWidthRef.current = currentWidth
      }, 150) // 150ms debounce
    }

    window.addEventListener('resize', handleResize)

    return () => {
      window.removeEventListener('resize', handleResize)
      if (resizeTimeoutRef.current) {
        clearTimeout(resizeTimeoutRef.current)
      }
    }
  }, [open['filters-accordion']])

  /**
   * Build URL params object from filter selections.
   * Converts display names to snake_case identifiers for URL.
   */
  const buildFilterUrlParams = (filters) => {
    const params = {}

    // Convert locations to URL identifiers
    if (filters.locations && filters.locations.length > 0) {
      params.location = filters.locations.map((loc) => getFilterIdentifier(loc))
    }

    // Convert topics to URL identifiers
    if (filters.topics && filters.topics.length > 0) {
      params.topic = filters.topics.map((topic) => getFilterIdentifier(topic))
    }

    // Convert event types to URL identifiers
    // Filter out 'page' as it's handled separately
    const eventTypes = filters.types?.filter((t) => t !== 'page') || []
    if (eventTypes.length > 0) {
      params.event = eventTypes.map((type) => {
        // Check if it's ALL_EVENTS
        if (type === 'ALL_EVENTS') {
          return 'all_events'
        }
        // For category codes, convert the display name if we have taxonomies
        const displayName = taxonomies?.eventCategories?.[type]
        return displayName ? getFilterIdentifier(displayName) : type
      })
    }

    // Handle page filter
    if (filters.types?.includes('page')) {
      params.page = 'content'
    }

    // Date filters
    if (filters.dateFrom) {
      params.dateFrom = filters.dateFrom
    }
    if (filters.dateTo) {
      params.dateTo = filters.dateTo
    }

    return params
  }

  const clearFilters = () => {
    // When locked by preFilter, preserve the pre-selected types
    let baseTypes = []
    if (preFilter === PRE_FILTER_EVENTS) {
      const eventCategories = taxonomies?.eventCategories || {}
      baseTypes = ['ALL_EVENTS', ...Object.keys(eventCategories)]
    } else if (preFilter === PRE_FILTER_NEWS) {
      baseTypes = ['news']
    } else if (preFilter === PRE_FILTER_CONTENT) {
      baseTypes = ['content']
    }

    setSelectedTypes(baseTypes)
    setSelectedLocations([])
    setSelectedTopics([])
    setDateFrom('')
    setDateTo('')
    onFiltersChangeRef.current({
      types: baseTypes,
      locations: [],
      topics: [],
      dateFrom: '',
      dateTo: '',
    })

    // Update URL - remove all filter params but keep search query
    updateUrl({}, ['location', 'topic', 'event', 'page', 'dateFrom', 'dateTo'])

    // Only close accordion on mobile
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
      handleAccordionToggle('filters-accordion')
    }
  }

  const applyFilters = () => {
    dataLayerPush({
      event: 'filter_applied',
      category: selectedTypes.join(', '),
      topic: selectedTopics.join(', '),
      event_location: selectedLocations.join(', '),
      event_date: (dateFrom && dateTo) ? dateFrom+' - '+dateTo : (dateFrom || dateTo)
    })

    const filters = {
      types: selectedTypes,
      locations: selectedLocations,
      topics: selectedTopics,
      dateFrom,
      dateTo,
    }

    onFiltersChangeRef.current(filters)

    // Update URL with filter params (remove old ones first, then add new)
    const urlParams = buildFilterUrlParams(filters)
    updateUrl(urlParams, ['location', 'topic', 'event', 'page', 'dateFrom', 'dateTo'])

    // Only close accordion on mobile
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
      handleAccordionToggle('filters-accordion')
    }
  }

  if (!taxonomies) {
    return (
      <div className="flex gap-1 items-center">
        <div className="w-12 h-12 scale-50">
          <Loader />
        </div>
        Loading filters...
      </div>
    )
  }

  const filterButtonClass = `px-6 py-2 rounded-full text-center uppercase transition-all duration-300 font-din grow shrink-0 text-label-lg bg-secondary text-white  disabled:cursor-not-allowed disabled:bg-grey-subtle disabled:text-primary :not([disabled]):hover:bg-grey-subtle-hover :not([disabled]):hover:underline`

  // Check if there are any applied filters (only check appliedFilters, not pending selections)
  const hasAppliedFilters = appliedFilters && (
    (appliedFilters.types && appliedFilters.types.length > 0) ||
    (appliedFilters.locations && appliedFilters.locations.length > 0) ||
    (appliedFilters.topics && appliedFilters.topics.length > 0) ||
    (appliedFilters.dateFrom && appliedFilters.dateFrom.trim() !== '') ||
    (appliedFilters.dateTo && appliedFilters.dateTo.trim() !== '')
  )

  // Count applied filters
  const countAppliedFilters = () => {
    if (!appliedFilters) return 0
    let count = 0
    if (appliedFilters.types && appliedFilters.types.length > 0) count += appliedFilters.types.length
    if (appliedFilters.locations && appliedFilters.locations.length > 0) count += appliedFilters.locations.length
    if (appliedFilters.topics && appliedFilters.topics.length > 0) count += appliedFilters.topics.length
    if (appliedFilters.dateFrom && appliedFilters.dateFrom.trim() !== '') count += 1
    if (appliedFilters.dateTo && appliedFilters.dateTo.trim() !== '') count += 1
    return count
  }

  const appliedFiltersCount = countAppliedFilters()

  const mobileTrigger = () => (
    <button
      className="lg:hidden py-2 px-4 font-din border-2 border-link rounded-full flex items-center justify-center w-full text-label-sm uppercase gap-2 font-bold hover:border-link-hover hover:bg-link-hover hover:text-white text-link"
      onClick={() => handleAccordionToggle('filters-accordion')}
    >
      <FilterIcon className="w-5 h-5" />
      Filter results
      {appliedFiltersCount > 0 && (
        <span className="ml-1">({appliedFiltersCount}<span className="hidden md:inline lg:hidden"> filter{appliedFiltersCount > 1 ? 's' : ''} applied</span>)</span>
      )}
    </button>
  )

  const filterBlock = ({className}) =>       <aside data-open={open['filters-accordion']} aria-label="Search filters" className={`max-lg:w-full max-lg:border-b max-lg:border-b-primary-border ${open['filters-accordion'] ? 'max-lg:max-h-[66vh] ' : 'max-lg:max-h-0'} transition-all duration-300 ${className}`}>
  <h3 className="text-display-sm font-apex-book text-primary flex items-center gap-2 justify-start max-lg:px-4 max-lg:pt-4">
    <FilterIcon className="w-6 h-6" />
    Filter by
    <button
      onClick={() => handleAccordionToggle('filters-accordion')}
      className="ml-auto lg:hidden text-secondary"
      aria-expanded={open['filters-accordion']}
      aria-controls="filters-accordion"
    >
      <XMarkIcon className="w-6 h-6 transition-all duration-300" />
    </button>
  </h3>

  <div id="filters-accordion" data-type="filter-panel-content" aria-hidden={!open['filters-accordion']} className={`${open['filters-accordion'] ? 'max-h-[calc(66vh-180px)] lg:max-h-max overflow-y-auto' : 'max-lg:max-h-0 max-lg:overflow-hidden'} transition-all duration-300 max-lg:px-4`}>

    {!preFilter && <PageFilters selectedTypes={selectedTypes} setSelectedTypes={setSelectedTypes} />}
    {(!preFilter || preFilter === PRE_FILTER_EVENTS) && <EventFilters eventCategories={taxonomies.eventCategories} selectedTypes={selectedTypes} setSelectedTypes={setSelectedTypes} />}
    <LocationFilters locations={taxonomies.locations} selectedLocations={selectedLocations} setSelectedLocations={setSelectedLocations} initialOpen={initialOpenSections.locations} />
    <TopicFilters topics={taxonomies.topics} selectedTopics={selectedTopics} setSelectedTopics={setSelectedTopics} initialOpen={initialOpenSections.topics} />
    <DateFilters dateFrom={dateFrom} dateTo={dateTo} setDateFrom={setDateFrom} setDateTo={setDateTo} />
  </div>

  <div data-type="filter-buttons" className="flex justify-between relative items-center gap-4 z-10 max-lg:py-10 max-lg:px-4">
    <button
      onClick={applyFilters}
      disabled={!open['filters-accordion'] || !hasUnappliedFilters}
      className={filterButtonClass}
    >
      Apply filters
    </button>
    <button
      onClick={clearFilters}
      disabled={!open['filters-accordion'] || !hasAppliedFilters}
      className={filterButtonClass}
    >
      Clear
    </button>
  </div>
</aside>

  return (
    <>
      {mobileTrigger()}
      {createPortal(filterBlock({className: 'lg:hidden bg-white rounded-t-3xl '}), document.getElementById('portal-wrapper'))}
      {filterBlock({className: 'max-lg:hidden'})}
    </>
  )
}

SearchFilters.propTypes = {
  onFiltersChange: PropTypes.func.isRequired,
  onPendingFiltersChange: PropTypes.func,
  appliedFilters: PropTypes.object,
  hasUnappliedFilters: PropTypes.bool,
  preFilter: PropTypes.string,
}

export default SearchFilters
