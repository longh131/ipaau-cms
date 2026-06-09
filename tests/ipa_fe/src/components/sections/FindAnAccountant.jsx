import { useState, useCallback } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { transformPaddingToTailwind } from '../../helpers/style'
import AccountantCard from '../helpers/findAnAccountant/AccountantCard'
import AccountantMap from '../helpers/findAnAccountant/AccountantMap'
import AccountantSearch from '../helpers/findAnAccountant/AccountantSearch'
import Loader from '../helpers/Loader'

async function searchAccountants(postcode, includeSurroundingSuburbs = false, radiusKm = null) {
  try {
    const params = new URLSearchParams({
      postcode: postcode,
      includeSurroundingSuburbs: includeSurroundingSuburbs.toString()
    })

    // Only add radius parameter if including surrounding suburbs and radius is provided
    if (includeSurroundingSuburbs && radiusKm != null && radiusKm > 0) {
      params.append('radiusKm', radiusKm.toString())
    }

    const response = await fetch(`${window.location.origin}/api/Accountant/search?${params.toString()}`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    return await response.json()
  } catch (error) {
    console.error('Error fetching accountants:', error)
    return []
  }
}

const FindAnAccountant = (props) => {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const [postcode, setPostcode] = useState('')
  const [includeSurrounding, setIncludeSurrounding] = useState(false)
  const [accountants, setAccountants] = useState([])
  const [selectedAccountant, setSelectedAccountant] = useState(null)
  const [isLoading, setIsLoading] = useState(false)
  const [hasSearched, setHasSearched] = useState(false)

  const handleSearch = async (e) => {
    e.preventDefault()
    if (!postcode?.trim()) return

    setIsLoading(true)
    setHasSearched(true)
    setSelectedAccountant(null)

    try {
      const radiusKm = includeSurrounding && props.surroundingResultsRadius ? props.surroundingResultsRadius : null
      const results = await searchAccountants(postcode.trim(), includeSurrounding, radiusKm)
      setAccountants(results || [])
    } catch (error) {
      console.error('Error searching for accountants:', error)
      setAccountants([])
    } finally {
      setIsLoading(false)
    }
  }

  const handleAccountantSelect = useCallback((accountant) => {
    setSelectedAccountant(accountant)
  }, [])

  const loadingBlock = () => {
    return (
      <div className="text-center py-12">
        <Loader className="mx-auto"/>
        <p className="text-primary text-md font-din">Searching for accountants...</p>
      </div>
    )
  }

  const noResultsBlock = () => {
    return (
      <div className="text-center py-12">
        <p className="text-primary font-din text-lg">
          {props.noResultsMessage || 'No accountants found for this postcode.'}
        </p>
      </div>
    )
  }

  const resultsBlock = () => {
    return (
      <div className="flex flex-col items-center lg:max-w-[75%] lg:mx-auto">
        <div className="flex flex-col md:flex-row items-center md:justify-between gap-2 mb-6 mt-12 w-full">
          <p className="text-primary text-center md:text-left text-xl md:text-2xl font-din">
            You searched for accountants in <strong className="block md:inline font-din font-medium  uppercase tracking-[0.8px] text-warm-plum">{postcode}</strong>
          </p>
        </div>
        {props.enableMapDisplay && (
          <div className="w-full" id="find-an-accountant-map">
          <AccountantMap
            accountants={accountants}
              selectedAccountant={selectedAccountant}
              onAccountantSelect={handleAccountantSelect}
            />
          </div>
        )}
        {accountants.length > 0 && (
          <div id="find-an-accountant-cards" className="space-y-10 mt-12 w-full">
            {accountants.map((accountant) => (
              <AccountantCard
                key={accountant.ID}
                accountant={accountant}
                isSelected={selectedAccountant?.ID === accountant.ID}
                onSelect={handleAccountantSelect}
              />
            ))}
          </div>
        )}
      </div>
    )
  }

  const displayBlock = (size) => {
    switch (size) {
      case 0:
        return noResultsBlock()
      default:
        return resultsBlock()
    }
  }

  return (
    <Section type="findAnAccountant" outerClass={componentPadding} innerClass="gap-6" {...props}>
      <div className="container">
        <AccountantSearch
          postcode={postcode}
          setPostcode={setPostcode}
          includeSurrounding={includeSurrounding}
          setIncludeSurrounding={setIncludeSurrounding}
          handleSearch={handleSearch}
          postCodeLabel={props.postCodeLabel}
          includeSurroundingsLabel={props.includeSurroundingsLabel}
          searchButtonLabel={props.searchButtonLabel}
          isLoading={isLoading}
        />

        {hasSearched && (
          <div>
            {isLoading ? loadingBlock() : displayBlock(accountants.length)}
          </div>
        )}

        {props.caption && (
          <div
            className="mt-6 pt-20 text-center"
            dangerouslySetInnerHTML={createMarkup(props.caption)}
          />
        )}
      </div>
    </Section>
  )
}

FindAnAccountant.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  caption: PropTypes.string,
  postCodeLabel: PropTypes.string,
  searchButtonLabel: PropTypes.string,
  includeSurroundingsLabel: PropTypes.string,
  noResultsMessage: PropTypes.string,
  enableMapDisplay: PropTypes.bool,
  surroundingResultsRadius: PropTypes.number
}

AccountantCard.propTypes = {
  accountant: PropTypes.object.isRequired,
  isSelected: PropTypes.bool,
  onSelect: PropTypes.func.isRequired
}

AccountantMap.propTypes = {
  accountants: PropTypes.array.isRequired,
  selectedAccountant: PropTypes.object,
  onAccountantSelect: PropTypes.func.isRequired
}

export default FindAnAccountant
