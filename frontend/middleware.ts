import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

export function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl
  const isPortal = pathname.startsWith('/client') || pathname.startsWith('/admin')
  if (!isPortal) return NextResponse.next()

  const token = req.cookies.get('auth')?.value
  if (!token) {
    const url = new URL('/login', req.url)
    url.searchParams.set('redirect', pathname)
    return NextResponse.redirect(url)
  }

  const role = req.cookies.get('role')?.value

  if (pathname.startsWith('/admin') && role !== 'ROLE_ADMIN') {
    return NextResponse.redirect(new URL('/client', req.url))
  }

  if (pathname.startsWith('/client') && role !== 'ROLE_CLIENT' && role !== 'ROLE_ADMIN') {
    return NextResponse.redirect(new URL('/admin', req.url))
  }

  return NextResponse.next()
}

export const config = {
  matcher: ['/client/:path*', '/admin/:path*'],
}
