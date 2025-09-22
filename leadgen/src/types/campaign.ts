import { Vertical } from './canonical';

export interface CampaignSpec {
  name: string;
  vertical: Vertical;
  geo: GeoFilter;
  filters: LeadFilters;
  sources: string[];
  enrichment: string[];
  budget: BudgetConfig;
  schedule: ScheduleConfig;
  destinations: DestinationConfig[];
}

export interface GeoFilter {
  city?: string;
  region?: string;
  country: string;
  radius_km: number;
  lat?: number;
  lon?: number;
}

export interface LeadFilters {
  min_rating?: number;
  review_count_min?: number;
  keywords?: string[];
  types?: string[];
  open_now?: boolean;
}

export interface BudgetConfig {
  max_cost_usd: number;
}

export interface ScheduleConfig {
  cron?: string;
  enabled: boolean;
}

export interface DestinationConfig {
  type: string;
  config_id: string;
}

export interface QueryPlan {
  sources: ProviderQuery[];
  estimated_cost: number;
  estimated_leads: number;
}

export interface ProviderQuery {
  provider: string;
  query_params: Record<string, any>;
  pagination?: PaginationConfig;
}

export interface PaginationConfig {
  cursor?: string;
  limit?: number;
  has_more: boolean;
}

export interface Page<T> {
  items: T[];
  next_cursor?: string;
  has_more: boolean;
  total_estimated?: number;
}

export interface RateLimitPolicy {
  requests_per_second: number;
  burst_limit: number;
  daily_quota?: number;
}

export interface CampaignRun {
  id: string;
  campaign_id: string;
  status: RunStatus;
  started_at: Date;
  completed_at?: Date;
  total_leads: number;
  successful_leads: number;
  failed_leads: number;
  cost_usd: number;
  error_message?: string;
  export_path?: string;
}

export enum RunStatus {
  PENDING = 'pending',
  RUNNING = 'running',
  COMPLETED = 'completed',
  FAILED = 'failed',
  CANCELLED = 'cancelled'
}
