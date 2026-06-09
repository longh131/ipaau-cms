import PropTypes from 'prop-types'

const ShowAll = ({ type, setShowAll, open }) => {
  return <button
  key={`show-all-${type}`}
  className="search-label w-full"
  onClick={() => setShowAll(true)}
  disabled={!open}
  aria-hidden={!open}
>
  Show all
</button>}

ShowAll.propTypes = {
  type: PropTypes.string,
  setShowAll: PropTypes.func,
  open: PropTypes.bool,
}

export default ShowAll
