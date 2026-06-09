import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { transformPaddingToTailwind } from '../../helpers/style'
import ButtonEl from '../helpers/ctas/Button'
import { ArrowRightIcon } from '@heroicons/react/24/solid'
import CardListBGImage from '../helpers/cards/curated/CardListBGImage'
import CardListIcon from '../helpers/cards/curated/CardListIcon'
import CardListNoImage from '../helpers/cards/curated/CardListNoImage'
import CardListHeroImage from '../helpers/cards/curated/CardListHeroImage'
import TitleBlock from '../helpers/TitleBlock'
import { generateDataHash } from '../../helpers/contentHash'
const colsClass = `col-span-2
md:[&:nth-child(3n+3)]:col-start-5
md:[&:nth-child(3n+2)]:col-start-3
md:[&:nth-child(3n+1)]:col-start-1
md:[&:last-child:first-child]:col-start-3
md:[&:last-child:nth-child(3n+1)]:col-start-3
md:[&:nth-last-child(2):first-child]:col-start-2
md:[&:nth-last-child(2):nth-child(3n+1)]:col-start-2
md:[&:nth-child(2):last-child]:col-start-4
md:[&:last-child:nth-child(3n+2)]:col-start-4`

function CardListCurated(props) {
  // Background Image => background
  // Hero Image => hero
  // Icon => icon
  const variant = props.cardVariant ? props.cardVariant.split(' ')[0].toLowerCase() : 'background'
  let cardItems = props.cardItems || []
  const [useTitle, setUseTitle] = useState(false)

  const initialNumber = props.initialNoOfCards || (variant == 'icon' ? 4 : 3)
  const [viewMore, setViewMore] = useState(cardItems.length <= initialNumber)
  const [textAlignment, setTextAlignment] = useState({
    flex: 'center',
    align: 'center',
  })
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  if (!viewMore) {
    cardItems = cardItems.slice(0, initialNumber)
  }

  useEffect(() => {
    setTextAlignment((prevState) => {
      const newState = { ...prevState }
      const alignment = props.contentAlignment?.toLowerCase() || 'center'
      if (newState.align !== props.contentAlignment?.toLowerCase()) {
        newState.flex = alignment === 'center' ? 'center' : 'around'
        newState.align = alignment === 'center' ? 'center' : 'left'
      }
      return newState
    })
  }, [])

  let ctaAlignmentClass = 'justify-start'
  if (props.cardLinkAlignment && props.cardLinkAlignment.toLowerCase() === 'center') {
    ctaAlignmentClass = `justify-center`
  }

  const generateTitle = () => {
    const heading = document.createElement('span')
    heading.className = 'max-md:text-display-xl text-display-2xl leading-tight tracking-[-.0253334em]'
    heading.innerHTML = props.sectionTitle
    return heading.outerHTML
  }
  useEffect(() => {
    // if we have either of the other two fields, we can use the title block we get from the <section> component.
    if (props.title) {
      if (props.description || props.tagline) {
        setUseTitle(true)
      }
    }
    setUseTitle(false)
  }, [props.title, props.description, props.tagline])

  const cardLinkAlignmentClass = props.cardLinkAlignment ? `text-${props.cardLinkAlignment.toLowerCase()}` : ''
  return (
    <Section
      type="cardListCurated"
      innerClass={`px-7 text-${textAlignment.align} ${componentPadding}`}
      headingClass="w-full"
      {...props}
      sectionTitle={useTitle}
      title={props.sectionTitle || props.title || ''}
    >
      <>
        {!useTitle && (
          <TitleBlock {...props} title={generateTitle()} contentAlignment="left" headingClass={`w-full text-secondary ${cardLinkAlignmentClass}`}/>
        )}
        {props.ctaLinkItem?.length ? (
          <div className={`flex flex-col sm:flex-row gap-6 ${ctaAlignmentClass}`}>
            {props.ctaLinkItem?.map((ctaLinkItem) => <ButtonEl key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} />)}
          </div>
        ) : undefined}
        {cardItems.length != 0 && (
          <div
            className={`grid grid-cols-1 md:grid-cols-6 ${props.title?.length || props.description?.length ? 'pt-10' : 'pt-0'}
          items-start gap-x-10 gap-y-12`}
          >
            {/* ${Math.min(cardItems.length, Math.max(cardItems.length, 3))} */}
            {cardItems.map((cardItem, i) => {
              switch (variant) {
                case 'background':
                  return <CardListBGImage key={generateDataHash(cardItem)} {...props} cardItem={cardItem} i={i} textAlignment={textAlignment} colsClass={colsClass} />
                case 'hero':
                  return <CardListHeroImage key={generateDataHash(cardItem)} {...props} cardItem={cardItem} colsClass={colsClass} />
                case 'icon':
                  return <CardListIcon key={generateDataHash(cardItem)} {...props} cardItem={cardItem} linkAlignment={props.cardLinkAlignment} colsClass={colsClass} />
                default:
                  return <CardListNoImage key={generateDataHash(cardItem)} {...props} cardItem={cardItem} colsClass={colsClass} cardBackgroundColour={props.cardBackgroundColour} contentAlignment={props.contentAlignment} />
              }
            })}
          </div>
        )}
        {!viewMore && (
          <div className="flex justify-center mt-20">
            <ButtonEl
              onClick={() => {
                setViewMore(true)
              }}
            >
              {props.viewMoreText || 'View more'}
              <ArrowRightIcon role="none" className="h-6 w-6 ml-2" />
            </ButtonEl>
          </div>
        )}
      </>
    </Section>
  )
}

CardListCurated.propTypes = {
  cardVariant: PropTypes.string,
  cardItems: PropTypes.array,
  initialNoOfCards: PropTypes.number,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  cardLinkAlignment: PropTypes.string,
  sectionTitle: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  ctaLinkItem: PropTypes.array,
  viewMoreText: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
}

export default CardListCurated
