import React, { useState } from 'react';

interface HeaderProps {
  onNavigate: (tab: string) => void;
  activeTab: string;
  onOpenLogin: () => void;
}

const Header: React.FC<HeaderProps> = ({ onNavigate, activeTab, onOpenLogin }) => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  const handleNavigation = (tab: string) => {
    onNavigate(tab);
    setIsMobileMenuOpen(false); // Close mobile menu when navigating
  };

  return (
    <header className="bg-[#0F1724]">
      <div className="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        {/* Brand */}
        <span 
          className="text-white font-semibold text-lg cursor-pointer hover:text-gray-200 transition-colors"
          onClick={() => handleNavigation('home')}
        >
          CounselRank.legal
        </span>

        {/* Desktop Menu */}
        <nav className="hidden md:flex items-center gap-6 text-sm text-gray-200">
          <button 
            className={`hover:text-white font-medium transition-colors ${activeTab === 'home' ? 'text-white' : ''}`}
            onClick={() => handleNavigation('home')}
          >
            Home
          </button>
          <div className="relative group">
            <button className="flex items-center hover:text-white">
              Services ▾
            </button>
            {/* Services Dropdown */}
            <div className="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <div className="py-2">
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('services')}
                >
                  SEO Services
                </button>
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('pricing')}
                >
                  Pricing Plans
                </button>
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('contact')}
                >
                  Get Started
                </button>
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
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('blog')}
                >
                  Blog
                </button>
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('case-studies')}
                >
                  Case Studies
                </button>
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('faqs')}
                >
                  FAQs
                </button>
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
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('about')}
                >
                  About Us
                </button>
                <button 
                  className="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                  onClick={() => handleNavigation('contact')}
                >
                  Contact
                </button>
              </div>
            </div>
          </div>
        </nav>

        {/* CTAs */}
        <div className="hidden md:flex items-center gap-3">
          <button 
            className="px-4 py-2 rounded-lg border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition"
            onClick={onOpenLogin}
          >
            Client Login
          </button>
          <button 
            className="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
            onClick={() => handleNavigation('contact')}
          >
            Book Demo
          </button>
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
          <nav className="px-6 py-4 space-y-4">
            <button 
              className={`block w-full text-left text-gray-200 hover:text-white font-medium ${activeTab === 'home' ? 'text-white' : ''}`}
              onClick={() => handleNavigation('home')}
            >
              Home
            </button>
            <div>
              <button className="flex items-center justify-between w-full text-gray-200 hover:text-white font-medium">
                Services
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div className="mt-2 ml-4 space-y-2">
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('services')}
                >
                  SEO Services
                </button>
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('pricing')}
                >
                  Pricing Plans
                </button>
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('contact')}
                >
                  Get Started
                </button>
              </div>
            </div>
            <div>
              <button className="flex items-center justify-between w-full text-gray-200 hover:text-white font-medium">
                Resources
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div className="mt-2 ml-4 space-y-2">
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('blog')}
                >
                  Blog
                </button>
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('case-studies')}
                >
                  Case Studies
                </button>
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('faqs')}
                >
                  FAQs
                </button>
              </div>
            </div>
            <div>
              <button className="flex items-center justify-between w-full text-gray-200 hover:text-white font-medium">
                Company
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div className="mt-2 ml-4 space-y-2">
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('about')}
                >
                  About Us
                </button>
                <button 
                  className="block w-full text-left text-gray-300 hover:text-white"
                  onClick={() => handleNavigation('contact')}
                >
                  Contact
                </button>
              </div>
            </div>
            <div className="pt-4 space-y-3">
              <button 
                className="w-full px-4 py-2 rounded-lg border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition"
                onClick={onOpenLogin}
              >
                Client Login
              </button>
              <button 
                className="w-full px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
                onClick={() => handleNavigation('contact')}
              >
                Book Demo
              </button>
            </div>
          </nav>
        </div>
      )}
    </header>
  );
};

export default Header;
