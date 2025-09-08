'use client';

import React, { useState, useEffect, useRef } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  LineChart, 
  Line, 
  AreaChart, 
  Area, 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  Legend, 
  ResponsiveContainer,
  ReferenceLine
} from 'recharts';
import { 
  Activity, 
  TrendingUp, 
  TrendingDown, 
  Zap, 
  Eye, 
  Users, 
  Phone,
  Globe,
  RefreshCw,
  Play,
  Pause,
  Square
} from 'lucide-react';

interface RealTimeData {
  timestamp: string;
  leads: number;
  calls: number;
  websiteViews: number;
  gbpViews: number;
  conversions: number;
}

interface RealTimeMetrics {
  currentLeads: number;
  currentCalls: number;
  currentViews: number;
  peakHour: string;
  avgResponseTime: number;
  conversionRate: number;
}

export default function RealTimeVisualization() {
  const { user } = useAuth();
  const { leads, getLeads } = useData();
  
  const [realTimeData, setRealTimeData] = useState<RealTimeData[]>([]);
  const [metrics, setMetrics] = useState<RealTimeMetrics | null>(null);
  const [isLive, setIsLive] = useState(false);
  const [timeRange, setTimeRange] = useState<'1h' | '6h' | '24h'>('1h');
  const intervalRef = useRef<NodeJS.Timeout | null>(null);

  useEffect(() => {
    if (isLive) {
      startRealTimeUpdates();
    } else {
      stopRealTimeUpdates();
    }

    return () => stopRealTimeUpdates();
  }, [isLive]);

  useEffect(() => {
    generateInitialData();
  }, [timeRange]);

  const startRealTimeUpdates = () => {
    intervalRef.current = setInterval(() => {
      addNewDataPoint();
    }, 5000); // Update every 5 seconds
  };

  const stopRealTimeUpdates = () => {
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
  };

  const generateInitialData = () => {
    const now = new Date();
    const dataPoints = timeRange === '1h' ? 12 : timeRange === '6h' ? 72 : 288;
    const intervalMinutes = timeRange === '1h' ? 5 : timeRange === '6h' ? 5 : 5;

    const initialData: RealTimeData[] = [];
    
    for (let i = dataPoints - 1; i >= 0; i--) {
      const timestamp = new Date(now.getTime() - i * intervalMinutes * 60 * 1000);
      const baseLeads = Math.floor(Math.random() * 3) + 1;
      const baseCalls = Math.floor(Math.random() * 5) + 2;
      const baseViews = Math.floor(Math.random() * 50) + 20;
      
      initialData.push({
        timestamp: timestamp.toLocaleTimeString('en-US', { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: false 
        }),
        leads: baseLeads,
        calls: baseCalls,
        websiteViews: baseViews,
        gbpViews: Math.floor(baseViews * 0.3),
        conversions: Math.floor(Math.random() * 2)
      });
    }

    setRealTimeData(initialData);
    updateMetrics(initialData);
  };

  const addNewDataPoint = () => {
    const now = new Date();
    const lastData = realTimeData[realTimeData.length - 1];
    
    // Generate realistic new data point
    const newDataPoint: RealTimeData = {
      timestamp: now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
      }),
      leads: Math.max(0, lastData.leads + Math.floor(Math.random() * 3) - 1),
      calls: Math.max(0, lastData.calls + Math.floor(Math.random() * 5) - 2),
      websiteViews: Math.max(0, lastData.websiteViews + Math.floor(Math.random() * 20) - 10),
      gbpViews: Math.max(0, lastData.gbpViews + Math.floor(Math.random() * 10) - 5),
      conversions: Math.max(0, lastData.conversions + Math.floor(Math.random() * 2))
    };

    const newData = [...realTimeData.slice(-47), newDataPoint]; // Keep last 48 data points
    setRealTimeData(newData);
    updateMetrics(newData);
  };

  const updateMetrics = (data: RealTimeData[]) => {
    const current = data[data.length - 1];
    const totalLeads = data.reduce((sum, point) => sum + point.leads, 0);
    const totalCalls = data.reduce((sum, point) => sum + point.calls, 0);
    const totalViews = data.reduce((sum, point) => sum + point.websiteViews, 0);
    const totalConversions = data.reduce((sum, point) => sum + point.conversions, 0);

    // Find peak hour
    const hourlyData = data.reduce((acc, point) => {
      const hour = point.timestamp.split(':')[0];
      acc[hour] = (acc[hour] || 0) + point.leads;
      return acc;
    }, {} as Record<string, number>);

    const peakHour = Object.entries(hourlyData).reduce((max, [hour, count]) => 
      count > max.count ? { hour, count } : max, 
      { hour: '00', count: 0 }
    );

    setMetrics({
      currentLeads: current.leads,
      currentCalls: current.calls,
      currentViews: current.websiteViews,
      peakHour: `${peakHour.hour}:00`,
      avgResponseTime: Math.floor(Math.random() * 30) + 5, // Mock data
      conversionRate: totalLeads > 0 ? (totalConversions / totalLeads) * 100 : 0
    });
  };

  const formatTimeRange = (range: string) => {
    switch (range) {
      case '1h': return 'Last Hour';
      case '6h': return 'Last 6 Hours';
      case '24h': return 'Last 24 Hours';
      default: return 'Last Hour';
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <Activity className="h-6 w-6" />
            Real-Time Analytics
          </h2>
          <p className="text-gray-600">Live data updates and performance monitoring</p>
        </div>
        <div className="flex items-center gap-2">
          <Badge variant={isLive ? 'default' : 'secondary'} className="flex items-center gap-1">
            <div className={`w-2 h-2 rounded-full ${isLive ? 'bg-green-500 animate-pulse' : 'bg-gray-400'}`} />
            {isLive ? 'Live' : 'Paused'}
          </Badge>
          <Button
            variant="outline"
            size="sm"
            onClick={() => setIsLive(!isLive)}
          >
            {isLive ? (
              <>
                <Pause className="h-4 w-4 mr-2" />
                Pause
              </>
            ) : (
              <>
                <Play className="h-4 w-4 mr-2" />
                Start Live
              </>
            )}
          </Button>
          <Button
            variant="outline"
            size="sm"
            onClick={generateInitialData}
          >
            <RefreshCw className="h-4 w-4 mr-2" />
            Refresh
          </Button>
        </div>
      </div>

      {/* Current Metrics */}
      {metrics && (
        <div className="grid grid-cols-1 md:grid-cols-6 gap-4">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Current Leads</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.currentLeads}</div>
              <p className="text-xs text-muted-foreground">Right now</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Active Calls</CardTitle>
              <Phone className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.currentCalls}</div>
              <p className="text-xs text-muted-foreground">In progress</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Website Views</CardTitle>
              <Eye className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.currentViews}</div>
              <p className="text-xs text-muted-foreground">Current hour</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Peak Hour</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.peakHour}</div>
              <p className="text-xs text-muted-foreground">Most active</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Response Time</CardTitle>
              <Zap className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.avgResponseTime}s</div>
              <p className="text-xs text-muted-foreground">Average</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Conversion Rate</CardTitle>
              <Globe className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{metrics.conversionRate.toFixed(1)}%</div>
              <p className="text-xs text-muted-foreground">Live rate</p>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Real-Time Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Activity Over Time */}
        <Card>
          <CardHeader>
            <CardTitle>Activity Over Time</CardTitle>
            <CardDescription>{formatTimeRange(timeRange)} - Live updates</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={realTimeData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis 
                    dataKey="timestamp" 
                    tick={{ fontSize: 12 }}
                    interval="preserveStartEnd"
                  />
                  <YAxis />
                  <Tooltip 
                    labelStyle={{ color: '#374151' }}
                    contentStyle={{ 
                      backgroundColor: 'white', 
                      border: '1px solid #e5e7eb',
                      borderRadius: '8px'
                    }}
                  />
                  <Legend />
                  <Area 
                    type="monotone" 
                    dataKey="leads" 
                    stackId="1" 
                    stroke="#3b82f6" 
                    fill="#3b82f6" 
                    fillOpacity={0.6}
                    name="Leads"
                  />
                  <Area 
                    type="monotone" 
                    dataKey="calls" 
                    stackId="1" 
                    stroke="#10b981" 
                    fill="#10b981" 
                    fillOpacity={0.6}
                    name="Calls"
                  />
                  <Area 
                    type="monotone" 
                    dataKey="conversions" 
                    stackId="1" 
                    stroke="#f59e0b" 
                    fill="#f59e0b" 
                    fillOpacity={0.6}
                    name="Conversions"
                  />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        {/* Website Traffic */}
        <Card>
          <CardHeader>
            <CardTitle>Website Traffic</CardTitle>
            <CardDescription>Views and engagement metrics</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={realTimeData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis 
                    dataKey="timestamp" 
                    tick={{ fontSize: 12 }}
                    interval="preserveStartEnd"
                  />
                  <YAxis />
                  <Tooltip 
                    labelStyle={{ color: '#374151' }}
                    contentStyle={{ 
                      backgroundColor: 'white', 
                      border: '1px solid #e5e7eb',
                      borderRadius: '8px'
                    }}
                  />
                  <Legend />
                  <Line 
                    type="monotone" 
                    dataKey="websiteViews" 
                    stroke="#8b5cf6" 
                    strokeWidth={2}
                    dot={{ fill: '#8b5cf6', strokeWidth: 2, r: 4 }}
                    name="Website Views"
                  />
                  <Line 
                    type="monotone" 
                    dataKey="gbpViews" 
                    stroke="#06b6d4" 
                    strokeWidth={2}
                    dot={{ fill: '#06b6d4', strokeWidth: 2, r: 4 }}
                    name="GBP Views"
                  />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Live Activity Feed */}
      <Card>
        <CardHeader>
          <CardTitle>Live Activity Feed</CardTitle>
          <CardDescription>Real-time events and notifications</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3 max-h-64 overflow-y-auto">
            {realTimeData.slice(-10).reverse().map((data, index) => (
              <div key={index} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
                <div className="flex-1">
                  <div className="flex items-center gap-2">
                    <span className="text-sm font-medium">{data.timestamp}</span>
                    <Badge variant="outline" className="text-xs">
                      {data.leads > 0 ? 'New Lead' : 'Activity'}
                    </Badge>
                  </div>
                  <div className="text-xs text-gray-600">
                    {data.leads > 0 && `${data.leads} new lead${data.leads > 1 ? 's' : ''}`}
                    {data.calls > 0 && ` • ${data.calls} call${data.calls > 1 ? 's' : ''}`}
                    {data.websiteViews > 0 && ` • ${data.websiteViews} views`}
                    {data.conversions > 0 && ` • ${data.conversions} conversion${data.conversions > 1 ? 's' : ''}`}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
