# 🗄️ CounselRank.legal Frontend Database Integration Guide

This guide explains how the React frontend integrates with the Symfony backend database through API calls, data management, and state synchronization.

## 📋 Overview

The frontend doesn't directly connect to the database. Instead, it communicates with the Symfony backend API, which handles all database operations. This architecture provides:

- **Security**: Database credentials are never exposed to the client
- **Scalability**: Backend can handle multiple frontend instances
- **Maintainability**: Database logic is centralized in the backend
- **Performance**: Backend can implement caching and optimization

## 🔌 API Service Layer

### 1. Base API Configuration

Create `src/services/api.ts`:

```typescript
import axios, { AxiosInstance, AxiosResponse } from 'axios';

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    // Request interceptor for authentication
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor for error handling
    this.api.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Handle unauthorized access
          localStorage.removeItem('auth_token');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }

  // Generic CRUD methods
  async get<T>(endpoint: string): Promise<T> {
    const response = await this.api.get<T>(endpoint);
    return response.data;
  }

  async post<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.post<T>(endpoint, data);
    return response.data;
  }

  async put<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.put<T>(endpoint, data);
    return response.data;
  }

  async delete<T>(endpoint: string): Promise<T> {
    const response = await this.api.delete<T>(endpoint);
    return response.data;
  }
}

export const apiService = new ApiService();
```

### 2. Entity-Specific Services

Create `src/services/leads.ts`:

```typescript
import { apiService } from './api';

export interface Lead {
  id: string;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  status: 'new' | 'contacted' | 'qualified' | 'converted';
  createdAt: string;
  updatedAt: string;
}

export interface CreateLeadRequest {
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
}

export class LeadService {
  static async getAll(): Promise<Lead[]> {
    return apiService.get<Lead[]>('/leads');
  }

  static async getById(id: string): Promise<Lead> {
    return apiService.get<Lead>(`/leads/${id}`);
  }

  static async create(lead: CreateLeadRequest): Promise<Lead> {
    return apiService.post<Lead>('/leads', lead);
  }

  static async update(id: string, lead: Partial<Lead>): Promise<Lead> {
    return apiService.put<Lead>(`/leads/${id}`, lead);
  }

  static async delete(id: string): Promise<void> {
    return apiService.delete<void>(`/leads/${id}`);
  }
}
```

Create `src/services/caseStudies.ts`:

```typescript
import { apiService } from './api';

export interface CaseStudy {
  id: string;
  title: string;
  description: string;
  content: string;
  imageUrl?: string;
  category: string;
  tags: string[];
  publishedAt: string;
  createdAt: string;
  updatedAt: string;
}

export interface CaseStudyFilters {
  category?: string;
  tags?: string[];
  search?: string;
  page?: number;
  limit?: number;
}

export class CaseStudyService {
  static async getAll(filters?: CaseStudyFilters): Promise<CaseStudy[]> {
    const params = new URLSearchParams();
    if (filters?.category) params.append('category', filters.category);
    if (filters?.tags) params.append('tags', filters.tags.join(','));
    if (filters?.search) params.append('search', filters.search);
    if (filters?.page) params.append('page', filters.page.toString());
    if (filters?.limit) params.append('limit', filters.limit.toString());

    const queryString = params.toString();
    const endpoint = queryString ? `/case_studies?${queryString}` : '/case_studies';
    
    return apiService.get<CaseStudy[]>(endpoint);
  }

  static async getById(id: string): Promise<CaseStudy> {
    return apiService.get<CaseStudy>(`/case_studies/${id}`);
  }

  static async getByCategory(category: string): Promise<CaseStudy[]> {
    return apiService.get<CaseStudy[]>(`/case_studies?category=${category}`);
  }
}
```

## 🎯 React Components Integration

### 1. Lead Form Component

