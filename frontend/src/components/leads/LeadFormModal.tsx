'use client';

import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Textarea } from '../ui/textarea';
import { Card, CardContent } from '../ui/card';
import { 
  User, 
  Mail, 
  Phone, 
  Building, 
  MapPin, 
  Globe,
  FileText,
  Plus,
  X
} from 'lucide-react';

interface Lead {
  id: string;
  fullName: string;
  email: string;
  phone?: string;
  firm?: string;
  website?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  message?: string;
  practiceAreas?: string[];
  status: string;
  createdAt?: string;
  updatedAt?: string;
}

interface LeadFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: (lead: Lead) => void;
  lead?: Lead | null;
}

const practiceAreaOptions = [
  'Personal Injury',
  'Criminal Defense',
  'Family Law',
  'Business Law',
  'Real Estate',
  'Estate Planning',
  'Immigration',
  'Employment Law',
  'Bankruptcy',
  'DUI/DWI',
  'Medical Malpractice',
  'Workers Compensation',
  'Social Security',
  'Tax Law',
  'Intellectual Property'
];

export default function LeadFormModal({ isOpen, onClose, onSave, lead }: LeadFormModalProps) {
  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    phone: '',
    firm: '',
    website: '',
    city: '',
    state: '',
    zipCode: '',
    message: '',
    practiceAreas: [] as string[],
    status: 'new_lead'
  });

  const [newPracticeArea, setNewPracticeArea] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});

  const isEditing = lead !== null && lead !== undefined;

  useEffect(() => {
    if (lead) {
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
        status: lead.status || 'new_lead'
      });
    } else {
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
        status: 'new_lead'
      });
    }
    setErrors({});
  }, [lead, isOpen]);

  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.fullName.trim()) {
      newErrors.fullName = 'Full name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email is invalid';
    }

    if (formData.phone && !/^[\d\s\-\+\(\)]+$/.test(formData.phone)) {
      newErrors.phone = 'Phone number is invalid';
    }

    if (formData.website && !/^https?:\/\/.+/.test(formData.website)) {
      newErrors.website = 'Website must start with http:// or https://';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    const leadData: Lead = {
      id: lead?.id || `lead_${Date.now()}`,
      fullName: formData.fullName.trim(),
      email: formData.email.trim(),
      phone: formData.phone.trim() || undefined,
      firm: formData.firm.trim() || undefined,
      website: formData.website.trim() || undefined,
      city: formData.city.trim() || undefined,
      state: formData.state.trim() || undefined,
      zipCode: formData.zipCode.trim() || undefined,
      message: formData.message.trim() || undefined,
      practiceAreas: formData.practiceAreas.length > 0 ? formData.practiceAreas : undefined,
      status: formData.status,
      createdAt: lead?.createdAt || new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };

    onSave(leadData);
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const addPracticeArea = (area: string) => {
    if (area && !formData.practiceAreas.includes(area)) {
      setFormData(prev => ({
        ...prev,
        practiceAreas: [...prev.practiceAreas, area]
      }));
    }
    setNewPracticeArea('');
  };

  const removePracticeArea = (area: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.filter(pa => pa !== area)
    }));
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <User className="h-5 w-5" />
            {isEditing ? 'Edit Lead' : 'Create New Lead'}
          </DialogTitle>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Basic Information */}
          <Card>
            <CardContent className="pt-6">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <User className="h-4 w-4" />
                Basic Information
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="fullName">Full Name *</Label>
                  <Input
                    id="fullName"
                    value={formData.fullName}
                    onChange={(e) => handleInputChange('fullName', e.target.value)}
                    placeholder="Enter full name"
                    className={errors.fullName ? 'border-red-500' : ''}
                  />
                  {errors.fullName && (
                    <p className="text-sm text-red-500">{errors.fullName}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">Email *</Label>
                  <Input
                    id="email"
                    type="email"
                    value={formData.email}
                    onChange={(e) => handleInputChange('email', e.target.value)}
                    placeholder="Enter email address"
                    className={errors.email ? 'border-red-500' : ''}
                  />
                  {errors.email && (
                    <p className="text-sm text-red-500">{errors.email}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="phone">Phone</Label>
                  <Input
                    id="phone"
                    value={formData.phone}
                    onChange={(e) => handleInputChange('phone', e.target.value)}
                    placeholder="Enter phone number"
                    className={errors.phone ? 'border-red-500' : ''}
                  />
                  {errors.phone && (
                    <p className="text-sm text-red-500">{errors.phone}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="firm">Law Firm</Label>
                  <Input
                    id="firm"
                    value={formData.firm}
                    onChange={(e) => handleInputChange('firm', e.target.value)}
                    placeholder="Enter law firm name"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Location Information */}
          <Card>
            <CardContent className="pt-6">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <MapPin className="h-4 w-4" />
                Location Information
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="city">City</Label>
                  <Input
                    id="city"
                    value={formData.city}
                    onChange={(e) => handleInputChange('city', e.target.value)}
                    placeholder="Enter city"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="state">State</Label>
                  <Input
                    id="state"
                    value={formData.state}
                    onChange={(e) => handleInputChange('state', e.target.value)}
                    placeholder="Enter state"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="zipCode">ZIP Code</Label>
                  <Input
                    id="zipCode"
                    value={formData.zipCode}
                    onChange={(e) => handleInputChange('zipCode', e.target.value)}
                    placeholder="Enter ZIP code"
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Website */}
          <Card>
            <CardContent className="pt-6">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <Globe className="h-4 w-4" />
                Website
              </h3>
              <div className="space-y-2">
                <Label htmlFor="website">Website URL</Label>
                <Input
                  id="website"
                  value={formData.website}
                  onChange={(e) => handleInputChange('website', e.target.value)}
                  placeholder="https://example.com"
                  className={errors.website ? 'border-red-500' : ''}
                />
                {errors.website && (
                  <p className="text-sm text-red-500">{errors.website}</p>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Practice Areas */}
          <Card>
            <CardContent className="pt-6">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <FileText className="h-4 w-4" />
                Practice Areas
              </h3>
              
              {/* Selected Practice Areas */}
              {formData.practiceAreas.length > 0 && (
                <div className="mb-4">
                  <div className="flex flex-wrap gap-2">
                    {formData.practiceAreas.map((area) => (
                      <div
                        key={area}
                        className="flex items-center gap-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-md text-sm"
                      >
                        {area}
                        <button
                          type="button"
                          onClick={() => removePracticeArea(area)}
                          className="ml-1 hover:text-red-600"
                        >
                          <X className="h-3 w-3" />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Add Practice Area */}
              <div className="flex gap-2">
                <Input
                  value={newPracticeArea}
                  onChange={(e) => setNewPracticeArea(e.target.value)}
                  placeholder="Add practice area"
                  onKeyPress={(e) => {
                    if (e.key === 'Enter') {
                      e.preventDefault();
                      addPracticeArea(newPracticeArea);
                    }
                  }}
                />
                <Button
                  type="button"
                  onClick={() => addPracticeArea(newPracticeArea)}
                  size="sm"
                  variant="outline"
                >
                  <Plus className="h-4 w-4" />
                </Button>
              </div>

              {/* Practice Area Options */}
              <div className="mt-4">
                <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">Common practice areas:</p>
                <div className="flex flex-wrap gap-2">
                  {practiceAreaOptions.map((area) => (
                    <button
                      key={area}
                      type="button"
                      onClick={() => addPracticeArea(area)}
                      className="text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                      disabled={formData.practiceAreas.includes(area)}
                    >
                      {area}
                    </button>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Message */}
          <Card>
            <CardContent className="pt-6">
              <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
                <FileText className="h-4 w-4" />
                Message
              </h3>
              <div className="space-y-2">
                <Label htmlFor="message">Message</Label>
                <Textarea
                  id="message"
                  value={formData.message}
                  onChange={(e) => handleInputChange('message', e.target.value)}
                  placeholder="Enter any additional message or notes"
                  rows={4}
                />
              </div>
            </CardContent>
          </Card>

          {/* Form Actions */}
          <div className="flex justify-end space-x-2">
            <Button type="button" variant="outline" onClick={onClose}>
              Cancel
            </Button>
            <Button type="submit">
              {isEditing ? 'Update Lead' : 'Create Lead'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
