import { useState } from 'react'
import PropTypes from 'prop-types'
import { createMarkup } from '../../helpers/markup'
import { ExclamationTriangleIcon, XMarkIcon } from '@heroicons/react/24/solid'
import ButtonEl from '../helpers/ctas/Button'
import themeConfig from '../../../theme.config'

const createCookieValue = (str) => {
  let value = 'alertcookie'

  if (str.length) {
    const el = document.createElement('div')
    el.innerHTML = str
    // get the innertext and strip whitespaces which are not allowed in cookie values.
    // limit the string to 25 characters to not overload things.
    value = el.innerText.toLowerCase().replace(/\W/g, '').substring(0, 25)
  }
  return value
}

const Alert = (props) => {
  const [active, setActive] = useState(true)
  //set the cookie to something a bit static to allow for security policy checks to still work.
  const cookieID = `_cookie-${props.type?.toLowerCase()}`
  // generate a cookie value based on the title.
  // not using a title on the alert will result in a more generic value being set, which may cause some issues with detection.
  const cookieValueStr = createCookieValue(props.title)

  const date = new Date()
  const numWeeks = 1
  date.setDate(date.getDate() + numWeeks * 7)

  let type = props.type?.toLowerCase() || 'warning'
  let hasSetCookie = false

  hasSetCookie = document.cookie
    .split(';')
    .some((item) => item.trim().startsWith(cookieID) && item.includes(cookieValueStr))

  const alertColors = themeConfig.settings.alerts[type] ?? themeConfig.settings.alerts['warning']
  const classes = 'top-0 left-0 w-full'

  const handleClick = () => {
    setActive((prevState) => {
      const newState = !prevState
      if (newState === false) {
        document.cookie = cookieID + '=' + cookieValueStr + '; expires=' + date.toUTCString() + '; path=/;'
      }
      return newState
    })
  }

  return (
    <>
      {hasSetCookie !== true && active && (
        <div className={classes} style={{ backgroundColor: alertColors.bg, color: alertColors.text }} role="alert">
          <div className="container lg:w-3/5 mx-auto px-4 py-4 flex justify-start items-start gap-6">
            <ExclamationTriangleIcon className="w-6 h-6 flex-shrink text-current" role="none" />
            <div className={`flex-grow  `}>
              {props.title && (
                <p className="font-semibold mb-4" data-rte="true" dangerouslySetInnerHTML={createMarkup(props.title)} />
              )}
              {props.description && (
                <p className="text-normal" data-rte="true" dangerouslySetInnerHTML={createMarkup(props.description)} />
              )}
            </div>
            <ButtonEl outerClass="flex-shrink text-current" onClick={handleClick} theme="none">
              <XMarkIcon className="w-6 h-6 flex-shrink text-current" />
              <span className="sr-only">Close this alert</span>
            </ButtonEl>
          </div>
        </div>
      )}
    </>
  )
}

Alert.propTypes = {
  type: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
}

export default Alert