```typescript
import React, { useState } from 'react';
import { LeadService, CreateLeadRequest } from '../services/leads';

export const LeadForm: React.FC = () => {
  const [formData, setFormData] = useState<CreateLeadRequest>({
    name: '',
    email: '',
    phone: '',
    company: '',
    message: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState<'idle' | 'success' | 'error'>('idle');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setSubmitStatus('idle');

    try {
      await LeadService.create(formData);
      setSubmitStatus('success');
      setFormData({ name: '', email: '', phone: '', company: '', message: '' });
    } catch (error) {
      console.error('Failed to submit lead:', error);
      setSubmitStatus('error');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  return (
    <form onSubmit={handleSubmit} className="lead-form">
      <div className="form-group">
        <label htmlFor="name">Name *</label>
        <input
          type="text"
          id="name"
          name="name"
          value={formData.name}
          onChange={handleChange}
          required
        />
      </div>

      <div className="form-group">
        <label htmlFor="email">Email *</label>
        <input
          type="email"
          id="email"
          name="email"
          value={formData.email}
          onChange={handleChange}
          required
        />
      </div>

      <div className="form-group">
        <label htmlFor="phone">Phone</label>
        <input
          type="tel"
          id="phone"
          name="phone"
          value={formData.phone}
          onChange={handleChange}
        />
      </div>

      <div className="form-group">
        <label htmlFor="company">Company</label>
        <input
          type="text"
          id="company"
          name="company"
          value={formData.company}
          onChange={handleChange}
        />
      </div>

      <div className="form-group">
        <label htmlFor="message">Message *</label>
        <textarea
          id="message"
          name="message"
          value={formData.message}
          onChange={handleChange}
          required
          rows={4}
        />
      </div>

      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? 'Submitting...' : 'Submit Lead'}
      </button>

      {submitStatus === 'success' && (
        <div className="success-message">
          Thank you! Your lead has been submitted successfully.
        </div>
      )}

      {submitStatus === 'error' && (
        <div className="error-message">
          Sorry, there was an error submitting your lead. Please try again.
        </div>
      )}
    </form>
  );
};
```

### 2. Case Studies List Component

```typescript
import React, { useState, useEffect } from 'react';
import { CaseStudyService, CaseStudy, CaseStudyFilters } from '../services/caseStudies';

export const CaseStudiesList: React.FC = () => {
  const [caseStudies, setCaseStudies] = useState<CaseStudy[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filters, setFilters] = useState<CaseStudyFilters>({
    page: 1,
    limit: 10
  });

  useEffect(() => {
    loadCaseStudies();
  }, [filters]);

  const loadCaseStudies = async () => {
    try {
      setLoading(true);
      const data = await CaseStudyService.getAll(filters);
      setCaseStudies(data);
      setError(null);
    } catch (err) {
      setError('Failed to load case studies');
      console.error('Error loading case studies:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleCategoryFilter = (category: string) => {
    setFilters(prev => ({
      ...prev,
      category: prev.category === category ? undefined : category,
      page: 1
    }));
  };

  const handleSearch = (searchTerm: string) => {
    setFilters(prev => ({
      ...prev,
      search: searchTerm || undefined,
      page: 1
    }));
  };

  if (loading) return <div>Loading case studies...</div>;
  if (error) return <div className="error">{error}</div>;

  return (
    <div className="case-studies">
      <div className="filters">
        <input
          type="text"
          placeholder="Search case studies..."
          onChange={(e) => handleSearch(e.target.value)}
          className="search-input"
        />
        
        <div className="category-filters">
          {['Web Development', 'Mobile Apps', 'Digital Marketing', 'SEO'].map(category => (
            <button
              key={category}
              onClick={() => handleCategoryFilter(category)}
              className={`category-btn ${filters.category === category ? 'active' : ''}`}
            >
              {category}
            </button>
          ))}
        </div>
      </div>

      <div className="case-studies-grid">
        {caseStudies.map(study => (
          <div key={study.id} className="case-study-card">
            {study.imageUrl && (
              <img src={study.imageUrl} alt={study.title} className="case-study-image" />
            )}
            <div className="case-study-content">
              <h3>{study.title}</h3>
              <p>{study.description}</p>
              <div className="case-study-meta">
                <span className="category">{study.category}</span>
                <span className="date">
                  {new Date(study.publishedAt).toLocaleDateString()}
                </span>
              </div>
              <div className="tags">
                {study.tags.map(tag => (
                  <span key={tag} className="tag">{tag}</span>
                ))}
              </div>
            </div>
          </div>
        ))}
      </div>

      {caseStudies.length === 0 && (
        <div className="no-results">
          No case studies found matching your criteria.
        </div>
      )}
    </div>
  );
};
```

## 🔄 State Management

### 1. React Context for Global State

Create `src/contexts/DataContext.tsx`:

