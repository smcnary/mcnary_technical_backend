// API service for connecting to Symfony backend
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';

// TypeScript interfaces matching your backend entities
export interface AuditIntake {
  id?: string;
  contactName?: string;
  contactEmail?: string;
  contactPhone?: string;
  websiteUrl: string;
  subdomains?: string[];
  stagingUrl?: string;
  cms: string;
  cmsVersion?: string;
  hostingProvider?: string;
  techStack?: Record<string, unknown>;
  hasGoogleAnalytics: boolean;
  hasSearchConsole: boolean;
  hasGoogleBusinessProfile: boolean;
  hasTagManager: boolean;
  gaPropertyId?: string;
  gscProperty?: string;
  gbpLocationIds?: string[];
  createdAt?: string;
  updatedAt?: string;
}

export interface AuditSubmission {
  audit: {
    companyName: string;
    website: string;
    industry: string;
    goals: string[];
    competitors: string;
    monthlyBudget: string;
    tier: string;
    notes: string;
  };
}

export interface Lead {
  id: string;
  fullName: string;
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
  message?: string;
  consent?: boolean;
  status: 'new' | 'contacted' | 'qualified' | 'proposal' | 'closed_won' | 'closed_lost';
  utmJson?: any[];
  createdAt?: string;
  updatedAt?: string;
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

export interface AuditRun {
  id: string;
  status: 'DRAFT' | 'QUEUED' | 'RUNNING' | 'COMPLETED' | 'FAILED' | 'CANCELED';
  startedAt?: string;
  finishedAt?: string;
  healthScore?: number;
  pagesCrawled?: number;
  issuesFound?: number;
  createdAt: string;
  websiteUrl: string;
  maxPages?: number;
  includeLighthouse?: boolean;
}

export interface AuditIssue {
  id: string;
  title: string;
  description: string;
  severity: 'P1' | 'P2' | 'P3';
  category: 'TECHNICAL' | 'ON_PAGE' | 'LOCAL' | 'CONTENT';
  affectedPages: number;
  status: 'OPEN' | 'IN_PROGRESS' | 'RESOLVED' | 'IGNORED';
  fixHint: string;
  createdAt: string;
  updatedAt?: string;
  resolvedAt?: string;
  pages?: string[];
}

export interface QuickWin {
  id: string;
  title: string;
  description: string;
  impact: 'HIGH' | 'MEDIUM' | 'LOW';
  effort: 'LOW' | 'MEDIUM' | 'HIGH';
  category: 'TECHNICAL' | 'ON_PAGE' | 'LOCAL' | 'CONTENT';
  affectedPages: number;
  estimatedTime: string;
}

export interface AuditMetrics {
  healthScore: number;
  previousScore?: number;
  scoreChange?: number;
  totalIssues: number;
  criticalIssues: number;
  pagesAnalyzed: number;
  lastAuditDate: string;
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
      credentials: 'include',
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
      return { message: 'Logged out successfully' };
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
      const endpoint = queryString ? `/api/leads?${queryString}` : '/api/leads';
      return this.fetchApi<ApiResponse<Lead>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Lead>>('/api/leads');
  }

  async importLeads(csvData: string, options?: {
    clientId?: string;
    sourceId?: string;
    overwriteExisting?: boolean;
  }): Promise<{
    message: string;
    imported_count: number;
    skipped_count: number;
    total_rows: number;
    errors?: string[];
  }> {
    return this.fetchApi('/api/v1/leads/import', {
      method: 'POST',
      body: JSON.stringify({
        csv_data: csvData,
        client_id: options?.clientId,
        source_id: options?.sourceId,
        overwrite_existing: options?.overwriteExisting || false,
      }),
    });
  }

  async getLead(id: string): Promise<Lead> {
    return this.fetchApi<Lead>(`/api/v1/leads/${id}`);
  }

  // Case Studies
  async getCaseStudies(): Promise<ApiResponse<CaseStudy>> {
    return this.fetchApi<ApiResponse<CaseStudy>>('/api/case_studies');
  }

  async getCaseStudy(id: string): Promise<CaseStudy> {
    return this.fetchApi<CaseStudy>(`/api/case_studies/${id}`);
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
      const endpoint = queryString ? `/api/campaigns?${queryString}` : '/api/campaigns';
      return this.fetchApi<ApiResponse<Campaign>>(endpoint);
    }
    return this.fetchApi<ApiResponse<Campaign>>('/api/campaigns');
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

  // Google Business Profile KPI methods
  async getGbpKpi(clientId: string): Promise<{
    connected: boolean;
    profileId?: string;
    kpi?: {
      views: { total: number; change: number; period: string };
      calls: { total: number; change: number; period: string };
      reviews: { average: number; total: number; change: number; period: string };
      localVisibility: { score: number; change: number; period: string };
      actions: { website_clicks: number; direction_requests: number; period: string };
    };
    lastUpdated?: string;
  }> {
    return this.fetchApi<{
      connected: boolean;
      profileId?: string;
      kpi?: {
        views: { total: number; change: number; period: string };
        calls: { total: number; change: number; period: string };
        reviews: { average: number; total: number; change: number; period: string };
        localVisibility: { score: number; change: number; period: string };
        actions: { website_clicks: number; direction_requests: number; period: string };
      };
      lastUpdated?: string;
    }>(`/api/v1/gbp/kpi/${clientId}`);
  }

  async connectGbp(clientId: string, profileId: string): Promise<{ message: string; profileId: string }> {
    return this.fetchApi<{ message: string; profileId: string }>(`/api/v1/gbp/connect/${clientId}`, {
      method: 'POST',
      body: JSON.stringify({ profileId }),
    });
  }

