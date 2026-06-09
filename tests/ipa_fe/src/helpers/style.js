import themeConfig from '../../theme.config'

const getRGB = (color) => {
  color = color ?? '#ffffff'

  let r, g, b

  if (color.match(/^rgb/)) {
    // If RGB --> store the red, green, blue values in separate variables
    color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/)

    r = color[1]
    g = color[2]
    b = color[3]
  } else {
    // If hex --> Convert it to RGB: http://gist.github.com/983661
    color = +('0x' + color.slice(1).replace(color.length < 5 && /./g, '$&$&'))

    r = color >> 16
    g = (color >> 8) & 255
    b = color & 255
  }
  return { r, g, b }
}

const lightOrDark = (color) => {
  // Variables for red, green, blue values
  let hsp,
    type = 'dark'

  if (!color || color === 'transparent') return 'light'

  const { r, g, b } = getRGB(color)
  // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
  hsp = Math.sqrt(0.299 * (r * r) + 0.587 * (g * g) + 0.114 * (b * b))

  // Using the HSP value, determine whether the color is light or dark
  hsp > 127.5 && (type = 'light')
  return type
}

const addBgImageClass = (desktop = undefined, mobile = undefined) => {
  let bgClass = ``

  // set the mobile background if it's available
  mobile && (bgClass += `bg-[image:var(--mobile-bg-url)] `)

  // if only a desktop background is set use the desktop for all breakpoints
  desktop && !mobile && (bgClass += `bg-[image:var(--desktop-bg-url)] `)

  // if both desktop and mobile are set only use the desktop image for mobile up
  desktop && mobile && (bgClass += `sm:bg-[image:var(--desktop-bg-url)] `)

  bgClass.length && (bgClass += `bg-center bg-cover bg-no-repeat`)

  return bgClass
}

const addGradient = (direction = 'left', fullMobile = false) => {
  let dir = 'to-r'
  const toStr = `to-[color:var(--gradient-from)]`
  let to = '' //this is used for non-gradient gradients (full-screen wash)

  // if we are sending the gradient to the full panel on mobile we just add the colour here
  // this will turn to transparent above tablet breakpoint.
  fullMobile && (to = `${toStr} md:to-transparent`)

  switch (direction.toLowerCase()) {
    case 'top':
      dir = 'to-b'
      break
    case 'right':
      dir = `to-l`
      break
    case 'bottom':
      dir = 'to-t'
      break
    case 'left':
      dir = `to-r`
      break
    case 'center':
      return `max-md:bg-[color:var(--gradient-from)] md:bg-gradient-to-r from-transparent via-[color:var(--gradient-from)] to-transparent`
    case 'full':
    case 'text area':
      dir = 'to-l'
      to = toStr
      break
  }

  return `bg-gradient-${dir} from-[color:var(--gradient-from)] ${to}`
}

const transformPaddingToTailwind = (value, hero = false, breadcrumbs = false) => {
  // we have a list of hard-coded padding values available to the user in the Component definition.
  // we need to map that numeric value to tailwind classes so it can be acted on correctly
  // if breadcrumbs is true, we add we add 3rem to the top padding
  const type = hero ? 'hero' : 'standard'
  const padding = {
    hero: {
      0: {
        top: 'pt-0',
        bottom: 'pb-0',
        mid: '',
        card: '',
      },
      24: {
        top: 'pt-6',
        bottom: 'pb-6',
        mid: 'pb-12',
        card: '-mt-6',
      },
      32: {
        top: 'pt-8',
        bottom: 'pb-8',
        mid: 'pb-16',
        card: '-mt-8',
      },
      48: {
        top: 'pt-12',
        bottom: 'pb-12',
        mid: 'pb-24',
        card: '-mt-12',
      },
      64: {
        top: 'pt-16',
        bottom: 'pb-16',
        mid: 'pb-32',
        card: '-mt-16',
      },
      96: {
        top: 'pt-24',
        bottom: 'pb-24',
        mid: 'pb-48',
        card: '-mt-24',
      },
      128: {
        top: 'pt-32',
        bottom: 'pb-32',
        mid: 'pb-64',
        card: '-mt-32',
      },
    },
    standard: {
      0: 'py-0',
      24: 'py-6',
      32: 'py-8',
      48: 'py-12',
      64: 'py-16',
      96: 'py-24',
      128: 'py-32',
    },
    // article: {
    //   0: 'py-0',
    //   24: 'py-6',
    //   32: 'py-8',
    //   48: 'py-12',
    //   64: 'py-16',
    //   96: 'py-24',
    //   128: 'py-32',
    // },
  }

  if (breadcrumbs) {
    // the cognitive complexity check for the combined version was too high, so we're dealing with breadcrumbs separately.
    padding.hero[0].top = 'pt-12'
    padding.hero[24].top = 'pt-18'
    padding.hero[32].top = 'pt-20'
    padding.hero[48].top = 'pt-24'
    padding.hero[64].top = 'pt-28'
    padding.hero[96].top = 'pt-32'
    padding.hero[128].top = 'pt-44'
    padding.standard = {
      0: 'pt-6 md:pt-12 pb-0',
      24: 'pt-12 md:pt-18 pb-6',
      32: 'pt-14 md:pt-20 pb-8',
      48: 'pt-16 md:pt-24 pb-12',
      64: 'pt-24 md:pt-28 pb-16',
      96: 'pt-28 md:pt-32 pb-24',
      128: 'pt-36 md:pt-44 pb-32',
    }
  }
  // if value is undefined, or not set in the component definition, we use the central value of 48px, or py-12
  return value && padding[type][value.toString()] ? padding[type][value.toString()] : padding[type]['48']
}

const showExternalLinkIcons = () => {
  document.body.classList.add(themeConfig.settings.showExternalLinkIcons ? 'show-external-icons' : 'no-external-icons')
}

export {
  lightOrDark,
  addGradient,
  addBgImageClass,
  getRGB,
  transformPaddingToTailwind,
  showExternalLinkIcons,
}
