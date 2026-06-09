import { useState } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import TitleBlock from '../helpers/TitleBlock'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function CalloutCard(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')

  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  return (
    <Section
      type="calloutCard"
      outerClass={componentPadding}
      sectionTitle={false}
      innerClass="flex justify-center"
      {...props}
    >
      <div className="flex max-lg:flex-col justify-center items-center max-lg:text-center mx-6 gap-6 w-full">
        {(props.title || props.description || props.tagline) && (
          <TitleBlock headingClass={`w-full ${props.ctaLink.length != 0 ? 'max-w-md' : ''}`} {...props} lightOrDark={lightOrDarkValue}/>
        )}
        {props.ctaLink.length != 0 && (
          <div className="flex max-lg:flex-col items-center gap-6">
            {props.ctaLink.map((ctaLinkItem) => {
              let content = ctaLinkItem.link?.name
              const url = ctaLinkItem.link?.url
              return (
                <div key={generateDataHash(ctaLinkItem)} className={url?.startsWith('tel:') ? 'tel' : undefined}>
                  <ButtonEl item={ctaLinkItem}>
                    {content}
                  </ButtonEl>
                </div>
              )
            })}
          </div>
        )}
      </div>
    </Section>
  )
}

CalloutCard.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  ctaLink: PropTypes.array,
}

export default CalloutCard
