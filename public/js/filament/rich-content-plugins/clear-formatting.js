export default () => {
    const { Extension } = window.FilamentRichEditor.tiptap.core

    const blocksToUnwrap = new Set(['blockquote', 'codeBlock', 'div'])

    return Extension.create({
        name: 'clearFormatting',
        addCommands() {
            return {
                clearFormatting:
                    () =>
                    ({ state, dispatch }) => {
                        const { from, to, empty } = state.selection
                        let rangeFrom = from
                        let rangeTo = to

                        if (empty) {
                            const { $from } = state.selection

                            rangeFrom = $from.start($from.depth)
                            rangeTo = $from.end($from.depth)
                        }

                        if (rangeFrom >= rangeTo) {
                            return false
                        }

                        const tr = state.tr
                        const { schema } = state
                        const linkMark = schema.marks.link
                        const linkSegments = []
                        const blockOps = []

                        state.doc.nodesBetween(rangeFrom, rangeTo, (node, pos) => {
                            if (node.isText) {
                                const start = Math.max(pos, rangeFrom)
                                const end = Math.min(pos + node.nodeSize, rangeTo)

                                if (start >= end) {
                                    return
                                }

                                if (linkMark) {
                                    const link = node.marks.find(
                                        (mark) => mark.type === linkMark,
                                    )

                                    if (link?.attrs?.href) {
                                        linkSegments.push({
                                            from: start,
                                            to: end,
                                            href: link.attrs.href,
                                            target: link.attrs.target || null,
                                            rel: link.attrs.rel || null,
                                        })
                                    }
                                }

                                Object.values(schema.marks).forEach((markType) => {
                                    tr.removeMark(start, end, markType)
                                })

                                return
                            }

                            if (!node.isBlock) {
                                return
                            }

                            if (node.type.name === 'image') {
                                const { id, src, alt, title, width, height, float } =
                                    node.attrs

                                blockOps.push({
                                    kind: 'image',
                                    pos,
                                    attrs: {
                                        id: id ?? null,
                                        src: src ?? null,
                                        alt: alt ?? null,
                                        title: title ?? null,
                                        width: width ?? null,
                                        height: height ?? null,
                                        float: float ?? null,
                                        style: null,
                                        class: float ? `cms-img-float-${float}` : null,
                                    },
                                })

                                return false
                            }

                            if (node.type.name === 'heading') {
                                blockOps.push({ kind: 'paragraph', pos })

                                return false
                            }

                            if (blocksToUnwrap.has(node.type.name)) {
                                blockOps.push({
                                    kind: 'unwrap',
                                    pos,
                                    nodeSize: node.nodeSize,
                                    content: node.content,
                                })

                                return false
                            }

                            if (
                                node.attrs?.style ||
                                node.attrs?.class ||
                                node.attrs?.textAlign
                            ) {
                                blockOps.push({
                                    kind: 'clearBlockAttrs',
                                    pos,
                                    type: node.type,
                                })
                            }
                        })

                        if (linkMark) {
                            linkSegments.forEach(
                                ({ from: start, to: end, href, target, rel }) => {
                                    tr.addMark(
                                        start,
                                        end,
                                        linkMark.create({ href, target, rel }),
                                    )
                                },
                            )
                        }

                        blockOps
                            .sort((left, right) => right.pos - left.pos)
                            .forEach((operation) => {
                                if (operation.kind === 'paragraph') {
                                    tr.setNodeMarkup(
                                        operation.pos,
                                        schema.nodes.paragraph,
                                        {},
                                    )

                                    return
                                }

                                if (operation.kind === 'unwrap') {
                                    tr.replaceWith(
                                        operation.pos,
                                        operation.pos + operation.nodeSize,
                                        operation.content,
                                    )

                                    return
                                }

                                if (operation.kind === 'clearBlockAttrs') {
                                    tr.setNodeMarkup(
                                        operation.pos,
                                        operation.type,
                                        {},
                                    )

                                    return
                                }

                                if (operation.kind === 'image') {
                                    tr.setNodeMarkup(
                                        operation.pos,
                                        schema.nodes.image,
                                        operation.attrs,
                                    )
                                }
                            })

                        if (!tr.docChanged) {
                            return false
                        }

                        if (dispatch) {
                            dispatch(tr)
                        }

                        return true
                    },
            }
        },
    })
}
