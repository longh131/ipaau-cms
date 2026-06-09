import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import VideoModal from '../../../sections/_VideoModal'
import { createMarkup, createGradientElement } from '../../../../helpers/markup'
import { lightOrDark, addBgImageClass } from '../../../../helpers/style'
import { Arrow } from '../../Icons'
import ButtonEl from '../../ctas/Button'
import PlayButtonDecorator from '../PlayButtonDecorator'
import { generateDataHash } from '../../../../helpers/contentHash'

const CardListBGImage = (props) => {
  const { cardItem, i } = props
  const [modalIsOpen, setModalIsOpen] = useState(false)
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [gradientColor, setGradientColor] = useState('#fff')

  useEffect(() => {
    const bgColor = props.cardBackgroundColour?.replace('#', '') ?? ''
    const fgColor = props.imageGradientColour?.replace('#', '') ?? ''
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setGradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
    // Only set colors on initial mount to prevent SSR hydration mismatches
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  function openModal() {
    setModalIsOpen(true)
  }

  function closeModal() {
    setModalIsOpen(false)
  }

  let link
  let links = []
  if (!cardItem.videoUrl && cardItem.ctaLinkItem?.length == 1 && cardItem.ctaLinkItem?.[0]?.link?.url && !cardItem.ctaLinkItem?.[0]?.link?.name) {
    link = cardItem.ctaLinkItem[0].link
  } else {
    links = cardItem.ctaLinkItem
  }

  const inner = () => {
    let textAlignmentClass = cardItem.titleAlignment?.toLowerCase() === 'center' ? 'self-center' : 'self-start'
    let cardLinkAlignmentClass = props.cardLinkAlignment?.toLowerCase() === 'center' ? 'items-center' : 'items-start'
  return <>
    {cardItem.videoUrl ? (
      <div className="flex grow items-center justify-center min-h-32">
        <PlayButtonDecorator isButton={true} onClick={openModal} />
      </div>
    ) : null}
    <div className="flex flex-col">
      {cardItem.title && !cardItem.useSmallTitle && (
        <h2
          className={`mb-0 text-secondary font-medium font-din text-2xl leading-[1.4] tracking-[.04em] ${textAlignmentClass} uppercase break-all line-clamp-3`}
        >
          {cardItem.title}
          {link?.url && <Arrow />}
        </h2>
      )}
      {cardItem.title && cardItem.useSmallTitle && (
        <h3
          className={`mb-4 font-bold text-lg ${textAlignmentClass} line-clamp-3`}
        >
          {cardItem.title}
        </h3>
      )}
      {cardItem.description && (
        <div className={`mt-2 line-clamp-2 ${textAlignmentClass}`} data-rte="true" dangerouslySetInnerHTML={createMarkup(cardItem.description)} />
      )}
      {links?.length ? (
        <div
          className={`flex flex-col ${cardLinkAlignmentClass} gap-6 mt-10`}
        >
          {links.map((ctaLinkItem, i) => <ButtonEl key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} theme="none" className="!p-0"/>)}
        </div>
      ) : undefined}
    </div>
  </>
  }

  const inlineStyles = () => {
    let mobileImageUrl = null
    let desktopImageUrl = null
    if (cardItem.mobileImage) {
      mobileImageUrl = `url(${cardItem.mobileImage.src})`
    }
    if (cardItem.desktopImage) {
      desktopImageUrl = `url(${cardItem.desktopImage.src})`
    }
    return {
      '--mobile-bg-url': mobileImageUrl,
      '--desktop-bg-url': desktopImageUrl,
      '--bg-color': backgroundColor,
      '--light-or-dark': lightOrDark(backgroundColor),
    }
  }

  return (
    <div
      key={generateDataHash(cardItem)}
      style={inlineStyles()}
      className={`relative w-full rounded-lg ${props.colsClass} ${(cardItem.desktopImage || cardItem.mobileImage) ? '' : 'border border-[#C6C6C6]'}
        bg-[color:var(--bg-color)] text-${lightOrDark(backgroundColor)} h-full overflow-hidden ${addBgImageClass(cardItem.desktopImage?.src, cardItem.mobileImage?.src)}`}
    >
      {props.imageGradientColour &&
        createGradientElement({
          type: props.imageGradientType,
          fromColor: gradientColor,
        })}
      {link?.url ? <a
        href={link.url}
        target={link.target}
        className="relative p-8 min-h-96 h-full mt-auto flex flex-col justify-end hover:bg-grey-subtle"
      >
        {inner()}
      </a> : <div className={`relative p-8 min-h-96 h-full mt-auto flex flex-col ${cardItem.videoUrl ? 'justify-between' : 'justify-end'}`}>
        {inner()}
      </div>}
      {cardItem.videoUrl ? (
        <VideoModal
          i={i}
          url={cardItem.videoUrl}
          open={modalIsOpen}
          onClose={closeModal}
        />
      ) : null}
    </div>
  )
}

CardListBGImage.propTypes = {
  cardItem: PropTypes.object.isRequired,
  i: PropTypes.number,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  imageGradientType: PropTypes.string,
  cardLinkAlignment: PropTypes.string,
  colsClass: PropTypes.string,
}

export default CardListBGImage
