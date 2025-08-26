'use client'

import Header from '../../components/Header';

export default function LeadsPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-4xl mx-auto px-6 py-12">
                  <h2 className="section-title">Get SEO Help</h2>
        <p className="section-subtitle">Submit your SEO inquiry and get connected with the right expert</p>
          <form className="space-y-6">
            <input type="text" placeholder="Your Name" required className="input-field" />
            <input type="email" placeholder="Your Email" required className="input-field" />
                          <textarea placeholder="Describe your SEO needs" rows={5} required className="input-field" />
            <button type="submit" className="btn-primary">Submit Inquiry</button>
          </form>
        </div>
      </main>
    </div>
  );
}
