import { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import ButtonEl from '../helpers/ctas/Button'
import themeConfig from '../../../theme.config'
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/react/24/solid'
import getSearchIcon from '../../helpers/searchIcon'

function Search(props) {
  const [searchActive, setSearchActive] = useState()
  const [searchTerm, setSearchTerm] = useState()

  const [placeholderText, setPlaceholderText] = useState('Enter search terms')
  const [searchButtonText, setSearchButtonText] = useState('Search')
  const [headingText, setHeadingText] = useState('What are you looking for?')

  useEffect(() => {
    const searchConfig = props.headerDataSource?.headerSearchConfiguration || null
    if (searchConfig) {
      setPlaceholderText(searchConfig.placeholderText || 'Enter search terms')
      setSearchButtonText(searchConfig.searchButtonText || 'Search')
      setHeadingText(searchConfig.headingText || 'What are you looking for?')
    }
  }, [props.headerDataSource])

  // Pre-fill search term from query string on mount
  useEffect(() => {
    const url = new URL(window.location)
    const search = new URLSearchParams(url.search)
    const q = search.get('q')
    if (q) {
      setSearchTerm(decodeURIComponent(q))
    }
  }, [])

  document.addEventListener('click', (evt) => {
    if (!evt.target.closest('[data-type="search"]') && !evt.target.closest('[data-type="searchForm"]')) {
      setSearchActive(false)
    }
  })

  const triggerContent = (
    <>
      <MagnifyingGlassIcon
        className={`${searchActive ? 'hidden' : 'block'} h-6 w-6 `}
        role="none"
      />
      <XMarkIcon className={`${searchActive ? 'block' : 'hidden'} h-6 w-6 `} role="none" />
      <span className="sr-only">Toggle search field</span>
    </>
  )

  useEffect(() => {
    setSearchActive((prevState) => (props.menuActive === true || props.ctasActive === true ? false : prevState))
  }, [props.menuActive, props.ctasActive])

  const submitForm = (evt) => {
    evt.preventDefault()
    const loc = window.location
    const searchUrl = new URL(`${loc.origin}/${themeConfig.settings.searchPageStub}`)
    searchUrl.searchParams.append('q', searchTerm || '')
    window.location = searchUrl
  }

  const detectSearchOnPage = () => {
    const searchResultsHeader = document.querySelector('[data-type="searchResultsHeader"]')
    if (searchResultsHeader) {
      searchResultsHeader.scrollIntoView({ behavior: 'smooth' })
      searchResultsHeader.querySelector('input[id="search"]').focus()
      return true
    }
    return false
  }

  const handleSearchClick = (evt) => {
    setSearchActive((prevState) => {
      const isActive = !prevState

      props.setMenuActive((prevState) => {
        let newState = prevState
        if (isActive === true) {
          // is search has become active turn the menu off
          newState = false
        }
        // otherwise do nothing.
        props.setCtasActive(newState)
        return newState
      })
      if (detectSearchOnPage()) {
        props.setSearchActive(false)
        return false
      } else {
        props.setSearchActive(isActive)
        document.querySelector('input[id="search-banner"]').focus()
      }
      return isActive
    })
  }

  return (
    <>
      <div
        data-type="search"
        className={`basis-1/10 self-center ml-auto peer/trigger ${searchActive && (props.menuActive === false || props.menuActive === undefined) ? 'active' : 'inactive'}`}
      >

        <ButtonEl theme="hollow" className="!shadow-none none mr-4 lg:mr-0" onClick={handleSearchClick} buttonType="default" function="search">
          {triggerContent}
        </ButtonEl>
      </div>
      <div
        data-type="searchForm"
        className={`group/wrapper ${searchActive ? 'active' : 'inactive'} absolute  w-screen top-[100%] flex peer-[.inactive]/trigger:h-0 transition-all z-50 duration-500 peer-[.active]/trigger:h-[12rem] overflow-hidden flex-nowrap bg-grey-subtle left-1/2 -translate-x-1/2 items-center elevation-bottom-4`}
      >
        <form
          className={`group/form ${searchActive ? ' active' : 'inactive'} container h-min max-md:px-8 md:px-12 basis-10/12 md:basis-7/12 mx-auto`}
          onSubmit={submitForm}
          role="search"
          aria-label="Sitewide"
          id="main-nav-search"
        >
          <h2 className="block mx-auto pt-6 pb-0 mb-6 text-center text-secondary text-display-sm md:text-display-md font-apex-book">{headingText}</h2>
          <div className="mx-auto pb-6 pt-0 mt-0 flex flex-nowrap items-stretch justify-center gap-0 group-[.active]/form:opacity-100 group-[.inactive]/form:opacity-0 transition-all duration-300 group-[.active]/form:delay-500">
            <input
              id="search-banner"
              type="search"
              value={searchTerm || ''}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder={placeholderText}
              autoComplete="off"
              tabIndex={searchActive ? 0 : -1}
              aria-hidden={searchActive ? 'false' : 'true'}
              aria-label="Site Search"
              className="rounded-l-full py-3 pr-5 pl-12 basis-auto grow text-primary border-primary border-r-0 bg-no-repeat "
              style={{
                  backgroundImage: `url("${getSearchIcon()}")`,
                  backgroundPosition: '1rem center',
                  backgroundSize: '1.5rem 1.5rem'
                }}
            />
            <button type="submit"
              className="label-sm !mb-0 rounded-r-full py-3 px-5 basis-auto shrink border-link bg-link text-white hover:bg-link-hover hover:border-link-hover focus-visible:bg-link-hover focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:bg-disabled disabled:border-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed border uppercase hover:underline focus-visible:underline "
              aria-label="Search"
              aria-hidden={searchActive ? 'false' : 'true'}
              tabIndex={searchActive ? 0 : -1}
            >
              {searchButtonText}
            </button>

          </div>
        </form>
      </div>
    </>
  )
}

Search.propTypes = {
  headerDataSource: PropTypes.object,
  menuActive: PropTypes.bool,
  ctasActive: PropTypes.bool,
  setMenuActive: PropTypes.func,
  setCtasActive: PropTypes.func,
  setSearchActive: PropTypes.func,
}

export default Search
