import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

export function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl
  const isPortal = pathname.startsWith('/client') || pathname.startsWith('/admin') || pathname.startsWith('/seo-clients')
  if (!isPortal) return NextResponse.next()

  // For now, let the client-side authentication handle access control
  // since we're using localStorage instead of cookies
  // The ProtectedRoute component will handle the actual access control
  return NextResponse.next()
}

export const config = {
  matcher: ['/client/:path*', '/admin/:path*', '/seo-clients/:path*'],
}