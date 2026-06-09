import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import TitleBlock from '../helpers/TitleBlock'
import ButtonEl from '../helpers/ctas/Button'
import { dataLayerPush } from '../../helpers/thirdparty'

function RegistrationBanner(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  const userIsRegistered = props.isUserRegistered === true

  return (
    <Section
      type="registrationBanner"
      outerClass={componentPadding}
      innerClass={`flex gap-6 items-center justify-center max-lg:text-center ${componentPadding} ${props.contentAlignment?.toLowerCase() === 'left' ? 'lg:!flex-row lg:justify-between' : 'lg:!flex-col justify-center'}`}
      {...props}
      sectionTitle={false}
    >
      {props.title && (
        <TitleBlock headingClass="basis-full lg:basis-[60%] max-lg:text-center " {...props} lightOrDark={lightOrDarkValue} title={props.title} />
      )}
      {/* Registration Button */}
      {props.registrationUrl && (
        <div className="flex gap-4 w-full flex-wrap justify-center basis-full lg:basis-[40%]">
          <ButtonEl
            link={ userIsRegistered ? null : {
              url: props.registrationUrl || '/',
              target: '_blank',
              rel: 'noopener noreferrer',
              label: 'Register Now',
            }}
            dataLayer={false}
            theme="primary"
            className="w-full md:w-auto"
            disabled={userIsRegistered}
            onClick={event => {
              const target = event.target
              dataLayerPush({
                event: 'feature_banner_click',
                click_text: target.innerText,
                banner_text: target.closest('section')?.querySelector('[data-type="section-title"]')?.innerText
              }, target)
            }}
          >
            {userIsRegistered ? 'Registered' : 'Register Now'}
          </ButtonEl>
        </div>
      )}
    </Section>
  )
}

RegistrationBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  registrationUrl: PropTypes.string,
  isUserRegistered: PropTypes.bool,
}

export default RegistrationBanner
