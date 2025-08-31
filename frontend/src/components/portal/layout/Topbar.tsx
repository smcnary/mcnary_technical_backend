"use client";

import Link from "next/link";

export default function Topbar() {
  return (
    <div className="h-16 border-b border-gray-100 px-6 flex items-center justify-between bg-white shadow-sm">
      {/* Logo and Brand */}
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 bg-black rounded-full flex items-center justify-center">
          <span className="text-white font-bold text-lg">TS</span>
        </div>
        <span className="text-xl font-semibold text-gray-900">tulsa-seo.com</span>
      </div>
      
      {/* Navigation Links */}
      <div className="hidden md:flex items-center gap-8">
        <Link 
          href="/" 
          className="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium"
        >
          Home
        </Link>
        <Link 
          href="/services/audit" 
          className="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium"
        >
          Get an Audit
        </Link>
        <Link 
          href="/client-login" 
          className="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium"
        >
          Client Dashboard
        </Link>
      </div>
    </div>
  );
}


