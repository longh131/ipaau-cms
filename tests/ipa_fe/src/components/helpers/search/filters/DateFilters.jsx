import PropTypes from 'prop-types'
import moment from 'moment'
import Calendar from './Calendar'
import { useRef, useState, useEffect } from 'react'
import { CalendarIcon } from './Icons'
import TopLevel from './elements/TopLevel'

const DateFilters = ({ dateFrom, dateTo, setDateFrom, setDateTo }) => {
  const [showCalendar, setShowCalendar] = useState(false)
  const calendarRef = useRef(null)

  const handleDateRangeChange = (startDate, endDate) => {
    console.log('startDate', startDate.format('YYYY-MM-DD'))
    console.log('endDate', endDate.format('YYYY-MM-DD'))
    if (startDate && endDate ) {
      setDateFrom(startDate.format('YYYY-MM-DD'))
      setDateTo(endDate.format('YYYY-MM-DD'))
    }
    setShowCalendar(false)
  }

    // Handle Escape key and click outside to close calendar
    useEffect(() => {
      if (!showCalendar) return

      const handleEscape = (event) => {
        if (event.key === 'Escape') {
          setShowCalendar(false)
        }
      }

      const handleClickOutside = (event) => {
        if (calendarRef.current && !calendarRef.current.contains(event.target)) {
          setShowCalendar(false)
        }
      }

      document.addEventListener('keydown', handleEscape)
      document.addEventListener('mousedown', handleClickOutside)

      return () => {
        document.removeEventListener('keydown', handleEscape)
        document.removeEventListener('mousedown', handleClickOutside)
      }
    }, [showCalendar])

  return (
    <>
      {/* Date Range Filter */}
      <div className="mb-6 relative" ref={calendarRef}>
        <TopLevel title="Date Range" />
        <button
          key={'date-range-label'}
          className={`flex gap-2 w-full items-center cursor-pointer border-y border-y-primary-border py-3 pl-6 hover:underline`}
          onClick={() => setShowCalendar(true)}
        >
          <CalendarIcon className="w-6 h-6" />
          {!dateFrom && !dateTo && 'Showing all dates'}
          {dateFrom === dateTo && dateFrom ? moment(dateFrom).format('DD/MM/yyyy') : ''}
          {dateFrom !== dateTo && dateFrom ? moment(dateFrom).format('DD/MM/yyyy') : ''}{' '}
          {dateFrom !== dateTo && dateTo ? `- ${moment(dateTo).format('DD/MM/yyyy')}` : ''}
        </button>
        {showCalendar && <Calendar currentStartDate={dateFrom} currentEndDate={dateTo} handleDateChange={handleDateRangeChange} setShowCalendar={setShowCalendar} />}
      </div>
    </>
  )
}

DateFilters.propTypes = {
  dateFrom: PropTypes.string,
  dateTo: PropTypes.string,
  setDateFrom: PropTypes.func.isRequired,
  setDateTo: PropTypes.func.isRequired,
}

export default DateFilters
