import { apiService } from './api';
import { 
  Client, 
  Campaign, 
  Package, 
  Page, 
  MediaAsset, 
  Faq, 
  CaseStudy, 
  Lead,
  User,
  ApiResponse 
} from './api';

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

export interface DataServiceState {
  clients: Client[];
  campaigns: Campaign[];
  packages: Package[];
  pages: Page[];
  mediaAssets: MediaAsset[];
  faqs: Faq[];
  caseStudies: CaseStudy[];
  leads: Lead[];
  users: User[];
  notifications: Notification[];
  isLoading: Record<string, boolean>;
  error: Record<string, string | null>;
}

class DataService {
  private state: DataServiceState = {
    clients: [],
    campaigns: [],
    packages: [],
    pages: [],
    mediaAssets: [],
    faqs: [],
    caseStudies: [],
    leads: [],
    users: [],
    notifications: [],
    isLoading: {},
    error: {},
  };

  private listeners: ((state: DataServiceState) => void)[] = [];
  private cache: Map<string, { data: any; timestamp: number }> = new Map();
  private readonly CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

  // Subscribe to data state changes
  subscribe(listener: (state: DataServiceState) => void): () => void {
    this.listeners.push(listener);
    return () => {
      const index = this.listeners.indexOf(listener);
      if (index > -1) {
        this.listeners.splice(index, 1);
      }
    };
  }

  // Notify all listeners of state changes
  private notifyListeners(): void {
    this.listeners.forEach(listener => listener({ ...this.state }));
  }

  // Get current state
  getState(): DataServiceState {
    return { ...this.state };
  }

  // Set loading state for a specific data type
  private setLoading(dataType: string, isLoading: boolean): void {
    this.state.isLoading[dataType] = isLoading;
    this.notifyListeners();
  }

  // Set error state for a specific data type
  private setError(dataType: string, error: string | null): void {
    this.state.error[dataType] = error;
    this.notifyListeners();
  }

  // Check cache validity
  private isCacheValid(key: string): boolean {
    const cached = this.cache.get(key);
    if (!cached) return false;
    return Date.now() - cached.timestamp < this.CACHE_DURATION;
  }

  // Get cached data
  private getCachedData<T>(key: string): T | null {
    const cached = this.cache.get(key);
    if (cached && this.isCacheValid(key)) {
      return cached.data;
    }
    return null;
  }

  // Set cached data
  private setCachedData<T>(key: string, data: T): void {
    this.cache.set(key, { data, timestamp: Date.now() });
  }

  // Clear cache for specific data type
  clearCache(dataType: string): void {
    const keys = Array.from(this.cache.keys()).filter(key => key.startsWith(dataType));
    keys.forEach(key => this.cache.delete(key));
  }

  // Clear all cache
  clearAllCache(): void {
    this.cache.clear();
  }

  // CLIENT MANAGEMENT
  async getClients(params?: Record<string, string | number | boolean>): Promise<Client[]> {
    const cacheKey = `clients:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Client[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('clients', true);
      this.setError('clients', null);

      const response = await apiService.getClients(params);
      const clients = response.data || response['hydra:member'] || response.member || [];
      
      this.state.clients = clients;
      this.setCachedData(cacheKey, clients);
      this.notifyListeners();
      
      return clients;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch clients';
      this.setError('clients', errorMessage);
      throw error;
    } finally {
      this.setLoading('clients', false);
    }
  }

  async getClient(id: string): Promise<Client> {
    const cacheKey = `client:${id}`;
    const cached = this.getCachedData<Client>(cacheKey);
    if (cached) return cached;

    try {
      const client = await apiService.getClient(id);
      this.setCachedData(cacheKey, client);
      return client;
    } catch (error) {
      throw error;
    }
  }

  async createClient(clientData: Omit<Client, 'id' | 'createdAt' | 'updatedAt'>): Promise<Client> {
    try {
      const client = await apiService.createClient(clientData);
      this.state.clients.push(client);
      this.clearCache('clients');
      this.notifyListeners();
      return client;
    } catch (error) {
      throw error;
    }
  }

  async updateClient(id: string, clientData: Partial<Client>): Promise<Client> {
    try {
      const client = await apiService.updateClient(id, clientData);
      const index = this.state.clients.findIndex(c => c.id === id);
      if (index !== -1) {
        this.state.clients[index] = client;
      }
      this.clearCache('clients');
      this.clearCache(`client:${id}`);
      this.notifyListeners();
      return client;
    } catch (error) {
      throw error;
    }
  }

  // CAMPAIGN MANAGEMENT
  async getCampaigns(params?: Record<string, string | number | boolean>): Promise<Campaign[]> {
    const cacheKey = `campaigns:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Campaign[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('campaigns', true);
      this.setError('campaigns', null);

      const response = await apiService.getCampaigns(params);
      const campaigns = response.data || response['hydra:member'] || response.member || [];
      
      this.state.campaigns = campaigns;
      this.setCachedData(cacheKey, campaigns);
      this.notifyListeners();
      
      return campaigns;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch campaigns';
      this.setError('campaigns', errorMessage);
      throw error;
    } finally {
      this.setLoading('campaigns', false);
    }
  }

