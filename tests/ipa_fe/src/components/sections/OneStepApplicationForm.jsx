import { useState, useEffect, useCallback } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup } from '../../helpers/markup'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import ButtonEl from '../helpers/ctas/Button'
import TitleBlock from '../helpers/TitleBlock'

const FORM_STATE_STORAGE_KEY = 'oneStepApplicationForm:pendingSubmission'
const PAYMENT_CANCELLED_PARAM = 'paymentCancelled'
const RECAPTCHA_READY_TIMEOUT_MS = 5000
const RECAPTCHA_POLL_INTERVAL_MS = 100

function waitForRecaptchaReady(timeoutMs) {
  const start = Date.now()
  return new Promise((resolve, reject) => {
    const check = () => {
      if (typeof window !== 'undefined' && window.grecaptcha && window.grecaptcha.execute) {
        resolve()
        return
      }
      if (Date.now() - start > timeoutMs) {
        reject(new Error('reCAPTCHA did not load in time. Please refresh the page and try again.'))
        return
      }
      setTimeout(check, RECAPTCHA_POLL_INTERVAL_MS)
    }
    check()
  })
}

function OneStepApplicationForm(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const recaptchaSiteKey = props.recaptchaPublicKey
  const recaptchaAction = props.recaptchaAction

  const [formData, setFormData] = useState({
    assessmentTypeId: '',
    titleId: '',
    firstName: '',
    lastName: '',
    email: '',
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [errorMessage, setErrorMessage] = useState('')
  const [infoMessage, setInfoMessage] = useState('')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')

  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  useEffect(() => {
    if (typeof window === 'undefined') return
    if (!recaptchaSiteKey) return
    if (document.getElementById('recaptcha-v3-script')) return

    const script = document.createElement('script')
    script.id = 'recaptcha-v3-script'
    script.src = `https://www.google.com/recaptcha/api.js?render=${recaptchaSiteKey}`
    script.async = true
    script.defer = true
    document.head.appendChild(script)
  }, [recaptchaSiteKey])

  useEffect(() => {
    if (typeof window === 'undefined') return

    const params = new URLSearchParams(window.location.search)
    if (params.get(PAYMENT_CANCELLED_PARAM) !== 'true') return

    try {
      const saved = window.sessionStorage.getItem(FORM_STATE_STORAGE_KEY)
      if (saved) {
        const parsed = JSON.parse(saved)
        if (parsed && typeof parsed === 'object') {
          setFormData((prev) => ({ ...prev, ...parsed }))
        }
        window.sessionStorage.removeItem(FORM_STATE_STORAGE_KEY)
      }
    } catch {
      // ignore malformed saved state
    }

    setInfoMessage('Your payment was cancelled. Your details have been restored so you can try again.')

    params.delete(PAYMENT_CANCELLED_PARAM)
    const cleanedSearch = params.toString()
    const cleanedUrl =
      window.location.pathname + (cleanedSearch ? `?${cleanedSearch}` : '') + window.location.hash
    window.history.replaceState({}, '', cleanedUrl)
  }, [])

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
    if (errorMessage) setErrorMessage('')
    if (infoMessage) setInfoMessage('')
  }

  const getRecaptchaToken = useCallback(async () => {
    if (!recaptchaSiteKey || !recaptchaAction) {
      throw new Error('reCAPTCHA is not configured')
    }
    await waitForRecaptchaReady(RECAPTCHA_READY_TIMEOUT_MS)
    return new Promise((resolve, reject) => {
      window.grecaptcha.ready(() => {
        window.grecaptcha
          .execute(recaptchaSiteKey, { action: recaptchaAction })
          .then((token) => resolve(token))
          .catch((err) => reject(err))
      })
    })
  }, [recaptchaSiteKey, recaptchaAction])

  const parseResponseBody = async (response) => {
    const contentType = response.headers.get('content-type') || ''
    if (!contentType.toLowerCase().includes('application/json')) return null
    try {
      return await response.json()
    } catch {
      return null
    }
  }

  const errorMessageForStatus = (status) => {
    if (status === 429) return 'Too many attempts. Please wait a moment and try again.'
    if (status === 503 || status === 502 || status === 504) {
      return 'The payment service is temporarily unavailable. Please try again in a few minutes.'
    }
    return 'Unable to process payment. Please try again later.'
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrorMessage('')
    setInfoMessage('')

    if (
      !formData.assessmentTypeId ||
      !formData.titleId ||
      !formData.firstName.trim() ||
      !formData.lastName.trim() ||
      !formData.email.trim()
    ) {
      setErrorMessage('Please fill in all required fields.')
      return
    }

    setIsSubmitting(true)

    const trimmedFormData = {
      assessmentTypeId: formData.assessmentTypeId,
      titleId: formData.titleId,
      firstName: formData.firstName.trim(),
      lastName: formData.lastName.trim(),
      email: formData.email.trim(),
    }

    const cancelUrl =
      typeof window !== 'undefined'
        ? `${window.location.origin}${window.location.pathname}?${PAYMENT_CANCELLED_PARAM}=true`
        : undefined

    try {
      if (typeof window !== 'undefined') {
        try {
          window.sessionStorage.setItem(FORM_STATE_STORAGE_KEY, JSON.stringify(trimmedFormData))
        } catch {
          // sessionStorage may be unavailable (private mode, quota); proceed without state restoration
        }
      }

      const recaptchaToken = await getRecaptchaToken()

      const response = await fetch('/api/payment/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...trimmedFormData,
          recaptchaToken,
          cancelUrl,
        }),
      })

      const data = await parseResponseBody(response)

      if (response.ok && data?.success && data?.url) {
        window.location.assign(data.url)
        return
      }

      setErrorMessage(data?.error || errorMessageForStatus(response.status))
      setIsSubmitting(false)
    } catch (error) {
      console.error('Payment error:', error)
      setErrorMessage(error?.message || 'An unexpected error occurred. Please try again later.')
      setIsSubmitting(false)
    }
  }

  const assessmentTypes = Array.isArray(props.assessmentTypes) ? props.assessmentTypes : []
  const titles = Array.isArray(props.titles) ? props.titles : []

  return (
    <Section
      type="oneStepApplicationForm"
      outerClass={`${componentPadding} overflow-hidden`}
      sectionTitle={false}
      {...props}
    >
      <div className="container mx-auto px-4 py-16 lg:py-20">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
          <div className="content-section">
            {(props.heading || props.description) && (
              <div>
                <TitleBlock {...props} title={props.heading} lightOrDark={lightOrDarkValue} />
                {props.description && (
                  <div
                    className="rte mt-4"
                    dangerouslySetInnerHTML={createMarkup(props.description)}
                  />
                )}
              </div>
            )}
          </div>

          <div className="form-section relative">
            <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-6">
              {errorMessage && (
                <div
                  role="alert"
                  aria-live="assertive"
                  className="p-4 bg-error-subtle border border-error-border rounded-lg text-error-bold"
                >
                  <p className="font-medium">Error</p>
                  <p className="text-sm mt-1">{errorMessage}</p>
                </div>
              )}

              {infoMessage && !errorMessage && (
                <div
                  role="status"
                  aria-live="polite"
                  className="p-4 bg-warning-subtle border border-warning-border rounded-lg text-warning-bold"
                >
                  <p className="font-medium">Payment cancelled</p>
                  <p className="text-sm mt-1">{infoMessage}</p>
                </div>
              )}

              <div>
                <label htmlFor="assessmentTypeId">
                  Assessment type<span className="text-error-bold ml-1">*</span>
                </label>
                <select
                  id="assessmentTypeId"
                  name="assessmentTypeId"
                  value={formData.assessmentTypeId}
                  onChange={handleChange}
                  required
                  disabled={isSubmitting}
                >
                  <option value="">Please select...</option>
                  {assessmentTypes.map((assessment) => (
                    <option key={assessment.id} value={assessment.id}>
                      {assessment.name} (${Number(assessment.price).toFixed(2)})
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label htmlFor="titleId">
                  Title<span className="text-error-bold ml-1">*</span>
                </label>
                <select
                  id="titleId"
                  name="titleId"
                  value={formData.titleId}
                  onChange={handleChange}
                  required
                  disabled={isSubmitting}
                >
                  <option value="">Please select...</option>
                  {titles.map((t, idx) => (
                    <option key={`${t.title}-${idx}`} value={t.title}>
                      {t.title}
                    </option>
                  ))}
                </select>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                  <label htmlFor="firstName">
                    First name<span className="text-error-bold ml-1">*</span>
                  </label>
                  <input
                    type="text"
                    id="firstName"
                    name="firstName"
                    value={formData.firstName}
                    onChange={handleChange}
                    required
                    disabled={isSubmitting}
                  />
                </div>
                <div>
                  <label htmlFor="lastName">
                    Last name<span className="text-error-bold ml-1">*</span>
                  </label>
                  <input
                    type="text"
                    id="lastName"
                    name="lastName"
                    value={formData.lastName}
                    onChange={handleChange}
                    required
                    disabled={isSubmitting}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="email">
                  Email address<span className="text-error-bold ml-1">*</span>
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  disabled={isSubmitting}
                />
              </div>

              <p className="label-xs">* Required fields</p>

              <p className="label-xs text-xs">
                This site is protected by reCAPTCHA and the Google{' '}
                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">
                  Privacy Policy
                </a>{' '}
                and{' '}
                <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">
                  Terms of Service
                </a>{' '}
                apply.
              </p>

              <div className="flex justify-center lg:justify-end">
                <ButtonEl
                  type="submit"
                  theme="primary"
                  label={isSubmitting ? 'PROCESSING...' : 'PAY NOW'}
                  disabled={isSubmitting}
                  className={`${isSubmitting ? 'opacity-50 cursor-not-allowed' : ''} max-sm:w-full`}
                />
              </div>
            </form>
          </div>
        </div>
      </div>
    </Section>
  )
}

OneStepApplicationForm.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  heading: PropTypes.string,
  description: PropTypes.string,
  recaptchaPublicKey: PropTypes.string,
  recaptchaAction: PropTypes.string,
  assessmentTypes: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.string,
      price: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
    })
  ),
  titles: PropTypes.arrayOf(
    PropTypes.shape({
      title: PropTypes.string,
    })
  ),
}

export default OneStepApplicationForm
