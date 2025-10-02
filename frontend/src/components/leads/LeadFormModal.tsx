'use client';

import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Textarea } from '../ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Badge } from '../ui/badge';
import { 
  User, 
  Mail, 
  Phone, 
  Building, 
  Globe, 
  MapPin, 
  Tag, 
  MessageSquare,
  Save,
  X,
  Plus
} from 'lucide-react';
import { apiService, Lead } from '../../services/api';

interface LeadFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: (lead: Lead) => void;
  lead?: Lead | null; // If provided, we're editing; if null, we're creating
}

interface LeadFormData {
  fullName: string;
  email: string;
  phone: string;
  firm: string;
  website: string;
  city: string;
  state: string;
  zipCode: string;
  message: string;
  practiceAreas: string[];
  newPracticeArea: string;
}

export default function LeadFormModal({ 
  isOpen, 
  onClose, 
  onSave, 
  lead 
}: LeadFormModalProps) {
  const [formData, setFormData] = useState<LeadFormData>({
    fullName: '',
    email: '',
    phone: '',
    firm: '',
    website: '',
    city: '',
    state: '',
    zipCode: '',
    message: '',
    practiceAreas: [],
    newPracticeArea: ''
  });
  
  const [isSaving, setIsSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  // Reset form when modal opens/closes or when lead changes
  useEffect(() => {
    if (isOpen) {
      if (lead) {
        // Editing existing lead
        setFormData({
          fullName: lead.fullName || '',
          email: lead.email || '',
          phone: lead.phone || '',
          firm: lead.firm || '',
          website: lead.website || '',
          city: lead.city || '',
          state: lead.state || '',
          zipCode: lead.zipCode || '',
          message: lead.message || '',
          practiceAreas: lead.practiceAreas || [],
          newPracticeArea: ''
        });
      } else {
        // Creating new lead
        setFormData({
          fullName: '',
          email: '',
          phone: '',
          firm: '',
          website: '',
          city: '',
          state: '',
          zipCode: '',
          message: '',
          practiceAreas: [],
          newPracticeArea: ''
        });
      }
      setErrors({});
    }
  }, [isOpen, lead]);

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.fullName.trim()) {
      newErrors.fullName = 'Full name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (formData.website && !/^https?:\/\/.+/.test(formData.website)) {
      newErrors.website = 'Please enter a valid URL (starting with http:// or https://)';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleInputChange = (field: keyof LeadFormData, value: string) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
    
    // Clear error when user starts typing
    if (errors[field]) {
      setErrors(prev => ({
        ...prev,
        [field]: ''
      }));
    }
  };

  const handleAddPracticeArea = () => {
    if (formData.newPracticeArea.trim() && !formData.practiceAreas.includes(formData.newPracticeArea.trim())) {
      setFormData(prev => ({
        ...prev,
        practiceAreas: [...prev.practiceAreas, prev.newPracticeArea.trim()],
        newPracticeArea: ''
      }));
    }
  };

  const handleRemovePracticeArea = (area: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.filter(pa => pa !== area)
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setIsSaving(true);
    try {
      const leadData = {
        fullName: formData.fullName.trim(),
        email: formData.email.trim(),
        phone: formData.phone.trim() || undefined,
        firm: formData.firm.trim() || undefined,
        website: formData.website.trim() || undefined,
        city: formData.city.trim() || undefined,
        state: formData.state.trim() || undefined,
        zipCode: formData.zipCode.trim() || undefined,
        message: formData.message.trim() || undefined,
        practiceAreas: formData.practiceAreas,
        status: lead?.status || 'new_lead' as const
      };

      let savedLead: Lead;
      
      if (lead) {
        // Update existing lead
        savedLead = await apiService.updateLead(lead.id, leadData);
      } else {
        // Create new lead
        savedLead = await apiService.submitLead(leadData);
      }

      onSave(savedLead);
      onClose();
    } catch (error) {
      console.error('Failed to save lead:', error);
      setErrors({ submit: 'Failed to save lead. Please try again.' });
    } finally {
      setIsSaving(false);
    }
  };

  const handleClose = () => {
    if (!isSaving) {
      onClose();
    }
  };

  return (
    <Dialog open={isOpen} onOpenChange={handleClose}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3">
            <User className="h-6 w-6" />
            {lead ? 'Edit Lead' : 'Create New Lead'}
          </DialogTitle>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Basic Information */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center gap-2">
                <User className="h-5 w-5" />
                Basic Information
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="fullName" className="flex items-center gap-2">
                    <User className="h-4 w-4" />
                    Full Name *
                  </Label>
                  <Input
                    id="fullName"
                    value={formData.fullName}
                    onChange={(e) => handleInputChange('fullName', e.target.value)}
                    placeholder="Enter full name"
                    className={errors.fullName ? 'border-red-500' : ''}
                  />
                  {errors.fullName && (
                    <p className="text-sm text-red-600">{errors.fullName}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email" className="flex items-center gap-2">
                    <Mail className="h-4 w-4" />
                    Email *
                  </Label>
                  <Input
                    id="email"
                    type="email"
                    value={formData.email}
                    onChange={(e) => handleInputChange('email', e.target.value)}
                    placeholder="Enter email address"
                    className={errors.email ? 'border-red-500' : ''}
                  />
                  {errors.email && (
                    <p className="text-sm text-red-600">{errors.email}</p>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="phone" className="flex items-center gap-2">
                  <Phone className="h-4 w-4" />
                  Phone Number
                </Label>
                <Input
                  id="phone"
                  value={formData.phone}
                  onChange={(e) => handleInputChange('phone', e.target.value)}
                  placeholder="Enter phone number"
                />
              </div>
            </CardContent>
          </Card>

          {/* Business Information */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center gap-2">
                <Building className="h-5 w-5" />
                Business Information
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="firm" className="flex items-center gap-2">
                  <Building className="h-4 w-4" />
                  Firm/Company Name
                </Label>
                <Input
                  id="firm"
                  value={formData.firm}
                  onChange={(e) => handleInputChange('firm', e.target.value)}
                  placeholder="Enter firm or company name"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="website" className="flex items-center gap-2">
                  <Globe className="h-4 w-4" />
                  Website
                </Label>
                <Input
                  id="website"
                  value={formData.website}
                  onChange={(e) => handleInputChange('website', e.target.value)}
                  placeholder="https://example.com"
                  className={errors.website ? 'border-red-500' : ''}
                />
                {errors.website && (
                  <p className="text-sm text-red-600">{errors.website}</p>
                )}
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="city" className="flex items-center gap-2">
                    <MapPin className="h-4 w-4" />
                    City
                  </Label>
                  <Input
                    id="city"
                    value={formData.city}
                    onChange={(e) => handleInputChange('city', e.target.value)}
                    placeholder="Enter city"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="state" className="flex items-center gap-2">
                    <MapPin className="h-4 w-4" />
                    State
                  </Label>
                  <Input
                    id="state"
                    value={formData.state}
                    onChange={(e) => handleInputChange('state', e.target.value)}
                    placeholder="Enter state"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="zipCode" className="flex items-center gap-2">
                    <MapPin className="h-4 w-4" />
                    Zip Code
                  </Label>
                  <Input
                    id="zipCode"
                    value={formData.zipCode}
                    onChange={(e) => handleInputChange('zipCode', e.target.value)}
                    placeholder="Enter zip code"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Practice Areas */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center gap-2">
                <Tag className="h-5 w-5" />
                Practice Areas
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex gap-2">
                <Input
                  value={formData.newPracticeArea}
                  onChange={(e) => handleInputChange('newPracticeArea', e.target.value)}
                  placeholder="Enter practice area"
                  onKeyPress={(e) => {
                    if (e.key === 'Enter') {
                      e.preventDefault();
                      handleAddPracticeArea();
                    }
                  }}
                />
                <Button type="button" onClick={handleAddPracticeArea} size="sm">
                  <Plus className="h-4 w-4" />
                </Button>
              </div>
              
              {formData.practiceAreas.length > 0 && (
                <div className="flex flex-wrap gap-2">
                  {formData.practiceAreas.map((area, index) => (
                    <Badge key={index} variant="secondary" className="flex items-center gap-1" data-testid="practice-area-badge">
                      {area}
                      <button
                        type="button"
                        onClick={() => handleRemovePracticeArea(area)}
                        className="ml-1 hover:text-red-600"
                      >
                        <X className="h-3 w-3" />
                      </button>
                    </Badge>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>

          {/* Message */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg flex items-center gap-2">
                <MessageSquare className="h-5 w-5" />
                Message/Notes
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                <Label htmlFor="message">Additional Message or Notes</Label>
                <Textarea
                  id="message"
                  value={formData.message}
                  onChange={(e) => handleInputChange('message', e.target.value)}
                  placeholder="Enter any additional message or notes about this lead..."
                  rows={4}
                />
              </div>
            </CardContent>
          </Card>

          {/* Error Messages */}
          {errors.submit && (
            <div className="text-center">
              <p className="text-sm text-red-600">{errors.submit}</p>
            </div>
          )}

          {/* Actions */}
          <div className="flex justify-end gap-2 pt-4 border-t">
            <Button type="button" variant="outline" onClick={handleClose} disabled={isSaving}>
              Cancel
            </Button>
            <Button type="submit" disabled={isSaving} className="bg-blue-600 hover:bg-blue-700">
              <Save className="h-4 w-4 mr-2" />
              {isSaving ? 'Saving...' : (lead ? 'Update Lead' : 'Create Lead')}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
