'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  Bell, 
  CheckCircle, 
  AlertCircle, 
  Info, 
  X, 
  Clock,
  TrendingUp,
  Users,
  Target,
  Settings,
  Calendar,
  ArrowRight
} from 'lucide-react';

interface Notification {
  id: string;
  type: 'success' | 'warning' | 'info' | 'milestone';
  title: string;
  message: string;
  action?: {
    label: string;
    url: string;
  };
  createdAt: string;
  read: boolean;
  category: 'onboarding' | 'performance' | 'system' | 'milestone';
}

export default function WorkflowNotifications() {
  const { user } = useAuth();
  const { leads, campaigns, clients } = useData();
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);

  useEffect(() => {
    generateNotifications();
  }, [user, leads, campaigns, clients]);

  const generateNotifications = () => {
    const newNotifications: Notification[] = [];

    // Onboarding notifications
    if (clients.length === 0) {
      newNotifications.push({
        id: 'complete-profile',
        type: 'info',
        title: 'Complete Your Profile',
        message: 'Finish setting up your company information to get started.',
        action: {
          label: 'Complete Profile',
          url: '/client/settings'
        },
        createdAt: new Date().toISOString(),
        read: false,
        category: 'onboarding'
      });
    }

    // Performance notifications
    if (leads.length === 0) {
      newNotifications.push({
        id: 'no-leads',
        type: 'warning',
        title: 'No Leads Yet',
        message: 'Start tracking your leads to see conversion metrics.',
        action: {
          label: 'Add Lead',
          url: '/client/leads'
        },
        createdAt: new Date().toISOString(),
        read: false,
        category: 'performance'
      });
    } else if (leads.length > 0 && leads.filter(l => l.status === 'pending').length > 5) {
      newNotifications.push({
        id: 'pending-leads',
        type: 'warning',
        title: 'Pending Leads',
        message: `You have ${leads.filter(l => l.status === 'pending').length} leads awaiting contact.`,
        action: {
          label: 'Review Leads',
          url: '/client/leads'
        },
        createdAt: new Date().toISOString(),
        read: false,
        category: 'performance'
      });
    }

    // Campaign notifications
    if (campaigns.length === 0) {
      newNotifications.push({
        id: 'create-campaign',
        type: 'info',
        title: 'Create Your First Campaign',
        message: 'Set up a marketing campaign to track your SEO efforts.',
        action: {
          label: 'Create Campaign',
          url: '/client/campaigns'
        },
        createdAt: new Date().toISOString(),
        read: false,
        category: 'performance'
      });
    }

    // Milestone notifications
    if (leads.length >= 10) {
      newNotifications.push({
        id: 'ten-leads-milestone',
        type: 'milestone',
        title: '🎉 Milestone Achieved!',
        message: 'Congratulations! You\'ve reached 10 qualified leads.',
        createdAt: new Date().toISOString(),
        read: false,
        category: 'milestone'
      });
    }

    if (campaigns.length >= 3) {
      newNotifications.push({
        id: 'three-campaigns-milestone',
        type: 'milestone',
        title: '🚀 Campaign Master!',
        message: 'You\'ve successfully created 3 campaigns. Keep up the great work!',
        createdAt: new Date().toISOString(),
        read: false,
        category: 'milestone'
      });
    }

    // System notifications
    const hasGoogleIntegration = clients.length > 0 && (
      clients[0]?.googleBusinessProfile?.profileId || 
      clients[0]?.googleAnalytics?.propertyId
    );

    if (!hasGoogleIntegration) {
      newNotifications.push({
        id: 'google-integration',
        type: 'info',
        title: 'Connect Google Services',
        message: 'Link your Google Business Profile and Analytics for better insights.',
        action: {
          label: 'Connect Google',
          url: '/client/settings'
        },
        createdAt: new Date().toISOString(),
        read: false,
        category: 'system'
      });
    }

    setNotifications(newNotifications);
    setUnreadCount(newNotifications.filter(n => !n.read).length);
  };

  const markAsRead = (notificationId: string) => {
    setNotifications(prev => 
      prev.map(notification => 
        notification.id === notificationId 
          ? { ...notification, read: true }
          : notification
      )
    );
    setUnreadCount(prev => Math.max(0, prev - 1));
  };

  const markAllAsRead = () => {
    setNotifications(prev => 
      prev.map(notification => ({ ...notification, read: true }))
    );
    setUnreadCount(0);
  };

  const dismissNotification = (notificationId: string) => {
    setNotifications(prev => prev.filter(n => n.id !== notificationId));
    setUnreadCount(prev => Math.max(0, prev - 1));
  };

  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'success':
        return <CheckCircle className="w-5 h-5 text-green-600" />;
      case 'warning':
        return <AlertCircle className="w-5 h-5 text-yellow-600" />;
      case 'info':
        return <Info className="w-5 h-5 text-blue-600" />;
      case 'milestone':
        return <Target className="w-5 h-5 text-purple-600" />;
      default:
        return <Bell className="w-5 h-5 text-gray-600" />;
    }
  };

  const getCategoryIcon = (category: string) => {
    switch (category) {
      case 'onboarding':
        return <Settings className="w-4 h-4" />;
      case 'performance':
        return <TrendingUp className="w-4 h-4" />;
      case 'system':
        return <Settings className="w-4 h-4" />;
      case 'milestone':
        return <Target className="w-4 h-4" />;
      default:
        return <Bell className="w-4 h-4" />;
    }
  };

  const getCategoryColor = (category: string) => {
    switch (category) {
      case 'onboarding':
        return 'bg-blue-100 text-blue-800';
      case 'performance':
        return 'bg-green-100 text-green-800';
      case 'system':
        return 'bg-gray-100 text-gray-800';
      case 'milestone':
        return 'bg-purple-100 text-purple-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));
    
    if (diffInHours < 1) return 'Just now';
    if (diffInHours < 24) return `${diffInHours}h ago`;
    if (diffInHours < 48) return 'Yesterday';
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle className="flex items-center gap-2">
                <Bell className="w-5 h-5" />
                Notifications
                {unreadCount > 0 && (
                  <Badge variant="destructive">{unreadCount}</Badge>
                )}
              </CardTitle>
              <CardDescription>
                Stay updated with important information and milestones
              </CardDescription>
            </div>
            {unreadCount > 0 && (
              <Button variant="outline" size="sm" onClick={markAllAsRead}>
                Mark All Read
              </Button>
            )}
          </div>
        </CardHeader>
      </Card>

      {/* Notifications List */}
      {notifications.length === 0 ? (
        <Card>
          <CardContent className="text-center py-8">
            <Bell className="w-12 h-12 mx-auto mb-4 text-gray-400" />
            <h3 className="text-lg font-semibold text-gray-900 mb-2">All caught up!</h3>
            <p className="text-gray-600">No notifications at the moment.</p>
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-4">
          {notifications.map((notification) => (
            <Card key={notification.id} className={`${!notification.read ? 'border-blue-200 bg-blue-50' : ''}`}>
              <CardContent className="p-4">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    {getNotificationIcon(notification.type)}
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                          <h3 className={`font-semibold ${!notification.read ? 'text-blue-900' : 'text-gray-900'}`}>
                            {notification.title}
                          </h3>
                          <Badge className={getCategoryColor(notification.category)}>
                            {notification.category}
                          </Badge>
                          {!notification.read && (
                            <div className="w-2 h-2 bg-blue-600 rounded-full"></div>
                          )}
                        </div>
                        <p className={`text-sm ${!notification.read ? 'text-blue-700' : 'text-gray-600'}`}>
                          {notification.message}
                        </p>
                        <div className="flex items-center gap-2 mt-2">
                          <Clock className="w-3 h-3 text-gray-400" />
                          <span className="text-xs text-gray-500">
                            {formatDate(notification.createdAt)}
                          </span>
                        </div>
                      </div>
                      
                      <div className="flex items-center gap-2 ml-4">
                        {notification.action && (
                          <Button 
                            size="sm" 
                            variant="outline"
                            onClick={() => {
                              markAsRead(notification.id);
                              window.location.href = notification.action!.url;
                            }}
                          >
                            {notification.action.label}
                            <ArrowRight className="w-3 h-3 ml-1" />
                          </Button>
                        )}
                        <Button
                          size="sm"
                          variant="ghost"
                          onClick={() => dismissNotification(notification.id)}
                        >
                          <X className="w-4 h-4" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}
