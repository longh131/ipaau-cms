import themeConfig from '../../theme.config'

const runTest = (selector) => {
  const elements = document.querySelectorAll(`${selector}:not([data-processed="true"])`)
  return elements
}

const loadProviderScript = (party, elements) => {
  const { script } = party
  const existingScript = document.getElementById(script.id)

  if (!existingScript) {
    const scr = document.createElement('script')
    scr.src = script.src
    scr.id = script.id

    // Check if location is set to 'head', otherwise append to body
    if (script.location === 'head') {
      document.head.appendChild(scr)
    } else {
      document.body.appendChild(scr)
    }

    scr.onload = () => {
      if (script.callback) script.callback()
      processElements(party, elements)
    }
} else {
    processElements(party, elements)
    if (script.callback) script.callback()
  }
}

const processElements = (party, elements) => {
  // set a processed flag so we only run each element once.
  elements.forEach((el) => {
    el.dataset.processed = 'true'
    const attributes = {}
    const elements = {}

    party.test.attributes.forEach((attr) => {
      attributes[attr] = el.getAttribute(attr)
    })

    party.test.elements.forEach((elem) => {
      elements[elem] = el.querySelectorAll(elem)
    })

    if (Object.keys(attributes).length) {
      party.callback.attributes(attributes)
    }
    if (Object.keys(elements).length) {
      party.callback.elements(elements)
    }
  })
}

const runThirdParty = () => {
  const thirdParties = themeConfig.thirdParty
  const thirdPartyKeys = Object.keys(themeConfig.thirdParty)

  if (!thirdPartyKeys?.length) return

  thirdPartyKeys.forEach((thirdParty) => {
    // get all the elements on the page that match the selector
    const party = thirdParties[thirdParty]
    const elements = runTest(party.test.selector)

    if (elements?.length) {
      // given that we have a target, we load the related script onto the page.
      elements.forEach((el) => {
        loadProviderScript(party, elements)
      })
    }
  })
}

const dataLayerPush = (data, target) => {
  if (target) {
    const sectionTitle = target.closest('section')?.querySelector('[data-type="section-title"]')
    if (sectionTitle) {
      const div = document.createElement('div')
      div.innerHTML = sectionTitle.innerHTML
      div.querySelector('.text-warm-plum')?.remove()
      data.page_section = div.innerText.trim()
    }
  }

  const prefix = window.location.protocol+'//'+window.location.hostname
  if (data?.destination_path?.startsWith(prefix+'/')) {
    data.destination_path = data.destination_path.substring(prefix.length)
  }
  console.log(data)

  window.dataLayer = window.dataLayer || []
  window.dataLayer.push(data)
}

export { runThirdParty, dataLayerPush }
