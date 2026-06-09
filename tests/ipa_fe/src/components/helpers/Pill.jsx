import PropTypes from 'prop-types'
import { LockClosedIcon } from "@heroicons/react/24/outline"

const Pill = (props) => {
  const theme = props.theme || 'primary'
  let themeClass = 'px-3 py-1 rounded-full uppercase font-din text-xs'
  switch (theme) {
    case 'primary':
      themeClass = `${themeClass} bg-grey-subtle text-primary`
      break
    case 'secondary':
      themeClass = `${themeClass} bg-secondary text-white`
      break
    case 'members':
      themeClass = `${themeClass} bg-secondary flex flex-nowrap gap-2 items-center justify-start text-white`
      break
  }

  let decorator = null

  if (theme === 'members') {
    decorator = <LockClosedIcon className="h-3 w-3" />
  }


  return (
    <div className={themeClass}>
      {decorator}
      {props.children}
    </div>
  )
}

Pill.propTypes = {
  theme: PropTypes.string,
  children: PropTypes.node,
}

export default Pill