  async initiateGbpAuth(clientId: string): Promise<void> {
    // Redirect to OAuth flow - this will be handled by the backend
    window.location.href = `${this.baseUrl}/api/v1/gbp/auth/${clientId}`;
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

  // Audit Intake methods
  async createAuditIntake(auditData: Omit<AuditIntake, 'id' | 'createdAt' | 'updatedAt'>): Promise<AuditIntake> {
    const response = await this.fetchApi<AuditIntake>('/api/v1/audits/intakes', {
      method: 'POST',
      body: JSON.stringify(auditData),
    });
    return response;
  }

  async getAuditIntake(id: string): Promise<AuditIntake> {
    const response = await this.fetchApi<AuditIntake>(`/api/v1/audits/intakes/${id}`);
    return response;
  }

  async updateAuditIntake(id: string, auditData: Partial<AuditIntake>): Promise<AuditIntake> {
    const response = await this.fetchApi<AuditIntake>(`/api/v1/audits/intakes/${id}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/merge-patch+json' },
      body: JSON.stringify(auditData),
    });
    return response;
  }

  async submitAuditWizard(submission: AuditSubmission): Promise<{ auditIntake: AuditIntake; user: User; token: string }> {
    // First, try to register the user using the client registration endpoint
    const userResponse = await this.fetchApi<LoginResponse>('/api/v1/clients/register', {
      method: 'POST',
      body: JSON.stringify({
        organization_name: submission.audit.companyName,
        organization_domain: submission.audit.website,
        client_name: submission.audit.companyName,
        client_website: submission.audit.website,
        admin_email: submission.account.email,
        admin_password: submission.account.password,
        admin_first_name: submission.account.firstName,
        admin_last_name: submission.account.lastName,
      }),
    });

    // Then create the audit intake
    const auditIntakeData: Omit<AuditIntake, 'id' | 'createdAt' | 'updatedAt'> = {
      contactName: `${submission.account.firstName} ${submission.account.lastName}`,
      contactEmail: submission.account.email,
      websiteUrl: submission.audit.website,
      cms: 'custom', // Default value
      techStack: {
        industry: submission.audit.industry,
        goals: submission.audit.goals,
        competitors: submission.audit.competitors,
        budget: submission.audit.monthlyBudget,
        tier: submission.audit.tier,
        notes: submission.audit.notes,
        companyName: submission.audit.companyName,
      },
      hasGoogleAnalytics: false,
      hasSearchConsole: false,
      hasGoogleBusinessProfile: false,
      hasTagManager: false,
    };

    const auditIntake = await this.createAuditIntake(auditIntakeData);

    return {
      auditIntake,
      user: userResponse.user,
      token: userResponse.token,
    };
  }

  // Audit Run methods
  async getAuditRuns(params?: Record<string, string | number | boolean>): Promise<ApiResponse<AuditRun>> {
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `/api/v1/audits/runs?${queryString}` : '/api/v1/audits/runs';
      return this.fetchApi<ApiResponse<AuditRun>>(endpoint);
    }
    return this.fetchApi<ApiResponse<AuditRun>>('/api/v1/audits/runs');
  }

  async getAuditRun(id: string): Promise<AuditRun> {
    return this.fetchApi<AuditRun>(`/api/v1/audits/runs/${id}`);
  }

  async startAuditRun(auditData: {
    websiteUrl: string;
    maxPages?: number;
    includeLighthouse?: boolean;
    keywords?: string[];
  }): Promise<AuditRun> {
    return this.fetchApi<AuditRun>('/api/v1/audits/runs', {
      method: 'POST',
      body: JSON.stringify(auditData),
    });
  }

  async getAuditIssues(auditRunId: string, params?: Record<string, string | number | boolean>): Promise<ApiResponse<AuditIssue>> {
    const baseEndpoint = `/api/v1/audits/runs/${auditRunId}/issues`;
    if (params) {
      const stringParams = Object.fromEntries(
        Object.entries(params).map(([key, value]) => [key, String(value)])
      );
      const queryString = new URLSearchParams(stringParams).toString();
      const endpoint = queryString ? `${baseEndpoint}?${queryString}` : baseEndpoint;
      return this.fetchApi<ApiResponse<AuditIssue>>(endpoint);
    }
    return this.fetchApi<ApiResponse<AuditIssue>>(baseEndpoint);
  }

  async updateAuditIssue(issueId: string, updates: Partial<AuditIssue>): Promise<AuditIssue> {
    return this.fetchApi<AuditIssue>(`/api/v1/audits/issues/${issueId}`, {
      method: 'PATCH',
      body: JSON.stringify(updates),
    });
  }

  async getAuditQuickWins(auditRunId: string): Promise<QuickWin[]> {
    return this.fetchApi<QuickWin[]>(`/api/v1/audits/runs/${auditRunId}/quick-wins`);
  }

  async getAuditMetrics(auditRunId: string): Promise<AuditMetrics> {
    return this.fetchApi<AuditMetrics>(`/api/v1/audits/runs/${auditRunId}/metrics`);
  }

  async exportAuditReport(auditRunId: string, format: 'pdf' | 'csv' | 'json' = 'pdf'): Promise<Blob> {
    const response = await fetch(`${this.baseUrl}/api/v1/audits/runs/${auditRunId}/export?format=${format}`, {
      headers: {
        'Authorization': `Bearer ${this.authToken}`,
      },
    });

    if (!response.ok) {
      throw new Error(`Failed to export audit report: ${response.status}`);
    }

    return response.blob();
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
