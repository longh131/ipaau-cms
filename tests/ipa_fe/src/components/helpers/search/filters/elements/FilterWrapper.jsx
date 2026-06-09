import PropTypes from 'prop-types'

const FilterWrapper = ({ children, open, showAll, id }) => {
  return (
    <div
    className={`${open ? 'block max-h-max overflow-y-auto' : 'max-h-0 overflow-hidden'} ${showAll && open ? '!max-h-max' : ''} transition-all duration-300`}
    id={id}
    aria-hidden={!open}
    >
      {children}
    </div>
  )
}

FilterWrapper.propTypes = {
  children: PropTypes.node,
  open: PropTypes.bool,
  showAll: PropTypes.bool,
  id: PropTypes.string,
}

export default FilterWrapper
