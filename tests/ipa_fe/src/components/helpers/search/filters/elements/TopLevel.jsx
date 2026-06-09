import PropTypes from 'prop-types'
import { ChevronDownIcon } from '@heroicons/react/24/solid'

const Title = ({ title, className }) => {
  return (
    <h4 className={`font-din font-bold text-primary text-lg ${className}`}>{title}</h4>
  )
}

const TopLevelButton = ({ handleAccordionToggle, open, title }) => {
  return (
    <button
      id={`${title.toLowerCase().replace(' ', '-')}-accordion-trigger`}
      className="flex w-full items-center border-b border-b-primary-border pb-2 justify-between gap-2"
      onClick={handleAccordionToggle}
      aria-expanded={!!open}
      aria-controls={`${title.toLowerCase().replace(' ', '-')}-accordion`}
    >
      <Title title={title} className="mb-0 pb-2" />
      <ChevronDownIcon className={`w-6 h-6 text-secondary transition-all duration-300 ${open ? 'rotate-180' : ''}`} />
    </button>
  )
}

const TopLevel = ({ handleAccordionToggle = null, open = false, title }) => {
  if (handleAccordionToggle) {
    return <TopLevelButton handleAccordionToggle={handleAccordionToggle} open={open} title={title} />
  }
  return <Title title={title} className="mb-3" />
}

Title.propTypes = {
  title: PropTypes.string,
  className: PropTypes.string,
}

TopLevelButton.propTypes = {
  handleAccordionToggle: PropTypes.func,
  open: PropTypes.bool,
  title: PropTypes.string,
}

TopLevel.propTypes = {
  handleAccordionToggle: PropTypes.func,
  open: PropTypes.bool,
  title: PropTypes.string,
}

export default TopLevel
