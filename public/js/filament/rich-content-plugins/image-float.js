export default () => {
    const { Extension } = window.FilamentRichEditor.tiptap.core

    const parseFloatAttribute = (element) => {
        const dataFloat = element.getAttribute('data-float')

        if (dataFloat === 'left' || dataFloat === 'right') {
            return dataFloat
        }

        if (element.classList.contains('cms-img-float-left')) {
            return 'left'
        }

        if (element.classList.contains('cms-img-float-right')) {
            return 'right'
        }

        return null
    }

    const renderFloatAttribute = (float) => {
        if (float !== 'left' && float !== 'right') {
            return {}
        }

        const margin = float === 'left' ? '0 1rem 1rem 0' : '0 0 1rem 1rem'

        return {
            'data-float': float,
            class: `cms-img-float-${float}`,
            style: `float: ${float}; margin: ${margin}; max-width: min(50%, 100%);`,
        }
    }

    return Extension.create({
        name: 'imageFloat',
        addGlobalAttributes() {
            return [
                {
                    types: ['image'],
                    attributes: {
                        float: {
                            default: null,
                            parseHTML: (element) => parseFloatAttribute(element),
                            renderHTML: (attributes) => renderFloatAttribute(attributes.float),
                        },
                    },
                },
            ]
        },
    })
}
