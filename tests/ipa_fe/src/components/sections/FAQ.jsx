import PropTypes from 'prop-types'
import Accordion from './Accordion'

function FAQ(props) {
  // FAQ reuses Accordion component
  // Pass all props through to Accordion
  return <Accordion {...props} />
}

FAQ.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  accordionPanelItems: PropTypes.arrayOf(
    PropTypes.shape({
      title: PropTypes.string,
      content: PropTypes.string,
    })
  ),
}

export default FAQ