```typescript
import React, { createContext, useContext, useReducer, ReactNode } from 'react';

interface DataState {
  leads: any[];
  caseStudies: any[];
  faqs: any[];
  loading: boolean;
  error: string | null;
}

type DataAction =
  | { type: 'SET_LOADING'; payload: boolean }
  | { type: 'SET_ERROR'; payload: string | null }
  | { type: 'SET_LEADS'; payload: any[] }
  | { type: 'SET_CASE_STUDIES'; payload: any[] }
  | { type: 'SET_FAQS'; payload: any[] }
  | { type: 'ADD_LEAD'; payload: any }
  | { type: 'UPDATE_LEAD'; payload: { id: string; data: any } }
  | { type: 'DELETE_LEAD'; payload: string };

const initialState: DataState = {
  leads: [],
  caseStudies: [],
  faqs: [],
  loading: false,
  error: null,
};

function dataReducer(state: DataState, action: DataAction): DataState {
  switch (action.type) {
    case 'SET_LOADING':
      return { ...state, loading: action.payload };
    
    case 'SET_ERROR':
      return { ...state, error: action.payload };
    
    case 'SET_LEADS':
      return { ...state, leads: action.payload };
    
    case 'SET_CASE_STUDIES':
      return { ...state, caseStudies: action.payload };
    
    case 'SET_FAQS':
      return { ...state, faqs: action.payload };
    
    case 'ADD_LEAD':
      return { ...state, leads: [...state.leads, action.payload] };
    
    case 'UPDATE_LEAD':
      return {
        ...state,
        leads: state.leads.map(lead =>
          lead.id === action.payload.id
            ? { ...lead, ...action.payload.data }
            : lead
        ),
      };
    
    case 'DELETE_LEAD':
      return {
        ...state,
        leads: state.leads.filter(lead => lead.id !== action.payload),
      };
    
    default:
      return state;
  }
}

interface DataContextType {
  state: DataState;
  dispatch: React.Dispatch<DataAction>;
}

const DataContext = createContext<DataContextType | undefined>(undefined);

export const DataProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [state, dispatch] = useReducer(dataReducer, initialState);

  return (
    <DataContext.Provider value={{ state, dispatch }}>
      {children}
    </DataContext.Provider>
  );
};

export const useData = () => {
  const context = useContext(DataContext);
  if (context === undefined) {
    throw new Error('useData must be used within a DataProvider');
  }
  return context;
};
```

### 2. Custom Hooks for Data Operations

Create `src/hooks/useLeads.ts`:

```typescript
import { useState, useEffect } from 'react';
import { LeadService, Lead, CreateLeadRequest } from '../services/leads';
import { useData } from '../contexts/DataContext';

export const useLeads = () => {
  const { state, dispatch } = useData();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const loadLeads = async () => {
    try {
      setLoading(true);
      setError(null);
      const leads = await LeadService.getAll();
      dispatch({ type: 'SET_LEADS', payload: leads });
    } catch (err) {
      setError('Failed to load leads');
      console.error('Error loading leads:', err);
    } finally {
      setLoading(false);
    }
  };

  const createLead = async (leadData: CreateLeadRequest) => {
    try {
      setLoading(true);
      setError(null);
      const newLead = await LeadService.create(leadData);
      dispatch({ type: 'ADD_LEAD', payload: newLead });
      return newLead;
    } catch (err) {
      setError('Failed to create lead');
      console.error('Error creating lead:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const updateLead = async (id: string, data: Partial<Lead>) => {
    try {
      setLoading(true);
      setError(null);
      const updatedLead = await LeadService.update(id, data);
      dispatch({ type: 'UPDATE_LEAD', payload: { id, data: updatedLead } });
      return updatedLead;
    } catch (err) {
      setError('Failed to update lead');
      console.error('Error updating lead:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const deleteLead = async (id: string) => {
    try {
      setLoading(true);
      setError(null);
      await LeadService.delete(id);
      dispatch({ type: 'DELETE_LEAD', payload: id });
    } catch (err) {
      setError('Failed to delete lead');
      console.error('Error deleting lead:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (state.leads.length === 0) {
      loadLeads();
    }
  }, []);

  return {
    leads: state.leads,
    loading,
    error,
    loadLeads,
    createLead,
    updateLead,
    deleteLead,
  };
};
```

## 🔒 Error Handling and Validation

### 1. API Error Types

```typescript
export interface ApiError {
  message: string;
  code?: string;
  details?: Record<string, any>;
  status?: number;
}

export class ApiException extends Error {
  public code?: string;
  public details?: Record<string, any>;
  public status?: number;

  constructor(error: ApiError) {
    super(error.message);
    this.name = 'ApiException';
    this.code = error.code;
    this.details = error.details;
    this.status = error.status;
  }
}
```

