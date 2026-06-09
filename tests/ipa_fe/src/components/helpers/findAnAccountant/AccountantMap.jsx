import PropTypes from 'prop-types'
import { useRef, useEffect, useState, useCallback } from 'react'
import { createMarkup } from '../../../helpers/markup'
import Loader from '../Loader'
let L = null

async function loadLeafletAsync() {
  if (!L && typeof window !== 'undefined') {
    const leafletModule = await import('leaflet')
    L = leafletModule.default
    delete L.Icon.Default.prototype._getIconUrl
    L.Icon.Default.mergeOptions({
      iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    })
    await import('leaflet/dist/leaflet.css')
  }
}

const standardPinSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none"><path d="M29.9995 4.99951C41.0451 4.99951 49.9993 13.954 49.9995 24.9995C49.9995 36.0452 39.9995 44.9995 29.9995 54.9995C19.9996 44.9996 9.99951 36.0451 9.99951 24.9995C9.99971 13.9541 18.9541 4.99964 29.9995 4.99951ZM30.0005 17.5005C25.8584 17.5005 22.5005 20.8584 22.5005 25.0005C22.5007 29.1425 25.8585 32.5005 30.0005 32.5005C34.1423 32.5002 37.5003 29.1423 37.5005 25.0005C37.5005 20.8585 34.1424 17.5008 30.0005 17.5005Z" fill="#0D2C6C"/></svg>`

const selectedPinSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none"><mask id="path-1-inside-1_15_2684" fill="white"><path d="M29.9995 4.99951C41.0451 4.99951 49.9993 13.954 49.9995 24.9995C49.9995 36.0452 39.9995 44.9995 29.9995 54.9995C19.9996 44.9996 9.99951 36.0451 9.99951 24.9995C9.99971 13.9541 18.9541 4.99964 29.9995 4.99951ZM30.0005 17.5005C25.8584 17.5005 22.5005 20.8584 22.5005 25.0005C22.5007 29.1425 25.8585 32.5005 30.0005 32.5005C34.1423 32.5002 37.5003 29.1423 37.5005 25.0005C37.5005 20.8585 34.1424 17.5008 30.0005 17.5005Z"/></mask><path d="M29.9995 4.99951C41.0451 4.99951 49.9993 13.954 49.9995 24.9995C49.9995 36.0452 39.9995 44.9995 29.9995 54.9995C19.9996 44.9996 9.99951 36.0451 9.99951 24.9995C9.99971 13.9541 18.9541 4.99964 29.9995 4.99951ZM30.0005 17.5005C25.8584 17.5005 22.5005 20.8584 22.5005 25.0005C22.5007 29.1425 25.8585 32.5005 30.0005 32.5005C34.1423 32.5002 37.5003 29.1423 37.5005 25.0005C37.5005 20.8585 34.1424 17.5008 30.0005 17.5005Z" fill="#992785"/><path d="M29.9995 4.99951V2.99951H29.9995L29.9995 4.99951ZM49.9995 24.9995H51.9995V24.9995L49.9995 24.9995ZM29.9995 54.9995L28.5853 56.4137C29.3663 57.1948 30.6327 57.1948 31.4137 56.4137L29.9995 54.9995ZM9.99951 24.9995L7.99951 24.9995V24.9995H9.99951ZM30.0005 17.5005L30.0006 15.5005H30.0005V17.5005ZM22.5005 25.0005H20.5005V25.0006L22.5005 25.0005ZM30.0005 32.5005V34.5005H30.0006L30.0005 32.5005ZM37.5005 25.0005L39.5005 25.0006V25.0005H37.5005ZM29.9995 4.99951V6.99951C39.9405 6.99951 47.9993 15.0585 47.9995 24.9995L49.9995 24.9995L51.9995 24.9995C51.9993 12.8494 42.1497 2.99951 29.9995 2.99951V4.99951ZM49.9995 24.9995H47.9995C47.9995 29.8883 45.7932 34.4602 42.1649 39.1713C38.5171 43.908 33.6517 48.5189 28.5853 53.5853L29.9995 54.9995L31.4137 56.4137C36.3473 51.4802 41.482 46.6138 45.3341 41.612C49.2058 36.5845 51.9995 31.1564 51.9995 24.9995H49.9995ZM29.9995 54.9995L31.4137 53.5853C26.3473 48.5189 21.482 43.9081 17.8341 39.1713C14.2059 34.4601 11.9995 29.8882 11.9995 24.9995H9.99951H7.99951C7.99951 31.1564 10.7932 36.5845 14.665 41.612C18.5171 46.6139 23.6518 51.4802 28.5853 56.4137L29.9995 54.9995ZM9.99951 24.9995L11.9995 24.9995C11.9997 15.0586 20.0586 6.99963 29.9995 6.99951L29.9995 4.99951L29.9995 2.99951C17.8495 2.99966 7.99973 12.8495 7.99951 24.9995L9.99951 24.9995ZM30.0005 17.5005V15.5005C24.7538 15.5005 20.5005 19.7538 20.5005 25.0005H22.5005H24.5005C24.5005 21.9629 26.9629 19.5005 30.0005 19.5005V17.5005ZM22.5005 25.0005L20.5005 25.0006C20.5007 30.247 24.7538 34.5005 30.0005 34.5005V32.5005V30.5005C26.9631 30.5005 24.5006 28.038 24.5005 25.0004L22.5005 25.0005ZM30.0005 32.5005L30.0006 34.5005C35.2468 34.5002 39.5002 30.2469 39.5005 25.0006L37.5005 25.0005L35.5005 25.0004C35.5003 28.0377 33.0377 30.5003 30.0004 30.5005L30.0005 32.5005ZM37.5005 25.0005H39.5005C39.5005 19.7538 35.2469 15.5008 30.0006 15.5005L30.0005 17.5005L30.0004 19.5005C33.0379 19.5007 35.5005 21.9632 35.5005 25.0005H37.5005Z" fill="#730660" mask="url(#path-1-inside-1_15_2684)"/></svg>`

