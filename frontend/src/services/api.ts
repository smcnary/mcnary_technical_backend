// API service for connecting to Python backend
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
  zipCode?: string;
  budget?: string;
  timeline?: string;
  notes?: string;
  message?: string;
  consent?: boolean;
  status: 'new_lead' | 'contacted' | 'interview_scheduled' | 'interview_completed' | 'application_received' | 'audit_in_progress' | 'audit_complete' | 'enrolled';
  statusLabel?: string;
  source?: string;
  client?: string;
  utmJson?: any[];
  techStack?: {
    url: string;
    technologies: Array<{
      name: string;
      confidence: number;
      version?: string;
      categories: string[];
      website?: string;
      description?: string;
    }>;
    lastAnalyzed?: string;
    error?: string;
  };
  interviewScheduled?: string;
  followUpDate?: string;
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

export interface Notification {
  id: string;
  title: string;
  message?: string;
  type: 'info' | 'success' | 'warning' | 'error';
  isRead: boolean;
  actionUrl?: string;
  actionLabel?: string;
  createdAt: string;
  readAt?: string;
  metadata?: any;
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
    return this.fetchApi<Lead>('/api/v1/leads/create-lead', {
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
      const response = await this.fetchApi<ApiResponse<Lead>>(endpoint);
      
      // Transform snake_case to camelCase for frontend compatibility
      if (response.data && Array.isArray(response.data)) {
        response.data = response.data.map((lead: any) => this.transformLeadData(lead));
      }
      
      return response;
    }
    const response = await this.fetchApi<ApiResponse<Lead>>('/api/v1/leads');
    
    // Transform snake_case to camelCase for frontend compatibility
    if (response.data && Array.isArray(response.data)) {
      response.data = response.data.map((lead: any) => this.transformLeadData(lead));
    }
    
    return response;
  }

  private transformLeadData(lead: any): Lead {
    return {
      ...lead,
      fullName: lead.full_name || lead.fullName,
      practiceAreas: lead.practice_areas || lead.practiceAreas || [],
      zipCode: lead.zip_code || lead.zipCode,
      createdAt: lead.created_at || lead.createdAt,
      updatedAt: lead.updated_at || lead.updatedAt,
    };
  }

  async importLeadgenData(leads: any[], clientId?: string, sourceId?: string): Promise<any> {
    return this.fetchApi('/api/v1/leads/leadgen-import', {
      method: 'POST',
      body: JSON.stringify({
        leads,
        client_id: clientId,
        source_id: sourceId
      })
    });
  }

