import React from 'react'
import ReactDOMServer from 'react-dom/server'
import { HelmetProvider } from 'react-helmet-async'
import App from './App'

export function render() {
  const helmetContext = {}

  const html = ReactDOMServer.renderToString(
    <React.StrictMode>
      <HelmetProvider context={helmetContext}>
        <App />
      </HelmetProvider>
    </React.StrictMode>
  )

  const { helmet } = helmetContext

  // Extract all Helmet tags for SSR
  const head = helmet
    ? `${helmet.title?.toString() || ''}
       ${helmet.priority?.toString() || ''}
       ${helmet.meta?.toString() || ''}
       ${helmet.link?.toString() || ''}
       ${helmet.script?.toString() || ''}`
    : ''

  return {
    html,
    head
  }
}
