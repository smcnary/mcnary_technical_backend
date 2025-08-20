import { NextRequest, NextResponse } from 'next/server';

export async function GET(_request: NextRequest) {
  try {
    // Redirect to Symfony backend's Microsoft OAuth endpoint
    const backendUrl = process.env.BACKEND_URL || 'http://localhost:8000';
    const redirectUrl = `${backendUrl}/api/auth/microsoft`;
    
    return NextResponse.redirect(redirectUrl);
  } catch (error) {
    console.error('Microsoft SSO error:', error);
    return NextResponse.redirect('/login?error=sso_failed');
  }
}
