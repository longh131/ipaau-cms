import PropTypes from 'prop-types'
import ButtonEl from '../ctas/Button'
import MapPinIcon from './MapPinIcon'

const AccountantSearch = ({ postcode, setPostcode, includeSurrounding, setIncludeSurrounding, handleSearch, postCodeLabel, includeSurroundingsLabel , searchButtonLabel, isLoading }) => {
  return (
    <form onSubmit={handleSearch} className="flex flex-col items-center space-y-6 mx-auto">
      <div className="flex gap-4 w-full">
        <div className="flex-1 relative">
          <MapPinIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-6 w-6 text-primary" />
          <input
            id="postcode"
            type="text"
            value={postcode}
            onChange={(e) => setPostcode(e.target.value)}
            placeholder="Enter your postcode"
            className="w-full pl-10 py-2.5 px-4 border border-primary-border rounded-full text-primary font-din"
            aria-label={postCodeLabel || 'Enter your postcode'}
            aria-required="true"
            required
          />
        </div>
      </div>

      <div className="flex items-center gap-2">
        <input
          id="includeSurrounding"
          type="checkbox"
          checked={includeSurrounding}
          onChange={(e) => setIncludeSurrounding(e.target.checked)}
          className="search-checkbox"
        />
        <label htmlFor="includeSurrounding" className="label-md !normal-case !mb-0">
          {includeSurroundingsLabel || 'Include surrounding suburbs in search'}
        </label>
      </div>

      <ButtonEl type="submit" className="mt-6" theme="primary" disabled={isLoading || !postcode.trim()}>
        {isLoading ? 'Searching...' : searchButtonLabel || 'SEARCH'}
      </ButtonEl>
    </form>
  )
}

AccountantSearch.propTypes = {
  postcode: PropTypes.string.isRequired,
  setPostcode: PropTypes.func.isRequired,
  includeSurrounding: PropTypes.bool.isRequired,
  setIncludeSurrounding: PropTypes.func.isRequired,
  handleSearch: PropTypes.func.isRequired,
  postCodeLabel: PropTypes.string,
  includeSurroundingsLabel: PropTypes.string,
  searchButtonLabel: PropTypes.string,
  isLoading: PropTypes.bool,
}

export default AccountantSearch
