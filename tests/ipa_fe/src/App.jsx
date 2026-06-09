import { useState, useEffect } from 'react'
import { Helmet, HelmetProvider } from 'react-helmet-async'
import './App.scss'
import Page from './components/Page'
import { ParallaxProvider } from 'react-scroll-parallax'
import { generateSchemas } from './helpers/schemas'

async function getComponents() {
  try {
    const response = await fetch('/data.json')
    if (!response.ok) {
      console.error(`Failed to load /data.json: ${response.status} ${response.statusText}`)
      return { error: true }
    }
    return await response.json()
  } catch (error) {
    console.error(error)
    return { error: true }
  }
}

const detectType = () => {
  const navigator = window.navigator
  const mobileOS = /iPad|iPhone|Android/.exec(navigator.userAgent) !== null
  if (mobileOS) {
    document.body.classList.add('isMobileOS')
  }
}

function App() {
  const [compData, setCompData] = useState()
  const [schemas, setSchemas] = useState(null)

  useEffect(() => {
    const loadData = async () => {
      // Only skip the local JSON fetch when CMS/Umbraco has injected a payload with
      // `result` (see Views/Page.cshtml). A bare `{}` or other truthy value must not
      // block loading public/data.json in standalone dev.
      if (typeof window !== 'undefined' && window?.pageData?.result) {
        setCompData(window.pageData)
      } else {
        console.log('Getting components from local file data.json')
        const response = await getComponents()
        if (response && !response.error) {
          setCompData(response)
        }
      }
    }
    loadData()
    detectType()
  }, [])

  useEffect(() => {
    if (process.env.NODE_ENV === 'development') {
      console.log('Page Data', compData)
    }
  }, [compData])

  useEffect(() => {
    if (compData?.result) {
      try {
        const jsonLdSchemas = generateSchemas(compData.result)
        setSchemas(jsonLdSchemas)
      } catch (error) {
        console.error('Schema generation failed:', error)
        setSchemas(null)
      }
    }
  }, [compData])

  return (
    <>
      {compData?.result && !compData.error && (
        <HelmetProvider>
          <Helmet>
            {compData.result.pageDescription && compData.result.pageDescription.length > 0 && (
              <meta name="description" content={compData.result.pageDescription} />
            )}
            {compData.result.pageKeywords && compData.result.pageKeywords.length > 0 && (
              <meta name="keywords" content={compData.result.pageKeywords} />
            )}
            {compData.result.canonicalUrl && compData.result.canonicalUrl.length > 0 && (
              <link rel="canonical" href={compData.result.canonicalUrl} />
            )}
            {compData.result.ogTitle && compData.result.ogTitle.length > 0 && (
              <meta content={compData.result.ogTitle} property="og:title" />
            )}
            {compData.result.ogDescription && compData.result.ogDescription.length > 0 && (
              <meta content={compData.result.ogDescription} property="og:description" />
            )}
            {compData.result.ogKeywords && compData.result.ogKeywords.length > 0 && (
              <meta content={compData.result.ogKeywords} property="og:keywords" />
            )}
            {compData.result.ogType && compData.result.ogType.length > 0 && (
              <meta content={compData.result.ogType} property="og:type" />
            )}
            {compData.result.ogImage && compData.result.ogImage.length > 0 && (
              <meta content={compData.result.ogImage} property="og:image" />
            )}
            {schemas && (
              <script type="application/ld+json">
                {JSON.stringify(schemas)}
              </script>
            )}
          </Helmet>
        </HelmetProvider>
      )}
      {compData?.result && !compData.error && (
        <ParallaxProvider>
          <Page
            res={compData.result}
            nav={compData.result.navigation}
            breadcrumbs={compData.result.breadcrumbs}
            footer={compData.result.navigation?.footer}
            content={compData.result.components}
            allData={compData.result}
          />
        </ParallaxProvider>
      )}
    </>
  )
}

export default App
