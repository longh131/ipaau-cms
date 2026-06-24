@extends('layouts.app')

@section('title', 'Institute of Public Accountants')
@section('canonical', url('/'))
@section('og_title', 'Your hub for accounting | Institute Public Accountants')

@section('json_ld')
    <script type="application/ld+json">
      {
        "@@context": "https://schema.org",
        "@@graph": [
          {
            "@@type": "Organization",
            "@@id": "https://www.publicaccountants.org.au#organization",
            "name": "Institute Public Accountants",
            "alternateName": "IPA",
            "url": "https://www.publicaccountants.org.au",
            "description": "The Institute of Public Accountants is the voice of small business advisors and accountants.",
            "contactPoint": {
              "@@type": "ContactPoint",
              "telephone": "+61-3-8665-3100",
              "email": "info@@publicaccountants.org.au",
              "contactType": "customer service"
            },
            "address": {
              "@@type": "PostalAddress",
              "streetAddress": "Level 6, 555 Lonsdale Street",
              "addressLocality": "Melbourne",
              "addressRegion": "VIC",
              "postalCode": "3000",
              "addressCountry": "AU"
            },
            "sameAs": [
              "https://www.google.com/",
              "https://www.facebook.com/",
              "https://www.youtube.com/"
            ],
            "potentialAction": {
              "@@type": "SearchAction",
              "target": {
                "@@type": "EntryPoint",
                "urlTemplate": "https://www.publicaccountants.org.au/search?q={search_term_string}"
              },
              "query-input": "required name=search_term_string"
            },
            "logo": {
              "@@type": "ImageObject",
              "url": "{{ asset('assets/img/ipa-logo.png') }}"
            }
          },
          {
            "@@type": "WebSite",
            "@@id": "https://www.publicaccountants.org.au#website",
            "url": "https://www.publicaccountants.org.au",
            "name": "Institute Public Accountants",
            "publisher": {
              "@@id": "https://www.publicaccountants.org.au#organization"
            }
          },
          {
            "@@type": "WebPage",
            "@@id": "https://www.publicaccountants.org.au/",
            "url": "https://www.publicaccountants.org.au/",
            "name": "Your hub for accounting | Institute Public Accountants",
            "description": "",
            "publisher": {
              "@@id": "https://www.publicaccountants.org.au#organization"
            },
            "isPartOf": {
              "@@id": "https://www.publicaccountants.org.au#website"
            },
            "about": {
              "@@id": "https://www.publicaccountants.org.au#organization"
            },
            "dateModified": "2025-09-23T03:55:53.000Z"
          }
        ]
      }
    </script>
@endsection

@section('content')
    @include('partials.home.main-content')
@endsection
