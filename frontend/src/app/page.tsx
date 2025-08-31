'use client'

import Header from '../components/common/Header';
import Footer from '../components/common/Footer';
import Link from 'next/link';
import HeroTulsaSEO from '../components/features/HeroTulsaSEO';

export default function HomePage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        {/* Hero Section */}
        <HeroTulsaSEO />


      </main>

      <Footer />
    </div>
  );
}
