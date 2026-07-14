(() => {
    const { Extension, Mark, Node } = window.FilamentRichEditor.tiptap.core

    const styledNodeTypes = [
        'paragraph',
        'heading',
        'bulletList',
        'orderedList',
        'listItem',
        'blockquote',
        'codeBlock',
        'hardBreak',
        'horizontalRule',
        'table',
        'tableCell',
        'tableHeader',
        'tableRow',
        'image',
        'grid',
        'gridColumn',
        'details',
        'detailsSummary',
        'detailsContent',
        'lead',
        'small',
        'div',
    ]

    const styleAttribute = {
        default: null,
        parseHTML: (element) => element.getAttribute('style') || null,
        renderHTML: (attributes) => {
            if (!attributes.style) {
                return {}
            }

            return { style: attributes.style }
        },
    }

    const classAttribute = {
        default: null,
        parseHTML: (element) => element.getAttribute('class') || null,
        renderHTML: (attributes) => {
            if (!attributes.class) {
                return {}
            }

            return { class: attributes.class }
        },
    }

    const inlineStyleOnNodes = Extension.create({
        name: 'inlineStyleOnNodes',
        addGlobalAttributes() {
            return [
                {
                    types: styledNodeTypes,
                    attributes: {
                        style: styleAttribute,
                        class: classAttribute,
                    },
                },
            ]
        },
    })

    const linkStyleAttributes = Extension.create({
        name: 'linkStyleAttributes',
        addGlobalAttributes() {
            return [
                {
                    types: ['link'],
                    attributes: {
                        style: styleAttribute,
                        class: classAttribute,
                    },
                },
            ]
        },
    })

    const divNode = Node.create({
        name: 'div',
        group: 'block',
        content: 'block*',
        defining: false,
        parseHTML() {
            return [{ tag: 'div', priority: 60 }]
        },
        renderHTML({ HTMLAttributes }) {
            return ['div', HTMLAttributes, 0]
        },
    })

    const genericSpanMark = Mark.create({
        name: 'genericSpan',
        priority: 1000,
        parseHTML() {
            return [
                {
                    tag: 'span',
                    getAttrs: (element) => {
                        const style = element.getAttribute('style')
                        const className = element.getAttribute('class')

                        if (!style && !className) {
                            return false
                        }

                        return {
                            style: style || null,
                            class: className || null,
                        }
                    },
                },
            ]
        },
        renderHTML({ HTMLAttributes }) {
            return ['span', HTMLAttributes, 0]
        },
        addAttributes() {
            return {
                style: styleAttribute,
                class: classAttribute,
            }
        },
    })

    const toggleWarmPlumH4 = Extension.create({
        name: 'toggleWarmPlumH4',
        addCommands() {
            return {
                toggleWarmPlumH4:
                    () =>
                    ({ editor }) => {
                        if (editor.isActive('heading', { level: 4 })) {
                            return editor.chain().focus().toggleHeading({ level: 4 }).run()
                        }

                        if (!editor.chain().focus().toggleHeading({ level: 4 }).run()) {
                            return false
                        }

                        const { state } = editor
                        const { $from } = state.selection
                        const markType = state.schema.marks.genericSpan

                        if (!markType) {
                            return true
                        }

                        for (let depth = $from.depth; depth > 0; depth--) {
                            const node = $from.node(depth)

                            if (node.type.name !== 'heading' || node.attrs.level !== 4) {
                                continue
                            }

                            return editor
                                .chain()
                                .focus()
                                .setTextSelection({ from: $from.start(depth), to: $from.end(depth) })
                                .setMark('genericSpan', { class: 'text-warm-plum' })
                                .run()
                        }

                        return true
                    },
            }
        },
    })

    const inlineStylePlugin = Extension.create({
        name: 'inlineStylePlugin',
        addExtensions() {
            return [inlineStyleOnNodes, linkStyleAttributes, divNode, genericSpanMark, toggleWarmPlumH4]
        },
    })

    return inlineStylePlugin
})()