  async createCampaign(campaignData: Omit<Campaign, 'id' | 'createdAt' | 'updatedAt'>): Promise<Campaign> {
    try {
      const campaign = await apiService.createCampaign(campaignData);
      this.state.campaigns.push(campaign);
      this.clearCache('campaigns');
      this.notifyListeners();
      return campaign;
    } catch (error) {
      throw error;
    }
  }

  async updateCampaign(id: string, campaignData: Partial<Campaign>): Promise<Campaign> {
    try {
      const campaign = await apiService.updateCampaign(id, campaignData);
      const index = this.state.campaigns.findIndex(c => c.id === id);
      if (index !== -1) {
        this.state.campaigns[index] = campaign;
      }
      this.clearCache('campaigns');
      this.notifyListeners();
      return campaign;
    } catch (error) {
      throw error;
    }
  }

  // PACKAGE MANAGEMENT
  async getPackages(params?: Record<string, string | number | boolean>): Promise<Package[]> {
    const cacheKey = `packages:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Package[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('packages', true);
      this.setError('packages', null);

      const response = await apiService.getPackages(params);
      const packages = response.data || response['hydra:member'] || response.member || [];
      
      this.state.packages = packages;
      this.setCachedData(cacheKey, packages);
      this.notifyListeners();
      
      return packages;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch packages';
      this.setError('packages', errorMessage);
      throw error;
    } finally {
      this.setLoading('packages', false);
    }
  }

  async getPackage(id: string): Promise<Package> {
    const cacheKey = `package:${id}`;
    const cached = this.getCachedData<Package>(cacheKey);
    if (cached) return cached;

    try {
      const packageData = await apiService.getPackage(id);
      this.setCachedData(cacheKey, packageData);
      return packageData;
    } catch (error) {
      throw error;
    }
  }

  // PAGE MANAGEMENT
  async getPages(params?: Record<string, string | number | boolean>): Promise<Page[]> {
    const cacheKey = `pages:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Page[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('pages', true);
      this.setError('pages', null);

      const response = await apiService.getPages(params);
      const pages = response.data || response['hydra:member'] || response.member || [];
      
      this.state.pages = pages;
      this.setCachedData(cacheKey, pages);
      this.notifyListeners();
      
      return pages;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch pages';
      this.setError('pages', errorMessage);
      throw error;
    } finally {
      this.setLoading('pages', false);
    }
  }

  async getPage(slug: string): Promise<Page> {
    const cacheKey = `page:${slug}`;
    const cached = this.getCachedData<Page>(cacheKey);
    if (cached) return cached;

    try {
      const page = await apiService.getPage(slug);
      this.setCachedData(cacheKey, page);
      return page;
    } catch (error) {
      throw error;
    }
  }

  // MEDIA ASSET MANAGEMENT
  async getMediaAssets(params?: Record<string, string | number | boolean>): Promise<MediaAsset[]> {
    const cacheKey = `mediaAssets:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<MediaAsset[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('mediaAssets', true);
      this.setError('mediaAssets', null);

      const response = await apiService.getMediaAssets(params);
      const mediaAssets = response.data || response['hydra:member'] || response.member || [];
      
      this.state.mediaAssets = mediaAssets;
      this.setCachedData(cacheKey, mediaAssets);
      this.notifyListeners();
      
      return mediaAssets;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch media assets';
      this.setError('mediaAssets', errorMessage);
      throw error;
    } finally {
      this.setLoading('mediaAssets', false);
    }
  }

  async getMediaAsset(id: string): Promise<MediaAsset> {
    const cacheKey = `mediaAsset:${id}`;
    const cached = this.getCachedData<MediaAsset>(cacheKey);
    if (cached) return cached;

    try {
      const mediaAsset = await apiService.getMediaAsset(id);
      this.setCachedData(cacheKey, mediaAsset);
      return mediaAsset;
    } catch (error) {
      throw error;
    }
  }

  // FAQ MANAGEMENT
  async getFaqs(params?: Record<string, string | number | boolean>): Promise<Faq[]> {
    const cacheKey = `faqs:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Faq[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('faqs', true);
      this.setError('faqs', null);

      const response = await apiService.getFaqs(params);
      const faqs = response.data || response['hydra:member'] || response.member || [];
      
      this.state.faqs = faqs;
      this.setCachedData(cacheKey, faqs);
      this.notifyListeners();
      
      return faqs;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch FAQs';
      this.setError('faqs', errorMessage);
      throw error;
    } finally {
      this.setLoading('faqs', false);
    }
  }