  // SEO Tracking API methods
  async getKeywords(clientId?: string, status?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (clientId) params.append('client_id', clientId);
    if (status) params.append('status', status);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/seo/keywords?${params}`);
  }

  async createKeyword(keywordData: any): Promise<any> {
    return this.fetchApi('/api/v1/seo/keywords', {
      method: 'POST',
      body: JSON.stringify(keywordData)
    });
  }

  async updateKeyword(keywordId: string, keywordData: any): Promise<any> {
    return this.fetchApi(`/api/v1/seo/keywords/${keywordId}`, {
      method: 'PUT',
      body: JSON.stringify(keywordData)
    });
  }

  async deleteKeyword(keywordId: string): Promise<void> {
    return this.fetchApi(`/api/v1/seo/keywords/${keywordId}`, {
      method: 'DELETE'
    });
  }

  async getRankings(keywordId?: string, clientId?: string, startDate?: string, endDate?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (keywordId) params.append('keyword_id', keywordId);
    if (clientId) params.append('client_id', clientId);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/seo/rankings?${params}`);
  }

  async createRanking(rankingData: any): Promise<any> {
    return this.fetchApi('/api/v1/seo/rankings', {
      method: 'POST',
      body: JSON.stringify(rankingData)
    });
  }

  async getReviews(clientId?: string, status?: string, source?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (clientId) params.append('client_id', clientId);
    if (status) params.append('status', status);
    if (source) params.append('source', source);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/seo/reviews?${params}`);
  }

  async createReview(reviewData: any): Promise<any> {
    return this.fetchApi('/api/v1/seo/reviews', {
      method: 'POST',
      body: JSON.stringify(reviewData)
    });
  }

  async getCitations(clientId?: string, status?: string, platformType?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (clientId) params.append('client_id', clientId);
    if (status) params.append('status', status);
    if (platformType) params.append('platform_type', platformType);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/seo/citations?${params}`);
  }

  async createCitation(citationData: any): Promise<any> {
    return this.fetchApi('/api/v1/seo/citations', {
      method: 'POST',
      body: JSON.stringify(citationData)
    });
  }

  async getKeywordPerformance(clientId: string, startDate: string, endDate: string): Promise<any> {
    return this.fetchApi(`/api/v1/seo/analytics/keyword-performance/${clientId}?start_date=${startDate}&end_date=${endDate}`);
  }

  async getReviewSummary(clientId: string): Promise<any> {
    return this.fetchApi(`/api/v1/seo/analytics/review-summary/${clientId}`);
  }

  async getCitationSummary(clientId: string): Promise<any> {
    return this.fetchApi(`/api/v1/seo/analytics/citation-summary/${clientId}`);
  }

  // Audit API methods
  async getProjects(clientId?: string, status?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (clientId) params.append('client_id', clientId);
    if (status) params.append('status', status);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/audits/projects?${params}`);
  }

  async createProject(projectData: any): Promise<any> {
    return this.fetchApi('/api/v1/audits/projects', {
      method: 'POST',
      body: JSON.stringify(projectData)
    });
  }

  async updateProject(projectId: string, projectData: any): Promise<any> {
    return this.fetchApi(`/api/v1/audits/projects/${projectId}`, {
      method: 'PUT',
      body: JSON.stringify(projectData)
    });
  }

  async deleteProject(projectId: string): Promise<void> {
    return this.fetchApi(`/api/v1/audits/projects/${projectId}`, {
      method: 'DELETE'
    });
  }

  async getAuditRuns(projectId?: string, state?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (projectId) params.append('project_id', projectId);
    if (state) params.append('state', state);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/audits/audit-runs?${params}`);
  }

  async createAuditRun(auditData: any): Promise<any> {
    return this.fetchApi('/api/v1/audits/audit-runs', {
      method: 'POST',
      body: JSON.stringify(auditData)
    });
  }

  async startAudit(auditRunId: string): Promise<any> {
    return this.fetchApi(`/api/v1/audits/audit-runs/${auditRunId}/start`, {
      method: 'POST'
    });
  }

  async getAuditSummary(auditRunId: string): Promise<any> {
    return this.fetchApi(`/api/v1/audits/audit-runs/${auditRunId}/summary`);
  }

  async getFindings(auditRunId?: string, pageId?: string, severity?: string, category?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (auditRunId) params.append('audit_run_id', auditRunId);
    if (pageId) params.append('page_id', pageId);
    if (severity) params.append('severity', severity);
    if (category) params.append('category', category);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/audits/findings?${params}`);
  }

  async updateFinding(findingId: string, status?: string, assignedTo?: string, notes?: string): Promise<any> {
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (assignedTo) params.append('assigned_to', assignedTo);
    if (notes) params.append('notes', notes);
    
    return this.fetchApi(`/api/v1/audits/findings/${findingId}?${params}`, {
      method: 'PUT'
    });
  }

  async getPages(auditRunId?: string, skip = 0, limit = 100): Promise<any> {
    const params = new URLSearchParams();
    if (auditRunId) params.append('audit_run_id', auditRunId);
    params.append('skip', skip.toString());
    params.append('limit', limit.toString());
    
    return this.fetchApi(`/api/v1/audits/pages?${params}`);
  }

  async getAuditReport(auditRunId: string, format: 'html' | 'csv' | 'json' = 'html'): Promise<any> {
    if (format === 'html') {
      return this.fetchApi(`/api/v1/audits/audit-runs/${auditRunId}/report`, {
        headers: {
          'Accept': 'text/html'
        }
      });
    } else if (format === 'csv') {
      return this.fetchApi(`/api/v1/audits/audit-runs/${auditRunId}/report.csv`, {
        headers: {
          'Accept': 'text/csv'
        }
      });
    } else {
      return this.fetchApi(`/api/v1/audits/audit-runs/${auditRunId}/report.json`);
    }
  }

  async getLeadEvents(leadId: string): Promise<any[]> {
    const response = await this.fetchApi(`/api/v1/leads/${leadId}/events`);
    return response.events || [];
  }

  async getLeadStatistics(leadId: string): Promise<any> {
    const response = await this.fetchApi(`/api/v1/leads/${leadId}/statistics`);
    return response.statistics;
  }

  async createLeadEvent(leadId: string, eventData: {
    type: string;
    direction?: string;
    duration?: number;
    notes?: string;
    outcome?: string;
    next_action?: string;
  }): Promise<any> {
    const response = await this.fetchApi(`/api/v1/leads/${leadId}/events`, {
      method: 'POST',
      body: JSON.stringify(eventData)
    });
    return response.event;
  }

  // LEADGEN EXECUTION (Admin Only)
  async executeLeadgenCampaign(config: any): Promise<any> {
    return this.fetchApi('/api/v1/admin/leadgen/execute', {
      method: 'POST',
      body: JSON.stringify(config)
    });
  }

  async getLeadgenVerticals(): Promise<any> {
    const response = await this.fetchApi('/api/v1/admin/leadgen/verticals');
    return response.verticals;
  }

  async getLeadgenSources(): Promise<any> {
    const response = await this.fetchApi('/api/v1/admin/leadgen/sources');
    return response.sources;
  }

  async getLeadgenCampaignStatus(campaignId: string): Promise<any> {
    const response = await this.fetchApi(`/api/v1/admin/leadgen/status/${campaignId}`);
    return response.status;
  }

  async getLeadgenTemplate(): Promise<any> {
    const response = await this.fetchApi('/api/v1/admin/leadgen/template');
    return response.template;
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

  async updateLead(id: string, leadData: Partial<Lead>): Promise<Lead> {
    return this.fetchApi<Lead>(`/api/v1/leads/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(leadData),
    });
  }

  // Lead Notes
  async getLeadNotes(id: string): Promise<{ notes: string }> {
    return this.fetchApi<{ notes: string }>(`/api/v1/leads/${id}/notes`);
  }

  async saveLeadNotes(id: string, notes: string): Promise<{ success: boolean }> {
    return this.fetchApi<{ success: boolean }>(`/api/v1/leads/${id}/notes`, {
      method: 'POST',
      body: JSON.stringify({ notes }),
    });
  }

  // Technology Stack Detection
  async analyzeLeadTechStack(leadId: string): Promise<Lead['techStack']> {
    const response = await this.fetchApi<{ techStack: Lead['techStack'] }>(`/api/v1/leads/${leadId}/tech-stack`, {
      method: 'POST',
    });
    return response.techStack;
  }

  async getLeadTechStack(leadId: string): Promise<Lead['techStack']> {
    const response = await this.fetchApi<{ techStack: Lead['techStack'] }>(`/api/v1/leads/${leadId}/tech-stack`);
    return response.techStack;
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
  async getCmsPages(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Page>> {
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

  // NOTIFICATIONS API
  async getNotifications(params?: Record<string, string | number | boolean>): Promise<ApiResponse<Notification>> {
    const queryString = params ? new URLSearchParams(
      Object.entries(params).map(([key, value]) => [key, String(value)])
    ).toString() : '';
    
    const endpoint = queryString ? `/api/v1/notifications?${queryString}` : '/api/v1/notifications';
    return this.fetchApi<ApiResponse<Notification>>(endpoint);
  }

  async getNotification(id: string): Promise<Notification> {
    return this.fetchApi<Notification>(`/api/v1/notifications/${id}`);
  }

  async markNotificationAsRead(id: string): Promise<void> {
    await this.fetchApi(`/api/v1/notifications/${id}/read`, {
      method: 'PATCH',
    });
  }

  async markNotificationAsUnread(id: string): Promise<void> {
    await this.fetchApi(`/api/v1/notifications/${id}/unread`, {
      method: 'PATCH',
    });
  }

  async markAllNotificationsAsRead(): Promise<void> {
    await this.fetchApi('/api/v1/notifications/mark-all-read', {
      method: 'PATCH',
    });
  }

  async deleteNotification(id: string): Promise<void> {
    await this.fetchApi(`/api/v1/notifications/${id}`, {
      method: 'DELETE',
    });
  }

  async getNotificationCount(): Promise<{ unread_count: number; total_count: number }> {
    return this.fetchApi<{ unread_count: number; total_count: number }>('/api/v1/notifications/count');
  }

  // GOOGLE SHEETS IMPORT API
  async importLeadsFromGoogleSheets(data: {
    spreadsheet_url: string;
    range?: string;
    client_id?: string;
    source_id?: string;
    overwrite_existing?: boolean;
  }): Promise<{
    message: string;
    imported: number;
    updated: number;
    skipped: number;
    total_processed: number;
    errors?: string[];
  }> {
    return this.fetchApi('/api/v1/leads/google-sheets-import', {
      method: 'POST',
      body: JSON.stringify(data),
    });
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
