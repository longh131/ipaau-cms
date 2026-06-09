import PropTypes from 'prop-types'
import { FilterItem } from './elements'

const PageFilters = ({ selectedTypes, setSelectedTypes }) => {
  const handlePageChange = () => {
    setSelectedTypes((prev) => (prev.includes('page') ? prev.filter((t) => t !== 'page') : [...prev, 'page']))
  }

  return (
    <FilterItem type="page" item="Content" checked={selectedTypes.includes('page')} onChange={handlePageChange} open={true} idx={0} showAll={true} />
  )
}

export default PageFilters

PageFilters.propTypes = {
  selectedTypes: PropTypes.array.isRequired,
  setSelectedTypes: PropTypes.func.isRequired,
}
