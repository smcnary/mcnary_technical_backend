// API service for connecting to Symfony backend
const API_BASE_URL = 'http://localhost:8000';

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

export interface ApiResponse<T> {
  '@context': string;
  '@id': string;
  '@type': string;
  totalItems?: number;
  member?: T[];
}

// API service class
export class ApiService {
  private baseUrl: string;

  constructor(baseUrl: string = API_BASE_URL) {
    this.baseUrl = baseUrl;
  }

  // Generic fetch method with error handling
  private async fetchApi<T>(endpoint: string, options?: RequestInit): Promise<T> {
    const url = `${this.baseUrl}${endpoint}`;
    const response = await fetch(url, {
      headers: {
        'Content-Type': 'application/ld+json',
        ...options?.headers,
      },
      ...options,
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.detail || `HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  // Lead Management
  async submitLead(leadData: Omit<Lead, 'id' | 'status' | 'createdAt' | 'updatedAt'>): Promise<Lead> {
    return this.fetchApi<Lead>('/leads', {
      method: 'POST',
      body: JSON.stringify(leadData),
    });
  }

  async getLeads(): Promise<ApiResponse<Lead>> {
    return this.fetchApi<ApiResponse<Lead>>('/leads');
  }

  async getLead(id: string): Promise<Lead> {
    return this.fetchApi<Lead>(`/leads/${id}`);
  }

  // Case Studies
  async getCaseStudies(): Promise<ApiResponse<CaseStudy>> {
    return this.fetchApi<ApiResponse<CaseStudy>>('/case_studies');
  }

  async getCaseStudy(id: string): Promise<CaseStudy> {
    return this.fetchApi<CaseStudy>(`/case_studies/${id}`);
  }

  // FAQs
  async getFaqs(): Promise<ApiResponse<Faq>> {
    return this.fetchApi<ApiResponse<Faq>>('/faqs');
  }

  async getFaq(id: string): Promise<Faq> {
    return this.fetchApi<Faq>(`/faqs/${id}`);
  }

  // Get API entry point to discover available endpoints
  async getApiInfo(): Promise<Record<string, unknown>> {
    return this.fetchApi<Record<string, unknown>>('/');
  }
}

// Export singleton instance
export const apiService = new ApiService();
export default apiService;
