'use client';

import React, { useState, useEffect } from 'react';
import { useData } from '../../hooks/useData';
import { useAuth } from '../../hooks/useAuth';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Alert, AlertDescription } from '../ui/alert';
import { Progress } from '../ui/progress';
import { 
  Play, 
  Settings, 
  MapPin, 
  Target, 
  DollarSign,
  Clock,
  CheckCircle,
  AlertCircle,
  X,
  Download,
  RefreshCw
} from 'lucide-react';
import ProtectedRoute from '../auth/ProtectedRoute';

interface CampaignConfig {
  name: string;
  vertical: string;
  geo: {
    city: string;
    region: string;
    country: string;
    radius_km: number;
  };
  filters: {
    min_rating: number;
    keywords: string[];
    exclude_keywords: string[];
    max_results: number;
  };
  sources: string[];
  enrichment: string[];
  budget: {
    max_cost_usd: number;
  };
  schedule: {
    enabled: boolean;
  };
  client_id?: string;
}

interface CampaignResult {
  campaign_id: string;
  leads_generated: number;
  leads_imported: number;
  leads_updated: number;
  leads_skipped: number;
  errors: string[];
  execution_time: number;
  cost: number;
}

export default function LeadgenExecution() {
  const { user, isAdmin } = useAuth();
  const { 
    executeLeadgenCampaign,
    getLeadgenVerticals,
    getLeadgenSources,
    getLeadgenTemplate,
    clients,
    getLoadingState,
    getErrorState,
    clearError
  } = useData();

  const [config, setConfig] = useState<CampaignConfig>({
    name: '',
    vertical: 'local_services',
    geo: {
      city: '',
      region: '',
      country: 'US',
      radius_km: 30
    },
    filters: {
      min_rating: 3.0,
      keywords: [],
      exclude_keywords: [],
      max_results: 100
    },
    sources: ['google_places'],
    enrichment: [],
    budget: {
      max_cost_usd: 25
    },
    schedule: {
      enabled: false
    }
  });

  const [verticals, setVerticals] = useState<Record<string, string>>({});
  const [sources, setSources] = useState<Record<string, string>>({});
  const [isExecuting, setIsExecuting] = useState(false);
  const [result, setResult] = useState<CampaignResult | null>(null);
  const [errors, setErrors] = useState<string[]>([]);
  const [keywordInput, setKeywordInput] = useState('');
  const [excludeKeywordInput, setExcludeKeywordInput] = useState('');

  const isLoading = getLoadingState('leadgen');
  const error = getErrorState('leadgen');

  useEffect(() => {
    loadInitialData();
  }, []);

  const loadInitialData = async () => {
    try {
      const [verticalsData, sourcesData, template] = await Promise.all([
        getLeadgenVerticals(),
        getLeadgenSources(),
        getLeadgenTemplate()
      ]);
      
      setVerticals(verticalsData);
      setSources(sourcesData);
      
      if (template) {
        setConfig(prev => ({ ...prev, ...template }));
      }
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  const handleExecute = async () => {
    if (!config.name || !config.geo.city || !config.geo.region) {
      setErrors(['Please fill in all required fields']);
      return;
    }

    setIsExecuting(true);
    setErrors([]);
    setResult(null);

    try {
      const campaignResult = await executeLeadgenCampaign(config);
      setResult(campaignResult.result);
    } catch (error) {
      setErrors([error instanceof Error ? error.message : 'Campaign execution failed']);
    } finally {
      setIsExecuting(false);
    }
  };

  const handleAddKeyword = () => {
    if (keywordInput.trim()) {
      setConfig(prev => ({
        ...prev,
        filters: {
          ...prev.filters,
          keywords: [...prev.filters.keywords, keywordInput.trim()]
        }
      }));
      setKeywordInput('');
    }
  };

  const handleRemoveKeyword = (index: number) => {
    setConfig(prev => ({
      ...prev,
      filters: {
        ...prev.filters,
        keywords: prev.filters.keywords.filter((_, i) => i !== index)
      }
    }));
  };

  const handleAddExcludeKeyword = () => {
    if (excludeKeywordInput.trim()) {
      setConfig(prev => ({
        ...prev,
        filters: {
          ...prev.filters,
          exclude_keywords: [...prev.filters.exclude_keywords, excludeKeywordInput.trim()]
        }
      }));
      setExcludeKeywordInput('');
    }
  };

  const handleRemoveExcludeKeyword = (index: number) => {
    setConfig(prev => ({
      ...prev,
      filters: {
        ...prev.filters,
        exclude_keywords: prev.filters.exclude_keywords.filter((_, i) => i !== index)
      }
    }));
  };

  const handleSourceToggle = (source: string) => {
    setConfig(prev => ({
      ...prev,
      sources: prev.sources.includes(source)
        ? prev.sources.filter(s => s !== source)
        : [...prev.sources, source]
    }));
  };

  const downloadTemplate = () => {
    const blob = new Blob([JSON.stringify(config, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'leadgen_campaign_config.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
  };

  if (!isAdmin()) {
    return (
      <div className="p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center text-gray-500">
              You don't have permission to access leadgen execution.
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Leadgen Execution</h1>
            <p className="text-gray-600 dark:text-gray-400 mt-1">
              Configure and execute lead generation campaigns
            </p>
          </div>
          <div className="flex gap-3">
            <Button variant="outline" onClick={downloadTemplate}>
              <Download className="h-4 w-4 mr-2" />
              Download Config
            </Button>
            <Button onClick={handleExecute} disabled={isExecuting}>
              <Play className="h-4 w-4 mr-2" />
              Execute Campaign
            </Button>
          </div>
        </div>

        {/* Error Display */}
        {(error || errors.length > 0) && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>
              <ul className="list-disc list-inside space-y-1">
                {error && <li>{error}</li>}
                {errors.map((err, index) => (
                  <li key={index}>{err}</li>
                ))}
              </ul>
            </AlertDescription>
            {error && (
              <Button variant="ghost" size="sm" onClick={() => clearError('leadgen')} className="mt-2">
                Dismiss
              </Button>
            )}
          </Alert>
        )}

        {/* Execution Progress */}
        {isExecuting && (
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-3 mb-4">
                <RefreshCw className="h-6 w-6 animate-spin text-blue-600" />
                <div>
                  <h3 className="font-semibold">Executing Campaign</h3>
                  <p className="text-sm text-gray-600 dark:text-gray-400">
                    Generating leads for {config.name}...
                  </p>
                </div>
              </div>
              <Progress value={undefined} className="w-full" />
            </CardContent>
          </Card>
        )}

        {/* Execution Result */}
        {result && (
          <Card className="border-green-200 bg-green-50 dark:bg-green-950/20">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-green-800 dark:text-green-200">
                <CheckCircle className="h-5 w-5" />
                Campaign Executed Successfully
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div className="text-center">
                  <p className="text-2xl font-bold text-green-600">{result.leads_generated}</p>
                  <p className="text-sm text-green-700 dark:text-green-300">Leads Generated</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-green-600">{result.leads_imported}</p>
                  <p className="text-sm text-green-700 dark:text-green-300">Leads Imported</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-blue-600">{result.leads_updated}</p>
                  <p className="text-sm text-blue-700 dark:text-blue-300">Leads Updated</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-600">{result.leads_skipped}</p>
                  <p className="text-sm text-gray-700 dark:text-gray-300">Leads Skipped</p>
                </div>
              </div>
              
              <div className="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                <div className="flex items-center justify-between text-sm">
                  <span>Execution Time: {result.execution_time}s</span>
                  <span>Cost: ${result.cost.toFixed(2)}</span>
                  <span>Campaign ID: {result.campaign_id}</span>
                </div>
              </div>

              {result.errors && result.errors.length > 0 && (
                <div className="mt-4">
                  <h4 className="font-semibold text-red-800 dark:text-red-200 mb-2">Errors:</h4>
                  <ul className="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                    {result.errors.map((error, index) => (
                      <li key={index}>{error}</li>
                    ))}
                  </ul>
                </div>
              )}
            </CardContent>
          </Card>
        )}

        {/* Configuration Form */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Basic Configuration */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="h-5 w-5" />
                Basic Configuration
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="name">Campaign Name *</Label>
                <Input
                  id="name"
                  value={config.name}
                  onChange={(e) => setConfig(prev => ({ ...prev, name: e.target.value }))}
                  placeholder="e.g., Tulsa Attorneys Campaign"
                />
              </div>

              <div>
                <Label htmlFor="vertical">Vertical *</Label>
                <select
                  id="vertical"
                  value={config.vertical}
                  onChange={(e) => setConfig(prev => ({ ...prev, vertical: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                >
                  {Object.entries(verticals).map(([key, label]) => (
                    <option key={key} value={key}>{label}</option>
                  ))}
                </select>
              </div>

              <div>
                <Label htmlFor="client">Assign to Client (Optional)</Label>
                <select
                  id="client"
                  value={config.client_id || ''}
                  onChange={(e) => setConfig(prev => ({ ...prev, client_id: e.target.value || undefined }))}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                >
                  <option value="">No client assignment</option>
                  {clients.map((client) => (
                    <option key={client.id} value={client.id}>
                      {client.name}
                    </option>
                  ))}
                </select>
              </div>
            </CardContent>
          </Card>

          {/* Geographic Configuration */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <MapPin className="h-5 w-5" />
                Geographic Settings
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="city">City *</Label>
                  <Input
                    id="city"
                    value={config.geo.city}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      geo: { ...prev.geo, city: e.target.value }
                    }))}
                    placeholder="e.g., Tulsa"
                  />
                </div>
                <div>
                  <Label htmlFor="region">Region *</Label>
                  <Input
                    id="region"
                    value={config.geo.region}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      geo: { ...prev.geo, region: e.target.value }
                    }))}
                    placeholder="e.g., OK"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="country">Country</Label>
                  <Input
                    id="country"
                    value={config.geo.country}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      geo: { ...prev.geo, country: e.target.value }
                    }))}
                    placeholder="e.g., US"
                  />
                </div>
                <div>
                  <Label htmlFor="radius">Radius (km)</Label>
                  <Input
                    id="radius"
                    type="number"
                    value={config.geo.radius_km}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      geo: { ...prev.geo, radius_km: parseInt(e.target.value) || 30 }
                    }))}
                    placeholder="30"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Filters */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="h-5 w-5" />
                Filters
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="min_rating">Minimum Rating</Label>
                  <Input
                    id="min_rating"
                    type="number"
                    min="0"
                    max="5"
                    step="0.1"
                    value={config.filters.min_rating}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      filters: { ...prev.filters, min_rating: parseFloat(e.target.value) || 3.0 }
                    }))}
                  />
                </div>
                <div>
                  <Label htmlFor="max_results">Max Results</Label>
                  <Input
                    id="max_results"
                    type="number"
                    value={config.filters.max_results}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      filters: { ...prev.filters, max_results: parseInt(e.target.value) || 100 }
                    }))}
                  />
                </div>
              </div>

              <div>
                <Label>Keywords</Label>
                <div className="flex gap-2 mb-2">
                  <Input
                    value={keywordInput}
                    onChange={(e) => setKeywordInput(e.target.value)}
                    placeholder="e.g., attorney"
                    onKeyPress={(e) => e.key === 'Enter' && handleAddKeyword()}
                  />
                  <Button onClick={handleAddKeyword} size="sm">
                    Add
                  </Button>
                </div>
                <div className="flex flex-wrap gap-2">
                  {config.filters.keywords.map((keyword, index) => (
                    <span
                      key={index}
                      className="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm"
                    >
                      {keyword}
                      <button
                        onClick={() => handleRemoveKeyword(index)}
                        className="hover:text-blue-600"
                      >
                        <X className="h-3 w-3" />
                      </button>
                    </span>
                  ))}
                </div>
              </div>

              <div>
                <Label>Exclude Keywords</Label>
                <div className="flex gap-2 mb-2">
                  <Input
                    value={excludeKeywordInput}
                    onChange={(e) => setExcludeKeywordInput(e.target.value)}
                    placeholder="e.g., criminal"
                    onKeyPress={(e) => e.key === 'Enter' && handleAddExcludeKeyword()}
                  />
                  <Button onClick={handleAddExcludeKeyword} size="sm">
                    Add
                  </Button>
                </div>
                <div className="flex flex-wrap gap-2">
                  {config.filters.exclude_keywords.map((keyword, index) => (
                    <span
                      key={index}
                      className="inline-flex items-center gap-1 px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-sm"
                    >
                      {keyword}
                      <button
                        onClick={() => handleRemoveExcludeKeyword(index)}
                        className="hover:text-red-600"
                      >
                        <X className="h-3 w-3" />
                      </button>
                    </span>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Sources and Budget */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <DollarSign className="h-5 w-5" />
                Sources & Budget
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label>Data Sources</Label>
                <div className="space-y-2 mt-2">
                  {Object.entries(sources).map(([key, label]) => (
                    <label key={key} className="flex items-center space-x-2">
                      <input
                        type="checkbox"
                        checked={config.sources.includes(key)}
                        onChange={() => handleSourceToggle(key)}
                        className="rounded"
                      />
                      <span>{label}</span>
                    </label>
                  ))}
                </div>
              </div>

              <div>
                <Label htmlFor="budget">Max Budget (USD)</Label>
                <Input
                  id="budget"
                  type="number"
                  value={config.budget.max_cost_usd}
                  onChange={(e) => setConfig(prev => ({ 
                    ...prev, 
                    budget: { ...prev.budget, max_cost_usd: parseFloat(e.target.value) || 25 }
                  }))}
                />
              </div>

              <div>
                <Label className="flex items-center space-x-2">
                  <input
                    type="checkbox"
                    checked={config.schedule.enabled}
                    onChange={(e) => setConfig(prev => ({ 
                      ...prev, 
                      schedule: { ...prev.schedule, enabled: e.target.checked }
                    }))}
                    className="rounded"
                  />
                  <span>Enable Scheduling</span>
                </Label>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </ProtectedRoute>
  );
}
