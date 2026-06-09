import { addGradient } from './style'
import dompurify from 'dompurify'

const addExternalAria = (el) => {
  const links = el.querySelectorAll('a')
  Array.from(links).forEach((link) => {
    if (link.target === '_blank') {
      link.setAttribute('aria-label', 'link opens in new tab or window')
    }
  })
  return el[el.classList.contains('has-table') ? 'outerHTML' : 'innerHTML'].toString()
}

const addTableClasses = (el) => {
  el.classList.add(el.querySelectorAll('table').length > 0 ? 'has-table' : 'no-table')
  el.querySelectorAll('td').forEach(td => {
    if (td.childNodes.length == 1 && td.childNodes[0].tagName == 'SPAN') {
      td.childNodes[0].classList.add('block')
    }
  })
  return el
}

const createMarkup = (markup) => {
  if (!markup) {
    return
  }
  let el = document.createElement('div')
  el.innerHTML = dompurify.sanitize(markup, {ADD_TAGS: ['iframe']})
  el = addTableClasses(el)
  return { __html: addExternalAria(el) }
}

const createGradientElement = ({ type, fromColor, fullMobile = false, classes = '' }) => {
  if (!type || type.toLowerCase() === 'none' || !fromColor) {
    return
  }
  return (
    <div
      style={{ '--gradient-from': fromColor }}
      className={`cua--gradient z-0 ${addGradient(type, fullMobile)} ${classes}`}
      aria-hidden="true"
    />
  )
}

const stripTitleHTML = (title) => {
  // If the title of the content block is not overridden in the CMS, it defaults to using the title field of the component that has been added.
  // This can result in themed HTML coming from the RTE field. We need to strip the HTML tags to get the plain text.
  const div = document.createElement('div')
  div.innerHTML = title.__html
  return div.innerText
}

const detectHeadingInContent = (content) => {
  const div = document.createElement('div')
  div.innerHTML = content
  return div.querySelector('h1, h2, h3, h4, h5, h6') !== null
}

const getSentenceCase = (name) => {
  if (!name) {
    return ''
  }
  return name
    .toLowerCase()
    .replace(/^./, str => str.toUpperCase()) // Capitalize first letter
    .trim()
}

const getFriendlyName = (name) => {
  if (!name) {
    return ''
  }
  return name
    .replace(/([A-Z])/g, ' $1') // Add space before capital letters
    .replace(/^./, str => str.toUpperCase()) // Capitalize first letter
    .trim()
}

export { createMarkup, createGradientElement, stripTitleHTML, getFriendlyName, getSentenceCase, detectHeadingInContent }
