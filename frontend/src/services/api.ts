// API service for connecting to Symfony backend
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';

// TypeScript interfaces matching your backend entities
export interface Lead {
  id: string;
  name: string;
  email: string;
  phone?: string;
  firm?: string;
  website?: string;
  practiceAreas: string[];
  city?: string;
  state?: string;
  budget?: string;
  timeline?: string;
  notes?: string;
  consent: boolean;
  status: 'pending' | 'contacted' | 'qualified' | 'disqualified';
  createdAt: string;
  updatedAt: string;
}

export interface CaseStudy {
  id: string;
  title: string;
  slug: string;
  summary?: string;
  metricsJson: Record<string, unknown>;
  heroImage?: string;
  practiceArea?: string;
  isActive: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
}

export interface Faq {
  id: string;
  question: string;
  answer: string;
  category?: string;
  sortOrder?: number;
  isActive: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
}

export interface User {
  id: string;
  username?: string;
  email: string;
  displayName?: string;
  name?: string;
  firstName?: string;
  lastName?: string;
  avatar?: string;
  roles: string[];
  clientId?: string;
  tenantId?: string;
  status?: string;
  lastLoginAt?: string;
  createdAt: string;
  updatedAt: string;
}

export interface Client {
  id: string;
  name: string;
  slug?: string;
  description?: string;
  website?: string;
  phone?: string;
  address?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  status?: string;
  tenantId?: string;
  metadata?: Record<string, unknown>;
  googleBusinessProfile?: {
    profileId?: string;
    rating?: number;
    reviewsCount?: number;
  };
  googleSearchConsole?: {
    property?: string;
    verificationStatus?: string;
  };
  googleAnalytics?: {
    propertyId?: string;
    trackingId?: string;
  };
  createdAt: string;
  updatedAt: string;
}

export interface Campaign {
  id: string;
  name: string;
  description?: string;
  type: string;
  status: string;
  clientId: string;
  startDate?: string;
  endDate?: string;
  budget?: number;
  goals?: string[];
  metrics?: string[];
  createdAt: string;
  updatedAt: string;
}

export interface Package {
  id: string;
  name: string;
  description?: string;
  price?: number;
  billingCycle?: string;
  features?: string[];
  isPopular?: boolean;
  sortOrder?: number;
  clientId?: string;
  createdAt: string;
  updatedAt: string;
}

export interface Page {
  id: string;
  title: string;
  slug: string;
  content?: string;
  type?: string;
  status?: string;
  sortOrder?: number;
  publishedAt?: string;
  createdAt: string;
  updatedAt: string;
}

export interface MediaAsset {
  id: string;
  filename: string;
  originalName: string;
  mimeType: string;
  fileSize: number;
  type?: string;
  status?: string;
  clientId?: string;
  url?: string;
  createdAt: string;
  updatedAt: string;
}

export interface ApiResponse<T> {
  data?: T[];
  pagination?: {
    page: number;
    per_page: number;
    total: number;
    pages: number;
  };
  '@context'?: string;
  '@id'?: string;
  '@type'?: string;
  'hydra:totalItems'?: number;
  'hydra:member'?: T[];
  totalItems?: number;
  member?: T[];
}

export interface LoginResponse {
  token: string;
  user: User;
}

export interface ApiError {
  error: string;
  details?: Record<string, string>;
  message?: string;
}

// API service class
export class ApiService {
  private baseUrl: string;
  private authToken: string | null = null;

  constructor(baseUrl: string = API_BASE_URL) {
    this.baseUrl = baseUrl;
    // Load token from localStorage if available
    this.authToken = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;
  }

  // Set authentication token
  setAuthToken(token: string): void {
    this.authToken = token;
    if (typeof window !== 'undefined') {
      localStorage.setItem('auth_token', token);
    }
  }

  // Clear authentication token
  clearAuthToken(): void {
    this.authToken = null;
    if (typeof window !== 'undefined') {
      localStorage.removeItem('auth_token');
    }
  }

