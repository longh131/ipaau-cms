import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import ButtonEl from '../helpers/ctas/Button'
import TitleBlock from '../helpers/TitleBlock'

function Newsletter(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    company: ''
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [submitStatus, setSubmitStatus] = useState(null) // 'success', 'error', or null
  const [errorMessage, setErrorMessage] = useState('')

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
    // Clear error message when user starts typing
    if (submitStatus === 'error') {
      setSubmitStatus(null)
      setErrorMessage('')
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setIsSubmitting(true)
    setSubmitStatus(null)
    setErrorMessage('')

    // Basic validation
    if (!formData.firstName.trim() || !formData.lastName.trim() || !formData.email.trim()) {
      setSubmitStatus('error')
      setErrorMessage('Please fill in all required fields.')
      setIsSubmitting(false)
      return
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(formData.email)) {
      setSubmitStatus('error')
      setErrorMessage('Please enter a valid email address.')
      setIsSubmitting(false)
      return
    }

    try {
      const response = await fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: formData.email.trim(),
          firstName: formData.firstName.trim(),
          lastName: formData.lastName.trim(),
          company: formData.company.trim() || null
        })
      })

      const data = await response.json()

      if (response.ok && data.success) {
        setSubmitStatus('success')
        // Reset form
        setFormData({
          firstName: '',
          lastName: '',
          email: '',
          company: ''
        })
      } else {
        setSubmitStatus('error')
        setErrorMessage(data.message || 'Failed to subscribe. Please try again later.')
      }
    } catch (error) {
      console.error('Newsletter subscription error:', error)
      setSubmitStatus('error')
      setErrorMessage('An error occurred. Please try again later.')
    } finally {
      setIsSubmitting(false)
    }
  }

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  return (
    <Section
      type="newsletter"
      outerClass={`${componentPadding} overflow-hidden`}
      sectionTitle={false}
      {...props}
    >
      <div className="container mx-auto px-4 py-16 lg:py-20">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
          {/* Left Side - Content */}
          <div className="content-section">
            {(props.heading || props.description) && (
              <div>
                <TitleBlock {...props} title={props.heading} lightOrDark={lightOrDarkValue} />
              </div>
            )}
          </div>

          {/* Right Side - Form */}
          <div className="form-section relative">
            <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
              {/* Success Message */}
              {submitStatus === 'success' && (
                <div className="mb-6 p-4 bg-success-subtle border border-success-border rounded-lg text-success-bold">
                  <p className="font-medium">Thank you for subscribing!</p>
                  <p className="text-sm mt-1">You'll receive our latest updates straight to your inbox.</p>
                </div>
              )}

              {/* Error Message */}
              {submitStatus === 'error' && (
                <div className="mb-6 p-4 bg-error-subtle border border-error-border rounded-lg text-error-bold">
                  <p className="font-medium">Subscription failed</p>
                  <p className="text-sm mt-1">{errorMessage}</p>
                </div>
              )}

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:col-span-2">
                {/* First Name */}
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

                {/* Last Name */}
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

              {/* Email */}
              <div className="lg:col-span-2">
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

              {/* Company */}
              <div className="lg:col-span-2"  >
                <label htmlFor="company">
                  Company
                </label>
                <input
                  type="text"
                  id="company"
                  name="company"
                  value={formData.company}
                  onChange={handleChange}
                  disabled={isSubmitting}
                />
              </div>

              {/* Required Fields Note */}
              <p className="label-xs ">* Required fields</p>

              {/* Submit Button */}
              <div className="flex justify-center lg:justify-end">
                <ButtonEl
                  type="submit"
                  theme="primary"
                  label="SUBSCRIBE"
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

Newsletter.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  heading: PropTypes.string,
  description: PropTypes.string,
}

export default Newsletter

