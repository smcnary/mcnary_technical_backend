'use client'

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

interface HeaderProps {
  onOpenLogin: () => void;
}

const Header: React.FC<HeaderProps> = ({ onOpenLogin }) => {
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
    <header className="bg-[#0F1724]">
      <div className="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        {/* Brand */}
        <Link 
          href="/"
          className="text-white font-semibold text-lg cursor-pointer hover:text-gray-200 transition-colors"
        >
          CounselRank.legal
        </Link>

        {/* Desktop Menu */}
        <nav className="hidden md:flex items-center gap-6 text-sm text-gray-200">
          <Link 
            href="/"
            className={`hover:text-white font-medium transition-colors ${isActive('/') ? 'text-white' : ''}`}
          >
            Home
          </Link>
          <div className="relative group">
            <button className="flex items-center hover:text-white">
              Services ▾
            </button>
            {/* Services Dropdown */}
            <div className="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <div className="py-2">
                <Link 
                  href="/services"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  SEO Services
                </Link>
                <Link 
                  href="/pricing"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  Pricing Plans
                </Link>
                <Link 
                  href="/contact"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  Get Started
                </Link>
              </div>
            </div>
          </div>
          <div className="relative group">
            <button className="flex items-center hover:text-white">
              Resources ▾
            </button>
            {/* Resources Dropdown */}
            <div className="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <div className="py-2">
                <Link 
                  href="/blog"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  Blog
                </Link>
                <Link 
                  href="/case-studies"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  Case Studies
                </Link>
                <Link 
                  href="/faqs"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  FAQs
                </Link>
              </div>
            </div>
          </div>
          <div className="relative group">
            <button className="flex items-center hover:text-white">
              Company ▾
            </button>
            {/* Company Dropdown */}
            <div className="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <div className="py-2">
                <Link 
                  href="/about"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  About Us
                </Link>
                <Link 
                  href="/contact"
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                >
                  Contact
                </Link>
              </div>
            </div>
          </div>
          <Link 
            href="/leads"
            className={`hover:text-white font-medium transition-colors ${isActive('/leads') ? 'text-white' : ''}`}
          >
            Get Legal Help
          </Link>
        </nav>

        {/* CTA Buttons */}
        <div className="hidden md:flex items-center gap-4">
          <button 
            onClick={onOpenLogin}
            className="text-gray-200 hover:text-white font-medium transition-colors"
          >
            Client Login
          </button>
          <Link 
            href="/contact"
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Get Started
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          className="md:hidden text-white p-2"
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
        <div className="md:hidden bg-[#0F1724] border-t border-gray-700">
          <div className="px-6 py-4 space-y-4">
            <Link 
              href="/"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Home
            </Link>
            <Link 
              href="/services"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Services
            </Link>
            <Link 
              href="/pricing"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Pricing
            </Link>
            <Link 
              href="/leads"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Get Legal Help
            </Link>
            <Link 
              href="/case-studies"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Case Studies
            </Link>
            <Link 
              href="/faqs"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              FAQs
            </Link>
            <Link 
              href="/blog"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Blog
            </Link>
            <Link 
              href="/about"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              About
            </Link>
            <Link 
              href="/contact"
              className="block text-gray-200 hover:text-white font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Contact
            </Link>
            <div className="pt-4 border-t border-gray-700">
              <button 
                onClick={() => {
                  onOpenLogin();
                  setIsMobileMenuOpen(false);
                }}
                className="block w-full text-left text-gray-200 hover:text-white font-medium mb-2"
              >
                Client Login
              </button>
              <Link 
                href="/contact"
                className="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-center"
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