  // Get current auth token
  getAuthToken(): string | null {
    return this.authToken;
  }

  // Generic fetch method with error handling
  private async fetchApi<T>(endpoint: string, options?: RequestInit): Promise<T> {
    const url = `${this.baseUrl}${endpoint}`;
    
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      ...(options?.headers as Record<string, string> || {}),
    };

    // Add auth token if available
    if (this.authToken) {
      headers['Authorization'] = `Bearer ${this.authToken}`;
    }

    const response = await fetch(url, {
      headers,
      ...options,
    });

    if (!response.ok) {
      if (response.status === 401) {
        // Unauthorized - clear token and redirect to login
        this.clearAuthToken();
        throw new Error('Authentication required');
      }
      
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.error || errorData.message || errorData.detail || `HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  // Authentication
  async login(email: string, password: string): Promise<LoginResponse> {
    const response = await this.fetchApi<LoginResponse>('/api/v1/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });
    
    if (response.token) {
      this.setAuthToken(response.token);
    }
    
    return response;
  }

  async logout(): Promise<{ message: string }> {
    try {
      const response = await this.fetchApi<{ message: string }>('/api/v1/auth/logout', {
        method: 'POST',
      });
      this.clearAuthToken();
      return response;
    } catch (error) {
      // Even if logout fails, clear local token
      this.clearAuthToken();
      throw error;
    }
  }

  async refreshToken(token: string): Promise<{ token: string }> {
    return this.fetchApi<{ token: string }>('/api/v1/auth/refresh', {
      method: 'POST',
      body: JSON.stringify({ token }),
    });
  }

  async getCurrentUser(): Promise<User> {
    return this.fetchApi<User>('/api/v1/me');
  }

  // Lead Management
  async submitLead(leadData: Omit<Lead, 'id' | 'status' | 'createdAt' | 'updatedAt'>): Promise<Lead> {
    return this.fetchApi<Lead>('/api/v1/leads', {
      method: 'POST',
      body: JSON.stringify(leadData),
    });
  }

  async getLeads(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Lead>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/leads?${queryString}` : '/api/v1/leads';
      return this.fetchApi<ApiResponse<Lead>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Lead>>('/api/v1/leads');
  }

  async getLead(id: string): Promise<Lead> {
    return this.fetchApi<Lead>(`/api/v1/leads/${id}`);
  }

  // Case Studies
  async getCaseStudies(): Promise<ApiResponse<CaseStudy>> {
    return this.fetchApi<ApiResponse<CaseStudy>>('/api/v1/case-studies');
  }

  async getCaseStudy(id: string): Promise<CaseStudy> {
    return this.fetchApi<CaseStudy>(`/api/v1/case-studies/${id}`);
  }

  // FAQs
  async getFaqs(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Faq>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/faqs?${queryString}` : '/api/v1/faqs';
      return this.fetchApi<ApiResponse<Faq>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Faq>>('/api/v1/faqs');
  }

  async getFaq(id: string): Promise<Faq> {
    return this.fetchApi<Faq>(`/api/v1/faqs/${id}`);
  }

  // Pages
  async getPages(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Page>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/pages?${queryString}` : '/api/v1/pages';
      return this.fetchApi<ApiResponse<Page>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Page>>('/api/v1/pages');
  }

  async getPage(slug: string): Promise<Page> {
    const response = await this.fetchApi<ApiResponse<Page>>(`/api/v1/pages?slug=${slug}`);
    const page = response.data?.[0] || response['hydra:member']?.[0] || response.member?.[0];
    if (!page) {
      throw new Error(`Page with slug '${slug}' not found`);
    }
    return page;
  }

  async getPageById(id: string): Promise<Page> {
    return this.fetchApi<Page>(`/api/v1/pages/${id}`);
  }

