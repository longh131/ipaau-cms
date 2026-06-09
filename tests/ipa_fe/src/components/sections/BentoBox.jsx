import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { transformPaddingToTailwind } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import Picture from '../helpers/Picture'
import { Arrow } from '../helpers/Icons'

const plainTitle = (title, hasImage = false) => {
  return <h3 className={`label-xl ${hasImage ? 'text-white' : 'text-primary'} mb-0`} dangerouslySetInnerHTML={createMarkup(title)} />
}

const linkTitle = (title, url, target, hasImage = false) => {
  return (
    <h3 className={`label-xl ${hasImage ? 'text-white' : 'text-primary'} mb-0`}>
      <a
        href={url}
        target={target}
        className="flex items-center justify-between w-full gap-2 group/bentoBoxLink "
        onClick={(event) => {
          dataLayerPush(
            {
              event: 'bento_click',
              click_text: card.link.linkText || card.title,
              destination_path: card.link.link?.url,
            },
            event.target,
          )
        }}
      >
        <span
          className="line-clamp-3 group-hover/bentoBoxLink:underline"
          dangerouslySetInnerHTML={createMarkup(title)}
        />
        <span className={`shrink-0 [&_path]:stroke-white [&_path]:text-white group-hover/bentoBoxLink:translate-x-2 transition-transform duration-300 ${hasImage ? '[&_path]:stroke-white [&_path]:text-white' : '[&_path]:stroke-primary [&_path]:text-primary'}`}>
          <Arrow />
        </span>
      </a>
    </h3>
  )
}

function BentoBox(props) {
  const [cardWrapper, setCardWrapper] = useState(null)
  const [cardsPerBlock, setCardsPerBlock] = useState(4)
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const cardContent = (card, index) => {
    const hasImage = card.backgroundImage
    const hasContent = card.title || card.link
    const cardKey = card.globalIndex !== undefined ? card.globalIndex : index

    return (
      <div
        key={cardKey}
        className={`relative overflow-hidden h-full w-full rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 ${
          hasImage ? 'min-h-[200px]' : 'min-h-[150px]'
        }`}
        style={{
          backgroundColor: hasImage ? 'transparent' : '#f8fafc',
        }}
      >
        {/* Background Image */}
        {hasImage && (
          <div className="absolute inset-0">
            <Picture
              desktopImage={card.backgroundImage}
              mobileImage={card.backgroundImage}
              className="w-full h-full object-cover"
            />
            {/* Dark overlay for text readability */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
          </div>
        )}

        {/* Content */}
        {hasContent && (
          <div className={`relative z-10 h-full flex flex-col break-all ${hasImage ? 'justify-end' : ' justify-center'} p-6`}>
            {/* if there is no link, we just render the h3 with no link */}
            {card.title && !card.link && plainTitle(card.title, hasImage)}
            {/* if there is a link and no title, we render the h3 with a link using the link title */}
            {card.link?.link?.url && !card.title &&
              linkTitle(card.link.link?.name, card.link.link?.url, card.link.link?.target, hasImage)}
            {/* if there is a title and a link, we render the h3 with a link using the title */}
            {card.title && card.link?.link?.url &&
              linkTitle(card.title, card.link.link?.url, card.link.link?.target, hasImage)}
          </div>
        )}
      </div>
    )
  }

  const tallCardWrapper = (cards) => (
    <div className="mt-10 grid gap-4 sm:mt-16 lg:grid-cols-3 lg:grid-rows-2">
      {cards[0] && <div className="relative lg:row-span-2 max-lg:aspect-[469/365]">{cardContent(cards[0], 0)}</div>}
      {cards[1] && <div className="relative max-lg:row-start-2 aspect-[469/365]">{cardContent(cards[1], 1)}</div>}
      {cards[2] && (
        <div className="relative max-lg:row-start-3 lg:col-start-2 lg:row-start-2 aspect-[469/365]">
          {cardContent(cards[2], 2)}
        </div>
      )}
      {cards[3] && <div className="relative lg:row-span-2 max-lg:aspect-[469/365]">{cardContent(cards[3], 3)}</div>}
    </div>
  )

  const wideCardWrapper = (cards) => (
    <div className="mt-10 grid grid-cols-1 gap-4 sm:mt-16 lg:grid-cols-6 lg:grid-rows-2">
      {cards[0] && <div className="flex p-px lg:col-span-4 max-lg:aspect-[469/365]">{cardContent(cards[0], 0)}</div>}
      {cards[1] && <div className="flex p-px lg:col-span-2 aspect-[469/365]">{cardContent(cards[1], 1)}</div>}
      {cards[2] && <div className="flex p-px lg:col-span-2 aspect-[469/365]">{cardContent(cards[2], 2)}</div>}
      {cards[3] && <div className="flex p-px lg:col-span-4 max-lg:aspect-[469/365]">{cardContent(cards[3], 3)}</div>}
    </div>
  )

  const fiveCardWrapper = (cards) => (
    <div className="mt-10 grid grid-cols-1 gap-4 sm:mt-16 lg:grid-cols-6 lg:grid-rows-2">
      {cards[0] && <div className="relative lg:col-span-3 max-lg:aspect-[469/365]">{cardContent(cards[0], 0)}</div>}
      {cards[1] && <div className="relative lg:col-span-3 max-lg:aspect-[469/365]">{cardContent(cards[1], 1)}</div>}
      {cards[2] && <div className="relative lg:col-span-2 aspect-[469/365]">{cardContent(cards[2], 2)}</div>}
      {cards[3] && <div className="relative lg:col-span-2 aspect-[469/365]">{cardContent(cards[3], 3)}</div>}
      {cards[4] && <div className="relative lg:col-span-2 aspect-[469/365]">{cardContent(cards[4], 4)}</div>}
    </div>
  )

  useEffect(() => {
    if (props.style === 'Tall cards') {
      setCardWrapper('tall')
    } else if (props.style === 'Wide cards') {
      setCardWrapper('wide')
    } else if (props.style === '5 cards') {
      setCardWrapper('five')
      setCardsPerBlock(5)
    }
  }, [props.style])

  return (
    <Section type="bentoBox" outerClass={componentPadding} {...props}>
      <div className="container mx-auto px-4">
        {/* Bento Box Grid */}
        {props.cards &&
          props.cards.length > 0 &&
          (() => {
            const cardGroups = []
            for (let i = 0; i < props.cards.length; i += cardsPerBlock) {
              cardGroups.push(props.cards.slice(i, i + cardsPerBlock))
            }

            return cardGroups.map((group, groupIndex) => {
              const key = `bento-block-${groupIndex}`
              const cardsWithGlobalIndex = group.map((card, localIndex) => ({
                ...card,
                globalIndex: groupIndex * cardsPerBlock + localIndex,
              }))

              if (cardWrapper === 'tall') {
                return <div key={key}>{tallCardWrapper(cardsWithGlobalIndex)}</div>
              } else if (cardWrapper === 'wide') {
                return <div key={key}>{wideCardWrapper(cardsWithGlobalIndex)}</div>
              } else if (cardWrapper === 'five') {
                return <div key={key}>{fiveCardWrapper(cardsWithGlobalIndex)}</div>
              }
              return null
            })
          })()}
      </div>
    </Section>
  )
}

BentoBox.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  style: PropTypes.string,
  cards: PropTypes.array,
}

export default BentoBox
