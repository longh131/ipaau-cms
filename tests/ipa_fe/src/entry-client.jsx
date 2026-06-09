import './index.scss'
import React from 'react'
import { createRoot, hydrateRoot } from 'react-dom/client'
import { HelmetProvider } from 'react-helmet-async'
import App from './App'

const container = document.getElementById('root')
if (!container) {
  throw new Error('Root element #root not found')
}

// Match entry-server.jsx: StrictMode > HelmetProvider > App. A mismatched tree breaks
// hydration; failed hydration can leave the app without a proper client mount, so
// useEffect in App never runs. In dev, render fresh with createRoot instead of
// hydrating so local development does not depend on a perfect SSR match.
const app = (
  <React.StrictMode>
    <HelmetProvider>
      <App />
    </HelmetProvider>
  </React.StrictMode>
)

if (import.meta.env.DEV) {
  createRoot(container).render(app)
} else {
  hydrateRoot(container, app)
}
