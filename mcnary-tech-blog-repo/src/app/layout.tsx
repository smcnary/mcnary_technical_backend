import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import { Header } from '@/components/layout/Header';
import { Footer } from '@/components/layout/Footer';

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  title: {
    default: 'McNary Tech Blog',
    template: '%s | McNary Tech Blog',
  },
  description: 'Technology insights and development tips from the McNary Tech team',
  keywords: ['technology', 'development', 'programming', 'software', 'engineering'],
  authors: [{ name: 'McNary Tech' }],
  creator: 'McNary Tech',
  openGraph: {
    type: 'website',
    locale: 'en_US',
    url: 'https://mcnarytech.com',
    title: 'McNary Tech Blog',
    description: 'Technology insights and development tips',
    siteName: 'McNary Tech Blog',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'McNary Tech Blog',
    description: 'Technology insights and development tips',
    creator: '@mcnarytech',
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-video-preview': -1,
      'max-image-preview': 'large',
      'max-snippet': -1,
    },
  },
  verification: {
    google: 'your-google-verification-code',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <div className="min-h-screen flex flex-col">
          <Header />
          <main className="flex-grow">
            {children}
          </main>
          <Footer />
        </div>
      </body>
    </html>
  );
}
