'use client'

import Header from '../../components/common/Header';
import OurServicesStacked from '../../components/features/OurServicesStacked';

export default function ServicesPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <OurServicesStacked className="py-12" />
      </main>
    </div>
  );
}
