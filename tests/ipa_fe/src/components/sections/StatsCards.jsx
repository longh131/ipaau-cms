import React from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { transformPaddingToTailwind } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function StatsCards(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  return (
    <Section
      type="statsCards"
      outerClass={componentPadding}
      {...props}
    >
      <div className="container mx-auto px-4">

        {/* Stats Cards Grid */}
        {props.statsCards && props.statsCards.length > 0 && (
          <div className={`grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-10 ${(props.title || props.description) ? 'mt-8' : ''}`}>
            {props.statsCards.map((card, index) => {
              let statisticValue = card.statisticValue
              if (statisticValue.endsWith('+')) {
                statisticValue = <>
                  {statisticValue.substr(0, statisticValue.length-1)}
                  <sup className="top-[-0.42em]">+</sup>
                </>
              }
              return <div
                key={generateDataHash(card)}
                className={`relative bg-white rounded-3xl py-6 lg:px-10 lg:py-24 text-center border-2 border-transparent shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-center ${index > 1 ? 'col-span-2 lg:col-span-1 px-5' : 'px-4'}`}
                style={{
                  background: 'linear-gradient(white, white) padding-box, linear-gradient(to right, #c93c9f, #f05f22) border-box',
                }}
              >
                {/* Statistic Value */}
                {card.statisticValue && (
                  <div className={`text-6xl xl:text-[130px] font-bold font-apex-book text-secondary leading-[1.2] tracking-[-.02em] break-words ${card.description ? 'xl:-mb-2' : ''}`}>
                    {statisticValue}
                  </div>
                )}

                {/* Description */}
                {card.description && (
                  <div
                    className="text-base lg:text-[20px] text-warm-plum leading-[1.3] tracking-[.04em] font-medium uppercase break-words"
                    dangerouslySetInnerHTML={{ __html: card.description }}
                  />
                )}
              </div>
            })}
          </div>
        )}
      </div>
    </Section>
  )
}

StatsCards.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  title: PropTypes.string,
  description: PropTypes.string,
  statsCards: PropTypes.arrayOf(
    PropTypes.shape({
      statisticValue: PropTypes.string,
      description: PropTypes.string,
    })
  ),
}

export default StatsCards
