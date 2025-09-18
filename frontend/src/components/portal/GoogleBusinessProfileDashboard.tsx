"use client";

import React, { useEffect, useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Progress } from "@/components/ui/progress";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { 
  Eye, 
  Phone, 
  Star, 
  TrendingUp, 
  TrendingDown, 
  MapPin, 
  ExternalLink,
  RefreshCw,
  AlertCircle,
  CheckCircle,
  Clock,
  Users,
  MessageSquare,
  Calendar,
  BarChart3,
  PieChart
} from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar, PieChart as RechartsPieChart, Cell } from "recharts";
import { useAuth } from "@/hooks/useAuth";
import api from "@/services/api";

interface GbpKpiData {
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
}

interface GbpError {
  message: string;
  type: 'connection' | 'data' | 'permission';
}

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884d8'];

export default function GoogleBusinessProfileDashboard() {
  const { user } = useAuth();
  const [gbpData, setGbpData] = useState<GbpKpiData | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [isConnecting, setIsConnecting] = useState<boolean>(false);
  const [error, setError] = useState<GbpError | null>(null);
  const [activeTab, setActiveTab] = useState<string>('overview');

  // Mock historical data for charts
  const [historicalData] = useState([
    { date: '2024-01-01', views: 1200, calls: 25, reviews: 4.2, visibility: 65 },
    { date: '2024-01-08', views: 1350, calls: 28, reviews: 4.3, visibility: 68 },
    { date: '2024-01-15', views: 1180, calls: 22, reviews: 4.4, visibility: 70 },
    { date: '2024-01-22', views: 1420, calls: 31, reviews: 4.5, visibility: 72 },
    { date: '2024-01-29', views: 1560, calls: 35, reviews: 4.6, visibility: 75 },
    { date: '2024-02-05', views: 1680, calls: 38, reviews: 4.7, visibility: 78 },
  ]);

  const [actionData] = useState([
    { name: 'Website Clicks', value: 892, color: '#0088FE' },
    { name: 'Direction Requests', value: 156, color: '#00C49F' },
    { name: 'Phone Calls', value: 348, color: '#FFBB28' },
    { name: 'Messages', value: 89, color: '#FF8042' },
  ]);

  useEffect(() => {
    if (user?.clientId) {
      loadGbpData();
    }
  }, [user?.clientId]);

  const loadGbpData = async () => {
    if (!user?.clientId) return;
    
    setIsLoading(true);
    setError(null);
    
    try {
      const response = await api.getGbpKpi(user.clientId);
      setGbpData(response);
    } catch (err: any) {
      if (err?.message?.includes('Google Business Profile not connected') || 
          err?.message?.includes('connected: false')) {
        setGbpData({ connected: false });
        setError(null);
      } else {
        setError({
          message: err?.message || "Failed to load Google Business Profile data",
          type: 'data'
        });
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleConnect = async () => {
    if (!user?.clientId) return;
    
    setIsConnecting(true);
    setError(null);
    
    try {
      // Use the API service method to initiate OAuth flow
      await api.initiateGbpAuth(user.clientId);
    } catch (err: any) {
      setError({
        message: err?.message || "Failed to initiate Google Business Profile connection",
        type: 'connection'
      });
      setIsConnecting(false);
    }
  };

  const formatNumber = (num: number): string => {
    if (num >= 1000000) {
      return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
      return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
  };

  const formatChange = (change: number): { text: string; positive: boolean } => {
    const positive = change >= 0;
    return {
      text: `${positive ? '+' : ''}${change}%`,
      positive
    };
  };

  const renderMetricCard = (
    title: string,
    value: number | string,
    change?: number,
    icon?: React.ReactNode,
    subtitle?: string,
    format?: 'number' | 'percentage' | 'rating'
  ) => {
    let displayValue = value;
    let displaySubtitle = subtitle;

    if (format === 'number' && typeof value === 'number') {
      displayValue = formatNumber(value);
    } else if (format === 'percentage' && typeof value === 'number') {
      displayValue = `${value}%`;
    } else if (format === 'rating' && typeof value === 'number') {
      displayValue = value.toFixed(1);
      displaySubtitle = `${displaySubtitle} (${value.toFixed(1)}/5.0)`;
    }

    const changeInfo = change !== undefined ? formatChange(change) : null;

    return (
      <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">{title}</CardTitle>
          {icon}
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{displayValue}</div>
          {displaySubtitle && (
            <p className="text-xs text-muted-foreground mt-1">{displaySubtitle}</p>
          )}
          {changeInfo && (
            <div className="flex items-center mt-2">
              {changeInfo.positive ? (
                <TrendingUp className="h-4 w-4 text-green-500 mr-1" />
              ) : (
                <TrendingDown className="h-4 w-4 text-red-500 mr-1" />
              )}
              <span className={`text-xs ${changeInfo.positive ? 'text-green-600' : 'text-red-600'}`}>
                {changeInfo.text} from last month
              </span>
            </div>
          )}
        </CardContent>
      </Card>
    );
  };

  if (!user?.clientId) {
    return (
      <Alert>
        <AlertCircle className="h-4 w-4" />
        <AlertTitle>Access Required</AlertTitle>
        <AlertDescription>
          You need to be associated with a client to view Google Business Profile data.
        </AlertDescription>
      </Alert>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold">Google Business Profile</h2>
          <p className="text-muted-foreground">
            Track your local search performance and customer interactions
          </p>
        </div>
        <div className="flex items-center gap-2">
          <Button
            onClick={loadGbpData}
            disabled={isLoading}
            variant="outline"
            size="sm"
          >
            {isLoading ? (
              <RefreshCw className="h-4 w-4 animate-spin mr-2" />
            ) : (
              <RefreshCw className="h-4 w-4 mr-2" />
            )}
            Refresh
          </Button>
        </div>
      </div>

      {/* Connection Status */}
      {!gbpData?.connected && (
        <Alert>
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Google Business Profile Not Connected</AlertTitle>
          <AlertDescription className="mt-2">
            Connect your Google Business Profile to see real-time insights about your local visibility, 
            customer interactions, and review performance.
            <div className="mt-4">
              <Button
                onClick={handleConnect}
                disabled={isConnecting}
                className="mr-2"
              >
                {isConnecting ? (
                  <>
                    <RefreshCw className="h-4 w-4 animate-spin mr-2" />
                    Connecting...
                  </>
                ) : (
                  <>
                    <ExternalLink className="h-4 w-4 mr-2" />
                    Connect Google Business Profile
                  </>
                )}
              </Button>
            </div>
          </AlertDescription>
        </Alert>
      )}

      {/* Error Display */}
      {error && (
        <Alert variant="destructive">
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Error</AlertTitle>
          <AlertDescription>
            {error.message}
            <div className="mt-2">
              <Button
                onClick={() => setError(null)}
                variant="outline"
                size="sm"
              >
                Dismiss
              </Button>
            </div>
          </AlertDescription>
        </Alert>
      )}

      {/* Connected Dashboard */}
      {gbpData?.connected && gbpData.kpi && (
        <>
          {/* Key Metrics */}
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            {renderMetricCard(
              "Profile Views",
              gbpData.kpi.views.total,
              gbpData.kpi.views.change,
              <Eye className="h-4 w-4 text-blue-500" />,
              `Last ${gbpData.kpi.views.period.replace('_', ' ')}`,
              'number'
            )}
            {renderMetricCard(
              "Phone Calls",
              gbpData.kpi.calls.total,
              gbpData.kpi.calls.change,
              <Phone className="h-4 w-4 text-green-500" />,
              `Last ${gbpData.kpi.calls.period.replace('_', ' ')}`,
              'number'
            )}
            {renderMetricCard(
              "Average Rating",
              gbpData.kpi.reviews.average,
              gbpData.kpi.reviews.change,
              <Star className="h-4 w-4 text-yellow-500" />,
              `${gbpData.kpi.reviews.total} total reviews`,
              'rating'
            )}
            {renderMetricCard(
              "Local Visibility",
              gbpData.kpi.localVisibility.score,
              gbpData.kpi.localVisibility.change,
              <MapPin className="h-4 w-4 text-purple-500" />,
              "Local search performance",
              'percentage'
            )}
          </div>

          {/* Detailed Analytics */}
          <Tabs value={activeTab} onValueChange={setActiveTab}>
            <TabsList className="grid w-full grid-cols-4">
              <TabsTrigger value="overview">Overview</TabsTrigger>
              <TabsTrigger value="performance">Performance</TabsTrigger>
              <TabsTrigger value="actions">Actions</TabsTrigger>
              <TabsTrigger value="reviews">Reviews</TabsTrigger>
            </TabsList>

            <TabsContent value="overview" className="space-y-4">
              <div className="grid gap-4 md:grid-cols-2">
                {/* Performance Trends */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center">
                      <BarChart3 className="h-5 w-5 mr-2" />
                      Performance Trends
                    </CardTitle>
                    <CardDescription>
                      Key metrics over the last 6 weeks
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <ResponsiveContainer width="100%" height={300}>
                      <LineChart data={historicalData}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="date" />
                        <YAxis />
                        <Tooltip />
                        <Legend />
                        <Line type="monotone" dataKey="views" stroke="#8884d8" strokeWidth={2} />
                        <Line type="monotone" dataKey="calls" stroke="#82ca9d" strokeWidth={2} />
                        <Line type="monotone" dataKey="visibility" stroke="#ffc658" strokeWidth={2} />
                      </LineChart>
                    </ResponsiveContainer>
                  </CardContent>
                </Card>

                {/* Action Breakdown */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center">
                      <PieChart className="h-5 w-5 mr-2" />
                      Customer Actions
                    </CardTitle>
                    <CardDescription>
                      How customers interact with your business
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <ResponsiveContainer width="100%" height={300}>
                      <RechartsPieChart>
                        <Pie
                          data={actionData}
                          cx="50%"
                          cy="50%"
                          labelLine={false}
                          label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                          outerRadius={80}
                          fill="#8884d8"
                          dataKey="value"
                        >
                          {actionData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={entry.color} />
                          ))}
                        </Pie>
                        <Tooltip />
                      </RechartsPieChart>
                    </ResponsiveContainer>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>

            <TabsContent value="performance" className="space-y-4">
              <div className="grid gap-4">
                <Card>
                  <CardHeader>
                    <CardTitle>Local Visibility Score</CardTitle>
                    <CardDescription>
                      Your visibility in local search results
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <span className="text-2xl font-bold">
                          {gbpData.kpi.localVisibility.score}%
                        </span>
                        <Badge variant={gbpData.kpi.localVisibility.score >= 70 ? 'default' : 'secondary'}>
                          {gbpData.kpi.localVisibility.score >= 70 ? 'Excellent' : 'Good'}
                        </Badge>
                      </div>
                      <Progress value={gbpData.kpi.localVisibility.score} className="h-2" />
                      <p className="text-sm text-muted-foreground">
                        {gbpData.kpi.localVisibility.change > 0 ? '+' : ''}{gbpData.kpi.localVisibility.change}% 
                        from last {gbpData.kpi.localVisibility.period.replace('_', ' ')}
                      </p>
                    </div>
                  </CardContent>
                </Card>

                <div className="grid gap-4 md:grid-cols-2">
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-lg">Website Clicks</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold">
                        {formatNumber(gbpData.kpi.actions.website_clicks)}
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Last {gbpData.kpi.actions.period.replace('_', ' ')}
                      </p>
                    </CardContent>
                  </Card>

                  <Card>
                    <CardHeader>
                      <CardTitle className="text-lg">Direction Requests</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold">
                        {formatNumber(gbpData.kpi.actions.direction_requests)}
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Last {gbpData.kpi.actions.period.replace('_', ' ')}
                      </p>
                    </CardContent>
                  </Card>
                </div>
              </div>
            </TabsContent>

            <TabsContent value="actions" className="space-y-4">
              <div className="grid gap-4">
                <Card>
                  <CardHeader>
                    <CardTitle>Customer Actions</CardTitle>
                    <CardDescription>
                      Breakdown of customer interactions with your business profile
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <ResponsiveContainer width="100%" height={400}>
                      <BarChart data={actionData}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="name" />
                        <YAxis />
                        <Tooltip />
                        <Bar dataKey="value" fill="#8884d8" />
                      </BarChart>
                    </ResponsiveContainer>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>

            <TabsContent value="reviews" className="space-y-4">
              <div className="grid gap-4">
                <Card>
                  <CardHeader>
                    <CardTitle>Review Performance</CardTitle>
                    <CardDescription>
                      Track your review metrics and rating trends
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-6">
                      <div className="flex items-center justify-between">
                        <div>
                          <div className="text-3xl font-bold flex items-center">
                            {gbpData.kpi.reviews.average.toFixed(1)}
                            <Star className="h-8 w-8 text-yellow-500 ml-2" />
                          </div>
                          <p className="text-muted-foreground">
                            Average rating from {gbpData.kpi.reviews.total} reviews
                          </p>
                        </div>
                        <Badge variant="outline">
                          +{gbpData.kpi.reviews.change} new reviews
                        </Badge>
                      </div>
                      
                      <div className="space-y-2">
                        {[5, 4, 3, 2, 1].map((rating) => (
                          <div key={rating} className="flex items-center space-x-2">
                            <span className="text-sm w-8">{rating}★</span>
                            <Progress 
                              value={Math.random() * 100} 
                              className="flex-1 h-2" 
                            />
                            <span className="text-sm text-muted-foreground w-12">
                              {Math.floor(Math.random() * 50)}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
          </Tabs>

          {/* Last Updated */}
          {gbpData.lastUpdated && (
            <div className="flex items-center text-sm text-muted-foreground">
              <Clock className="h-4 w-4 mr-2" />
              Last updated: {new Date(gbpData.lastUpdated).toLocaleString()}
            </div>
          )}
        </>
      )}
    </div>
  );
}
