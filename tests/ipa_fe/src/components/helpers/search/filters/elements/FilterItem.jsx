import PropTypes from 'prop-types'
import { useEffect, useRef } from 'react'
import { textToSnakeCase } from '../../../../../helpers/text'

const FilterItem = ({ type, item, checked, onChange, open, idx, showAll, labelClass = '', indeterminate = false }) => {
  const itemId = textToSnakeCase(item)
  const checkboxRef = useRef(null)

  useEffect(() => {
    if (checkboxRef.current) {
      checkboxRef.current.indeterminate = indeterminate
    }
  }, [indeterminate])

  return (
    <label key={`${type}-${item}`} className={`search-label ${idx > 3 && !showAll ? 'hidden' : ''} ${labelClass}`}>
      <input
        ref={checkboxRef}
        disabled={!open || (idx > 3 && !showAll)}
        aria-hidden={!open || (idx > 3 && !showAll)}
        type="checkbox"
        id={`${type}-${itemId}`}
        name={`${type}-${item}`}
        checked={checked}
        onChange={onChange}
        className="search-checkbox"
      />
      <span className="text-primary text-lg">{item}</span>
    </label>
  )
}

FilterItem.propTypes = {
  type: PropTypes.string,
  item: PropTypes.string,
  checked: PropTypes.bool,
  onChange: PropTypes.func,
  open: PropTypes.bool,
  idx: PropTypes.number,
  showAll: PropTypes.bool,
  indeterminate: PropTypes.bool,
  labelClass: PropTypes.string,
}

export default FilterItem
