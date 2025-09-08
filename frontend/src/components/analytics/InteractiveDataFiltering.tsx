'use client';

import React, { useState, useEffect, useMemo } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table';
import { 
  Filter, 
  Search, 
  SortAsc, 
  SortDesc, 
  Download, 
  RefreshCw,
  Calendar,
  Users,
  Target,
  TrendingUp,
  TrendingDown,
  Eye,
  BarChart3,
  PieChart,
  Loader2,
  AlertCircle,
  ChevronDown,
  ChevronUp
} from 'lucide-react';

interface FilterState {
  search: string;
  status: string;
  dateRange: {
    start: string;
    end: string;
  };
  source: string;
  practiceArea: string;
  sortBy: string;
  sortOrder: 'asc' | 'desc';
}

interface DataInsight {
  metric: string;
  value: number;
  change: number;
  trend: 'up' | 'down' | 'stable';
}

export default function InteractiveDataFiltering() {
  const { user } = useAuth();
  const { 
    leads, 
    campaigns, 
    getLeads, 
    getCampaigns,
    getLoadingState, 
    getErrorState 
  } = useData();

  const [filters, setFilters] = useState<FilterState>({
    search: '',
    status: '',
    dateRange: {
      start: '',
      end: ''
    },
    source: '',
    practiceArea: '',
    sortBy: 'createdAt',
    sortOrder: 'desc'
  });

  const [showAdvancedFilters, setShowAdvancedFilters] = useState(false);
  const [selectedRows, setSelectedRows] = useState<string[]>([]);
  const [insights, setInsights] = useState<DataInsight[]>([]);

  const isLoading = getLoadingState('leads') || getLoadingState('campaigns');

  // Filter and sort data
  const filteredData = useMemo(() => {
    let filtered = [...leads];

    // Apply search filter
    if (filters.search) {
      filtered = filtered.filter(lead => 
        lead.name?.toLowerCase().includes(filters.search.toLowerCase()) ||
        lead.email?.toLowerCase().includes(filters.search.toLowerCase()) ||
        lead.firm?.toLowerCase().includes(filters.search.toLowerCase())
      );
    }

    // Apply status filter
    if (filters.status) {
      filtered = filtered.filter(lead => lead.status === filters.status);
    }

    // Apply date range filter
    if (filters.dateRange.start) {
      filtered = filtered.filter(lead => 
        new Date(lead.createdAt) >= new Date(filters.dateRange.start)
      );
    }
    if (filters.dateRange.end) {
      filtered = filtered.filter(lead => 
        new Date(lead.createdAt) <= new Date(filters.dateRange.end)
      );
    }

    // Apply source filter
    if (filters.source) {
      filtered = filtered.filter(lead => 
        lead.practiceAreas?.includes(filters.source)
      );
    }

    // Apply practice area filter
    if (filters.practiceArea) {
      filtered = filtered.filter(lead => 
        lead.practiceAreas?.includes(filters.practiceArea)
      );
    }

    // Apply sorting
    filtered.sort((a, b) => {
      let aValue: any = a[filters.sortBy as keyof typeof a];
      let bValue: any = b[filters.sortBy as keyof typeof b];

      if (filters.sortBy === 'createdAt') {
        aValue = new Date(aValue);
        bValue = new Date(bValue);
      }

      if (typeof aValue === 'string') {
        aValue = aValue.toLowerCase();
        bValue = bValue.toLowerCase();
      }

      if (filters.sortOrder === 'asc') {
        return aValue > bValue ? 1 : -1;
      } else {
        return aValue < bValue ? 1 : -1;
      }
    });

    return filtered;
  }, [leads, filters]);

  // Calculate insights
  useEffect(() => {
    const totalLeads = leads.length;
    const filteredLeads = filteredData.length;
    const qualifiedLeads = leads.filter(l => l.status === 'qualified').length;
    const conversionRate = totalLeads > 0 ? (qualifiedLeads / totalLeads) * 100 : 0;

    // Calculate trends (mock data for now)
    const previousPeriodLeads = Math.floor(totalLeads * 0.8);
    const leadChange = totalLeads - previousPeriodLeads;
    const leadTrend = leadChange > 0 ? 'up' : leadChange < 0 ? 'down' : 'stable';

    setInsights([
      {
        metric: 'Total Leads',
        value: totalLeads,
        change: Math.abs(leadChange),
        trend: leadTrend
      },
      {
        metric: 'Filtered Results',
        value: filteredLeads,
        change: 0,
        trend: 'stable'
      },
      {
        metric: 'Conversion Rate',
        value: conversionRate,
        change: 2.3,
        trend: 'up'
      },
      {
        metric: 'Qualified Leads',
        value: qualifiedLeads,
        change: 5,
        trend: 'up'
      }
    ]);
  }, [leads, filteredData]);

  const handleFilterChange = (key: keyof FilterState, value: any) => {
    setFilters(prev => ({
      ...prev,
      [key]: value
    }));
  };

  const handleSort = (column: string) => {
    if (filters.sortBy === column) {
      setFilters(prev => ({
        ...prev,
        sortOrder: prev.sortOrder === 'asc' ? 'desc' : 'asc'
      }));
    } else {
      setFilters(prev => ({
        ...prev,
        sortBy: column,
        sortOrder: 'asc'
      }));
    }
  };

  const clearFilters = () => {
    setFilters({
      search: '',
      status: '',
      dateRange: { start: '', end: '' },
      source: '',
      practiceArea: '',
      sortBy: 'createdAt',
      sortOrder: 'desc'
    });
    setSelectedRows([]);
  };

  const exportFilteredData = () => {
    const csvContent = [
      ['Name', 'Email', 'Phone', 'Firm', 'Status', 'Practice Areas', 'Created At'],
      ...filteredData.map(lead => [
        lead.name || '',
        lead.email || '',
        lead.phone || '',
        lead.firm || '',
        lead.status || '',
        lead.practiceAreas?.join(', ') || '',
        new Date(lead.createdAt).toLocaleDateString()
      ])
    ].map(row => row.join(',')).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `filtered-leads-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  const getSortIcon = (column: string) => {
    if (filters.sortBy !== column) return null;
    return filters.sortOrder === 'asc' ? 
      <SortAsc className="h-4 w-4" /> : 
      <SortDesc className="h-4 w-4" />;
  };

  const getTrendIcon = (trend: string) => {
    switch (trend) {
      case 'up':
        return <TrendingUp className="h-4 w-4 text-green-500" />;
      case 'down':
        return <TrendingDown className="h-4 w-4 text-red-500" />;
      default:
        return <BarChart3 className="h-4 w-4 text-gray-500" />;
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'pending':
        return 'secondary';
      case 'contacted':
        return 'default';
      case 'qualified':
        return 'default';
      case 'disqualified':
        return 'destructive';
      default:
        return 'secondary';
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Interactive Data Filtering</h2>
          <p className="text-gray-600">Filter, sort, and analyze your data with advanced controls</p>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" onClick={exportFilteredData}>
            <Download className="h-4 w-4 mr-2" />
            Export Filtered
          </Button>
          <Button variant="outline" onClick={clearFilters}>
            <RefreshCw className="h-4 w-4 mr-2" />
            Clear Filters
          </Button>
        </div>
      </div>

      {/* Data Insights */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        {insights.map((insight, index) => (
          <Card key={index}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">{insight.metric}</CardTitle>
              {getTrendIcon(insight.trend)}
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{insight.value.toFixed(insight.metric.includes('Rate') ? 1 : 0)}{insight.metric.includes('Rate') ? '%' : ''}</div>
              <p className="text-xs text-muted-foreground">
                {insight.change > 0 ? '+' : ''}{insight.change} from last period
              </p>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>Filters & Search</CardTitle>
              <CardDescription>Refine your data with advanced filtering options</CardDescription>
            </div>
            <Button
              variant="outline"
              size="sm"
              onClick={() => setShowAdvancedFilters(!showAdvancedFilters)}
            >
              {showAdvancedFilters ? (
                <>
                  <ChevronUp className="h-4 w-4 mr-2" />
                  Hide Advanced
                </>
              ) : (
                <>
                  <ChevronDown className="h-4 w-4 mr-2" />
                  Show Advanced
                </>
              )}
            </Button>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          {/* Basic Filters */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="space-y-2">
              <Label htmlFor="search">Search</Label>
              <div className="relative">
                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                <Input
                  id="search"
                  placeholder="Search leads..."
                  value={filters.search}
                  onChange={(e) => handleFilterChange('search', e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div className="space-y-2">
              <Label htmlFor="status">Status</Label>
              <Select value={filters.status} onValueChange={(value) => handleFilterChange('status', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="All statuses" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="">All statuses</SelectItem>
                  <SelectItem value="pending">Pending</SelectItem>
                  <SelectItem value="contacted">Contacted</SelectItem>
                  <SelectItem value="qualified">Qualified</SelectItem>
                  <SelectItem value="disqualified">Disqualified</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="practiceArea">Practice Area</Label>
              <Select value={filters.practiceArea} onValueChange={(value) => handleFilterChange('practiceArea', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="All practice areas" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="">All practice areas</SelectItem>
                  <SelectItem value="Personal Injury">Personal Injury</SelectItem>
                  <SelectItem value="Criminal Defense">Criminal Defense</SelectItem>
                  <SelectItem value="Family Law">Family Law</SelectItem>
                  <SelectItem value="Estate Planning">Estate Planning</SelectItem>
                  <SelectItem value="Business Law">Business Law</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          {/* Advanced Filters */}
          {showAdvancedFilters && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
              <div className="space-y-2">
                <Label htmlFor="startDate">Start Date</Label>
                <Input
                  id="startDate"
                  type="date"
                  value={filters.dateRange.start}
                  onChange={(e) => handleFilterChange('dateRange', { ...filters.dateRange, start: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="endDate">End Date</Label>
                <Input
                  id="endDate"
                  type="date"
                  value={filters.dateRange.end}
                  onChange={(e) => handleFilterChange('dateRange', { ...filters.dateRange, end: e.target.value })}
                />
              </div>
            </div>
          )}

          {/* Active Filters */}
          <div className="flex flex-wrap gap-2">
            {filters.search && (
              <Badge variant="outline" className="flex items-center gap-1">
                Search: {filters.search}
                <button onClick={() => handleFilterChange('search', '')}>×</button>
              </Badge>
            )}
            {filters.status && (
              <Badge variant="outline" className="flex items-center gap-1">
                Status: {filters.status}
                <button onClick={() => handleFilterChange('status', '')}>×</button>
              </Badge>
            )}
            {filters.practiceArea && (
              <Badge variant="outline" className="flex items-center gap-1">
                Practice: {filters.practiceArea}
                <button onClick={() => handleFilterChange('practiceArea', '')}>×</button>
              </Badge>
            )}
            {(filters.dateRange.start || filters.dateRange.end) && (
              <Badge variant="outline" className="flex items-center gap-1">
                Date Range
                <button onClick={() => handleFilterChange('dateRange', { start: '', end: '' })}>×</button>
              </Badge>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Data Table */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>Filtered Results</CardTitle>
              <CardDescription>
                {filteredData.length} of {leads.length} leads
                {selectedRows.length > 0 && ` • ${selectedRows.length} selected`}
              </CardDescription>
            </div>
            {selectedRows.length > 0 && (
              <div className="flex items-center gap-2">
                <Button variant="outline" size="sm">
                  Bulk Action
                </Button>
                <Button variant="outline" size="sm" onClick={() => setSelectedRows([])}>
                  Clear Selection
                </Button>
              </div>
            )}
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-6 w-6 animate-spin mr-2" />
              Loading data...
            </div>
          ) : filteredData.length === 0 ? (
            <div className="text-center py-8">
              <Users className="h-12 w-12 mx-auto mb-4 text-gray-400" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">No Results Found</h3>
              <p className="text-gray-600">Try adjusting your filters to see more data.</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-12">
                      <input
                        type="checkbox"
                        checked={selectedRows.length === filteredData.length && filteredData.length > 0}
                        onChange={(e) => {
                          if (e.target.checked) {
                            setSelectedRows(filteredData.map(lead => lead.id));
                          } else {
                            setSelectedRows([]);
                          }
                        }}
                      />
                    </TableHead>
                    <TableHead>
                      <button 
                        onClick={() => handleSort('name')}
                        className="flex items-center gap-1 hover:text-blue-600"
                      >
                        Name {getSortIcon('name')}
                      </button>
                    </TableHead>
                    <TableHead>
                      <button 
                        onClick={() => handleSort('email')}
                        className="flex items-center gap-1 hover:text-blue-600"
                      >
                        Email {getSortIcon('email')}
                      </button>
                    </TableHead>
                    <TableHead>
                      <button 
                        onClick={() => handleSort('status')}
                        className="flex items-center gap-1 hover:text-blue-600"
                      >
                        Status {getSortIcon('status')}
                      </button>
                    </TableHead>
                    <TableHead>Practice Areas</TableHead>
                    <TableHead>
                      <button 
                        onClick={() => handleSort('createdAt')}
                        className="flex items-center gap-1 hover:text-blue-600"
                      >
                        Created {getSortIcon('createdAt')}
                      </button>
                    </TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredData.map((lead) => (
                    <TableRow key={lead.id}>
                      <TableCell>
                        <input
                          type="checkbox"
                          checked={selectedRows.includes(lead.id)}
                          onChange={(e) => {
                            if (e.target.checked) {
                              setSelectedRows(prev => [...prev, lead.id]);
                            } else {
                              setSelectedRows(prev => prev.filter(id => id !== lead.id));
                            }
                          }}
                        />
                      </TableCell>
                      <TableCell className="font-medium">
                        {lead.name || '—'}
                      </TableCell>
                      <TableCell>{lead.email}</TableCell>
                      <TableCell>
                        <Badge variant={getStatusBadgeVariant(lead.status)}>
                          {lead.status}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        {lead.practiceAreas && lead.practiceAreas.length > 0 ? (
                          <div className="flex flex-wrap gap-1">
                            {lead.practiceAreas.slice(0, 2).map((area, index) => (
                              <Badge key={index} variant="outline" className="text-xs">
                                {area}
                              </Badge>
                            ))}
                            {lead.practiceAreas.length > 2 && (
                              <Badge variant="outline" className="text-xs">
                                +{lead.practiceAreas.length - 2}
                              </Badge>
                            )}
                          </div>
                        ) : (
                          '—'
                        )}
                      </TableCell>
                      <TableCell>{formatDate(lead.createdAt)}</TableCell>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4 mr-2" />
                            View
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
