import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { transformPaddingToTailwind } from '../../helpers/style'

// Helper function to format date
const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('en-AU', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Helper function to format date range
const formatDateRange = (startDate, endDate) => {
  if (!startDate && !endDate) return null

  const start = formatDate(startDate)
  const end = formatDate(endDate)

  if (!start && !end) return null
  if (!start) return end
  if (!end) return start

  // If both dates are the same, show only one
  if (start === end) return start

  // Otherwise show range
  return `${start} - ${end}`
}

// Helper function to format currency
const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return null
  return new Intl.NumberFormat('en-AU', {
    style: 'currency',
    currency: 'AUD'
  }).format(amount)
}

function EventDetailsBlock(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const event = props.event

  // If no event data, don't render anything
  if (!event) {
    return null
  }

  const dateRange = formatDateRange(event.start, event.end)
  const memberPrice = formatCurrency(event.memberPriceFrom)
  const nonMemberPrice = formatCurrency(event.nonMemberPriceFrom)

  return (
    <Section
      type="eventDetailsBlock"
      outerClass={componentPadding}
      {...props}
    >
      <div className="flex flex-col gap-8">
        {/* Event Image */}
        {event.image && (
          <div className="w-full">
            <img
              src={event.image}
              alt={event.title || 'Event image'}
              className="w-full h-auto rounded-lg"
            />
          </div>
        )}

        {/* Event Title */}
        {event.title && (
          <h1 className="text-4xl lg:text-5xl xl:text-6xl mb-0 font-bold text-primary leading-tight">
            {event.title}
          </h1>
        )}

        {/* Event Date */}
        {dateRange && (
          <div className="flex items-center gap-2">
            <svg className="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span className="text-lg font-medium">{dateRange}</span>
          </div>
        )}

        {/* Event Metadata */}
        <div className="flex flex-wrap gap-4">
          {event.category && (
            <span className="px-4 py-2 bg-secondary text-primary rounded-full font-medium">
              {event.category}
            </span>
          )}
          {event.state && (
            <span className="px-4 py-2 bg-gray-100 text-gray-700 rounded-full">
              {event.state}
            </span>
          )}
          {event.country && event.country !== 'Australia' && (
            <span className="px-4 py-2 bg-gray-100 text-gray-700 rounded-full">
              {event.country}
            </span>
          )}
          {event.status && (
            <span className="px-4 py-2 bg-gray-100 text-gray-700 rounded-full">
              {event.status}
            </span>
          )}
        </div>

        {/* Short Description */}
        {event.shortDescription && (
          <div
            className="text-xl text-gray-700 leading-relaxed"
            data-rte="true"
            dangerouslySetInnerHTML={createMarkup(event.shortDescription)}
          />
        )}

        {/* Pricing and Registration Info */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Member Pricing */}
          {memberPrice && (
            <div className="bg-primary text-white p-6 rounded-lg">
              <h3 className="text-sm font-semibold uppercase mb-2">Member Price</h3>
              <p className="text-3xl font-bold">{memberPrice}</p>
              {event.memberPriceFrom > 0 && <p className="text-sm mt-1">From</p>}
            </div>
          )}

          {/* Non-Member Pricing */}
          {nonMemberPrice && (
            <div className="bg-gray-100 p-6 rounded-lg">
              <h3 className="text-sm font-semibold uppercase mb-2">Non-Member Price</h3>
              <p className="text-3xl font-bold text-primary">{nonMemberPrice}</p>
              {event.nonMemberPriceFrom > 0 && <p className="text-sm mt-1">From</p>}
            </div>
          )}

          {/* CPD Points */}
          {event.upToCPD && event.upToCPD > 0 && (
            <div className="bg-secondary p-6 rounded-lg">
              <h3 className="text-sm font-semibold uppercase mb-2">CPD Points</h3>
              <p className="text-3xl font-bold text-primary">
                {event.upToCPD}
                {event.upToCPD > 1 ? ' Points' : ' Point'}
              </p>
            </div>
          )}

          {/* Spaces Available */}
          {event.spaces !== null && event.spaces !== undefined && (
            <div className="bg-gray-100 p-6 rounded-lg">
              <h3 className="text-sm font-semibold uppercase mb-2">Spaces Available</h3>
              <p className="text-3xl font-bold text-primary">
                {event.spaces > 0 ? event.spaces : 'Limited'}
              </p>
              {event.maxRegistrants && (
                <p className="text-sm mt-1">of {event.maxRegistrants} total</p>
              )}
            </div>
          )}

          {/* Current Attendees */}
          {event.attendees !== null && event.attendees !== undefined && event.attendees > 0 && (
            <div className="bg-gray-100 p-6 rounded-lg">
              <h3 className="text-sm font-semibold uppercase mb-2">Current Attendees</h3>
              <p className="text-3xl font-bold text-primary">{event.attendees}</p>
            </div>
          )}

          {/* Member Only Badge */}
          {event.memberOnly && (
            <div className="bg-primary text-white p-6 rounded-lg flex items-center justify-center">
              <div className="text-center">
                <svg className="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                </svg>
                <p className="font-bold">Members Only</p>
              </div>
            </div>
          )}
        </div>

        {/* Topics */}
        {event.topics && (
          <div className="border-t pt-6">
            <h3 className="text-2xl font-bold text-primary mb-4">Topics</h3>
            <div
              className="prose max-w-none"
              data-rte="true"
              dangerouslySetInnerHTML={createMarkup(event.topics)}
            />
          </div>
        )}

        {/* Long Description */}
        {event.longDescription && (
          <div className="border-t pt-6">
            <h3 className="text-2xl font-bold text-primary mb-4">Event Details</h3>
            <div
              className="prose max-w-none"
              data-rte="true"
              dangerouslySetInnerHTML={createMarkup(event.longDescription)}
            />
          </div>
        )}

        {/* Event Key (for debugging/reference) */}
        {event.eventKey && (
          <div className="text-sm text-gray-500 border-t pt-4">
            Event Code: {event.eventKey}
          </div>
        )}
      </div>
    </Section>
  )
}

EventDetailsBlock.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  event: PropTypes.object,
}

export default EventDetailsBlock
