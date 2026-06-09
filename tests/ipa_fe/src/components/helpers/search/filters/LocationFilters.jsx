import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import { TopLevel, ShowAll, FilterItem, FilterWrapper } from './elements'

const LocationFilters = ({ locations, selectedLocations, setSelectedLocations, initialOpen = false }) => {
  const [showAll, setShowAll] = useState(false)
  const [open, setOpen] = useState(initialOpen)

  // Update open state if initialOpen changes (e.g., from URL params)
  useEffect(() => {
    if (initialOpen) {
      setOpen(true)
    }
  }, [initialOpen])

  const handleAccordionToggle = () => {
    if (open) {
      setShowAll(false)
    }
    setOpen(!open)
  }

  const handleLocationChange = (location) => {
    setSelectedLocations((prev) => (prev.includes(location) ? prev.filter((l) => l !== location) : [...prev, location]))
  }

  return (
    <>
      {/* Location Filter */}
      <div className="mb-6">
        <TopLevel handleAccordionToggle={handleAccordionToggle} open={open} title="Location" />
        <FilterWrapper id="location-accordion" open={open} showAll={showAll}>
          {locations?.map((location, idx) => (
            <FilterItem
              key={location}
              type="location"
              item={location}
              checked={selectedLocations.includes(location)}
              onChange={() => handleLocationChange(location)}
              open={open}
              idx={idx}
              showAll={showAll}
            />
          ))}
          {locations?.length > 4 && !showAll && <ShowAll type="locations" setShowAll={setShowAll} open={open} />}
        </FilterWrapper>
      </div>
    </>
  )
}

LocationFilters.propTypes = {
  locations: PropTypes.array,
  selectedLocations: PropTypes.array.isRequired,
  setSelectedLocations: PropTypes.func.isRequired,
  initialOpen: PropTypes.bool,
}

export default LocationFilters
