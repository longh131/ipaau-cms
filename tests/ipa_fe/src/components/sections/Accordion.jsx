import { useState } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { PlusIcon, MinusIcon } from '@heroicons/react/24/outline'
import { transformPaddingToTailwind } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import themeConfig from '../../../theme.config'
import { generateDataHash } from '../../helpers/contentHash'

function Accordion(props) {
  const [open, setOpen] = useState({})
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const alignment = props.contentAlignment?.toLowerCase() || 'center'

  return (
    <Section
      type="accordion"
      outerClass={`${componentPadding} ${alignment == 'center' ? 'centered' : ''}`}
      headingClass={`text-${alignment}`}
      innerClass="px-7"
      {...props}
    >
      {props.accordionPanelItems?.length > 0 && (
        <ul
          className={`border-b border-grey-subtle ${(props.title || props.description || props.tagline) ? 'mt-16' : ''}`}
        >
          {props.accordionPanelItems.map((item, i) => (
            <li key={`accordion-${generateDataHash(item.title)}`} className="border-t border-grey-subtle">
              <button
                className={`flex justify-between items-center text-left w-full pt-6 ${open[i] ? 'pb-2' : 'pb-6'}`}
                onClick={event => {
                  setOpen((previousOpen) => {
                    dataLayerPush({
                      event: 'accordion_interaction',
                      action: !previousOpen[i] ? 'open' : 'close',
                      click_text: item.title
                    }, event.target)
                    if (themeConfig.settings.onlyOneAccordionOpen) {
                      const newState = { ...previousOpen }
                      newState[i] = !newState[i]
                      for (let x in newState) {
                        if (Number(x) !== Number(i)) {
                          newState[x] = false
                        }
                      }
                      return newState
                    } else {
                      return {
                        ...previousOpen,
                        [i]: !previousOpen[i],
                      }
                    }
                  })
                }}
              >
                <span className="text-secondary text-xl font-medium">{item.title}</span>
                <div className="ml-2 circle rounded-xl  border-2 border-secondary text-warm-plum">
                {open[i] ? (
                  <>
                    <MinusIcon className="w-4 h-4 shrink-0 " role="none" />
                    <span className="sr-only">Close Accordion</span>
                  </>
                ) : (
                  <>
                    <PlusIcon role="none" className="w-4 h-4 shrink-0" />
                    <span className="sr-only">Open Accordion</span>
                  </>
                )}
                </div>
              </button>
              {
                <div
                  data-rte="true"
                  dangerouslySetInnerHTML={createMarkup(item.content)}
                  className={`mb-6 ${open[i] ? '' : 'hidden'}`}
                />
              }
            </li>
          ))}
        </ul>
      )}
    </Section>
  )
}

Accordion.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  accordionPanelItems: PropTypes.arrayOf(
    PropTypes.shape({
      title: PropTypes.string,
      content: PropTypes.string,
    })
  ),
}

export default Accordion
