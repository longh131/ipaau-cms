import { useState } from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/solid'

// Global constant for the maximum allowed date (6 months from today)
const SIX_MONTHS_FROM_NOW = moment().add(6, 'months')

const Heading = ({ date, changeMonth, resetDate }) => {
  const nextMonth = moment(date).add(1, 'month')
  const isNextMonthDisabled = nextMonth.isAfter(SIX_MONTHS_FROM_NOW, 'month')

  return (
  <nav className="calendar--nav w-full flex justify-between items-center py-2">
    <button type="button" onClick={() => resetDate()}>
      <h4 className="font-inter grow text-display-xs m-0 p-0 font-bold text-black">
        {date.format('MMMM YYYY')}
      </h4>
    </button>

    <div className="flex gap-2">
      <button type="button" className="group/previous disabled:cursor-not-allowed"  onClick={() => changeMonth(date.month() - 1)}>
        <ChevronLeftIcon className="w-6 h-6 text-black group-disabled/previous:text-disabled" />
      </button>
      <button type="button" className="group/next disabled:cursor-not-allowed" disabled={isNextMonthDisabled} onClick={() => changeMonth(date.month() + 1)}>
        <ChevronRightIcon className="w-6 h-6 text-black group-disabled/next:text-disabled" />
      </button>
    </div>
  </nav>
)}

Heading.propTypes = {
  date: PropTypes.object.isRequired,
  changeMonth: PropTypes.func.isRequired,
  resetDate: PropTypes.func.isRequired,
}

const Day = ({ currentDate, date, startDate, endDate, maxAllowedDate, onClick }) => {
  const className = []
  const attributes = []

  className.push('day')

  // Check if date is more than 6 months in the future
  const isDisabled = date.isAfter(maxAllowedDate, 'day')

  if (isDisabled) {
    className.push('disabled')
    attributes.tabIndex = -1
  } else {
    attributes.tabIndex = 0
  }

  if (moment().isSame(date, 'day')) {
    className.push('active')
  }

  if (startDate && date.isSame(startDate, 'day')) {
    className.push('start')
  }

  if (startDate && endDate && date.isBetween(startDate, endDate, 'day')) {
    className.push('between')
  }

  if (endDate && date.isSame(endDate, 'day')) {
    className.push('end')
  }

  if (!date.isSame(currentDate, 'month')) {
    className.push('muted')
  }

  const handleClick = () => {
    if (!isDisabled) {
      onClick(date)
    }
  }

  return (
    <button type="button" {...attributes} onClick={handleClick} className={`${className.join(' ')} relative inline-flex items-center justify-center select-none ${isDisabled ? 'cursor-not-allowed' : 'cursor-pointer'}`}>
      <span className="day-inner relative z-10">
        {date.date()}
      </span>
    </button>
  )
}

Day.propTypes = {
  currentDate: PropTypes.object.isRequired,
  date: PropTypes.object.isRequired,
  startDate: PropTypes.object,
  endDate: PropTypes.object,
  maxAllowedDate: PropTypes.object.isRequired,
  onClick: PropTypes.func.isRequired,
  isDisabled: PropTypes.bool,
  currentStartDate: PropTypes.object,
  currentEndDate: PropTypes.object,
}

