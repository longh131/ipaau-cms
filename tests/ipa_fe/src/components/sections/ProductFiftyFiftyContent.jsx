import PropTypes from 'prop-types'
import Section from './_Section'
import { useState, useEffect } from 'react'
import { createMarkup } from '../../helpers/markup'
import ButtonEl from '../helpers/ctas/Button'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import TitleBlock from '../helpers/TitleBlock'
import { generateDataHash } from '../../helpers/contentHash'

const ProductFiftyFiftyContent = (props) => {
  const [, setHighlightColor] = useState('secondary')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setHighlightColor((prevState) => {
      if (props.backgroundColor === prevState) {
        return 'primary'
      }
      return prevState
    })
  }, [])
  useState(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  return (
    <Section type="product5050" innerClass={`max-w-none ${componentPadding.top}`} sectionTitle={false} {...props}>
      {(props.title || props.description || props.tagline) && (
        <div className={`container mx-auto ${componentPadding.mid} `}>
          <TitleBlock headingClass={`w-full `} {...props} lightOrDark={lightOrDarkValue}/>
        </div>
      )}
      <div
        className={`${componentPadding.card} ${componentPadding.bottom} grid lg:grid-cols-2 grid-flow-row-dense grid-rows-[repeat(10,_auto)] lg:max-w-[90%] xl:max-w-[75%] 2xl:max-w-[60%] mx-auto  justify-center before:block before:absolute before:bottom-[-1px] before:left-0 before:w-[100vw] content-[''] before:h-[25%] before:z-[-1] before:bg-white`}
      >
        {props.contentBlockItems.map((item) => {
          return (
            <div className="grid border-2 bg-white text-primary text-shadow-off rounded-3xl max-w-[65ch] shadow-2xl grid-rows-subgrid row-span-5 mx-8 p-8 mb-6 mt-0 " key={generateDataHash(item)}>
              {item.title && <div className={`font-bold text-lg text-center`}>{item.title}</div>}

              {item.description && (
                <div
                  className={`text-sm mt-2`}
                  data-rte="true"
                  dangerouslySetInnerHTML={createMarkup(item.description)}
                ></div>
              )}

              {item.ctaLink?.length > 0 && (
                <ButtonEl
                  item={item.ctaLink[0]}
                  className="!justify-center mt-6"
                />
              )}

              {item.product?.variants?.length > 0 && (
                <div
                  className={`${item.ctaLink?.length > 0 || (item.description && item.ctaLink?.length < 1) ? 'mt-8' : ''}`}
                >
                </div>
              )}
            </div>
          )
        })}
      </div>
    </Section>
  )
}

ProductFiftyFiftyContent.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  contentBlockItems: PropTypes.array,
}

export default ProductFiftyFiftyContent
