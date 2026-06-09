import PropTypes from 'prop-types'
import { useState, useEffect } from 'react'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import Picture from '../helpers/Picture'
import TitleBlock from '../helpers/TitleBlock'
import ButtonEl from '../helpers/ctas/Button'
import { generateDataHash } from '../../helpers/contentHash'
import { ChevronRightIcon } from '@heroicons/react/24/solid'

function FeatureBlock(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  if (!props.teamMembers || props.teamMembers.length === 0) {
    return null
  }

  return (
    <Section
      type="featureBlock"
      outerClass={componentPadding}
      sectionTitle={false}
      {...props}
    >
      <div className="container">
        {/* Header Section */}
        {(props.title || props.description || props.tagline) && (
          <TitleBlock headingClass="mb-16" {...props} lightOrDark={lightOrDarkValue}/>
        )}

        {/* Team Members Grid */}
        <div className={`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 text-center ${props.ctaLink ? 'mb-12' : ''}`}>
          {props.teamMembers.map((member) => (
            <div key={generateDataHash(member)}>
              {/* Profile Picture */}
              {member.profilePicture && (
                <div className="mb-6">
                  <div className="w-[99px] aspect-square mx-auto rounded-full overflow-hidden bg-gray-200">
                    <Picture
                      desktopImage={member.profilePicture}
                      mobileImage={member.profilePicture}
                      className="w-full h-full object-cover"
                    />
                  </div>
                </div>
              )}

              {/* Member Name */}
              {member.memberName && (
                <div
                  className={`label-xl text-secondary ${member.memberTitle ? 'mb-4' : ''}`}
                  dangerouslySetInnerHTML={createMarkup(member.memberName)}
                />
              )}

              {/* Member Title */}
              {member.memberTitle && (
                <p
                  className="text-xl"
                  dangerouslySetInnerHTML={createMarkup(member.memberTitle)}
                />
              )}
              {member.link && (
                <div>
                  <ButtonEl
                    theme="text"
                    link={member.link}
                    className="max-md:w-full"
                  >
                    {member.link.name}
                    <ChevronRightIcon className="w-5 h-5 ml-1" />
                  </ButtonEl>
                </div>
              )}
            </div>
          ))}
        </div>

        {/* Call-to-Action Button */}
        {props.ctaLink && (
          <div className="text-center">
            <ButtonEl
              link={props.ctaLink.link}
              className="max-md:w-full"
            />
          </div>
        )}
      </div>
    </Section>
  )
}

FeatureBlock.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  teamMembers: PropTypes.arrayOf(
    PropTypes.shape({
      profilePicture: PropTypes.object,
      memberName: PropTypes.string,
      memberTitle: PropTypes.string,
    })
  ),
  ctaLink: PropTypes.shape({
    link: PropTypes.object,
  }),
}

export default FeatureBlock
