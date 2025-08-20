import { NextRequest, NextResponse } from 'next/server';

export async function GET(_request: NextRequest) {
  try {
    // Redirect to Symfony backend's Google OAuth endpoint
    const backendUrl = process.env.BACKEND_URL || 'http://localhost:8000';
    const redirectUrl = `${backendUrl}/api/auth/google`;
    
    return NextResponse.redirect(redirectUrl);
  } catch (error) {
    console.error('Google SSO error:', error);
    return NextResponse.redirect('/login?error=sso_failed');
  }
}
