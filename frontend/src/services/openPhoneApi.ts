// OpenPhone API service for frontend
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';

export interface OpenPhoneIntegration {
  id: string;
  clientId: string;
  clientName: string;
  phoneNumber: string;
  displayName?: string;
  status: string;
  isDefault: boolean;
  autoLogCalls: boolean;
  autoLogMessages: boolean;
  syncContacts: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface OpenPhoneCallLog {
  id: string;
  openPhoneCallId: string;
  clientId: string;
  clientName: string;
  integrationId: string;
  direction: 'inbound' | 'outbound';
  status: 'answered' | 'missed' | 'voicemail' | 'busy' | 'failed';
  fromNumber?: string;
  toNumber?: string;
  duration?: number;
  startedAt?: string;
  endedAt?: string;
  recordingUrl?: string;
  transcript?: string;
  isFollowUpRequired: boolean;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

export interface OpenPhoneMessageLog {
  id: string;
  openPhoneMessageId: string;
  clientId: string;
  clientName: string;
  integrationId: string;
  direction: 'inbound' | 'outbound';
  status: 'sent' | 'delivered' | 'failed' | 'pending';
  fromNumber?: string;
  toNumber?: string;
  content: string;
  attachments?: any[];
  sentAt: string;
  isFollowUpRequired: boolean;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

export interface OpenPhoneNumber {
  id: string;
  phoneNumber: string;
  displayName?: string;
  status: string;
}

class OpenPhoneApiService {
  private async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const url = `${API_BASE_URL}/api/v1/openphone${endpoint}`;
    
    const response = await fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
    });

    if (!response.ok) {
      const error = await response.json().catch(() => ({ error: 'Unknown error' }));
      throw new Error(error.error || `HTTP ${response.status}`);
    }

    const data = await response.json();
    return data.data || data;
  }

  async getPhoneNumbers(): Promise<OpenPhoneNumber[]> {
    return this.request<OpenPhoneNumber[]>('/phone-numbers');
  }

  async getIntegrations(): Promise<OpenPhoneIntegration[]> {
    return this.request<OpenPhoneIntegration[]>('/integrations');
  }

  async createIntegration(data: {
    clientId: string;
    phoneNumber: string;
    displayName?: string;
    isDefault?: boolean;
    autoLogCalls?: boolean;
    autoLogMessages?: boolean;
    syncContacts?: boolean;
  }): Promise<OpenPhoneIntegration> {
    return this.request<OpenPhoneIntegration>('/integrations', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async updateIntegration(id: string, data: Partial<OpenPhoneIntegration>): Promise<OpenPhoneIntegration> {
    return this.request<OpenPhoneIntegration>(`/integrations/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  async deleteIntegration(id: string): Promise<void> {
    await this.request(`/integrations/${id}`, {
      method: 'DELETE',
    });
  }

  async makeCall(data: {
    phoneNumberId: string;
    toNumber: string;
    fromNumber?: string;
  }): Promise<any> {
    return this.request('/calls', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async sendMessage(data: {
    phoneNumberId: string;
    toNumber: string;
    message: string;
  }): Promise<any> {
    return this.request('/messages', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async syncIntegration(id: string): Promise<{
    callLogsSynced: number;
    messageLogsSynced: number;
    totalSynced: number;
  }> {
    return this.request(`/integrations/${id}/sync`, {
      method: 'POST',
    });
  }

  async getCallLogs(params?: {
    clientId?: string;
    integrationId?: string;
    limit?: number;
    offset?: number;
  }): Promise<OpenPhoneCallLog[]> {
    const searchParams = new URLSearchParams();
    if (params?.clientId) searchParams.append('clientId', params.clientId);
    if (params?.integrationId) searchParams.append('integrationId', params.integrationId);
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.offset) searchParams.append('offset', params.offset.toString());

    const queryString = searchParams.toString();
    const endpoint = queryString ? `/call-logs?${queryString}` : '/call-logs';
    
    return this.request<OpenPhoneCallLog[]>(endpoint);
  }

  async getMessageLogs(params?: {
    clientId?: string;
    integrationId?: string;
    limit?: number;
    offset?: number;
  }): Promise<OpenPhoneMessageLog[]> {
    const searchParams = new URLSearchParams();
    if (params?.clientId) searchParams.append('clientId', params.clientId);
    if (params?.integrationId) searchParams.append('integrationId', params.integrationId);
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.offset) searchParams.append('offset', params.offset.toString());

    const queryString = searchParams.toString();
    const endpoint = queryString ? `/message-logs?${queryString}` : '/message-logs';
    
    return this.request<OpenPhoneMessageLog[]>(endpoint);
  }
}

export const openPhoneApiService = new OpenPhoneApiService();
