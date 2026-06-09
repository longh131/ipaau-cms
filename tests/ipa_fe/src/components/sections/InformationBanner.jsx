import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import Image from '../helpers/Image'
import TitleBlock from '../helpers/TitleBlock'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function InformationBanner(props) {
  const [textAlignment, setTextAlignment] = useState({
    flex: 'center',
    align: 'center',
  })
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  useEffect(() => {
    setTextAlignment((prevState) => {
      const newState = { ...prevState }
      const alignment = props.contentAlignment?.toLowerCase() || 'center'
      if (newState.align !== props.contentAlignment?.toLowerCase()) {
        newState.flex = alignment === 'center' ? 'center' : 'start'
        newState.align = alignment === 'center' ? 'center' : 'left'
      }
      return newState
    })
  }, [])

  return (
    <Section
      type="informationBanner"
      outerClass={componentPadding}
      sectionTitle={false}
      innerClass={`px-7 text-${textAlignment.align}`}
      {...props}
      mobileBackgroundImage={props.mobileImage}
      desktopBackgroundImage={props.desktopImage}
    >
      <Image {...props.image} className="mx-auto mb-7" />
      {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue}/>}

      {props.ctaLink.length != 0 && (
        <div className={`flex justify-${textAlignment.flex}  space-x-6 mt-10`}>
          {props.ctaLink.map((ctaLinkItem) => <ButtonEl key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} />)}
        </div>
      )}
    </Section>
  )
}

InformationBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
}