const Days = ({ date, startDate, endDate, maxAllowedDate, onClick }) => {
  const thisDate = moment(date)
  const daysInMonth = moment(date).daysInMonth()
  const firstDayDate = moment(date).startOf('month')
  const previousMonth = moment(date).subtract(1, 'month')
  const previousMonthDays = previousMonth.daysInMonth()
  const nextMonth = moment(date).add(1, 'month')
  const days = []
  const labels = []

  for (let i = 1; i <= 7; i++) {
    labels.push(
      <span key={`label-${i}`} className="label">
        {moment().day(i).format('ddd')}
      </span>
    )
  }

  for (let i = firstDayDate.day() || 7; i > 1; i--) {
    previousMonth.date(previousMonthDays - i + 2)

    days.push(
      <Day
        key={moment(previousMonth).format('DD MM YYYY')}
        onClick={onClick}
        currentDate={date}
        date={moment(previousMonth)}
        startDate={startDate}
        endDate={endDate}
        maxAllowedDate={maxAllowedDate}
      />
    )
  }

  for (let i = 1; i <= daysInMonth; i++) {
    thisDate.date(i)

    days.push(
      <Day
        key={moment(thisDate).format('DD MM YYYY')}
        onClick={onClick}
        currentDate={date}
        date={moment(thisDate)}
        startDate={startDate}
        endDate={endDate}
        maxAllowedDate={maxAllowedDate}
      />
    )
  }

  const daysCount = days.length
  for (let i = 1; i <= 42 - daysCount; i++) {
    nextMonth.date(i)
    days.push(
      <Day
        key={moment(nextMonth).format('DD MM YYYY')}
        onClick={onClick}
        currentDate={date}
        date={moment(nextMonth)}
        startDate={startDate}
        endDate={endDate}
        maxAllowedDate={maxAllowedDate}
      />
    )
  }

  return (
    <nav className="calendar--days">
      {labels}
      {days}
    </nav>
  )
}

Days.propTypes = {
  date: PropTypes.object.isRequired,
  startDate: PropTypes.object,
  endDate: PropTypes.object,
  maxAllowedDate: PropTypes.object.isRequired,
  onClick: PropTypes.func.isRequired,
}

function Calendar({ currentStartDate, currentEndDate, handleDateChange, setShowCalendar }) {
  const [date, setDate] = useState(moment())
  const initialStartDate = currentStartDate ? moment(currentStartDate) : moment()
  const initialEndDate = currentEndDate ? moment(currentEndDate) : moment()
  const [startDate, setStartDate] = useState(initialStartDate)
  const [endDate, setEndDate] = useState(initialEndDate)
  const [, setCurrentMonth] = useState(initialStartDate.month() + 1)

  const resetDate = () => {
    setDate(moment())
    setCurrentMonth(moment().month() + 1)
  }

  const changeMonth = (month) => {
    const newDate = moment(date).month(month)
    setDate(newDate)
  }

  const changeDate = (clickedDate) => {
    let newStartDate = startDate
    let newEndDate = endDate

    if (
      !startDate ||
      !endDate ||
      clickedDate.isBefore(startDate, 'day') ||
      !startDate.isSame(endDate, 'day')
    ) {
      newStartDate = moment(clickedDate)
      newEndDate = moment(clickedDate)
    } else if (
      startDate &&
      endDate &&
      clickedDate.isSame(startDate, 'day') &&
      clickedDate.isSame(endDate, 'day')
    ) {
      newStartDate = null
      newEndDate = null
    } else if (startDate && clickedDate.isAfter(startDate, 'day')) {
      newEndDate = moment(clickedDate)
    }

    setStartDate(newStartDate)
    setEndDate(newEndDate)
  }

  const hasDatesSelected = startDate && endDate

  return (
    <div className="calendar">
      <Heading date={date} changeMonth={changeMonth} resetDate={resetDate} />
      <Days onClick={changeDate} date={date} startDate={startDate} endDate={endDate} maxAllowedDate={SIX_MONTHS_FROM_NOW} />
      <button
        disabled={!hasDatesSelected}
        className={`px-6 py-2 mt-3 rounded-full text-center uppercase w-full transition-all duration-300 font-din grow-0 shrink-0 text-label-lg border border-link hover:border-link-hover bg-link hover:bg-link-hover text-white hover:underline cursor-pointer disabled:border-disabled disabled:bg-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed`}
        onClick={() => hasDatesSelected && handleDateChange(startDate, endDate)}
      >
        Apply dates
      </button>
      <button className="px-6 py-2 mt-3 rounded-full text-center uppercase w-full  transition-all duration-300 font-din grow-0 shrink-0 text-label-lg border border-link hover:border-link-hover bg-white text-link hover:text-link-hover hover:underline" onClick={() => setShowCalendar(false)}>Cancel</button>
    </div>
  )
}

Calendar.propTypes = {
  handleDateChange: PropTypes.func.isRequired,
  setShowCalendar: PropTypes.func.isRequired,
}

export default Calendar