  // Media Assets
  async getMediaAssets(params?: Record<string, string | number | boolean>): Promise<ApiResponse<MediaAsset>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/media-assets?${queryString}` : '/api/v1/media-assets';
      return this.fetchApi<ApiResponse<MediaAsset>>(endpoint);
    }
    return this.fetchApi<ApiResponse<MediaAsset>>('/api/v1/media-assets');
  }

  async getMediaAsset(id: string): Promise<MediaAsset> {
    return this.fetchApi<MediaAsset>(`/api/v1/media-assets/${id}`);
  }

  // Packages
  async getPackages(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Package>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/packages?${queryString}` : '/api/v1/packages';
      return this.fetchApi<ApiResponse<Package>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Package>>('/api/v1/packages');
  }

  async getPackage(id: string): Promise<Package> {
    return this.fetchApi<Package>(`/api/v1/packages/${id}`);
  }

  // Campaigns
  async getCampaigns(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Campaign>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/campaigns?${queryString}` : '/api/v1/campaigns';
      return this.fetchApi<ApiResponse<Campaign>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Campaign>>('/api/v1/campaigns');
  }

  async createCampaign(campaignData: Omit<Campaign, 'id' | 'createdAt' | 'updatedAt'>): Promise<Campaign> {
    return this.fetchApi<Campaign>('/api/v1/campaigns', {
      method: 'POST',
      body: JSON.stringify(campaignData),
    });
  }

  async updateCampaign(id: string, campaignData: Partial<Campaign>): Promise<Campaign> {
    return this.fetchApi<Campaign>(`/api/v1/campaigns/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(campaignData),
    });
  }

  // Users (Admin only)
  async getUsers(params?: Record<string, string | number | boolean>): Promise<ApiResponse<User>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/users?${queryString}` : '/api/v1/users';
      return this.fetchApi<ApiResponse<User>>(endpoint);
    }
    return this.fetchApi<ApiResponse<User>>('/api/v1/users');
  }

  async createUser(userData: Omit<User, 'id' | 'createdAt' | 'updatedAt'>): Promise<User> {
    return this.fetchApi<User>('/api/v1/users', {
      method: 'POST',
      body: JSON.stringify(userData),
    });
  }

  async updateUser(id: string, userData: Partial<User>): Promise<User> {
    return this.fetchApi<User>(`/api/v1/users/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(userData),
    });
  }

  // Clients
  async getClients(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Client>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/clients?${queryString}` : '/api/v1/clients';
      return this.fetchApi<ApiResponse<Client>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Client>>('/api/v1/clients');
  }

  async getClient(id: string): Promise<Client> {
    return this.fetchApi<Client>(`/api/v1/clients/${id}`);
  }

  async createClient(clientData: Omit<Client, 'id' | 'createdAt' | 'updatedAt'>): Promise<Client> {
    return this.fetchApi<Client>('/api/v1/clients', {
      method: 'POST',
      body: JSON.stringify(clientData),
    });
  }

  async updateClient(id: string, clientData: Partial<Client>): Promise<Client> {
    return this.fetchApi<Client>(`/api/v1/clients/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(clientData),
    });
  }

  // Get API entry point to discover available endpoints
  async getApiInfo(): Promise<Record<string, unknown>> {
    return this.fetchApi<Record<string, unknown>>('/api');
  }

  // Health check
  async healthCheck(): Promise<{ status: string }> {
    try {
      await this.fetchApi('/api');
      return { status: 'healthy' };
    } catch (error) {
      return { status: 'unhealthy' };
    }
  }

  // Check if user is authenticated
  isAuthenticated(): boolean {
    return !!this.authToken;
  }

  // Get user role from token (basic implementation)
  getUserRole(): string | null {
    if (!this.authToken) return null;
    
    try {
      // Basic JWT payload extraction (for client-side use only)
      const payload = JSON.parse(atob(this.authToken.split('.')[1]));
      return payload.roles?.[0] || null;
    } catch (error) {
      return null;
    }
  }
}

// Export singleton instance
export const apiService = new ApiService();
export default apiService;
