import { Suspense, useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import Loader from '../helpers/Loader'
import themeConfig from '../../../theme.config'
import ReactPaginate from 'react-paginate'
import { ChevronLeftIcon, ChevronRightIcon, MagnifyingGlassIcon } from '@heroicons/react/24/solid'
import { transformPaddingToTailwind } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import Pill from '../helpers/Pill'
import SearchFilters from './SearchFilters'
import getSearchIcon from '../../helpers/searchIcon'
import constants from '../../helpers/constants'
import { SearchResultsProvider } from '../helpers/search/SearchResultsContext'

async function getResults(searchTerm, page = 1, pageSize = 8, filters = {}) {
  try {
    const params = new URLSearchParams({
      query: searchTerm,
      page: page.toString(),
      pageSize: pageSize.toString()
    })

    // Check for preFilter modes first — these take precedence over user type selections
    const isNewsSelected = filters?.types?.includes(constants.search.contentTypes.news)
    const isContentOnlySelected = filters?.types?.includes(constants.search.contentTypes.contentOnly)

    if (isNewsSelected) {
      params.append('type', constants.search.contentTypes.news)
    } else if (isContentOnlySelected) {
      params.append('type', constants.search.contentTypes.contentOnly)
    } else {
      // Check if 'page' filter is selected
      const isPageSelected = filters?.types?.includes(constants.search.contentTypes.page)

      // Filter out 'page' from types to get only event-related types
      const eventTypes = filters?.types ? filters.types.filter(t => t !== constants.search.contentTypes.page) : []

      // Check if any event-specific filters are active (date range or event type)
      // Note: Topics and locations can apply to both events and content pages
      // Exclude 'page' from this check since it's a separate content type filter
      const hasEventSpecificFilters =
        (eventTypes && eventTypes.length > 0) ||
        filters.dateFrom ||
        filters.dateTo

      // Set content type filter
      // If both page and event types are selected, don't set type filter (returns all content types)
      // If only page is selected, filter to pages only
      // If only event types are selected, filter to events only
      if (isPageSelected && hasEventSpecificFilters) {
        // Both selected - don't set type filter, return all content types
        // Don't append type parameter
      } else if (isPageSelected) {
        // Only page selected
        params.append('type', constants.search.contentTypes.page)
      } else if (hasEventSpecificFilters) {
        // Only event types selected - restrict search to events only
        params.append('type', constants.search.contentTypes.event)
      }

      // Add category filters (event types only, exclude 'page')
      // Pass the category codes to the categories parameter (unless ALL_EVENTS is selected)
      if (eventTypes && eventTypes.length > 0) {
        // If ALL_EVENTS is selected, don't filter by specific categories
        if (!eventTypes.includes(constants.search.contentTypes.allEvents)) {
          eventTypes.forEach(type => params.append('categories', type))
        }
      }
    }

    // Add location filter
    if (filters.locations && filters.locations.length > 0) {
      filters.locations.forEach(location => params.append('location', location))
    }

    // Add topic filters
    if (filters.topics && filters.topics.length > 0) {
      filters.topics.forEach(topic => params.append('topics', topic))
    }

    // Add date range filters
    if (filters.dateFrom) {
      params.append('dateFrom', filters.dateFrom)
    }
    if (filters.dateTo) {
      params.append('dateTo', filters.dateTo)
    }

    const response = await fetch(`${window.location.origin}/api/SearchApi/Search?${params.toString()}`)
    const responseJson = await response.json()
    return responseJson
  } catch (error) {
    console.error('Error fetching search results:', error)
    return { success: false, errorMessage: 'Failed to fetch search results. Please try again.' }
  }
}

const ResultsWrapper = (props) => {
  const [results, setResults] = useState(null)
  const [searchTerm, setSearchTerm] = useState()
  const [currentPage, setCurrentPage] = useState(1)
  const [filters, setFilters] = useState(() => {
    if (props.preFilter === constants.search.preFilters.events) return { types: [constants.search.contentTypes.allEvents] }
    if (props.preFilter === constants.search.preFilters.news) return { types: [constants.search.contentTypes.news] }
    if (props.preFilter === constants.search.preFilters.content) return { types: [constants.search.contentTypes.contentOnly] }
    return {}
  })
  const [taxonomies, setTaxonomies] = useState(null)
  const [hasResults, setHasResults] = useState(false)
  const [hasUnappliedFilters, setHasUnappliedFilters] = useState(false)
  const pageSize = props.pageSize > 0 ? props.pageSize : 8 // Default to 8 if pageSize is not provided or is 0

  useEffect(() => {
    // Fetch taxonomies for category mapping
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

  useEffect(() => {
    if (props.searchTerm === undefined) return
    setSearchTerm(props.searchTerm ?? '')
    setCurrentPage(1) // Reset to page 1 when search term changes
  }, [props.searchTerm])

  useEffect(() => {
    // Allow empty searches, but prevent search before searchTerm is initialized from URL
    if (searchTerm === undefined) return

    const fetchResults = async () => {
      const response = await getResults(searchTerm, currentPage, pageSize, filters)
      if (response && !response.errorMessage) {
        setResults(response)
        setHasResults(response.result.items.length > 0)
      }
    }

    fetchResults()
  }, [searchTerm, currentPage, pageSize, filters])

  const handlePageClick = (event) => {
    const newPage = event.selected + 1 // ReactPaginate uses 0-based indexing
    setCurrentPage(newPage)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleFiltersChange = (newFilters) => {
    setFilters(newFilters)
    setCurrentPage(1) // Reset to page 1 when filters change
    setHasUnappliedFilters(false) // Reset flag when filters are applied
  }

  // Compare two filter objects to check if they're different
  const filtersAreDifferent = (appliedFilters, pendingFilters) => {
    if (!appliedFilters && !pendingFilters) return false
    if (!appliedFilters || !pendingFilters) return true

    const normalizeArray = (arr) => {
      if (!arr || !Array.isArray(arr) || arr.length === 0) return ''
      return [...arr].sort((a, b) => a.localeCompare(b)).join(',')
    }
    const normalizeString = (str) => (str || '').trim()

    const appliedTypes = normalizeArray(appliedFilters?.types)
    const pendingTypes = normalizeArray(pendingFilters?.types)
    const appliedLocations = normalizeArray(appliedFilters?.locations)
    const pendingLocations = normalizeArray(pendingFilters?.locations)
    const appliedTopics = normalizeArray(appliedFilters?.topics)
    const pendingTopics = normalizeArray(pendingFilters?.topics)
    const appliedDateFrom = normalizeString(appliedFilters?.dateFrom)
    const pendingDateFrom = normalizeString(pendingFilters?.dateFrom)
    const appliedDateTo = normalizeString(appliedFilters?.dateTo)
    const pendingDateTo = normalizeString(pendingFilters?.dateTo)

    return (
      appliedTypes !== pendingTypes ||
      appliedLocations !== pendingLocations ||
      appliedTopics !== pendingTopics ||
      appliedDateFrom !== pendingDateFrom ||
      appliedDateTo !== pendingDateTo
    )
  }

  const handlePendingFiltersChange = (pendingFilters) => {
    if (!pendingFilters) return
    const hasChanges = filtersAreDifferent(filters, pendingFilters)
    setHasUnappliedFilters(hasChanges)
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-6 xl:grid-cols-4 gap-8">
      {/* Filter Sidebar - Left Side */}
      <div className="lg:col-span-2 xl:col-span-1">
        {hasResults && (
          <a href="#results" className="max-h-0 overflow-hidden focus-visible:max-h-max transition-all duration-300 text-primary font-din block mb-4">
            Skip to results
          </a>
        )}
        <SearchFilters
          onFiltersChange={handleFiltersChange}
          onPendingFiltersChange={handlePendingFiltersChange}
          appliedFilters={filters}
          hasUnappliedFilters={hasUnappliedFilters}
          preFilter={props.preFilter}
        />
      </div>

      {/* Search Results - Right Side */}
      <div className="lg:col-span-4 xl:col-span-3">
        {/* Empty State */}
        {!hasResults && (
          <div className="py-12">
            <div className="mb-4">
              <MagnifyingGlassIcon className="h-12 w-12 text-secondary" />
            </div>
            <h3 className="text-display-sm font-apex-book text-secondary mb-2">No results found</h3>
            <p className="text-primary font-din mb-6">
              We couldn&apos;t find any results matching your search criteria. Try adjusting your filters or search term.
            </p>
            <div>
              <p className="text-primary font-din font-bold mb-2">Suggestions:</p>
              <ul className="list-disc list-inside text-primary font-din space-y-1">
                <li>Check your spelling</li>
                <li>Try different or more general keywords</li>
                <li>Remove some filters to broaden your search</li>
              </ul>
            </div>
          </div>
        )}

        {/* Results List */}
        <ul className="divide-y" id="results">
          {results?.result?.items?.map((res, idx) => {
            return <ResultsItem key={`result-${res.itemId || idx}`} result={res} {...{idx, searchTerm}} quantity={results?.result?.items.length} taxonomies={taxonomies} />
          })}
        </ul>

        {/* Results Count and Pagination */}
        {results?.result?.items && results.result.items.length > 0 && (
          <div className="flex max-lg:flex-col max-lg:items-start max-lg:gap-y-4 max-lg:justify-center justify-between items-center border-t border-grey-subtle  sm:px-0 pt-6">
            <span className="text-primary text-lg font-din max-lg:inline-block max-lg:mx-auto">
              Showing {((results?.result?.currentPage || 1) - 1) * (results?.result?.pageSize || pageSize) + 1}-
              {Math.min((results?.result?.currentPage || 1) * (results?.result?.pageSize || pageSize), results?.result?.totalCount || 0)} out of{' '}
              {results?.result?.totalCount || 0} results
            </span>

            {(results?.result?.totalPages || 0) > 1 && (
              <ReactPaginate
                breakLabel="..."
                previousLabel={
                  <>
                    <span className="sr-only">Previous</span>
                    <ChevronLeftIcon role="presentation" className="h-6 w-6" aria-hidden="true" />
                  </>
                }
                onPageChange={handlePageClick}
                pageRangeDisplayed={5}
                pageCount={results?.result?.totalPages || 0}
                forcePage={(results?.result?.currentPage || 1) - 1}
                nextLabel={
                  <>
                    <span className="sr-only">Next</span>
                    <ChevronRightIcon role="presentation" className="h-6 w-6" aria-hidden="true" />
                  </>
                }
                renderOnZeroPageCount={null}
              />
            )}
          </div>
        )}
      </div>
    </div>
  )
}

const ResultsItem = (props) => {
  const [, setOnPage] = useState(false)
  useEffect(() => {
    setOnPage(() => {
      return props.idx >= props.startIndex && props.idx <= props.endIndex
    })
  }, [props.startIndex, props.endIndex])

  // Format date range for display
  const formatDateRange = (startDate, endDate) => {
    if (!startDate && !endDate) return null

    // If only one date is provided, show it
    if (!startDate) return endDate
    if (!endDate) return startDate

    // If both dates are the same, show only one
    if (startDate === endDate) return startDate

    // Otherwise show range
    return `${startDate} - ${endDate}`
  }

  const dateRange = formatDateRange(props.result.startDate, props.result.endDate)


  const title = props.result?.title ?? props.result?.nodeName
  return (
    <li className="py-8" data-idx={props.idx}>
      <div className="flex gap-4 mb-4 items-center">
        {props.result.categories?.map((category) => (
          <Pill key={category.id}>{category.name}</Pill>
        ))}

        {props.result.category && props.taxonomies?.eventCategories && (
          <Pill>{props.taxonomies.eventCategories[props.result.category]}</Pill>
        )}

        {dateRange && (
          <span className="bg-transparent text-md font-din text-primary">{dateRange}</span>
        )}
      </div>
      <a href={props.result?.url} className="inline-block mb-4" onClick={() => {
        dataLayerPush({
          event: 'search_result_selected',
          category: 'Results',
          keyword: props.searchTerm,
          click_text: title,
          quantity: props.quantity,
          is_event: props.result.category
        })
      }}>
        <strong className="text-display-md font-apex-book text-secondary inline-block mb-0">
          {title}
        </strong>
      </a>
      {props.result?.description && (
        <p
          data-rte="true"
          className="text-primary font-din"
          dangerouslySetInnerHTML={createMarkup(props.result?.description)}
        ></p>
      )}
    </li>
  )
}

const ResultsHeader = (props) => {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const submitForm = (evt) => {
    evt.preventDefault()

    dataLayerPush({
      event: 'onsite_search',
      keyword: props.searchTerm
    })

    const loc = window.location
    const searchUrl = new URL(`${loc.origin}/${themeConfig.settings.searchPageStub}`)
    searchUrl.searchParams.append('q', props.searchTerm || '')
    window.location = searchUrl
  }

  useEffect(() => {
    const handleKeyDown = (evt) => {
      if (evt.ctrlKey && evt.key === '/') {
        evt.preventDefault()
        document.querySelector('input[id="search"]').focus()
      }
    }
    document.addEventListener('keydown', handleKeyDown)
  }, [])

  const bgClass = props.hideBackground ? '' : 'bg-grey-subtle'

  return (
    <Section {...props} outerClass={`${componentPadding} ${bgClass}`} type="searchResultsHeader">
      <div className={`${bgClass} relative left-1/2 -translate-x-1/2 w-screen`}>
        <div className="container mx-auto px-7">
          <h2 className="block mx-auto pt-6 pb-0 mb-6 text-center text-secondary text-display-sm md:text-display-md font-apex-book">
            What are you looking for?
          </h2>
          <div className="md:w-3/4 mx-auto">
            <form role="search" className="flex md:flex-nowrap gap-0" onSubmit={submitForm}>
              <input
                id="search"
                type="search"
                value={props.searchTerm || ''}
                onChange={(e) => props.setSearchTerm(e.target.value)}
                placeholder={props.placeholderText || "Enter search terms"}
                autoComplete="off"
                aria-label="sitewide search"
                className="rounded-l-full py-3 pr-5 pl-12 basis-auto grow text-primary border-primary border-r-0 bg-no-repeat"
                style={{
                  backgroundImage: `url("${getSearchIcon()}")`,
                  backgroundPosition: '1rem center',
                  backgroundSize: '1.5rem 1.5rem',
                  maxWidth: 'calc(100% - 110px)'
                }}
              />
              <button
                type="submit"
                className="label-sm !mb-0 rounded-r-full py-3 px-5 basis-auto shrink border-link bg-link text-white hover:bg-link-hover hover:border-link-hover focus-visible:bg-link-hover focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:bg-disabled disabled:border-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed border uppercase hover:underline focus-visible:underline "
                aria-label={props.searchButtonText || "Search"}
              >
                {props.searchButtonText || "Search"}
              </button>
            </form>
            <span className="text-xs hidden lg:block text-left mt-2 text-primary font-din">
              [ Ctrl + / ] to search
            </span>
          </div>
        </div>
      </div>
    </Section>
  )
}

const SearchResult = (props) => {
  const [searchTerm, setSearchTerm] = useState()
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)


  useEffect(() => {
    const url = new URL(window.location)
    const search = new URLSearchParams(url.search)
    const q = search.get('q')
    setSearchTerm(q ? decodeURIComponent(q) : '')
  }, [])

  return (
    <SearchResultsProvider>
      <Suspense key={`search-loader`} fallback={<Loader />}>
        <ResultsHeader {...props} searchTerm={searchTerm} setSearchTerm={setSearchTerm} />
        <Section type="searchResult" outerClass={`${componentPadding}`} innerClass="px-7" {...props}>
          <ResultsWrapper {...props} searchTerm={searchTerm} />
        </Section>
      </Suspense>
    </SearchResultsProvider>
  )
}

SearchResult.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  pageSize: PropTypes.number,
  placeholderText: PropTypes.string,
  searchButtonText: PropTypes.string,
  preFilter: PropTypes.string,
  hideBackground: PropTypes.bool,
}

ResultsWrapper.propTypes = {
  searchTerm: PropTypes.string,
  pageSize: PropTypes.number,
  preFilter: PropTypes.string,
}

ResultsHeader.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  searchTerm: PropTypes.string,
  setSearchTerm: PropTypes.func,
  placeholderText: PropTypes.string,
  searchButtonText: PropTypes.string,
  hideBackground: PropTypes.bool,
}

ResultsItem.propTypes = {
  result: PropTypes.shape({
    itemId: PropTypes.string,
    categories: PropTypes.arrayOf(PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.string
    })),
    category: PropTypes.string,
    contentType: PropTypes.string,
    startDate: PropTypes.string,
    endDate: PropTypes.string,
    url: PropTypes.string,
    title: PropTypes.string,
    nodeName: PropTypes.string,
    description: PropTypes.string
  }),
  idx: PropTypes.number,
  startIndex: PropTypes.number,
  endIndex: PropTypes.number,
  taxonomies: PropTypes.shape({
    eventCategories: PropTypes.object
  }),
  quantity: PropTypes.number,
  searchTerm: PropTypes.string,
}

export default SearchResult