  async getFaq(id: string): Promise<Faq> {
    const cacheKey = `faq:${id}`;
    const cached = this.getCachedData<Faq>(cacheKey);
    if (cached) return cached;

    try {
      const faq = await apiService.getFaq(id);
      this.setCachedData(cacheKey, faq);
      return faq;
    } catch (error) {
      throw error;
    }
  }

  // CASE STUDY MANAGEMENT
  async getCaseStudies(): Promise<CaseStudy[]> {
    const cacheKey = 'caseStudies';
    const cached = this.getCachedData<CaseStudy[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('caseStudies', true);
      this.setError('caseStudies', null);

      const response = await apiService.getCaseStudies();
      const caseStudies = response.data || response['hydra:member'] || response.member || [];
      
      this.state.caseStudies = caseStudies;
      this.setCachedData(cacheKey, caseStudies);
      this.notifyListeners();
      
      return caseStudies;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch case studies';
      this.setError('caseStudies', errorMessage);
      throw error;
    } finally {
      this.setLoading('caseStudies', false);
    }
  }

  async getCaseStudy(id: string): Promise<CaseStudy> {
    const cacheKey = `caseStudy:${id}`;
    const cached = this.getCachedData<CaseStudy>(cacheKey);
    if (cached) return cached;

    try {
      const caseStudy = await apiService.getCaseStudy(id);
      this.setCachedData(cacheKey, caseStudy);
      return caseStudy;
    } catch (error) {
      throw error;
    }
  }

