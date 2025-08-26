// API utility functions for client authentication

export interface LoginResponse {
  token: string;
  user: {
    id: string;
    email: string;
    name?: string;
    first_name?: string;
    last_name?: string;
    role: string;
    status: string;
    client_id?: string;
    tenant_id?: string;
    organization_id: string;
    created_at: string;
    last_login_at?: string;
  };
  client?: {
    id: string;
    name: string;
    slug: string;
    description?: string;
    website?: string;
    phone?: string;
    address?: string;
    city?: string;
    state?: string;
    zip_code?: string;
    country?: string;
    industry: string;
    status: string;
  };
}

export interface RegistrationResponse {
  message: string;
  organization: {
    id: string;
    name: string;
    domain?: string;
  };
  tenant: {
    id: string;
    name: string;
    slug: string;
  };
  client: {
    id: string;
    name: string;
    slug: string;
    status: string;
  };
  admin_user: {
    id: string;
    email: string;
    role: string;
    status: string;
  };
}

export interface ApiError {
  error: string;
  details?: Record<string, string>;
}

/**
 * Login a client user
 */
export async function loginClient(email: string, password: string): Promise<LoginResponse> {
  const response = await fetch('/api/v1/clients/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });

  if (!response.ok) {
    const error: ApiError = await response.json();
    throw new Error(error.error || 'Login failed');
  }

  return response.json();
}

/**
 * Register a new client
 */
export async function registerClient(data: {
  organization_name: string;
  organization_domain?: string;
  client_name: string;
  client_website?: string;
  client_phone?: string;
  client_address?: string;
  client_city?: string;
  client_state?: string;
  client_zip_code?: string;
  admin_email: string;
  admin_password: string;
  admin_first_name?: string;
  admin_last_name?: string;
}): Promise<RegistrationResponse> {
  const response = await fetch('/api/v1/clients/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    const error: ApiError = await response.json();
    throw new Error(error.error || 'Registration failed');
  }

  return response.json();
}

/**
 * Store authentication data in localStorage
 */
export function storeAuthData(loginResponse: LoginResponse): void {
  if (typeof window !== 'undefined') {
    if (loginResponse.token) {
      localStorage.setItem('auth_token', loginResponse.token);
    }
    
    if (loginResponse.user) {
      localStorage.setItem('userData', JSON.stringify(loginResponse.user));
    }
    
    if (loginResponse.client) {
      localStorage.setItem('clientData', JSON.stringify(loginResponse.client));
    }
  }
}

/**
 * Clear authentication data from localStorage
 */
export function clearAuthData(): void {
  if (typeof window !== 'undefined') {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('userData');
    localStorage.removeItem('clientData');
  }
}

/**
 * Get stored authentication token
 */
export function getAuthToken(): string | null {
  return typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;
}

/**
 * Make an authenticated API request
 */
export async function authenticatedFetch(url: string, options: RequestInit = {}): Promise<Response> {
  const token = getAuthToken();
  
  return fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      'Authorization': token ? `Bearer ${token}` : '',
      'Content-Type': 'application/json',
    },
  });
}
