'use client'

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

const Header: React.FC = () => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const pathname = usePathname();

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  const isActive = (path: string) => {
    if (path === '/') {
      return pathname === '/';
    }
    return pathname.startsWith(path);
  };

  return (
    <header className="bg-gradient-to-r from-gray-900 via-blue-900 to-gray-800 sticky top-0 z-50 shadow-lg">
      <div className="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        {/* Brand */}
        <Link 
          href="/"
          className="text-white font-bold text-xl cursor-pointer hover:text-blue-200 transition-all duration-200 flex items-center gap-2"
        >
          <div className="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
            <span className="text-white font-bold text-sm">TS</span>
          </div>
          tulsa-seo.com
        </Link>

        {/* Desktop Menu */}
        <nav className="hidden lg:flex items-center gap-8 text-sm">
          <Link 
            href="/"
            className={`nav-link ${isActive('/') ? 'active' : ''}`}
          >
            Home
          </Link>
          <div className="relative group">
            <button className="nav-link flex items-center gap-1">
              Services 
              <svg className="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            {/* Services Dropdown */}
            <div className="dropdown-menu">
              <div className="py-2">
                <Link 
                  href="/services"
                  className="dropdown-item"
                >
                  SEO Services
                </Link>
                {/* Removed Pricing Plans */}
                <Link 
                  href="/contact"
                  className="dropdown-item"
                >
                  Get Started
                </Link>
              </div>
            </div>
          </div>
          <div className="relative group">
            <button className="nav-link flex items-center gap-1">
              Resources 
              <svg className="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            {/* Resources Dropdown */}
            <div className="dropdown-menu">
              <div className="py-2">
                <Link 
                  href="/blog"
                  className="dropdown-item"
                >
                  Blog
                </Link>
                {/* Removed Case Studies */}
                <Link 
                  href="/faqs"
                  className="dropdown-item"
                >
                  FAQs
                </Link>
              </div>
            </div>
          </div>
          <div className="relative group">
            <button className="nav-link flex items-center gap-1">
              Company 
              <svg className="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            {/* Company Dropdown */}
            <div className="dropdown-menu">
              <div className="py-2">
                <Link 
                  href="/about"
                  className="dropdown-item"
                >
                  About Us
                </Link>
                <Link 
                  href="/contact"
                  className="dropdown-item"
                >
                  Contact
                </Link>
              </div>
            </div>
          </div>
          <Link 
            href="/leads"
            className={`nav-link ${isActive('/leads') ? 'active' : ''}`}
          >
            Get SEO Help
          </Link>
        </nav>

        {/* CTA Buttons */}
        <div className="hidden lg:flex items-center gap-4">
          <Link 
            href="/login"
            className="text-gray-200 hover:text-white font-medium transition-all duration-200 hover:bg-white/10 px-4 py-2 rounded-lg"
          >
            Client Login
          </Link>
          <Link 
            href="/contact"
            className="btn-primary"
          >
            Get Started
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          className="lg:hidden text-white p-2 hover:bg-white/10 rounded-lg transition-all duration-200"
          onClick={toggleMobileMenu}
          aria-label="Toggle mobile menu"
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {isMobileMenuOpen ? (
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            ) : (
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            )}
          </svg>
        </button>
      </div>

      {/* Mobile Menu */}
      {isMobileMenuOpen && (
        <div className="lg:hidden bg-gradient-to-b from-gray-800 to-gray-900 border-t border-gray-700 animate-fade-in">
          <div className="px-6 py-6 space-y-2">
            <Link 
              href="/"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Home
            </Link>
            <Link 
              href="/services"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Services
            </Link>
            {/* Removed Pricing from mobile */}
            <Link 
              href="/leads"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Get SEO Help
            </Link>
            {/* Removed Case Studies from mobile */}
            <Link 
              href="/faqs"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              FAQs
            </Link>
            <Link 
              href="/blog"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Blog
            </Link>
            <Link 
              href="/about"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              About
            </Link>
            <Link 
              href="/contact"
              className="mobile-menu-item"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Contact
            </Link>
            <div className="pt-4 border-t border-gray-700 space-y-3">
              <Link 
                href="/login"
                className="mobile-menu-item w-full text-left"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                Client Login
              </Link>
              {/* Removed Need Account from mobile */}
              <Link 
                href="/contact"
                className="btn-primary w-full text-center block"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                Get Started
              </Link>
            </div>
          </div>
        </div>
      )}
    </header>
  );
};

export default Header;
