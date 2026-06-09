import PropTypes from 'prop-types'
import { FilterItem } from './elements'
import constants from '../../../../helpers/constants'

const EventFilters = ({ eventCategories, selectedTypes, setSelectedTypes }) => {

  const handleTypeChange = (type) => {
    setSelectedTypes((prev) => {
      // Preserve 'page' selection as it's independent
      const pageSelection = prev.includes(constants.search.contentTypes.page) ? [constants.search.contentTypes.page] : []
      const allEventCategories = eventCategories ? Object.keys(eventCategories) : []

      // If clicking "ALL_EVENTS"
      if (type === constants.search.contentTypes.allEvents) {
        const isCurrentlySelected = prev.includes(constants.search.contentTypes.allEvents)
        if (isCurrentlySelected) {
          // Deselecting ALL_EVENTS: remove it and all sub-items
          return pageSelection
        } else {
          // Selecting ALL_EVENTS: select it and all sub-items
          return [...pageSelection, constants.search.contentTypes.allEvents, ...allEventCategories]
        }
      }

      // If clicking a specific category
      const withoutAllEvents = prev.filter((t) => t !== constants.search.contentTypes.allEvents && t !== constants.search.contentTypes.page)
      const isCategorySelected = withoutAllEvents.includes(type)
      const newSelection = isCategorySelected
        ? withoutAllEvents.filter((t) => t !== type)
        : [...withoutAllEvents, type]

      // Check if all sub-items are now selected
      const allSubItemsSelected = allEventCategories.length > 0 && allEventCategories.every(cat => newSelection.includes(cat))

      // If all sub-items are selected, also select ALL_EVENTS
      // If not all sub-items are selected, ensure ALL_EVENTS is not selected
      const finalSelection = allSubItemsSelected
        ? [...pageSelection, constants.search.contentTypes.allEvents, ...newSelection]
        : [...pageSelection, ...newSelection]

      return finalSelection
    })
  }

  // Calculate if ALL_EVENTS should be indeterminate
  // Indeterminate when some (but not all) specific categories are selected
  // Exclude 'page' since it's a separate filter type, not an event category
  const allEventCategories = eventCategories ? Object.keys(eventCategories) : []
  const selectedSpecificCategories = selectedTypes.filter(t => t !== constants.search.contentTypes.allEvents && t !== constants.search.contentTypes.page)
  const isAllEventsChecked = selectedTypes.includes(constants.search.contentTypes.allEvents)
  // Indeterminate when: not all checked, but some are selected (and not all)
  const isIndeterminate = !isAllEventsChecked && selectedSpecificCategories.length > 0 && selectedSpecificCategories.length < allEventCategories.length

  return (
    <>
      {/* Event Category Filter */}
      {eventCategories && Object.keys(eventCategories).length > 0 && (
        <div className="mb-6">
          {/* All Events option */}
          <FilterItem type="event" item="Events" checked={selectedTypes.includes(constants.search.contentTypes.allEvents)} indeterminate={isIndeterminate} onChange={() => handleTypeChange(constants.search.contentTypes.allEvents)} open={true} idx={0} showAll={true} />

          {/* Specific event categories */}
          {Object.entries(eventCategories).map(([code, displayName]) => (
            <FilterItem key={code} type="event" item={displayName} checked={selectedTypes.includes(code)} onChange={() => handleTypeChange(code)} open={true} idx={0} showAll={true} labelClass={`pl-12`} />
          ))}
        </div>
      )}
    </>
  )
}

EventFilters.propTypes = {
  eventCategories: PropTypes.object,
  selectedTypes: PropTypes.array.isRequired,
  setSelectedTypes: PropTypes.func.isRequired,
}

export default EventFilters
