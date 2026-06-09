import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { dataLayerPush } from '../../helpers/thirdparty'
import ButtonEl from '../helpers/ctas/Button'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import TitleBlock from '../helpers/TitleBlock'
import moment from 'moment'
import { generateDataHash } from '../../helpers/contentHash'

function EventHeader(props) {
  const [, setStartDate] = useState(null)
  const [, setEndDate] = useState(null)
  const [formattedDate, setFormattedDate] = useState('')

  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])


  let ctaButtons = null
  if (props.ctaLinks?.length > 0) {
    ctaButtons = (
      <div className="component-cta flex flex-col shrink-0 sm:flex-row gap-4">
        {props.ctaLinks.map((button) => (
          <ButtonEl
            key={generateDataHash(button)}
            link={button.link}
            theme="outline"
            className="px-8 py-3 text-base font-medium border-2 border-primary text-primary bg-white hover:bg-primary hover:text-white transition-colors duration-200 uppercase tracking-wide"
          >
            {button.link.name || 'FIND OUT MORE'}
          </ButtonEl>
        ))}
      </div>
    )
  }

  const generateTitle = () => {
    const heading = document.createElement('span')
    heading.className = 'max-md:text-display-2xl text-display-3xl leading-tight tracking-[-.0253334em]'
    heading.innerHTML = props.title
    return heading.outerHTML
  }

  useEffect(() => {
    const date = props.keyInformation?.date
    if (!date) {
      setStartDate(null)
      setEndDate(null)
      setFormattedDate('')
      return
    }

    const splitDate = date.split(' - ')
    const start = moment(splitDate[0].trim())
    const end = splitDate[1] ? moment(splitDate[1].trim()) : null

    setStartDate(start)
    setEndDate(end)

    let formatted = ''

    if (end?.isValid()) {
      const startYear = start.year()
      const endYear = end.year()
      const startMonth = start.month()
      const endMonth = end.month()

      if (startYear !== endYear) {
        // Different years: 'xth monthnameA yyyy - yth monthnameB yyyy'
        formatted = `${start.format('Do MMMM YYYY')} - ${end.format('Do MMMM YYYY')}`
      } else if (startMonth !== endMonth) {
        // Different months, same year: 'xth monthnameA - yth monthnameB yyyy'
        formatted = `${start.format('Do MMMM')} - ${end.format('Do MMMM YYYY')}`
      } else {
        // Same month and year: 'xth - yth monthname yyyy'
        formatted = `${start.format('Do')} - ${end.format('Do MMMM YYYY')}`
      }
    } else {
      // Single date: 'xth monthname yyyy'
      formatted = start.format('Do MMMM YYYY')
    }

    setFormattedDate(formatted)
  }, [props.keyInformation?.date])

  // TODO make this available to switch to en-GB on the IFA site
  const formatter = new Intl.NumberFormat('en-AU', {
    style: 'currency',
    // TODO make this available to switch to GBP on the IFA site
    currency: 'AUD',

    trailingZeroDisplay: 'stripIfInteger',
  })

  const formatAmount = (amount) => {
    return formatter.format(parseInt(Math.floor(amount.replace('$', ''))))
  }

  const generateFees = () => {
    const fees = props.keyInformation?.fees
    if (!fees) {
      return null
    }
    const feeParts = fees.split(' | ')
    const feesArray = []
    feeParts.forEach((fee) => {
      const feePart = fee.split(': ')
      feesArray.push({
        type: feePart[0],
        amount: feePart[1],
      })
    })
    return feesArray.map((fee) => (
      <span className="block" key={fee.type}>
        {fee.type}: from {formatAmount(fee.amount)}
      </span>
    ))
  }

  const userIsRegistered = props.isUserRegistered === true

  const cityStateLine = [
    props.keyInformation?.city,
    [props.keyInformation?.state, props.keyInformation?.postalCode].filter(Boolean).join(' ')
  ].filter(Boolean).join(' ')

  return (
    <Section
      type="eventHeader"
      outerClass={componentPadding}
      sectionTitle={false}
      {...props}
    >
        <div className="grid grid-cols-1 lg:grid-cols-[minmax(0,auto)_minmax(0,max-content)] grid-rows-[repeat(4,minmax(0,max-content))] gap-y-4 lg:gap-y-6 lg:gap-x-20">
          <div className="grid grid-rows-subgrid col-start-1 row-start-1 row-span-4">
            {/* Important: we need to pass in the space in the string as the tagline if no event info label is provided as the entire header block is built on a nested grid, and we don't have control over the items in the titleblock component */}
            <TitleBlock
              {...props}
              tagline={props.eventCategoryName || 'Event'}
              lightOrDark={lightOrDarkValue}
              title={generateTitle()}
              headingClass="col-start-1 row-start-1 row-span-3 grid grid-rows-subgrid col-end-1"
              level="1"
            />
            <div className="content-section content-section-main col-start-1 row-start-4 row-span-1 max-lg:empty:hidden">
              {ctaButtons}
            </div>
          </div>

          {/* Key Information Section */}
          {props.keyInformation && (
            <div className="content-section content-section-key-info mt-8 bg-[oklch(from_white_l_c_h_/_0.75)] backdrop-blur-sm rounded-2xl p-8 w-full lg:w-96 shrink-0 lg:col-start-2 lg:row-start-3 lg:row-span-2">
              <h2 className="text-label-md md:text-label-xl font-semibold font-din text-secondary uppercase mb-6">
                KEY INFORMATION
              </h2>
              <div className="grid grid-cols-[minmax(0,max-content)_1fr] gap-2 md:gap-4 text-primary font-din text-md md:text-xl">
                {formattedDate && (
                  <>
                    <span className="font-bold">Date:</span>
                    <span className="">{formattedDate}</span>
                  </>
                )}
                {props.keyInformation.location && (
                  <>
                    <span className="font-bold">Location:</span>
                    <span>
                      {props.keyInformation.address1 && <span className="block">{props.keyInformation.address1}</span>}
                      {props.keyInformation.address2 && <span className="block">{props.keyInformation.address2}</span>}
                      {props.keyInformation.address3 && <span className="block">{props.keyInformation.address3}</span>}
                      {cityStateLine && (
                        <span className="block">{cityStateLine}</span>
                      )}
                      {props.keyInformation.country && props.keyInformation.country !== props.keyInformation.homeCountry && (
                        <span className="block">{props.keyInformation.country}</span>
                      )}
                    </span>
                  </>
                )}
                {props.keyInformation.time && (
                  <>
                    <span className="font-bold">Time:</span>
                    <span className="">{props.keyInformation.time}</span>
                  </>
                )}
                {props.keyInformation.cpdHours && (
                  <>
                    <span className="font-bold">CPD:</span>
                    <span className="">{`${Math.floor(props.keyInformation.cpdHours)} ${Math.floor(props.keyInformation.cpdHours) === 1 ? 'Hour' : 'Hours'}`}</span>
                  </>
                )}
                {props.keyInformation.fees && (
                  <>
                    <span className="font-bold">Fees:</span>
                    <span className="">{generateFees()}</span>
                  </>
                )}

                {/* Registration Status */}
                {props.registrationUrl && (
                  <div className="col-span-2 pt-4">
                    <ButtonEl
                      link={ userIsRegistered ? null : {
                        url: props.registrationUrl || '/',
                        target: '_blank',
                        rel: 'noopener noreferrer',
                        label: 'Register Now',
                      }}
                      dataLayer={false}
                      theme="primary"
                      className="w-full"
                      onClick={event => {
                        dataLayerPush({
                          event: 'registration_intent',
                          event_date: props.keyInformation.date,
                          event_location: props.keyInformation.location,
                          event_time: props.keyInformation.time,
                          fee: props.keyInformation.fees,
                          component_name: 'Event key information',
                          event_title: props.title
                        })
                      }}
                      disabled={userIsRegistered}
                    >
                      {userIsRegistered ? 'Registered' : 'Register Now'}
                    </ButtonEl>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>
    </Section>
  )
}

EventHeader.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  title: PropTypes.string,
  keyInformation: PropTypes.object,
  ctaLinks: PropTypes.array,
  registrationUrl: PropTypes.string,
  isUserRegistered: PropTypes.bool,
  eventCategoryName: PropTypes.string,
}

export default EventHeader
