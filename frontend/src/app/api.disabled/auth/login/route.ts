import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    
    // Forward the request to your Symfony backend
    const response = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000'}/api/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(body),
    });

    const data = await response.json();

    if (!response.ok) {
      return NextResponse.json(
        { message: data.message || 'Login failed' },
        { status: response.status }
      );
    }

    // Set cookies if the backend returns them
    const responseHeaders = new Headers();
    if (data.token) {
      responseHeaders.set('Set-Cookie', `auth_token=${data.token}; HttpOnly; Secure; SameSite=Strict; Path=/`);
    }

    // Return success response
    return NextResponse.json(data, { 
      status: 200,
      headers: responseHeaders
    });
  } catch (error) {
    console.error('Login error:', error);
    return NextResponse.json(
      { message: 'Internal server error' },
      { status: 500 }
    );
  }
}
