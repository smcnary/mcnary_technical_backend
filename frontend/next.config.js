/** @type {import('next').NextConfig} */
const nextConfig = {
  // Enable static export for S3 hosting
  output: 'export',
  trailingSlash: true,
  images: {
    unoptimized: true,
    domains: [],
  },
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
