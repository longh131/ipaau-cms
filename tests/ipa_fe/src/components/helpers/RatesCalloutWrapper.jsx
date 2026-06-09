import PropTypes from 'prop-types'
import RatesCalloutBlock from '../helpers/RatesCalloutBlock'
import TitleBlock from './TitleBlock'
import { createMarkup } from '../../helpers/markup'
import { CheckIcon } from '@heroicons/react/24/solid'
import { generateDataHash } from '../../helpers/contentHash'

const RatesCalloutWrapper = (props) => {
  return (
    <div
      className={`rounded-3xl ${!props.embedded ? 'border-2 border-gray' : 'shadow-2xl w-full'} p-2 grid lg:grid-cols-10 gap-2 ${props.className}`}
    >
      <div className="px-8 py-6 lg:col-start-1 lg:col-end-6 xl:col-end-7">
        {(props.title || props.description || props.tagline) && <TitleBlock {...props} />}

        {props.subtitle && (
          <div
            className={`${props.title && 'mt-10 font-bold text-secondary '} ${themeConfig.settings.useReadableContentWidth ? 'max-w-prose' : ''}`}
            data-rte="true"
            dangerouslySetInnerHTML={createMarkup(props.subtitle)}
          />
        )}

        {props.products?.length > 0 && props.products[0]?.uniqueSellingPoints?.length ? (
          <ul className="grid lg:grid-cols-2 gap-x-6 gap-y-4 mt-10">
            {props.products[0]?.uniqueSellingPoints.map((item) => (
              <li key={generateDataHash(item)} className="flex">
                <CheckIcon className="h-6 w-6 mr-4 text-secondary flex-shrink-0" />
                {item}
              </li>
            ))}
          </ul>
        ) : null}
      </div>
      <div
        className={`p-10 lg:col-start-7 lg:col-end-11 lg:py-16 bg-gray rounded-2xl border-2 border-gray text-center`}
      >
        {props.products?.length ? (
          <div className="my-6 @container/callout max-w-[24rem] mx-auto flex justify-around gap-12 max-sm:flex-col max-sm:gap-4">
            <RatesCalloutBlock
              {...props}
              showCtaLinks={props.showCtaLinks}
              embedded={props.embedded}
              rate={props.products[0]}
              idx={0}
            />
          </div>
        ) : null}
        {props.ctaLinkDescription && <div className="mt-6 text-xs">{props.ctaLinkDescription}</div>}
      </div>
    </div>
  )
}

RatesCalloutWrapper.propTypes = {
  embedded: PropTypes.bool,
  className: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  subtitle: PropTypes.string,
  products: PropTypes.array,
  showCtaLinks: PropTypes.bool,
  ctaLinkDescription: PropTypes.string,
}

export default RatesCalloutWrapper
