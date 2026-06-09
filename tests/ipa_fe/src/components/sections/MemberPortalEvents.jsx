import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import TitleBlock from '../helpers/TitleBlock'
import { lightOrDark, transformPaddingToTailwind } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'


function MemberPortalEvents(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  const  userName = props.allData?.memberPortal?.name ? `, ${props.allData.memberPortal.name}` : ''

  return (
    <Section type="memberPortalEvents" outerClass={componentPadding} {...props} sectionTitle={false}>
      <div className={`mb-16 w-2/3 mx-auto text-center`}>
        <TitleBlock {...props} title={`<h1 class="banner text-center">Welcome${userName}</h1>`} lightOrDark={lightOrDarkValue}/>
      </div>
      <div className={`mx-6`}>
        {props.title && (
          <div className='text-highlight' dangerouslySetInnerHTML={createMarkup(props.title)} />
        )}
        <div className='w-full relative overflow-hidden'>
          <div className='flex flex-nowrap gap-8'>
            {props.events?.length && props.events?.map((memberEvent) => (
              <>
                {memberEvent?.functions?.length && memberEvent?.functions?.map((memberFunction) => {
                  return (
                    <div className='mb-4 basis-[40%] shrink-0' key={generateDataHash(memberFunction)}>
                      <div className='flex gap-4'>
                        {memberFunction?.category && (
                          <span className='category'>{memberFunction.category}</span>
                        )}
                        <span className='text-primary'>
                          {new Date(memberFunction.startDateTime).toLocaleDateString()}
                          {new Date(memberFunction.startDateTime).toLocaleDateString() !== new Date(memberFunction.endDateTime).toLocaleDateString() ? (<span dangerouslySetInnerHTML={createMarkup(`&nbsp;&mdash;&nbsp;${new Date(memberFunction.endDateTime).toLocaleDateString()}`)}></span>) : ''}
                        </span>
                      </div>
                      <h3 className="text-highlight text-event">{memberFunction.name}</h3>
                      {memberFunction.description && (
                        <p className='line-clamp-3 text-primary' dangerouslySetInnerHTML={createMarkup(memberFunction.description)} />
                      )}
                    </div>
                  )
                })
                }
              </>
            ))}
          </div>
        </div>
      </div>
    </Section>
  )
}

MemberPortalEvents.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  allData: PropTypes.object,
  title: PropTypes.string,
  events: PropTypes.array,
}

export default MemberPortalEvents
