import React, { useEffect, useState, useCallback, useRef } from 'react'
import PropTypes from 'prop-types'
import ButtonEl from './helpers/ctas/Button'
import { ChevronDownIcon } from '@heroicons/react/24/solid'
import { UserCircleIcon } from '@heroicons/react/24/outline'
import { dataLayerPush } from '../helpers/thirdparty'
/**
 * AuthButton Component
 *
 * Displays a login/logout button in the header that:
 * - Shows "Sign In" when user is not authenticated
 * - Shows a profile dropdown when authenticated (avatar + chevron)
 * - Manages SSO login/logout flow
 * - Session token is stored securely as HTTP-only cookie on backend
 * - Login/logout URLs are fetched from the backend API (configured in appsettings)
 */
export function AuthButton() {
  const [isAuthenticated, setIsAuthenticated] = useState(false)
  const [loading, setLoading] = useState(true)
  const [user, setUser] = useState(null)
  const [memberDashboardUrl, setMemberDashboardUrl] = useState(null)
  const [dropdownOpen, setDropdownOpen] = useState(false)
  const [imgError, setImgError] = useState(false)
  const dropdownRef = useRef(null)

  // Check authentication status on component mount
  const checkAuthStatus = useCallback(async () => {
    try {
      const response = await fetch('/api/authentication/validate-session', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include' // Include cookies in the request
      })

      if (response.ok) {
        const data = await response.json()
        setIsAuthenticated(data.isValid)
        if (data.isValid && data.user) {
          setUser(data.user)
          setMemberDashboardUrl(data.memberDashboardUrl)
        }
      } else {
        setIsAuthenticated(false)
      }
    } catch (error) {
      if (process.env.NODE_ENV === 'development') {
        console.error('Auth check failed:', error)
      }
      setIsAuthenticated(false)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    checkAuthStatus()
  }, [checkAuthStatus])

  // Close dropdown on click outside or Escape key
  useEffect(() => {
    const handleClickOutside = (evt) => {
      if (dropdownRef.current && !dropdownRef.current.contains(evt.target)) {
        setDropdownOpen(false)
      }
    }
    const handleKeyDown = (evt) => {
      if (evt.key === 'Escape') setDropdownOpen(false)
    }
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleKeyDown)
    return () => {
      document.removeEventListener('click', handleClickOutside)
      document.removeEventListener('keydown', handleKeyDown)
    }
  }, [])

  // Build the login URL with current path as returnUrl
  const currentPath = typeof window !== 'undefined'
    ? window.location.pathname + window.location.search + window.location.hash
    : '/'
  const loginUrl = `/api/authentication/login?returnUrl=${encodeURIComponent(currentPath)}`

  /**
   * Handles logout - clears local session and redirects to IMIS Cloud logout
   */
  const handleLogout = async () => {
    try {
      // Get the full current URL to return to after IMIS logout
      const returnUrl = window.location.origin + window.location.pathname + window.location.search

      const response = await fetch('/api/authentication/logout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include', // Include cookies in the request
        body: JSON.stringify({
          returnUrl
        })
      })

      if (response.ok) {
        const data = await response.json()

        // Clear local state
        setIsAuthenticated(false)
        setUser(null)
        setDropdownOpen(false)

        // Redirect to IMIS Cloud logout URL if provided, otherwise go home
        if (data.imisLogoutUrl) {
          window.location.href = data.imisLogoutUrl
        } else {
          window.location.href = '/'
        }
      } else {
        throw new Error('Logout request failed')
      }
    } catch (error) {
      if (process.env.NODE_ENV === 'development') {
        console.error('Logout error:', error)
      }
      alert('Logout failed. Please try again.')
    }
  }

  if (loading) {
    return null
  }

  const showAvatar = isAuthenticated && !imgError

  const loggedOutContent = (
    <ButtonEl
      aria-label="Sign in to your account"
      theme="hollow"
      function="login"
      link={{ url: loginUrl }}
      dataLayer={false}
      onClick={() => {
        dataLayerPush({
          event: 'login_intent'
        })
      }}
    >
      Sign In
    </ButtonEl>
  )

  const loggedInContent = (
    <div ref={dropdownRef} className="relative">
      <button
        onClick={() => setDropdownOpen((prev) => !prev)}
        aria-haspopup="true"
        aria-expanded={dropdownOpen}
        aria-label="Account menu"
        className="flex items-center gap-1.5 rounded-full border border-neutral-300 bg-white px-2 py-1.5 transition-colors hover:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/50"
      >
        {showAvatar ? (
          <img
            src="/api/authentication/profile-picture"
            alt=""
            className="h-9 w-9 rounded-full object-cover"
            onError={() => setImgError(true)}
          />
        ) : (
          <UserCircleIcon className="h-9 w-9 text-neutral-500" />
        )}
        <ChevronDownIcon
          className={`h-4 w-4 text-neutral-500 transition-transform duration-300 ${dropdownOpen ? 'rotate-180' : ''}`}
        />
      </button>

      <div
        className={`${dropdownOpen ? 'scale-y-100 opacity-100' : 'pointer-events-none scale-y-0 opacity-0'} absolute right-0 z-50 mt-2 min-w-[220px] origin-top rounded-lg bg-white shadow-xl ring-1 ring-black/5 transition-all duration-200`}
        aria-hidden={!dropdownOpen}
      >
        {(user?.displayName || user?.designation) && (
          <div className="border-b border-neutral-100 px-4 py-3">
            {user.displayName && (
              <p className="text-sm font-semibold text-neutral-900">{user.displayName}</p>
            )}
            {user.designation && (
              <p className="text-xs text-neutral-500">{user.designation}</p>
            )}
          </div>
        )}
        <div className="py-1">
          {memberDashboardUrl && (
            <a
              href={memberDashboardUrl}
              className="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
            >
              My Dashboard
            </a>
          )}
          <button
            onClick={handleLogout}
            className="block w-full px-4 py-2 text-left text-sm text-neutral-700 hover:bg-neutral-50"
          >
            Sign Out
          </button>
        </div>
      </div>
    </div>
  )

  return <div className="flex items-center gap-4 mr-4 xl:ml-4 xl:mr-0">
    {isAuthenticated ? loggedInContent : loggedOutContent}
  </div>
}

AuthButton.propTypes = {}
