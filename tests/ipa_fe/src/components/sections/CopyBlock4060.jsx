import PropTypes from 'prop-types'
import BasicContentWithColumns from './BasicContentWithColumns'

function CopyBlock4060(props) {
  // Copy Block 40/60 reuses BasicContentWithColumns
  return <BasicContentWithColumns {...props} />
}

CopyBlock4060.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  decoration: PropTypes.string,
  decorationWidth: PropTypes.string,
  columnSpacing: PropTypes.string,
  contentColumnItems: PropTypes.array,
  contentColumnItemsAlignment: PropTypes.string,
  backgroundColor: PropTypes.string,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
}

export default CopyBlock4060
