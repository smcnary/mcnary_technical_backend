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
  isActive: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
}

export interface User {
  id: string;
  username: string;
  email: string;
  displayName: string;
  roles: string[];
  clientId?: string;
  createdAt: string;
  updatedAt: string;
}

export interface Client {
  id: string;
  name: string;
  email: string;
  phone?: string;
  address?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  website?: string;
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

export interface ApiResponse<T> {
  '@context'?: string;
  '@id'?: string;
  '@type'?: string;
  'hydra:totalItems'?: number;
  'hydra:member'?: T[];
  totalItems?: number;
  member?: T[];
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
      throw new Error(errorData.detail || errorData.message || `HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  // Authentication
  async login(username: string, password: string): Promise<{ token: string; user: User }> {
    const response = await this.fetchApi<{ token: string; user: User }>('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify({ username, password }),
    });
    
    if (response.token) {
      this.setAuthToken(response.token);
    }
    
    return response;
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
  async getPage(slug: string): Promise<Record<string, unknown>> {
    return this.fetchApi<Record<string, unknown>>(`/api/v1/pages?slug=${slug}`);
  }

  // Media Assets
  async getMediaAsset(id: string): Promise<Record<string, unknown>> {
    return this.fetchApi<Record<string, unknown>>(`/api/v1/media-assets/${id}`);
  }

  // Packages
  async getPackages(): Promise<ApiResponse<Record<string, unknown>>> {
    return this.fetchApi<ApiResponse<Record<string, unknown>>>('/api/v1/packages');
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
}

// Export singleton instance
export const apiService = new ApiService();
export default apiService;