### 2. Form Validation

```typescript
import { useState } from 'react';

interface ValidationRules {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
  custom?: (value: any) => string | null;
}

interface ValidationErrors {
  [key: string]: string;
}

export const useFormValidation = <T extends Record<string, any>>(
  initialData: T,
  validationRules: Record<keyof T, ValidationRules>
) => {
  const [data, setData] = useState<T>(initialData);
  const [errors, setErrors] = useState<ValidationErrors>({});

  const validateField = (field: keyof T, value: any): string | null => {
    const rules = validationRules[field];
    
    if (rules.required && !value) {
      return `${field} is required`;
    }
    
    if (rules.minLength && value && value.length < rules.minLength) {
      return `${field} must be at least ${rules.minLength} characters`;
    }
    
    if (rules.maxLength && value && value.length > rules.maxLength) {
      return `${field} must be no more than ${rules.maxLength} characters`;
    }
    
    if (rules.pattern && value && !rules.pattern.test(value)) {
      return `${field} format is invalid`;
    }
    
    if (rules.custom) {
      return rules.custom(value);
    }
    
    return null;
  };

  const validateForm = (): boolean => {
    const newErrors: ValidationErrors = {};
    let isValid = true;

    Object.keys(validationRules).forEach(field => {
      const error = validateField(field as keyof T, data[field as keyof T]);
      if (error) {
        newErrors[field] = error;
        isValid = false;
      }
    });

    setErrors(newErrors);
    return isValid;
  };

  const setFieldValue = (field: keyof T, value: any) => {
    setData(prev => ({ ...prev, [field]: value }));
    
    // Clear error when user starts typing
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  return {
    data,
    errors,
    setFieldValue,
    validateForm,
    setData,
  };
};
```

## 📊 Data Synchronization

### 1. Real-time Updates (Optional)

```typescript
import { useEffect, useRef } from 'react';

export const useWebSocket = (url: string, onMessage: (data: any) => void) => {
  const ws = useRef<WebSocket | null>(null);

  useEffect(() => {
    ws.current = new WebSocket(url);

    ws.current.onopen = () => {
      console.log('WebSocket connected');
    };

    ws.current.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        onMessage(data);
      } catch (error) {
        console.error('Failed to parse WebSocket message:', error);
      }
    };

    ws.current.onerror = (error) => {
      console.error('WebSocket error:', error);
    };

    ws.current.onclose = () => {
      console.log('WebSocket disconnected');
    };

    return () => {
      if (ws.current) {
        ws.current.close();
      }
    };
  }, [url, onMessage]);

  const sendMessage = (data: any) => {
    if (ws.current && ws.current.readyState === WebSocket.OPEN) {
      ws.current.send(JSON.stringify(data));
    }
  };

  return { sendMessage };
};
```

### 2. Polling for Updates

```typescript
import { useEffect, useRef } from 'react';

export const usePolling = (
  callback: () => void,
  interval: number,
  enabled: boolean = true
) => {
  const intervalRef = useRef<NodeJS.Timeout | null>(null);

  useEffect(() => {
    if (enabled) {
      intervalRef.current = setInterval(callback, interval);
    }

    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
      }
    };
  }, [callback, interval, enabled]);

  const stopPolling = () => {
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
  };

  const startPolling = () => {
    if (!intervalRef.current) {
      intervalRef.current = setInterval(callback, interval);
    }
  };

  return { stopPolling, startPolling };
};
```

## 🚨 Best Practices

### 1. Data Fetching
- Use React Query or SWR for advanced caching
- Implement proper loading and error states
- Handle network failures gracefully
- Cache responses when appropriate

### 2. State Management
- Keep API calls in services, not components
- Use React Context for global state
- Implement optimistic updates for better UX
- Handle concurrent updates properly

### 3. Error Handling
- Provide meaningful error messages
- Implement retry mechanisms
- Log errors for debugging
- Gracefully degrade functionality

### 4. Performance
- Implement pagination for large datasets
- Use virtualization for long lists
- Debounce search inputs
- Lazy load components and data

## 📚 Additional Resources

- [Axios Documentation](https://axios-http.com/)
- [React Query](https://tanstack.com/query/latest)
- [SWR](https://swr.vercel.app/)
- [React Context Best Practices](https://react.dev/learn/passing-data-deeply-with-context)

---

**Happy integrating! 🚀**
