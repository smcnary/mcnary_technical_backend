/** @type {import('next').NextConfig} */
const nextConfig = {
  // Enable static export for S3 hosting only in production
  ...(process.env.NODE_ENV === 'production' && {
    output: 'export',
    trailingSlash: true,
    images: {
      unoptimized: true,
      domains: [],
    },
  }),
  // Fix hydration issues
  reactStrictMode: false,
  swcMinify: true,
  // Exclude API routes from static export
  // experimental: {
  //   appDir: true, // This is now default in Next.js 13+
  // },
  // Exclude API routes from build
  pageExtensions: ['tsx', 'ts', 'jsx', 'js'],
  // Remove rewrites for static export
  // async rewrites() {
  //   return [
  //     {
  //       source: '/api/:path*',
  //       destination: 'http://localhost:8000/api/:path*',
  //     },
  //   ];
  // },
}

module.exports = nextConfig
