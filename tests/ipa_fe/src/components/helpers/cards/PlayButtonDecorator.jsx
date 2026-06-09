import PropTypes from 'prop-types'
import { PlayIcon } from '@heroicons/react/24/outline'
import { PlayIcon as PlayIconSolid } from '@heroicons/react/24/solid'

const sharedIconStyles = 'absolute scale-100 top-0 left-0 w-full text-link group-hover/video:text-link-hover transition-all duration-300 group-hover/video:scale-110'

const sharedButtonStyles = 'relative md:max-lg:h-12 md:max-lg:w-12 md:max-lg:!py-2 md:max-lg:!px-0 h-20 w-20 mx-auto my-auto max-md:!p-0 lg:!p-0 group/video'

const PlayButtonIcon = () => <>
  <PlayIcon role="none" className={`${sharedIconStyles} opacity-100 group-hover/video:opacity-0`} />
  <PlayIconSolid role="none" className={`${sharedIconStyles} opacity-0 group-hover/video:opacity-100`} />
  <span className="sr-only">Play video</span>
</>


const PlayButtonDecorator = ({ className = '', isButton = false, onClick = null }) => {
  return <>
    {isButton ? (
      <button className={`${sharedButtonStyles} ${className}`} onClick={onClick} aria-label="Play video">
        <PlayButtonIcon />
      </button>
    ) : (
      <div className={`${sharedButtonStyles} ${className}`}>
        <PlayButtonIcon />
      </div>
    )}
  </>
}

PlayButtonDecorator.propTypes = {
  className: PropTypes.string,
  isButton: PropTypes.bool,
  onClick: PropTypes.func,
}

export default PlayButtonDecorator
