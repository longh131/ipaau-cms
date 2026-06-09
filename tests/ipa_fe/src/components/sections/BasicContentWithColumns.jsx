import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import TitleBlock from '../helpers/TitleBlock'
import { useEffect, useState } from 'react'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function BasicContentWithColumns(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const [columnClass, setColumnClass] = useState('grid-cols-1')
  const [alignmentClass, setAlignmentClass] = useState('')
  const [, setBottomDecorator] = useState('')
  const [borderWidthClass, setBorderWidthClass] = useState('')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')

  useEffect(() => {
    const decorationStyle = props.decoration?.toLowerCase() || 'none'
    const decorationWidth = parseInt(props.decorationWidth?.replace('px', '')) || 0

    let borderWidth = ''
    if (decorationWidth > 0) {
      switch (decorationStyle) {
        case 'horizontal':
          borderWidth = decorationWidth === 2 ? '[&>div]:border-t-2 ' : '[&>div]:border-t-4 '
          break
        case 'vertical':
          borderWidth = decorationWidth === 2 ? '[&>div]:border-l-2 ' : '[&>div]:border-l-4 '
          break
        case 'both':
          borderWidth = decorationWidth === 2 ? '[&>div]:border-t-2 [&>div]:border-l-2' : '[&>div]:border-t-4 [&>div]:border-l-4'
          break
      }
    }

    if (props.columnSpacing === '100') {
      // if the editor selects to have a divider, but does not select columns, reset everything.
      borderWidth = ''
    }

    setBorderWidthClass(borderWidth)

    // always turn off the borders for the first item
    borderWidth = `${borderWidth} [&>div:first-child]:border-l-0 [&>div:first-child]:border-t-0`

  }, [props.decorationWidth, props.columnSpacing])

  const twoColsFirstColPaddingHorizontal = ' [&>*:nth-child(2n-3)]:lg:pr-5'
  const twoColsSecondColPaddingHorizontal = ' [&>*:nth-child(2n-2)]:lg:pl-5'
  const twoColsPaddingVertical = '[&>*:nth-child(n+3)]:pt-5'

  const threeColsFirstColPaddingHorizontal = ' [&>*:nth-child(2n-3)]:lg:pr-5'
  const threeColsSecondColPaddingHorizontal = ' [&>*:nth-child(2n-2)]:lg:px-5'
  const threeColsThirdColPaddingHorizontal = ' [&>*:nth-child(2n-1)]:lg:pl-5'
  const threeColsPaddingVertical = '[&>*:nth-child(n+4)]:pt-5'

  // four columns drop down to two columns under lg breakpoint, so we need to add the padding to the first and second columns.
  const fourColsFirstColPaddingHorizontal = ' [&>div]:max-md:pr-0 [&>div]:max-md:pl-0  [&>*:nth-child(2n-3)]:md:max-lg:pr-5 [&>*:nth-child(2n-3)]:lg:pr-5'
  const fourColsSecondColPaddingHorizontal = ' [&>*:nth-child(2n-2)]:md:max-lg:pl-5 [&>*:nth-child(2n-2)]:lg:px-5'
  const fourColsThirdColPaddingHorizontal = ' [&>*:nth-child(2n-1)]:lg:pl-5'
  const fourColsPaddingVertical = 'xl:[&>*:nth-child(n+5)]:pt-5'

  useEffect(() => {
    const decorationStyle = props.decoration?.toLowerCase() || 'none'
    if (decorationStyle === 'horizontal' || decorationStyle === 'both') {
      // if the horizontal borders need to be set they are always set for all items in single-stacked mode.
      setBottomDecorator(` [&>div]:max-lg:pt-5`)
    }

    let cols = ''
    // define grid classes for the columns
    switch (props.columnSpacing) {
      case '100':
        cols = 'grid-cols-1' // with both decorators active the 100% width never needs the left border
        break
      case '50-50':
        cols = `grid-cols-1 lg:grid-cols-2 `
        break
      case '33-33-33':
        cols = `grid-cols-1 lg:grid-cols-3 `
        break
      case '25-25-25-25':
        cols = `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
        break
      case '60-40':
        cols = `grid-cols-1 lg:grid-cols-[6fr,_4fr]`
        break
      case '40-60':
        cols = `grid-cols-1 lg:grid-cols-[4fr,_6fr]`
        break
      case '80-20':
        cols = `grid-cols-1 lg:grid-cols-[6fr,_4fr] xl:grid-cols-[8fr,_2fr]`
        break
      case '20-80':
        cols = `grid-cols-1 lg:grid-cols-[4fr,_6fr] xl:grid-cols-[2fr,_8fr]`
        break
      case '25-50-25':
        cols = `grid-cols-1 lg:grid-cols-[1fr,_2fr,_1fr] `
        break
    }

    let columnBorderClasses = ''
    // define border classes for the columns
    switch (props.columnSpacing) {
      // we default to having the border-width and colours defined for all items. This will turn them off when they are not required.
      case '50-50':
      case '60-40':
      case '40-60':
      case '80-20':
      case '20-80':
        columnBorderClasses += ' [&>div:first-child]:border-l-0 [&>div:first-child]:border-t-0'
        // turn off the left border in non-stacked mode for items 1, 3, 5, 7, etc
        // turn off the left border in stacked mode for the all items
        columnBorderClasses += ' [&>div]:max-lg:border-l-0 [&>*:nth-child(3n+3)]:lg:!border-l-0'
        // turn off the top row of borders in non-stacked mode
        columnBorderClasses += ' [&>div:nth-child(2)]:xl:border-t-0 '
        break
      case '33-33-33':
      case '25-50-25':
        columnBorderClasses += ' [&>div:first-child]:border-l-0 [&>div:first-child]:border-t-0 [&>div:nth-child(-n+3)]:lg:border-t-0'
        // turn off the top border in non-stacked mode for the second and third items
        // turn off the left border in non-stacked mode for items 1, 4, 7, 10, etc
        columnBorderClasses += ' [&>div]:max-lg:border-l-0 [&>*:nth-child(4n+4)]:lg:!border-l-0 '
        // turn off the top row of borders in non-stacked mode
        columnBorderClasses += ' [&>*:nth-child(2)]:lg:!border-t-0 [&>*:nth-child(3)]:lg:!border-t-0'
        break
      case '25-25-25-25':
        // turn off the top border for all items in non-stacked mode, the top two columns in tablet mode, and the first item in mobile mode.
        columnBorderClasses = ' [&>div:first-child]:border-t-0 [&>div:first-child]:border-l-0 [&>div:nth-child(-n+2)]:md:border-t-0 [&>div:nth-child(-n+4)]:lg:border-t-0'
        // turn off the top border in non-stacked mode for the second, third and fourth items
        // turn off the left border in non-stacked mode for items 1, 5, 9, 13, etc
        columnBorderClasses += ' [&>div]:max-md:border-l-0 [&>div:nth-child(2n-3)]:max-lg:border-l-0'
        // turn off the top row of borders in non-stacked mode
        columnBorderClasses += ' [&>*:nth-child(2)]:lg:!border-t-0 [&>*:nth-child(3)]:lg:!border-t-0 [&>*:nth-child(4)]:lg:!border-t-0'
        break
    }

    cols += columnBorderClasses

    let columnPaddingClasses = ''
    switch (props.columnSpacing) {
      // define padding classes for the columns
      case '50-50':
      case '60-40':
      case '40-60':
      case '80-20':
      case '20-80':
        columnPaddingClasses += `${twoColsFirstColPaddingHorizontal} ${twoColsSecondColPaddingHorizontal} ${twoColsPaddingVertical}`
        break
      case '33-33-33':
      case '25-50-25':
        columnPaddingClasses += `${threeColsFirstColPaddingHorizontal} ${threeColsSecondColPaddingHorizontal} ${threeColsThirdColPaddingHorizontal} ${threeColsPaddingVertical}`
        break
      case '25-25-25-25':
        columnPaddingClasses += `${fourColsFirstColPaddingHorizontal} ${fourColsSecondColPaddingHorizontal} ${fourColsThirdColPaddingHorizontal} ${fourColsPaddingVertical}`
        break
    }
    cols += columnPaddingClasses
    setColumnClass(cols)

    let alignment = ''
    switch (props.columnContentAlignment) {
      case 'Top':
        alignment = 'justify-start'
        break
      case 'Middle':
        alignment = 'justify-center'
        break
      case 'Bottom':
        alignment = 'justify-end'
    }
    setAlignmentClass(alignment)
    setLightOrDarkValue(lightOrDark(props.backgroundColor))

  }, [])

  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  return (
    <Section type="basicContentWithColumns" outerClass={componentPadding} innerClass="px-7" {...props}>
      {props.contentColumnItems?.length > 0 && (
        // <div className={`column-wrapper grid *:px-6 *:my-6   ${decorationClass} ${columnClass} ${alignmentClass}`}>
        <div
          className={`column-wrapper grid items-stretch text-${alignment} ${columnClass} ${borderWidthClass} [&>div]:border-[color:var(--ipa-border-color)]`}
          data-bw={borderWidthClass}
          style={{
            '--ipa-border-width': props.decorationWidth ?? '0',
            '--ipa-border-color': props.decorationColour || 'transparent',
            '--ipa-border-style': props.decorationStyle?.toLowerCase() ?? 'solid',
          }}
        >
          {props.contentColumnItems?.map((col) => {
            const contentAlignment = props.contentAlignment === 'Left' ? 'justify-start' : 'justify-center'
            return (
              <div
                key={generateDataHash(col.title)}
                className={`max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 ${alignmentClass} border-solid `}
              >
                <TitleBlock
                  tagline={col.tagline}
                  title={col.title}
                  description={col.description}
                  contentAlignment={props.contentAlignment}
                  lightOrDark={lightOrDarkValue}
                />
                {col.ctaLinkItem?.length > 0 && (
                  <div className={`basis-${props.basis ? props.basis : 'auto'} mt-12 flex flex-col sm:flex-row ${contentAlignment} flex-wrap gap-6 mt-12 mb-6`}>
                  {col.ctaLinkItem.map((ctaLinkItem) => (
                    <ButtonEl className="max-sm:w-full" key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} />
                  ))}
                  </div>
                )}
              </div>
            )
          })}
        </div>
      )}
    </Section>
  )
}

BasicContentWithColumns.propTypes = {
  componentPadding: PropTypes.string,
  decorationColour: PropTypes.string,
  decorationStyle: PropTypes.string,
  breadcrumbs: PropTypes.any,
  decoration: PropTypes.string,
  decorationWidth: PropTypes.string,
  columnSpacing: PropTypes.string,
  contentColumnItems: PropTypes.array,
  contentColumnAlignment: PropTypes.string,
  backgroundColor: PropTypes.string,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  basis: PropTypes.string,
}

export default BasicContentWithColumns
