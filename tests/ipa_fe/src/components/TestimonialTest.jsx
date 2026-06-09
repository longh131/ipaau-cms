import React from 'react'
import TestimonialCarousel from './sections/TestimonialCarousel'
import testimonialsData from '../data/testimonials.json'

const TestimonialTest = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <header className="p-8 text-center bg-gray-800 text-white">
        <h1 className="text-3xl font-bold mb-2">Testimonial Carousel Component Test</h1>
        <p className="text-gray-300">This is a test page to demonstrate the Testimonial Carousel component</p>
      </header>
      
      <main className="py-8">
        <TestimonialCarousel {...testimonialsData} />
      </main>
      
      <footer className="p-8 text-center bg-gray-700 text-white">
        <p>Test page for IPA Testimonial Carousel Component</p>
      </footer>
    </div>
  )
}

export default TestimonialTest
