'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { 
  Save, 
  User, 
  Building2, 
  Globe, 
  Phone, 
  MapPin, 
  Mail,
  Loader2,
  AlertCircle,
  CheckCircle
} from 'lucide-react';
import { Client } from '@/services/api';

export default function ClientSettingsPage() {
  const { user, isClientAdmin } = useAuth();
  const { 
    clients, 
    getClient, 
    updateClient, 
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [clientData, setClientData] = useState<Client | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [saveMessage, setSaveMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    website: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zipCode: '',
    googleBusinessProfile: {
      profileId: '',
      rating: 0,
      reviewsCount: 0
    },
    googleSearchConsole: {
      property: '',
      verificationStatus: ''
    },
    googleAnalytics: {
      propertyId: '',
      trackingId: ''
    }
  });

  const loadingState = getLoadingState('clients');
  const errorState = getErrorState('clients');

  useEffect(() => {
    if (user?.clientId) {
      loadClientData();
    }
  }, [user?.clientId]);

  const loadClientData = async () => {
    if (!user?.clientId) return;
    
    setIsLoading(true);
    try {
      clearError('clients');
      const client = await getClient(user.clientId);
      setClientData(client);
      setFormData({
        name: client.name || '',
        description: client.description || '',
        website: client.website || '',
        phone: client.phone || '',
        address: client.address || '',
        city: client.city || '',
        state: client.state || '',
        zipCode: client.zipCode || '',
        googleBusinessProfile: {
          profileId: client.googleBusinessProfile?.profileId || '',
          rating: client.googleBusinessProfile?.rating || 0,
          reviewsCount: client.googleBusinessProfile?.reviewsCount || 0
        },
        googleSearchConsole: {
          property: client.googleSearchConsole?.property || '',
          verificationStatus: client.googleSearchConsole?.verificationStatus || ''
        },
        googleAnalytics: {
          propertyId: client.googleAnalytics?.propertyId || '',
          trackingId: client.googleAnalytics?.trackingId || ''
        }
      });
    } catch (err) {
      console.error('Failed to load client data:', err);
    } finally {
      setIsLoading(false);
    }
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleNestedInputChange = (parent: string, field: string, value: string) => {
    setFormData(prev => ({
      ...prev,
      [parent]: {
        ...prev[parent as keyof typeof prev],
        [field]: value
      }
    }));
  };

  const handleSave = async () => {
    if (!user?.clientId || !isClientAdmin) return;

    setIsSaving(true);
    setSaveMessage(null);

    try {
      await updateClient(user.clientId, formData);
      setSaveMessage({ type: 'success', text: 'Settings saved successfully!' });
      await loadClientData(); // Reload to get updated data
    } catch (err: any) {
      setSaveMessage({ 
        type: 'error', 
        text: err?.message || 'Failed to save settings. Please try again.' 
      });
    } finally {
      setIsSaving(false);
    }
  };

  if (isLoading) {
    return (
      <div className="space-y-6">
        <PageHeader title="Settings" subtitle="Manage your account settings" />
        <div className="flex items-center justify-center py-8">
          <Loader2 className="h-6 w-6 animate-spin mr-2" />
          Loading settings...
        </div>
      </div>
    );
  }

  if (errorState) {
    return (
      <div className="space-y-6">
        <PageHeader title="Settings" subtitle="Manage your account settings" />
        <div className="flex items-center justify-center py-8 text-red-600">
          <AlertCircle className="h-6 w-6 mr-2" />
          {errorState}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Settings" 
        subtitle="Manage your account settings and preferences" 
      />

      {/* Save Message */}
      {saveMessage && (
        <div className={`flex items-center gap-2 p-4 rounded-lg ${
          saveMessage.type === 'success' 
            ? 'bg-green-50 text-green-700 border border-green-200' 
            : 'bg-red-50 text-red-700 border border-red-200'
        }`}>
          {saveMessage.type === 'success' ? (
            <CheckCircle className="h-5 w-5" />
          ) : (
            <AlertCircle className="h-5 w-5" />
          )}
          {saveMessage.text}
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Basic Information */}
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Building2 className="h-5 w-5" />
                Basic Information
              </CardTitle>
              <CardDescription>
                Update your company's basic information and contact details
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="name">Company Name *</Label>
                  <Input
                    id="name"
                    value={formData.name}
                    onChange={(e) => handleInputChange('name', e.target.value)}
                    placeholder="Enter company name"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="website">Website</Label>
                  <div className="relative">
                    <Globe className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                    <Input
                      id="website"
                      value={formData.website}
                      onChange={(e) => handleInputChange('website', e.target.value)}
                      placeholder="https://example.com"
                      className="pl-10"
                    />
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="description">Description</Label>
                <Textarea
                  id="description"
                  value={formData.description}
                  onChange={(e) => handleInputChange('description', e.target.value)}
                  placeholder="Brief description of your company"
                  rows={3}
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="phone">Phone</Label>
                  <div className="relative">
                    <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                    <Input
                      id="phone"
                      value={formData.phone}
                      onChange={(e) => handleInputChange('phone', e.target.value)}
                      placeholder="(555) 123-4567"
                      className="pl-10"
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email</Label>
                  <div className="relative">
                    <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                    <Input
                      id="email"
                      value={user?.email || ''}
                      disabled
                      className="pl-10 bg-gray-50"
                    />
                  </div>
                </div>
              </div>

              <Separator />

              <div className="space-y-4">
                <h3 className="text-lg font-medium">Address Information</h3>
                <div className="space-y-2">
                  <Label htmlFor="address">Street Address</Label>
                  <div className="relative">
                    <MapPin className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                    <Input
                      id="address"
                      value={formData.address}
                      onChange={(e) => handleInputChange('address', e.target.value)}
                      placeholder="123 Main Street"
                      className="pl-10"
                    />
                  </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="city">City</Label>
                    <Input
                      id="city"
                      value={formData.city}
                      onChange={(e) => handleInputChange('city', e.target.value)}
                      placeholder="City"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="state">State</Label>
                    <Input
                      id="state"
                      value={formData.state}
                      onChange={(e) => handleInputChange('state', e.target.value)}
                      placeholder="State"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="zipCode">ZIP Code</Label>
                    <Input
                      id="zipCode"
                      value={formData.zipCode}
                      onChange={(e) => handleInputChange('zipCode', e.target.value)}
                      placeholder="12345"
                    />
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Google Integrations */}
        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Globe className="h-5 w-5" />
                Google Business Profile
              </CardTitle>
              <CardDescription>
                Connect your Google Business Profile for local SEO insights
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="gbp-profile-id">Profile ID</Label>
                <Input
                  id="gbp-profile-id"
                  value={formData.googleBusinessProfile.profileId}
                  onChange={(e) => handleNestedInputChange('googleBusinessProfile', 'profileId', e.target.value)}
                  placeholder="gcid:123456789"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="gbp-rating">Rating</Label>
                  <Input
                    id="gbp-rating"
                    type="number"
                    min="0"
                    max="5"
                    step="0.1"
                    value={formData.googleBusinessProfile.rating}
                    onChange={(e) => handleNestedInputChange('googleBusinessProfile', 'rating', e.target.value)}
                    placeholder="4.5"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="gbp-reviews">Reviews Count</Label>
                  <Input
                    id="gbp-reviews"
                    type="number"
                    min="0"
                    value={formData.googleBusinessProfile.reviewsCount}
                    onChange={(e) => handleNestedInputChange('googleBusinessProfile', 'reviewsCount', e.target.value)}
                    placeholder="150"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Google Search Console</CardTitle>
              <CardDescription>
                Connect your Search Console property for SEO data
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="gsc-property">Property URL</Label>
                <Input
                  id="gsc-property"
                  value={formData.googleSearchConsole.property}
                  onChange={(e) => handleNestedInputChange('googleSearchConsole', 'property', e.target.value)}
                  placeholder="https://example.com"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="gsc-status">Verification Status</Label>
                <Input
                  id="gsc-status"
                  value={formData.googleSearchConsole.verificationStatus}
                  onChange={(e) => handleNestedInputChange('googleSearchConsole', 'verificationStatus', e.target.value)}
                  placeholder="verified"
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Google Analytics</CardTitle>
              <CardDescription>
                Connect your Analytics property for traffic insights
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="ga-property-id">Property ID</Label>
                <Input
                  id="ga-property-id"
                  value={formData.googleAnalytics.propertyId}
                  onChange={(e) => handleNestedInputChange('googleAnalytics', 'propertyId', e.target.value)}
                  placeholder="GA4-123456789"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="ga-tracking-id">Tracking ID</Label>
                <Input
                  id="ga-tracking-id"
                  value={formData.googleAnalytics.trackingId}
                  onChange={(e) => handleNestedInputChange('googleAnalytics', 'trackingId', e.target.value)}
                  placeholder="G-XXXXXXXXXX"
                />
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Save Button */}
      <div className="flex justify-end">
        <Button 
          onClick={handleSave} 
          disabled={isSaving || !isClientAdmin}
          className="min-w-[120px]"
        >
          {isSaving ? (
            <>
              <Loader2 className="h-4 w-4 mr-2 animate-spin" />
              Saving...
            </>
          ) : (
            <>
              <Save className="h-4 w-4 mr-2" />
              Save Changes
            </>
          )}
        </Button>
      </div>
    </div>
  );
}