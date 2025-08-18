import React, { useState, useCallback } from 'react';
import { apiService, Lead } from '../services/api';

interface LeadFormData {
  name: string;
  email: string;
  phone: string;
  firm: string;
  website: string;
  practiceAreas: string[];
  city: string;
  state: string;
  budget: string;
  timeline: string;
  notes: string;
  consent: boolean;
}

interface FormErrors {
  [key: string]: string;
}

const LeadForm: React.FC = () => {
  const [formData, setFormData] = useState<LeadFormData>({
    name: '',
    email: '',
    phone: '',
    firm: '',
    website: '',
    practiceAreas: [],
    city: '',
    state: '',
    budget: '',
    timeline: '',
    notes: '',
    consent: false,
  });

  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [formErrors, setFormErrors] = useState<FormErrors>({});

  const practiceAreaOptions = [
    'Personal Injury',
    'Criminal Defense',
    'Family Law',
    'Business Law',
    'Real Estate',
    'Employment Law',
    'Estate Planning',
    'Bankruptcy',
    'Immigration',
    'Tax Law',
  ];

  const budgetOptions = [
    'Under $5,000',
    '$5,000 - $10,000',
    '$10,000 - $25,000',
    '$25,000 - $50,000',
    '$50,000+',
  ];

  const timelineOptions = [
    'Immediate',
    'Within 30 days',
    'Within 3 months',
    'Within 6 months',
    'No specific timeline',
  ];

  const validateForm = useCallback((): boolean => {
    const errors: FormErrors = {};

    if (!formData.name.trim()) {
      errors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      errors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      errors.email = 'Please enter a valid email address';
    }

    if (formData.phone && !/^[+]?[1-9]\d{0,15}$/.test(formData.phone.replace(/[\s\-()]/g, ''))) {
      errors.phone = 'Please enter a valid phone number';
    }

    if (formData.website && !/^https?:\/\/.+/.test(formData.website)) {
      errors.website = 'Please enter a valid website URL starting with http:// or https://';
    }

    if (!formData.consent) {
      errors.consent = 'You must consent to being contacted';
    }

    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  }, [formData]);

  const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value, type } = e.target;
    
    if (type === 'checkbox') {
      const checked = (e.target as HTMLInputElement).checked;
      setFormData(prev => ({ ...prev, [name]: checked }));
    } else {
      setFormData(prev => ({ ...prev, [name]: value }));
    }
    
    // Clear error when user starts typing
    if (formErrors[name]) {
      setFormErrors(prev => ({ ...prev, [name]: '' }));
    }
  }, [formErrors]);

  const handlePracticeAreaChange = useCallback((area: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.includes(area)
        ? prev.practiceAreas.filter(a => a !== area)
        : [...prev.practiceAreas, area]
    }));
  }, []);

  const handleSubmit = useCallback(async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const lead: Lead = {
        id: Date.now().toString(), // Temporary ID for demo
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        firm: formData.firm,
        website: formData.website,
        practiceAreas: formData.practiceAreas,
        city: formData.city,
        state: formData.state,
        budget: formData.budget,
        timeline: formData.timeline,
        notes: formData.notes,
        status: 'pending',
        consent: formData.consent,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
      };

      await apiService.submitLead(lead);
      setSuccess(true);
      setFormData({
        name: '',
        email: '',
        phone: '',
        firm: '',
        website: '',
        practiceAreas: [],
        city: '',
        state: '',
        budget: '',
        timeline: '',
        notes: '',
        consent: false,
      });
      setFormErrors({});
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to submit lead';
      setError(errorMessage);
      console.error('Error submitting lead:', err);
    } finally {
      setLoading(false);
    }
  }, [formData, validateForm]);

  const resetForm = useCallback(() => {
    setSuccess(false);
    setError(null);
    setFormErrors({});
  }, []);

  if (success) {
    return (
      <div className="max-w-2xl mx-auto text-center py-12">
        <div className="bg-green-50 border border-green-200 rounded-lg p-8">
          <h2 className="text-3xl font-bold text-green-800 mb-4">Thank You!</h2>
          <p className="text-green-700 mb-6">Your inquiry has been submitted successfully to CounselRank.legal. We&apos;ll be in touch soon!</p>
          <button onClick={resetForm} className="btn-primary">
            Submit Another Inquiry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto px-6 py-8">
      <div className="text-center mb-8">
        <h2 className="text-3xl font-bold text-gray-900 mb-4">Get Legal Help</h2>
        <p className="text-lg text-gray-600">Fill out the form below and we&apos;ll connect you with the right legal assistance through CounselRank.legal.</p>
      </div>
      
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-6 mb-8" role="alert">
          <h3 className="text-lg font-semibold text-red-800 mb-2">Submission Error</h3>
          <p className="text-red-700">{error}</p>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6" noValidate>
        <div className="grid md:grid-cols-2 gap-6">
          <div>
            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input
              type="text"
              id="name"
              name="name"
              value={formData.name}
              onChange={handleInputChange}
              required
              placeholder="Enter your full name"
              aria-describedby={formErrors.name ? 'name-error' : undefined}
              aria-invalid={!!formErrors.name}
              className={`input-field ${formErrors.name ? 'border-red-500 focus:ring-red-500' : ''}`}
            />
            {formErrors.name && (
              <span id="name-error" className="text-sm text-red-600 mt-1 block" role="alert">{formErrors.name}</span>
            )}
          </div>
          
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input
              type="email"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleInputChange}
              required
              placeholder="Enter your email"
              aria-describedby={formErrors.email ? 'email-error' : undefined}
              aria-invalid={!!formErrors.email}
              className={`input-field ${formErrors.email ? 'border-red-500 focus:ring-red-500' : ''}`}
            />
            {formErrors.email && (
              <span id="email-error" className="text-sm text-red-600 mt-1 block" role="alert">{formErrors.email}</span>
            )}
          </div>
        </div>

        <div className="grid md:grid-cols-2 gap-6">
          <div>
            <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              value={formData.phone}
              onChange={handleInputChange}
              placeholder="Enter your phone number"
              aria-describedby={formErrors.phone ? 'phone-error' : undefined}
              aria-invalid={!!formErrors.phone}
              className={`input-field ${formErrors.phone ? 'border-red-500 focus:ring-red-500' : ''}`}
            />
            {formErrors.phone && (
              <span id="phone-error" className="text-sm text-red-600 mt-1 block" role="alert">{formErrors.phone}</span>
            )}
          </div>
          
          <div>
            <label htmlFor="firm" className="block text-sm font-medium text-gray-700 mb-2">Law Firm (if applicable)</label>
            <input
              type="text"
              id="firm"
              name="firm"
              value={formData.firm}
              onChange={handleInputChange}
              placeholder="Enter firm name"
              className="input-field"
            />
          </div>
        </div>

        <div>
          <label htmlFor="website" className="block text-sm font-medium text-gray-700 mb-2">Website</label>
          <input
            type="url"
            id="website"
            name="website"
            value={formData.website}
            onChange={handleInputChange}
            placeholder="https://yourwebsite.com"
            aria-describedby={formErrors.website ? 'website-error' : undefined}
            aria-invalid={!!formErrors.website}
            className={`input-field ${formErrors.website ? 'border-red-500 focus:ring-red-500' : ''}`}
          />
          {formErrors.website && (
            <span id="website-error" className="text-sm text-red-600 mt-1 block" role="alert">{formErrors.website}</span>
          )}
        </div>

        <div>
          <fieldset className="border border-gray-300 rounded-lg p-6">
            <legend className="text-lg font-medium text-gray-900 px-2">Practice Areas</legend>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
              {practiceAreaOptions.map(area => (
                <label key={area} className="flex items-center space-x-3 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={formData.practiceAreas.includes(area)}
                    onChange={() => handlePracticeAreaChange(area)}
                    aria-label={`Select ${area} practice area`}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span className="text-gray-700">{area}</span>
                </label>
              ))}
            </div>
          </fieldset>
        </div>

        <div className="grid md:grid-cols-2 gap-6">
          <div>
            <label htmlFor="city" className="block text-sm font-medium text-gray-700 mb-2">City</label>
            <input
              type="text"
              id="city"
              name="city"
              value={formData.city}
              onChange={handleInputChange}
              placeholder="Enter your city"
              className="input-field"
            />
          </div>
          
          <div>
            <label htmlFor="state" className="block text-sm font-medium text-gray-700 mb-2">State</label>
            <input
              type="text"
              id="state"
              name="state"
              value={formData.state}
              onChange={handleInputChange}
              placeholder="Enter your state"
              className="input-field"
            />
          </div>
        </div>

        <div className="grid md:grid-cols-2 gap-6">
          <div>
            <label htmlFor="budget" className="block text-sm font-medium text-gray-700 mb-2">Budget Range</label>
            <select
              id="budget"
              name="budget"
              value={formData.budget}
              onChange={handleInputChange}
              className="input-field"
            >
              <option value="">Select budget range</option>
              {budgetOptions.map(option => (
                <option key={option} value={option}>{option}</option>
              ))}
            </select>
          </div>
          
          <div>
            <label htmlFor="timeline" className="block text-sm font-medium text-gray-700 mb-2">Timeline</label>
            <select
              id="timeline"
              name="timeline"
              value={formData.timeline}
              onChange={handleInputChange}
              className="input-field"
            >
              <option value="">Select timeline</option>
              {timelineOptions.map(option => (
                <option key={option} value={option}>{option}</option>
              ))}
            </select>
          </div>
        </div>

        <div>
          <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
          <textarea
            id="notes"
            name="notes"
            value={formData.notes}
            onChange={handleInputChange}
            rows={4}
            placeholder="Tell us more about your legal needs..."
            className="input-field"
          />
        </div>

        <div>
          <label className="flex items-start space-x-3 cursor-pointer">
            <input
              type="checkbox"
              name="consent"
              checked={formData.consent}
              onChange={handleInputChange}
              required
              aria-describedby={formErrors.consent ? 'consent-error' : undefined}
              aria-invalid={!!formErrors.consent}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
            />
            <span className="text-gray-700">I consent to being contacted regarding my inquiry *</span>
          </label>
          {formErrors.consent && (
            <span id="consent-error" className="text-sm text-red-600 mt-1 block" role="alert">{formErrors.consent}</span>
          )}
        </div>

        <button 
          type="submit" 
          className="w-full btn-primary py-4 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
          disabled={loading}
          aria-describedby={loading ? 'loading-description' : undefined}
        >
          {loading ? 'Submitting...' : 'Submit Inquiry'}
        </button>
        {loading && (
          <div id="loading-description" className="sr-only">
            Form is being submitted, please wait
          </div>
        )}
      </form>
    </div>
  );
};

export default LeadForm;