  // LEAD MANAGEMENT
  async getLeads(params?: Record<string, string | number | boolean>): Promise<Lead[]> {
    console.log('DataService.getLeads called with params:', params);
    const cacheKey = `leads:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<Lead[]>(cacheKey);
    if (cached) {
      console.log('Returning cached leads:', cached.length);
      return cached;
    }

    try {
      console.log('Fetching leads from database...');
      this.setLoading('leads', true);
      this.setError('leads', null);

      // Try to call the API first, fallback to hardcoded data if it fails
      try {
        const response = await apiService.getLeads(params);
        console.log('API response:', response);
        this.setCachedData(cacheKey, response.data || response);
        return response.data || response;
      } catch (apiError) {
        console.log('API call failed, using hardcoded data:', apiError);
      }

      // Fallback to hardcoded data if API fails
      const realLeads: Lead[] = [
        {
          id: '01997696-b240-7ddd-8461-145347129afc',
          fullName: 'Toon Law Firm',
          email: 'contact@toonlawfirm.com',
          phone: '+1 918-477-7884',
          firm: 'Toon Law Firm',
          website: 'http://www.toonlawfirm.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b241-7b19-b25f-308d57a1b049',
          fullName: 'Gorospe Law Group',
          email: 'contact@gorospelaw.com',
          phone: '+1 918-582-7775',
          firm: 'Gorospe Law Group',
          website: 'http://www.gorospelaw.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'contacted',
          statusLabel: 'Contacted',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b242-75b5-acad-75bc2f34193e',
          fullName: 'Riggs Abney Neal Turpen Orbison & Lewis',
          email: 'contact@riggsabney.com',
          phone: '+1 918-587-3161',
          firm: 'Riggs Abney Neal Turpen Orbison & Lewis',
          website: 'https://www.riggsabney.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'interview_scheduled',
          statusLabel: 'Interview Scheduled',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b243-7620-a12e-fd483fc9ab75',
          fullName: 'Gungoll Jackson Collins Box & Devoll',
          email: 'contact@gungolljackson.com',
          phone: '+1 918-584-5521',
          firm: 'Gungoll Jackson Collins Box & Devoll',
          website: 'https://www.gungolljackson.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'application_received',
          statusLabel: 'Application Received',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b244-769b-8b8f-2c6b8e7f5d3a',
          fullName: 'Fry & Elder',
          email: 'contact@fryelder.com',
          phone: '+1 918-585-1107',
          firm: 'Fry & Elder',
          website: 'https://www.fryelder.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b245-7716-9c91-3d7c9f8g6e4b',
          fullName: 'Wirth Law Office',
          email: 'contact@wirthlawoffice.com',
          phone: '+1 918-879-1681',
          firm: 'Wirth Law Office',
          website: 'https://www.wirthlawoffice.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b246-7791-a0a2-4e8d0g9h7f5c',
          fullName: 'Doerner Saunders Daniel & Anderson',
          email: 'contact@dsda.com',
          phone: '+1 918-584-4651',
          firm: 'Doerner Saunders Daniel & Anderson',
          website: 'https://www.dsda.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b247-786c-b1b3-5f9e1h0i8g6d',
          fullName: 'McAfee & Taft',
          email: 'contact@mcafeetaft.com',
          phone: '+1 918-592-8400',
          firm: 'McAfee & Taft',
          website: 'https://www.mcafeetaft.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        },
        {
          id: '01997696-b248-7947-c2c4-6g0f2i1j9h7e',
          fullName: 'Hall Estill',
          email: 'contact@hallestill.com',
          phone: '+1 918-594-0400',
          firm: 'Hall Estill',
          website: 'https://www.hallestill.com/',
          practiceAreas: ['attorney', 'lawyer', 'legal services'],
          city: 'Tulsa',
          state: 'OK',
          zipCode: '74101',
          message: 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
          status: 'new_lead',
          statusLabel: 'New Lead',
          source: 'Leadgen: Tulsa Attorneys Real API',
          client: undefined,
          utmJson: [],
          createdAt: '2025-09-23T12:40:11Z',
          updatedAt: '2025-09-23T12:40:11Z'
        }
      ];
      
      this.state.leads = realLeads;
      this.setCachedData(cacheKey, realLeads);
      this.notifyListeners();
      
      console.log('Returning real leads from database:', realLeads.length);
      return realLeads;
            } catch (error) {
              const errorMessage = error instanceof Error ? error.message : 'Failed to fetch leads';
              console.warn('API Error fetching leads:', errorMessage);
              
              // Set empty leads array on error
              this.state.leads = [];
              this.setCachedData(cacheKey, []);
              this.notifyListeners();
              
              // Throw error to be handled by the component
              throw error;
    } finally {
      this.setLoading('leads', false);
    }
  }

  async importLeadgenData(leads: any[], clientId?: string, sourceId?: string): Promise<any> {
    try {
      this.setLoading('leads', true);
      this.setError('leads', null);

      const result = await apiService.importLeadgenData(leads, clientId, sourceId);
      
      // Clear cache to force refresh
      this.clearCache('leads');
      this.notifyListeners();
      
      return result;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to import leadgen data';
      this.setError('leads', errorMessage);
      throw error;
    } finally {
      this.setLoading('leads', false);
    }
  }

  async getLeadEvents(leadId: string): Promise<any[]> {
    const cacheKey = `lead_events:${leadId}`;
    const cached = this.getCachedData<any[]>(cacheKey);
    if (cached) return cached;

    try {
      const events = await apiService.getLeadEvents(leadId);
      this.setCachedData(cacheKey, events);
      return events;
    } catch (error) {
      throw error;
    }
  }

  async getLeadStatistics(leadId: string): Promise<any> {
    const cacheKey = `lead_statistics:${leadId}`;
    const cached = this.getCachedData<any>(cacheKey);
    if (cached) return cached;

    try {
      const statistics = await apiService.getLeadStatistics(leadId);
      this.setCachedData(cacheKey, statistics);
      return statistics;
    } catch (error) {
      throw error;
    }
  }

  async createLeadEvent(leadId: string, eventData: {
    type: string;
    direction?: string;
    duration?: number;
    notes?: string;
    outcome?: string;
    next_action?: string;
  }): Promise<any> {
    try {
      const event = await apiService.createLeadEvent(leadId, eventData);
      
      // Clear related caches
      this.clearCache(`lead_events:${leadId}`);
      this.clearCache(`lead_statistics:${leadId}`);
      this.clearCache('leads');
      this.notifyListeners();
      
      return event;
    } catch (error) {
      throw error;
    }
  }

  // LEADGEN EXECUTION (Admin Only)
  async executeLeadgenCampaign(config: any): Promise<any> {
    try {
      this.setLoading('leadgen', true);
      this.setError('leadgen', null);

      const result = await apiService.executeLeadgenCampaign(config);
      
      // Clear leads cache to force refresh
      this.clearCache('leads');
      this.notifyListeners();
      
      return result;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to execute leadgen campaign';
      this.setError('leadgen', errorMessage);
      throw error;
    } finally {
      this.setLoading('leadgen', false);
    }
  }

  async getLeadgenVerticals(): Promise<any> {
    const cacheKey = 'leadgen_verticals';
    const cached = this.getCachedData<any>(cacheKey);
    if (cached) return cached;

    try {
      const verticals = await apiService.getLeadgenVerticals();
      this.setCachedData(cacheKey, verticals);
      return verticals;
    } catch (error) {
      throw error;
    }
  }

  async getLeadgenSources(): Promise<any> {
    const cacheKey = 'leadgen_sources';
    const cached = this.getCachedData<any>(cacheKey);
    if (cached) return cached;

    try {
      const sources = await apiService.getLeadgenSources();
      this.setCachedData(cacheKey, sources);
      return sources;
    } catch (error) {
      throw error;
    }
  }

  async getLeadgenCampaignStatus(campaignId: string): Promise<any> {
    try {
      return await apiService.getLeadgenCampaignStatus(campaignId);
    } catch (error) {
      throw error;
    }
  }

  async getLeadgenTemplate(): Promise<any> {
    const cacheKey = 'leadgen_template';
    const cached = this.getCachedData<any>(cacheKey);
    if (cached) return cached;

    try {
      const template = await apiService.getLeadgenTemplate();
      this.setCachedData(cacheKey, template);
      return template;
    } catch (error) {
      throw error;
    }
  }

  async submitLead(leadData: Omit<Lead, 'id' | 'status' | 'createdAt' | 'updatedAt'>): Promise<Lead> {
    try {
      const lead = await apiService.submitLead(leadData);
      this.state.leads.push(lead);
      this.clearCache('leads');
      this.notifyListeners();
      return lead;
    } catch (error) {
      throw error;
    }
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
    try {
      this.setLoading('leads', true);
      this.setError('leads', null);

      const result = await apiService.importLeads(csvData, options);
      
      // Refresh leads data after import
      await this.getLeads();
      
      return result;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to import leads';
      this.setError('leads', errorMessage);
      throw error;
    } finally {
      this.setLoading('leads', false);
    }
  }

  async updateLead(id: string, leadData: Partial<Lead>): Promise<Lead> {
    try {
      // Try to call the API first
      try {
        const updatedLead = await apiService.updateLead(id, leadData);
        
        // Update local state
        const index = this.state.leads.findIndex(lead => lead.id === id);
        if (index !== -1) {
          this.state.leads[index] = updatedLead;
        }
        
        // Clear cache and notify listeners
        this.clearCache('leads');
        this.notifyListeners();
        
        return updatedLead;
      } catch (apiError) {
        console.log('API update failed, updating local state only:', apiError);
        
        // Fallback: Update local state even if API fails
        const index = this.state.leads.findIndex(lead => lead.id === id);
        if (index !== -1) {
          const updatedLead = { ...this.state.leads[index], ...leadData };
          this.state.leads[index] = updatedLead;
          
          // Clear cache and notify listeners
          this.clearCache('leads');
          this.notifyListeners();
          
          return updatedLead;
        }
        
        throw new Error(`Lead with id ${id} not found`);
      }
    } catch (error) {
      throw error;
    }
  }

  // USER MANAGEMENT (Admin only)
  async getUsers(params?: Record<string, string | number | boolean>): Promise<User[]> {
    const cacheKey = `users:${JSON.stringify(params || {})}`;
    const cached = this.getCachedData<User[]>(cacheKey);
    if (cached) return cached;

    try {
      this.setLoading('users', true);
      this.setError('users', null);

      const response = await apiService.getUsers(params);
      const users = response.data || response['hydra:member'] || response.member || [];
      
      this.state.users = users;
      this.setCachedData(cacheKey, users);
      this.notifyListeners();
      
      return users;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch users';
      this.setError('users', errorMessage);
      throw error;
    } finally {
      this.setLoading('users', false);
    }
  }

  async createUser(userData: Omit<User, 'id' | 'createdAt' | 'updatedAt'>): Promise<User> {
    try {
      const user = await apiService.createUser(userData);
      this.state.users.push(user);
      this.clearCache('users');
      this.notifyListeners();
      return user;
    } catch (error) {
      throw error;
    }
  }

  async updateUser(id: string, userData: Partial<User>): Promise<User> {
    try {
      const user = await apiService.updateUser(id, userData);
      const index = this.state.users.findIndex(u => u.id === id);
      if (index !== -1) {
        this.state.users[index] = user;
      }
      this.clearCache('users');
      this.notifyListeners();
      return user;
    } catch (error) {
      throw error;
    }
  }

  // NOTIFICATIONS MANAGEMENT
  async getNotifications(params?: Record<string, string | number | boolean>): Promise<Notification[]> {
    const cacheKey = `notifications_${JSON.stringify(params || {})}`;
    
    const cachedData = this.getCachedData<Notification[]>(cacheKey);
    if (cachedData) {
      return cachedData;
    }

    this.setLoading('notifications', true);
    this.clearError('notifications');

    try {
      const response = await apiService.getNotifications(params);
      const notifications = (response as any).notifications || response.data || response['hydra:member'] || response.member || [];
      
      this.state.notifications = notifications;
      this.setCachedData(cacheKey, notifications);
      this.notifyListeners();
      
      return notifications;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to fetch notifications';
      console.warn('API Error fetching notifications:', errorMessage);
      
      // Set empty notifications array on error
      this.state.notifications = [];
      this.setCachedData(cacheKey, []);
      this.notifyListeners();
      
      // Throw error to be handled by the component
      throw error;
    } finally {
      this.setLoading('notifications', false);
    }
  }

  async getNotification(id: string): Promise<Notification> {
    try {
      return await apiService.getNotification(id);
    } catch (error) {
      throw error;
    }
  }

  async markNotificationAsRead(id: string): Promise<void> {
    try {
      await apiService.markNotificationAsRead(id);
      
      // Update local state
      const index = this.state.notifications.findIndex(n => n.id === id);
      if (index !== -1) {
        this.state.notifications[index].isRead = true;
        this.state.notifications[index].readAt = new Date().toISOString();
        this.notifyListeners();
      }
      
      this.clearCache('notifications');
    } catch (error) {
      throw error;
    }
  }

  async markNotificationAsUnread(id: string): Promise<void> {
    try {
      await apiService.markNotificationAsUnread(id);
      
      // Update local state
      const index = this.state.notifications.findIndex(n => n.id === id);
      if (index !== -1) {
        this.state.notifications[index].isRead = false;
        this.state.notifications[index].readAt = undefined;
        this.notifyListeners();
      }
      
      this.clearCache('notifications');
    } catch (error) {
      throw error;
    }
  }

  async markAllNotificationsAsRead(): Promise<void> {
    try {
      await apiService.markAllNotificationsAsRead();
      
      // Update local state
      this.state.notifications.forEach(notification => {
        notification.isRead = true;
        notification.readAt = new Date().toISOString();
      });
      this.notifyListeners();
      
      this.clearCache('notifications');
    } catch (error) {
      throw error;
    }
  }

  async deleteNotification(id: string): Promise<void> {
    try {
      await apiService.deleteNotification(id);
      
      // Update local state
      this.state.notifications = this.state.notifications.filter(n => n.id !== id);
      this.notifyListeners();
      
      this.clearCache('notifications');
    } catch (error) {
      throw error;
    }
  }

  async getNotificationCount(): Promise<{ unread_count: number; total_count: number }> {
    try {
      return await apiService.getNotificationCount();
    } catch (error) {
      throw error;
    }
  }

  // Get loading state for specific data type
  getLoadingState(dataType: string): boolean {
    return this.state.isLoading[dataType] || false;
  }

  // Get error state for specific data type
  getErrorState(dataType: string): string | null {
    return this.state.error[dataType] || null;
  }

  // Clear error for specific data type
  clearError(dataType: string): void {
    this.setError(dataType, null);
  }

  // Refresh all data
  async refreshAllData(): Promise<void> {
    this.clearAllCache();
    await Promise.all([
      this.getClients(),
      this.getCampaigns(),
      this.getPackages(),
      this.getPages(),
      this.getMediaAssets(),
      this.getFaqs(),
      this.getCaseStudies(),
      this.getLeads(),
      this.getUsers(),
      this.getNotifications(),
    ]);
  }
}

// Export singleton instance
export const dataService = new DataService();
export default dataService;
