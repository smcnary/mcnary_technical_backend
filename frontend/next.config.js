/** @type {import('next').NextConfig} */
const nextConfig = {
  // Remove static export to support API routes
  // output: 'export',
  trailingSlash: true,
  images: {
    unoptimized: true,
    domains: [],
  },
  async rewrites() {
    return [
      {
        source: '/api/:path*',
        destination: 'http://localhost:8000/api/:path*',
      },
    ];
  },
}

module.exports = nextConfig
