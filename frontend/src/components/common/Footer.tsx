import Link from 'next/link';

export default function Footer() {
  return (
    <footer className="relative isolate bg-gradient-to-b from-[#0c0a17] to-black text-white">
      {/* soft background glow */}
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute left-[10%] top-10 h-64 w-64 rounded-full bg-indigo-600/10 blur-2xl" />
        <div className="absolute right-[12%] bottom-10 h-48 w-48 rounded-full bg-emerald-500/10 blur-2xl" />
      </div>

      <div className="max-w-7xl mx-auto px-6 py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Company Info */}
          <div className="lg:col-span-2">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <span className="text-white font-bold text-lg">TS</span>
              </div>
              <span className="text-xl font-bold text-white">tulsa-seo.com</span>
            </div>
            <p className="text-white/70 mb-6 max-w-md leading-relaxed">
              We help businesses dominate Google search with local + AI-first SEO strategies that drive real results and attract more customers.
            </p>
            <div className="flex space-x-4">
              <a href="#" className="group p-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 transition-all duration-200 hover:border-indigo-500/30">
                <span className="sr-only">LinkedIn</span>
                <svg className="h-5 w-5 text-white/70 group-hover:text-indigo-300 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.047-1.852-3.047-1.853 0-2.136 1.445-2.136 2.939v5.677H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
              </a>
              <a href="#" className="group p-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 transition-all duration-200 hover:border-emerald-500/30">
                <span className="sr-only">Twitter</span>
                <svg className="h-5 w-5 text-white/70 group-hover:text-emerald-300 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
              </a>
            </div>
          </div>

          {/* Services */}
          <div>
            <h3 className="text-lg font-semibold mb-4 text-white">Services</h3>
            <ul className="space-y-3">
              <li>
                <Link href="/services" className="text-white/70 hover:text-indigo-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  SEO Services
                </Link>
              </li>
              <li>
                <Link href="/leads" className="text-white/70 hover:text-indigo-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  Get SEO Help
                </Link>
              </li>
            </ul>
          </div>

          {/* Company */}
          <div>
            <h3 className="text-lg font-semibold mb-4 text-white">Company</h3>
            <ul className="space-y-3">
              <li>
                <Link href="/about" className="text-white/70 hover:text-emerald-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/blog" className="text-white/70 hover:text-emerald-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  Blog
                </Link>
              </li>
              <li>
                <Link href="/faqs" className="text-white/70 hover:text-emerald-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  FAQs
                </Link>
              </li>
              <li>
                <Link href="/contact" className="text-white/70 hover:text-emerald-300 transition-colors duration-200 hover:translate-x-1 inline-block">
                  Contact
                </Link>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom Section */}
        <div className="border-t border-white/10 mt-12 pt-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <p className="text-white/60 text-sm">
              © 2024 McNary Technical LLC. All rights reserved.
            </p>
            <div className="flex space-x-6 mt-4 md:mt-0">
              <Link href="/privacy" className="text-white/60 hover:text-indigo-300 text-sm transition-colors duration-200">
                Privacy Policy
              </Link>
              <Link href="/terms" className="text-white/60 hover:text-indigo-300 text-sm transition-colors duration-200">
                Terms of Service
              </Link>
              <Link href="/cookies" className="text-white/60 hover:text-indigo-300 text-sm transition-colors duration-200">
                Cookie Policy
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* subtle top separator to blend with adjoining sections */}
      <div className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />
    </footer>
  );
}
