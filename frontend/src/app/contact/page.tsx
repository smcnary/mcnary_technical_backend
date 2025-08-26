'use client'

import Header from '../../components/Header';

export default function ContactPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-6xl mx-auto px-6 py-12">
          <h2 className="section-title">Get In Touch</h2>
          <p className="section-subtitle">Ready to dominate search results? Let&apos;s discuss your business&apos;s SEO strategy</p>
          <div className="grid md:grid-cols-2 gap-12">
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-6">Send us a message</h3>
              <form className="space-y-4">
                <input type="text" placeholder="Name *" required className="input-field" />
                <input type="email" placeholder="Email *" required className="input-field" />
                <input type="tel" placeholder="Phone" className="input-field" />
                <input type="text" placeholder="Business Name" className="input-field" />
                <select className="input-field">
                  <option value="">Select practice area</option>
                  <option value="personal-injury">Personal Injury</option>
                  <option value="family-law">Family Law</option>
                  <option value="criminal-defense">Criminal Defense</option>
                  <option value="business-law">Business Law</option>
                </select>
                <textarea placeholder="Message *" rows={5} required className="input-field" />
                <button type="submit" className="btn-primary">Send Message</button>
              </form>
            </div>
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
              <div className="space-y-4 text-gray-600">
                <div>
                  <strong className="text-gray-900">Email:</strong> hello@tulsa-seo.com
                </div>
                <div>
                  <strong className="text-gray-900">Phone:</strong> (555) 123-4567
                </div>
                <div>
                  <strong className="text-gray-900">Address:</strong> 123 Business Street, Suite 100<br />
                  New York, NY 10001
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
