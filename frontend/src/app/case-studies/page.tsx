'use client'

import Header from '../../components/Header';

export default function CaseStudiesPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <section className="bg-gray-900 text-white py-20">
          <div className="container mx-auto px-6 text-center">
            <h2 className="text-4xl font-extrabold">Case Studies</h2>
            <p className="mt-4 text-lg text-gray-400">
              Explore how our AI-First SEO strategies helped businesses in different industries grow.
            </p>

            <div className="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
              
              {/* Legal */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">Law Firm – Personal Injury</h3>
                <p className="mt-3 text-gray-300">
                  Ranked #1 for &ldquo;Tulsa car accident lawyer&rdquo; → Increased leads by 200% in 6 months.
                </p>
              </div>
              
              {/* Medical */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">Healthcare – Dental Clinic</h3>
                <p className="mt-3 text-gray-300">
                  Helped local dentist dominate Google Maps, driving 75+ new patient calls per month.
                </p>
              </div>
              
              {/* Home Services */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">Home Services – Roofing Company</h3>
                <p className="mt-3 text-gray-300">
                  Achieved top rankings for &ldquo;Tulsa roof repair&rdquo; → $1.2M revenue growth in one season.
                </p>
              </div>
              
              {/* E-Commerce */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">E-Commerce – Fashion Retailer</h3>
                <p className="mt-3 text-gray-300">
                  Optimized product pages & AI keyword clustering → 3x online sales in 9 months.
                </p>
              </div>
              
              {/* Restaurants */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">Restaurant – Local BBQ Chain</h3>
                <p className="mt-3 text-gray-300">
                  Google Business optimization boosted reservations by 60% and doubled catering orders.
                </p>
              </div>

              {/* Real Estate */}
              <div className="bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                <h3 className="text-xl font-semibold">Real Estate – Local Broker</h3>
                <p className="mt-3 text-gray-300">
                  AI-powered content strategy ranked for 50+ local keywords → 40% more qualified leads.
                </p>
              </div>

            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
