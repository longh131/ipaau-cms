import PropTypes from 'prop-types'
import { EnvelopeIcon, PhoneIcon, GlobeAltIcon, ArrowTopRightOnSquareIcon } from '@heroicons/react/24/outline'
import MapPinIcon from './MapPinIcon'
import { createMarkup } from '../../../helpers/markup'

const AccountantList = ({ items, label }) => {
  return (
    <div className="mb-4">
      <p className="text-primary font-din font-bold mb-2">{label}</p>
      <ul className="list-disc list-inside text-primary font-din space-y-1 ml-2">
        {items.map((item, idx) => (
          <li key={`${label}-${item}-${idx}`} className="normal-case">
            {item}
          </li>
        ))}
      </ul>
    </div>
  )
}

const AccountantCard = ({ accountant, isSelected, onSelect }) => {
  return (
    <div
      className={`border-b border-primary-border pb-10 text-primary transition-all`}
    >
      {accountant.company && <p className="label-xl text-secondary uppercasemb-4 mb-4">{accountant.company}</p>}
      {accountant.fullName && <button onClick={(e) => { e.preventDefault(); onSelect(accountant) }} className="text-link hover:text-link-hover hover:underline"><h3 className="text-display-md mb-4">{accountant.fullName}</h3></button>}

      <div className="mb-4 grid grid-cols-1 md:grid-cols-[40%_calc(30%-40px)_calc(30%-40px)] lg:grid-cols-3 gap-6 md:gap-10 text-lg text-primary font-din">

        <div className="col-span-1 col-start-1">

          <div className="grid grid-cols-[minmax(0,min-content)_1fr] gap-2 mb-4 md:mb-8">
            <MapPinIcon className="h-6 w-6 mt-0.5" />
            <span>{accountant.fullAddress}</span>
          </div>
          {accountant.phone && (
            <div className="grid grid-cols-[minmax(0,min-content)_1fr] gap-2 mb-4 md:mb-8">
              <PhoneIcon className="h-6 w-6 text-secondary" />
              <a href={`tel:${accountant.phone}`} className="text-link hover:text-link-hover hover:underline">
                {accountant.phone}
              </a>
            </div>
          )}
          {accountant.email && (
            <div className="grid grid-cols-[minmax(0,min-content)_1fr] gap-2 mb-4 md:mb-8">
              <EnvelopeIcon className="h-6 w-6 text-secondary" />
              <a href={`mailto:${accountant.email}`} className="text-link hover:text-link-hover hover:underline break-all">
                {accountant.email}
              </a>
            </div>
          )}
          {accountant.website && (
            <div className="grid grid-cols-[minmax(0,min-content)_1fr] gap-2 mb-4 md:mb-8">
              <GlobeAltIcon className="h-6 w-6 text-secondary" />
              <a
                href={accountant.website.startsWith('http') ? accountant.website : `https://${accountant.website}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-link hover:text-link-hover hover:underline break-all flex items-center gap-1"
              >
                {accountant.website}
                <ArrowTopRightOnSquareIcon className="h-3 w-3 text-secondary" />
              </a>
            </div>
          )}
        </div>
        {accountant.servicesList.length > 0 && (
          <div className="col-span-1 md:col-start-2">
            <AccountantList items={accountant.servicesList} label="Services" />
          </div>
        )}
        {accountant.languagesList.length > 0 && (
          <div className={`col-span-1 ${accountant.servicesList.length > 0 ? 'md:col-start-3' : 'md:col-start-2'}`}>
            <AccountantList items={accountant.languagesList} label="Languages Spoken" />
          </div>
        )}
      </div>
      {accountant.additionalInfo && (
        <div className="border-t border-grey-subtle pt-4 mt-4">
          <strong className="block text-primary font-din font-bold mb-2">Additional Info</strong>
          <p className="text-primary font-din" dangerouslySetInnerHTML={createMarkup(accountant.additionalInfo)} />
        </div>
      )}
    </div>
  )
}

AccountantList.propTypes = {
  items: PropTypes.arrayOf(PropTypes.string).isRequired,
  label: PropTypes.string.isRequired,
}

AccountantCard.propTypes = {
  accountant: PropTypes.shape({
    company: PropTypes.string,
    fullName: PropTypes.string,
    fullAddress: PropTypes.string,
    phone: PropTypes.string,
    email: PropTypes.string,
    website: PropTypes.string,
    servicesList: PropTypes.arrayOf(PropTypes.string),
    languagesList: PropTypes.arrayOf(PropTypes.string),
    additionalInfo: PropTypes.string,
  }).isRequired,
  isSelected: PropTypes.bool,
  onSelect: PropTypes.func.isRequired,
}

export default AccountantCard
