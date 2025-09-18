'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { 
  Save, 
  User, 
  Bell, 
  Shield, 
  Loader2,
  AlertCircle,
  CheckCircle,
  ArrowLeft
} from 'lucide-react';
import { useRouter } from 'next/navigation';
import './user-preferences.css';

export default function UserPreferencesPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);
  
  // Form state
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    timezone: 'America/Chicago',
    language: 'en',
    notifications: {
      email: true,
      sms: false,
      push: true,
      marketing: false
    },
    privacy: {
      profilePublic: false,
      showEmail: false,
      showPhone: false
    }
  });

  useEffect(() => {
    if (user) {
      setFormData(prev => ({
        ...prev,
        firstName: user.firstName || 'Sean',
        lastName: user.lastName || 'Dobbs',
        email: user.email || 'newuser@example.com',
        phone: (user as any).phone || '7862133333'
      }));
    }
  }, [user]);

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleNestedChange = (parent: string, field: string, value: boolean) => {
    setFormData(prev => {
      const parentData = prev[parent as keyof typeof prev] as Record<string, boolean>;
      return {
        ...prev,
        [parent]: {
          ...parentData,
          [field]: value
        }
      };
    });
  };

  const handleSave = async () => {
    setLoading(true);
    setError(null);
    setSuccess(null);

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Update user data
      // Note: In a real implementation, this would call an API to update user preferences
      console.log('Saving user preferences:', formData);
      
      setSuccess('Preferences saved successfully!');
    } catch (err) {
      setError('Failed to save preferences. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  if (!user) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin mx-auto mb-4" />
          <p className="text-gray-600">Loading user preferences...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="user-preferences min-h-screen bg-gray-50 dark:bg-gray-900">
      {/* Fixed Header */}
      <div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <div className="max-w-4xl mx-auto px-6 sm:px-8 lg:px-10">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center gap-4">
              <Button
                variant="ghost"
                size="sm"
                onClick={() => router.push('/client')}
                className="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 px-3 py-2 rounded-lg transition-all duration-200"
              >
                <ArrowLeft className="h-4 w-4" />
                <span className="hidden sm:inline">Back to Dashboard</span>
              </Button>
              <div className="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
              <h1 className="text-xl font-semibold text-gray-900 dark:text-white">User Preferences</h1>
            </div>
            <div className="flex items-center space-x-4">
              <div className="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-4xl mx-auto px-6 sm:px-8 lg:px-10 py-8 pb-24">
        <div className="space-y-8">
          {/* Success/Error Messages */}
          {success && (
            <div className="success-message bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-6 py-4 rounded-lg flex items-center gap-3 shadow-sm">
              <CheckCircle className="h-5 w-5 flex-shrink-0" />
              <span className="font-medium">{success}</span>
            </div>
          )}
          
          {error && (
            <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-6 py-4 rounded-lg flex items-center gap-3 shadow-sm">
              <AlertCircle className="h-5 w-5 flex-shrink-0" />
              <span className="font-medium">{error}</span>
            </div>
          )}

          {/* Profile Information */}
          <Card className="card-hover bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
            <CardHeader className="pb-6 px-6 pt-6">
              <CardTitle className="flex items-center gap-3 text-lg font-semibold text-gray-900 dark:text-white">
                <User className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                Profile Information
              </CardTitle>
              <CardDescription className="text-gray-600 dark:text-gray-400 mt-2">
                Update your personal information and contact details
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6 px-6 pb-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="firstName" className="text-sm font-medium text-gray-700 dark:text-gray-300">First Name</Label>
                  <Input
                    id="firstName"
                    value={formData.firstName}
                    onChange={(e) => handleInputChange('firstName', e.target.value)}
                    placeholder="Enter your first name"
                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="lastName" className="text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</Label>
                  <Input
                    id="lastName"
                    value={formData.lastName}
                    onChange={(e) => handleInputChange('lastName', e.target.value)}
                    placeholder="Enter your last name"
                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  />
                </div>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="email" className="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</Label>
                  <Input
                    id="email"
                    type="email"
                    value={formData.email}
                    onChange={(e) => handleInputChange('email', e.target.value)}
                    placeholder="Enter your email address"
                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="phone" className="text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</Label>
                  <Input
                    id="phone"
                    type="tel"
                    value={formData.phone}
                    onChange={(e) => handleInputChange('phone', e.target.value)}
                    placeholder="Enter your phone number"
                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Notification Preferences */}
          <Card className="card-hover bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
            <CardHeader className="pb-6 px-6 pt-6">
              <CardTitle className="flex items-center gap-3 text-lg font-semibold text-gray-900 dark:text-white">
                <Bell className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                Notification Preferences
              </CardTitle>
              <CardDescription className="text-gray-600 dark:text-gray-400 mt-2">
                Choose how you want to receive notifications
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6 px-6 pb-6">
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="email-notifications" className="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Receive notifications via email</p>
                </div>
                <Switch
                  id="email-notifications"
                  checked={formData.notifications.email}
                  onCheckedChange={(checked) => handleNestedChange('notifications', 'email', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
              
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="sms-notifications" className="text-sm font-medium text-gray-700 dark:text-gray-300">SMS Notifications</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Receive notifications via text message</p>
                </div>
                <Switch
                  id="sms-notifications"
                  checked={formData.notifications.sms}
                  onCheckedChange={(checked) => handleNestedChange('notifications', 'sms', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
              
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="push-notifications" className="text-sm font-medium text-gray-700 dark:text-gray-300">Push Notifications</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Receive browser push notifications</p>
                </div>
                <Switch
                  id="push-notifications"
                  checked={formData.notifications.push}
                  onCheckedChange={(checked) => handleNestedChange('notifications', 'push', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
              
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="marketing-notifications" className="text-sm font-medium text-gray-700 dark:text-gray-300">Marketing Emails</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Receive marketing and promotional emails</p>
                </div>
                <Switch
                  id="marketing-notifications"
                  checked={formData.notifications.marketing}
                  onCheckedChange={(checked) => handleNestedChange('notifications', 'marketing', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
            </CardContent>
          </Card>

          {/* Privacy Settings */}
          <Card className="card-hover bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
            <CardHeader className="pb-6 px-6 pt-6">
              <CardTitle className="flex items-center gap-3 text-lg font-semibold text-gray-900 dark:text-white">
                <Shield className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                Privacy Settings
              </CardTitle>
              <CardDescription className="text-gray-600 dark:text-gray-400 mt-2">
                Control your privacy and visibility settings
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6 px-6 pb-6">
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="profile-public" className="text-sm font-medium text-gray-700 dark:text-gray-300">Public Profile</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Make your profile visible to other users</p>
                </div>
                <Switch
                  id="profile-public"
                  checked={formData.privacy.profilePublic}
                  onCheckedChange={(checked) => handleNestedChange('privacy', 'profilePublic', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
              
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="show-email" className="text-sm font-medium text-gray-700 dark:text-gray-300">Show Email</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Display your email address on your profile</p>
                </div>
                <Switch
                  id="show-email"
                  checked={formData.privacy.showEmail}
                  onCheckedChange={(checked) => handleNestedChange('privacy', 'showEmail', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
              
              <div className="flex items-center justify-between py-3">
                <div className="flex-1">
                  <Label htmlFor="show-phone" className="text-sm font-medium text-gray-700 dark:text-gray-300">Show Phone</Label>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">Display your phone number on your profile</p>
                </div>
                <Switch
                  id="show-phone"
                  checked={formData.privacy.showPhone}
                  onCheckedChange={(checked) => handleNestedChange('privacy', 'showPhone', checked)}
                  className="data-[state=checked]:bg-gray-800 data-[state=unchecked]:bg-gray-200"
                />
              </div>
            </CardContent>
          </Card>

        </div>
      </div>

      {/* Sticky Save Button */}
      <div className="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-20">
        <div className="max-w-4xl mx-auto px-6 sm:px-8 lg:px-10 py-4">
          <div className="flex justify-end">
            <Button 
              onClick={handleSave} 
              disabled={loading}
              className="sticky-button flex items-center gap-2 bg-gray-800 dark:bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                <Save className="h-4 w-4" />
              )}
              {loading ? 'Saving...' : 'Save Preferences'}
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
