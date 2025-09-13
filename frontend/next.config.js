/** @type {import('next').NextConfig} */
const nextConfig = {
  // Disable ESLint during development to avoid blocking the server
  eslint: {
    ignoreDuringBuilds: true,
  },
  // Enable static export for S3 hosting only in production
  ...(process.env.NODE_ENV === 'production' && {
    output: 'export',
    trailingSlash: true,
    images: {
      unoptimized: true,
      domains: [],
    },
  }),
  // Exclude API routes from build
  pageExtensions: ['tsx', 'ts', 'jsx', 'js'],
  // Ensure proper hydration in development
  reactStrictMode: false,
  swcMinify: false,
  // Disable experimental features that might cause hydration issues
  // experimental: {
  //   optimizePackageImports: ['lucide-react', '@radix-ui/react-icons'],
  // },
}

module.exports = nextConfig
