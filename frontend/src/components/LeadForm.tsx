import React, { useState, useCallback } from 'react';
import { apiService, Lead } from '../services/api';
import './LeadForm.css';

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

  const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
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

  const handlePracticeAreaChange = useCallback((practiceArea: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.includes(practiceArea)
        ? prev.practiceAreas.filter(pa => pa !== practiceArea)
        : [...prev.practiceAreas, practiceArea]
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
      // Filter out empty fields
      const submitData = Object.fromEntries(
        Object.entries(formData).filter(([_, value]) => 
          value !== '' && value !== false && (Array.isArray(value) ? value.length > 0 : true)
        )
      );

      await apiService.submitLead(submitData as Omit<Lead, 'id' | 'status' | 'createdAt' | 'updatedAt'>);
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
      <div className="lead-form-success">
        <h2>Thank You!</h2>
        <p>Your inquiry has been submitted successfully to CounselRank.legal. We&apos;ll be in touch soon!</p>
        <button onClick={resetForm} className="btn-primary">
          Submit Another Inquiry
        </button>
      </div>
    );
  }

  return (
    <div className="lead-form-container">
      <h2>Get Legal Help</h2>
      <p>Fill out the form below and we&apos;ll connect you with the right legal assistance through CounselRank.legal.</p>
      
      {error && (
        <div className="error-message" role="alert">
          <h3>Submission Error</h3>
          <p>{error}</p>
        </div>
      )}

      <form onSubmit={handleSubmit} className="lead-form" noValidate>
        <div className="form-row">
          <div className="form-group">
            <label htmlFor="name">Full Name *</label>
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
            />
            {formErrors.name && (
              <span id="name-error" className="error-text" role="alert">{formErrors.name}</span>
            )}
          </div>
          
          <div className="form-group">
            <label htmlFor="email">Email Address *</label>
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
            />
            {formErrors.email && (
              <span id="email-error" className="error-text" role="alert">{formErrors.email}</span>
            )}
          </div>
        </div>

        <div className="form-row">
          <div className="form-group">
            <label htmlFor="phone">Phone Number</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              value={formData.phone}
              onChange={handleInputChange}
              placeholder="Enter your phone number"
              aria-describedby={formErrors.phone ? 'phone-error' : undefined}
              aria-invalid={!!formErrors.phone}
            />
            {formErrors.phone && (
              <span id="phone-error" className="error-text" role="alert">{formErrors.phone}</span>
            )}
          </div>
          
          <div className="form-group">
            <label htmlFor="firm">Law Firm (if applicable)</label>
            <input
              type="text"
              id="firm"
              name="firm"
              value={formData.firm}
              onChange={handleInputChange}
              placeholder="Enter firm name"
            />
          </div>
        </div>

        <div className="form-group">
          <label htmlFor="website">Website</label>
          <input
            type="url"
            id="website"
            name="website"
            value={formData.website}
            onChange={handleInputChange}
            placeholder="https://yourwebsite.com"
            aria-describedby={formErrors.website ? 'website-error' : undefined}
            aria-invalid={!!formErrors.website}
          />
          {formErrors.website && (
            <span id="website-error" className="error-text" role="alert">{formErrors.website}</span>
          )}
        </div>

        <div className="form-group">
          <fieldset>
            <legend>Practice Areas</legend>
            <div className="checkbox-grid">
              {practiceAreaOptions.map(area => (
                <label key={area} className="checkbox-item">
                  <input
                    type="checkbox"
                    checked={formData.practiceAreas.includes(area)}
                    onChange={() => handlePracticeAreaChange(area)}
                    aria-label={`Select ${area} practice area`}
                  />
                  <span>{area}</span>
                </label>
              ))}
            </div>
          </fieldset>
        </div>

        <div className="form-row">
          <div className="form-group">
            <label htmlFor="city">City</label>
            <input
              type="text"
              id="city"
              name="city"
              value={formData.city}
              onChange={handleInputChange}
              placeholder="Enter your city"
            />
          </div>
          
          <div className="form-group">
            <label htmlFor="state">State</label>
            <input
              type="text"
              id="state"
              name="state"
              value={formData.state}
              onChange={handleInputChange}
              placeholder="Enter your state"
            />
          </div>
        </div>

        <div className="form-row">
          <div className="form-group">
            <label htmlFor="budget">Budget Range</label>
            <select
              id="budget"
              name="budget"
              value={formData.budget}
              onChange={handleInputChange}
            >
              <option value="">Select budget range</option>
              {budgetOptions.map(option => (
                <option key={option} value={option}>{option}</option>
              ))}
            </select>
          </div>
          
          <div className="form-group">
            <label htmlFor="timeline">Timeline</label>
            <select
              id="timeline"
              name="timeline"
              value={formData.timeline}
              onChange={handleInputChange}
            >
              <option value="">Select timeline</option>
              {timelineOptions.map(option => (
                <option key={option} value={option}>{option}</option>
              ))}
            </select>
          </div>
        </div>

        <div className="form-group">
          <label htmlFor="notes">Additional Notes</label>
          <textarea
            id="notes"
            name="notes"
            value={formData.notes}
            onChange={handleInputChange}
            rows={4}
            placeholder="Tell us more about your legal needs..."
          />
        </div>

        <div className="form-group">
          <label className="checkbox-item">
            <input
              type="checkbox"
              name="consent"
              checked={formData.consent}
              onChange={handleInputChange}
              required
              aria-describedby={formErrors.consent ? 'consent-error' : undefined}
              aria-invalid={!!formErrors.consent}
            />
            <span>I consent to being contacted regarding my inquiry *</span>
          </label>
          {formErrors.consent && (
            <span id="consent-error" className="error-text" role="alert">{formErrors.consent}</span>
          )}
        </div>

        <button 
          type="submit" 
          className="btn-submit"
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