const customPinIcon = (type) => {
  return L.divIcon({
    className: "leaflet-data-marker",
    html: type === 'standard' ? standardPinSVG : selectedPinSVG,
    iconSize    : [64, 64],
    iconAnchor  : [32, 64],
    popupAnchor : [0, -32]
  })
}

const getPopupContent = (accountant) => {
  let html = ''

  if (accountant.fullName) {
    html += `<div class="text-secondary font-medium block mb-2 text-xl font-apex-book">${accountant.fullName}</div>`
  }

  if (accountant.company) {
    html += `<div class="text-secondary label-sm mb-2">${accountant.company}</div>`
  }

  if (accountant.fullAddress) {
    html += `<div class="text-primary text-lg mb-2">${accountant.fullAddress}</div>`
  }

  if (accountant.email) {
    html += `<div class="text-primary text-lg mb-2"><a href="mailto:${accountant.email}" class="!text-link hover:!text-link-hover hover:underline break-all">${accountant.email}</a></div>`
  }

  return html
}

const AccountantMap = ({ accountants, selectedAccountant, onAccountantSelect }) => {
  const mapRef = useRef(null)
  const mapInstanceRef = useRef(null)
  const [theMap, setTheMap] = useState(null)
  const [leafletLoaded, setLeafletLoaded] = useState(false)
  const [isGeocoding, setIsGeocoding] = useState(false)
  const [hasNoMarkers, setHasNoMarkers] = useState(false)
  const markersRef = useRef(new Map()) // Map of accountant.id -> marker
  const isInitializedRef = useRef(false)

  // Load Leaflet library
  useEffect(() => {
    const loadLeaflet = async () => {
      await loadLeafletAsync()
      setLeafletLoaded(true)
    }

    loadLeaflet()
  }, [])

  const initializeMap = useCallback(() => {
    if (!L || !mapRef.current || mapInstanceRef.current) return

    isInitializedRef.current = true

    // Use requestAnimationFrame to ensure DOM is fully ready
    requestAnimationFrame(() => {
      if (!mapRef.current || mapInstanceRef.current) return

      mapInstanceRef.current = L.map(mapRef.current, {
        preferCanvas: false
      }).setView([-25.2744, 133.7751], 5)

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(mapInstanceRef.current)

      // Invalidate size to ensure map renders correctly after container is visible
      const invalidateSize = () => {
        if (mapInstanceRef.current) {
          mapInstanceRef.current.invalidateSize()
        }
      }

      // Call invalidateSize multiple times to ensure it works
      setTimeout(invalidateSize, 0)
      setTimeout(invalidateSize, 100)
      setTimeout(invalidateSize, 300)
      setTimeout(invalidateSize, 500)

      setTheMap(mapInstanceRef.current)
    })
  }, [])

  const removeObsoleteMarkers = (accountantIds) => {
    for (const [accountantId, marker] of markersRef.current.entries()) {
      if (!accountantIds.has(accountantId)) {
        marker.remove()
        markersRef.current.delete(accountantId)
      }
    }
  }

  const updateMapBounds = (map) => {
    if (markersRef.current.size === 0) {
      setHasNoMarkers(true)
      return
    }

    updateMarkerIcons()
    const group = new L.featureGroup(Array.from(markersRef.current.values()))
    map.fitBounds(group.getBounds().pad(0.1))
    setHasNoMarkers(false)
  }

  const geocodeAccountant = async (accountant) => {
    const lat = accountant.latitude
    const lon = accountant.longitude

    if (lat == null || lon == null) {
      return { success: false, accountant }
    }

    return { success: true, accountant, lat, lon }
  }

  const createMarker = (accountant, lat, lon, map) => {
    const isSelected = selectedAccountant?.id === accountant.id
    const marker = L.marker([lat, lon], {
      icon: customPinIcon(isSelected ? 'selected' : 'standard')
    }).addTo(map)

    marker.bindPopup(createMarkup(getPopupContent(accountant)).__html)

    marker.on('click', () => {
      onAccountantSelect(accountant)
    })

    markersRef.current.set(accountant.id, marker)
    return marker
  }

  const processGeocodeResults = (geocodeResults, map) => {
    geocodeResults.forEach((result) => {
      if (result.status === 'fulfilled' && result.value.success) {
        const { accountant, lat, lon } = result.value
        createMarker(accountant, lat, lon, map)
      }
    })
  }

  const addMarkers = async (map) => {
    if (!map || !L || accountants.length === 0) return

    const accountantIds = new Set(accountants.map(acc => acc.id))
    removeObsoleteMarkers(accountantIds)

    const accountantsToGeocode = accountants.filter(
      accountant => accountant.fullAddress && !markersRef.current.has(accountant.id)
    )

    if (accountantsToGeocode.length === 0) {
      updateMapBounds(map)
      return
    }

    setIsGeocoding(true)

    try {
      const geocodePromises = accountantsToGeocode.map(geocodeAccountant)
      const geocodeResults = await Promise.allSettled(geocodePromises)
      processGeocodeResults(geocodeResults, map)
      updateMapBounds(map)
    } finally {
      setIsGeocoding(false)
    }
  }

  const updateMarkerIcons = () => {
    if (!L || markersRef.current.size === 0) return

    // Update all markers based on selectedAccountant
    for (const [accountantId, marker] of markersRef.current.entries()) {
      const isSelected = selectedAccountant?.id === accountantId
      marker.setIcon(customPinIcon(isSelected ? 'selected' : 'standard'))
    }
  }

  // Function to check and initialize map
  const checkAndInitializeMap = useCallback(() => {
    if (!leafletLoaded || !L || !mapRef.current || isInitializedRef.current || mapInstanceRef.current) return

    let timeoutId = null
    let retryCount = 0
    const maxRetries = 20 // Try for up to 2 seconds (20 * 100ms)

    const checkAndInitialize = () => {
      // Check if container is actually in the DOM and has dimensions
      const container = mapRef.current
      if (!container) {
        if (retryCount < maxRetries) {
          retryCount++
          timeoutId = setTimeout(checkAndInitialize, 100)
        }
        return
      }

      const rect = container.getBoundingClientRect()
      const hasDimensions = rect.width > 0 && rect.height > 0
      const isVisible = rect.width > 0 && rect.height > 0 && window.getComputedStyle(container).display !== 'none'

      if (hasDimensions && isVisible && !mapInstanceRef.current && !isInitializedRef.current) {
        initializeMap()
      } else if (retryCount < maxRetries && (!hasDimensions || !isVisible)) {
        // Container not ready yet, wait a bit and try again
        retryCount++
        timeoutId = setTimeout(checkAndInitialize, 100)
      }
    }

    // Start checking immediately
    checkAndInitialize()

    return () => {
      if (timeoutId) {
        clearTimeout(timeoutId)
      }
    }
  }, [leafletLoaded, initializeMap])

  // Initialize map when Leaflet is loaded
  useEffect(() => {
    checkAndInitializeMap()
  }, [checkAndInitializeMap])

  // Also try to initialize when accountants arrive (in case container wasn't ready before)
  useEffect(() => {
    if (accountants.length > 0 && leafletLoaded && !mapInstanceRef.current) {
      // Small delay to ensure container is rendered
      const timer = setTimeout(() => {
        checkAndInitializeMap()
      }, 100)
      return () => clearTimeout(timer)
    }
  }, [accountants.length, leafletLoaded, checkAndInitializeMap])

  // Add/update markers when accountants change
  useEffect(() => {
    if (theMap && accountants.length > 0) {
      setHasNoMarkers(false) // Reset state when new accountants are loaded
      addMarkers(theMap)
    } else if (theMap && accountants.length === 0) {
      // Clear markers and reset state when accountants list is empty
      markersRef.current.forEach(marker => marker.remove())
      markersRef.current.clear()
      setHasNoMarkers(false)
    }
  }, [accountants, theMap])

  // Update marker icons and open popup when selectedAccountant changes
  useEffect(() => {
    if (theMap) {
      updateMarkerIcons()

      // Open the popup for the selected accountant's marker
      if (selectedAccountant?.id && markersRef.current.has(selectedAccountant.id)) {
        const marker = markersRef.current.get(selectedAccountant.id)
        marker.openPopup()
      }
    }
  }, [selectedAccountant, theMap])

  return (
    <div ref={mapRef} style={{ height: '600px', width: '100%' }} className="rounded-xl relative">
      {isGeocoding && (
        <div className="absolute top-0 left-0 w-full h-full z-[500] flex flex-col gap-4 justify-center items-center">
          <div className="bg-black opacity-50 p-4 absolute top-0 left-0 right-0 bottom-0"></div>
          <Loader className="mx-auto z-10"/>
          <div className="text-white text-lg z-10 font-din">Loading accountants...</div>
        </div>
      )}
      {!isGeocoding && hasNoMarkers && markersRef.current.size === 0 && (
        <div className="absolute top-0 left-0 w-full h-full z-[500] flex flex-col gap-4 justify-center items-center">
          <div className="bg-black opacity-50 p-4 absolute top-0 left-0 right-0 bottom-0"></div>
          <div className="text-white text-lg z-10 font-din">No map data found for accountants. You can <a href="#find-an-accountant-cards" className="!text-white underline hover:decoration-double">view the list of accountants</a> instead.</div>
        </div>
      )}
    </div>
  )
}

AccountantMap.propTypes = {
  accountants: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
      fullName: PropTypes.string,
      company: PropTypes.string,
      fullAddress: PropTypes.string,
      email: PropTypes.string,
    })
  ).isRequired,
  selectedAccountant: PropTypes.shape({
    id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  }),
  onAccountantSelect: PropTypes.func.isRequired,
}

export default AccountantMap
