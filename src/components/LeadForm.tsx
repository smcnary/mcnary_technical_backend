import React, { useState } from 'react';
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

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value, type } = e.target;
    if (type === 'checkbox') {
      const checked = (e.target as HTMLInputElement).checked;
      setFormData(prev => ({ ...prev, [name]: checked }));
    } else {
      setFormData(prev => ({ ...prev, [name]: value }));
    }
  };

  const handlePracticeAreaChange = (practiceArea: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.includes(practiceArea)
        ? prev.practiceAreas.filter(pa => pa !== practiceArea)
        : [...prev.practiceAreas, practiceArea]
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      // Filter out empty fields
      const submitData = Object.fromEntries(
        Object.entries(formData).filter(([_, value]) => 
          value !== '' && value !== false && (Array.isArray(value) ? value.length > 0 : true)
        )
      );

      await apiService.submitLead(submitData as any);
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
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to submit lead');
    } finally {
      setLoading(false);
    }
  };

  if (success) {
    return (
      <div className="lead-form-success">
        <h2>Thank You!</h2>
        <p>Your inquiry has been submitted successfully. We'll be in touch soon!</p>
        <button onClick={() => setSuccess(false)} className="btn-primary">
          Submit Another Inquiry
        </button>
      </div>
    );
  }

  return (
    <div className="lead-form-container">
      <h2>Get Legal Help</h2>
      <p>Fill out the form below and we'll connect you with the right legal assistance.</p>
      
      {error && (
        <div className="error-message">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="lead-form">
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
            />
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
            />
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
            />
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
          />
        </div>

        <div className="form-group">
          <label>Practice Areas</label>
          <div className="checkbox-grid">
            {practiceAreaOptions.map(area => (
              <label key={area} className="checkbox-item">
                <input
                  type="checkbox"
                  checked={formData.practiceAreas.includes(area)}
                  onChange={() => handlePracticeAreaChange(area)}
                />
                <span>{area}</span>
              </label>
            ))}
          </div>
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
            />
            <span>I consent to being contacted regarding my inquiry *</span>
          </label>
        </div>

        <button 
          type="submit" 
          className="btn-submit"
          disabled={loading}
        >
          {loading ? 'Submitting...' : 'Submit Inquiry'}
        </button>
      </form>
    </div>
  );
};

export default LeadForm;
