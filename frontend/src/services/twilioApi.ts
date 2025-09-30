// Twilio API service for frontend
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';

export interface TwilioIntegration {
  id: string;
  clientId: string;
  clientName: string;
  phoneNumber: string;
  twilioPhoneNumberSid: string;
  displayName?: string;
  status: string;
  isDefault: boolean;
  autoLogCalls: boolean;
  autoLogMessages: boolean;
  syncContacts: boolean;
  recordCalls: boolean;
  transcribeCalls: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface TwilioCallLog {
  id: string;
  twilioCallSid: string;
  clientId: string;
  clientName: string;
  integrationId: string;
  direction: 'inbound' | 'outbound';
  status: 'queued' | 'ringing' | 'in-progress' | 'completed' | 'busy' | 'failed' | 'no-answer' | 'canceled';
  fromNumber?: string;
  toNumber?: string;
  duration?: number;
  startedAt?: string;
  endedAt?: string;
  recordingUrl?: string;
  transcript?: string;
  cost?: number;
  currency?: string;
  isFollowUpRequired: boolean;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

export interface TwilioMessageLog {
  id: string;
  twilioMessageSid: string;
  clientId: string;
  clientName: string;
  integrationId: string;
  direction: 'inbound' | 'outbound';
  status: 'queued' | 'sending' | 'sent' | 'receiving' | 'received' | 'delivered' | 'undelivered' | 'failed';
  fromNumber?: string;
  toNumber?: string;
  content: string;
  attachments?: any[];
  numSegments?: number;
  cost?: number;
  currency?: string;
  sentAt: string;
  isFollowUpRequired: boolean;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

export interface TwilioNumber {
  sid: string;
  phoneNumber: string;
  friendlyName?: string;
  voiceUrl?: string;
  smsUrl?: string;
  status: string;
  capabilities: {
    voice: boolean;
    sms: boolean;
    mms: boolean;
  };
}

export interface TwilioCapabilityToken {
  token: string;
  identity: string;
}

class TwilioApiService {
  private getAuthToken(): string | null {
    if (typeof window === 'undefined') return null;
    return localStorage.getItem('token');
  }

  private async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const url = `${API_BASE_URL}/api/v1/twilio${endpoint}`;
    
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> || {}),
    };

    // Add auth token if available
    const token = this.getAuthToken();
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(url, {
      ...options,
      headers,
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data.data || data;
  }

  // Phone Numbers
  async getPhoneNumbers(): Promise<TwilioNumber[]> {
    return this.request<TwilioNumber[]>('/phone-numbers');
  }

  async purchasePhoneNumber(phoneNumber: string, friendlyName?: string): Promise<TwilioNumber> {
    return this.request<TwilioNumber>('/phone-numbers/purchase', {
      method: 'POST',
      body: JSON.stringify({ phoneNumber, friendlyName }),
    });
  }

  // Integrations
  async getIntegrations(): Promise<TwilioIntegration[]> {
    return this.request<TwilioIntegration[]>('/integrations');
  }

  async createIntegration(data: {
    clientId: string;
    phoneNumber: string;
    twilioPhoneNumberSid: string;
    displayName?: string;
    isDefault?: boolean;
    autoLogCalls?: boolean;
    autoLogMessages?: boolean;
    syncContacts?: boolean;
    recordCalls?: boolean;
    transcribeCalls?: boolean;
  }): Promise<TwilioIntegration> {
    return this.request<TwilioIntegration>('/integrations', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async updateIntegration(id: string, data: Partial<TwilioIntegration>): Promise<TwilioIntegration> {
    return this.request<TwilioIntegration>(`/integrations/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  async deleteIntegration(id: string): Promise<void> {
    return this.request<void>(`/integrations/${id}`, {
      method: 'DELETE',
    });
  }

  async syncIntegration(id: string): Promise<{
    callLogsSynced: number;
    messageLogsSynced: number;
    totalSynced: number;
  }> {
    return this.request<{
      callLogsSynced: number;
      messageLogsSynced: number;
      totalSynced: number;
    }>(`/integrations/${id}/sync`, {
      method: 'POST',
    });
  }

  // Calls
  async makeCall(data: {
    fromNumber: string;
    toNumber: string;
    twimlUrl?: string;
    twiml?: string;
  }): Promise<any> {
    return this.request<any>('/calls', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async getCallLogs(params?: {
    clientId?: string;
    integrationId?: string;
    limit?: number;
    offset?: number;
  }): Promise<TwilioCallLog[]> {
    const queryParams = new URLSearchParams();
    if (params?.clientId) queryParams.append('clientId', params.clientId);
    if (params?.integrationId) queryParams.append('integrationId', params.integrationId);
    if (params?.limit) queryParams.append('limit', params.limit.toString());
    if (params?.offset) queryParams.append('offset', params.offset.toString());

    const endpoint = queryParams.toString() ? `/call-logs?${queryParams}` : '/call-logs';
    return this.request<TwilioCallLog[]>(endpoint);
  }

  // Messages
  async sendMessage(data: {
    fromNumber: string;
    toNumber: string;
    message: string;
  }): Promise<any> {
    return this.request<any>('/messages', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async getMessageLogs(params?: {
    clientId?: string;
    integrationId?: string;
    limit?: number;
    offset?: number;
  }): Promise<TwilioMessageLog[]> {
    const queryParams = new URLSearchParams();
    if (params?.clientId) queryParams.append('clientId', params.clientId);
    if (params?.integrationId) queryParams.append('integrationId', params.integrationId);
    if (params?.limit) queryParams.append('limit', params.limit.toString());
    if (params?.offset) queryParams.append('offset', params.offset.toString());

    const endpoint = queryParams.toString() ? `/message-logs?${queryParams}` : '/message-logs';
    return this.request<TwilioMessageLog[]>(endpoint);
  }

  // Capability Token for client-side calling
  async generateCapabilityToken(identity: string, permissions?: string[]): Promise<TwilioCapabilityToken> {
    return this.request<TwilioCapabilityToken>('/capability-token', {
      method: 'POST',
      body: JSON.stringify({ identity, permissions }),
    });
  }
}

export const twilioApiService = new TwilioApiService();
