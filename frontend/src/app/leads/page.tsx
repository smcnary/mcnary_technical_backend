'use client'

import { useState } from 'react';
import Header from '../../components/common/Header';
import { ApiService } from '../../services/api';

export default function LeadsPage() {
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
    practiceAreas: 'SEO Services'
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState<'idle' | 'success' | 'error'>('idle');

  const apiService = new ApiService();

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setSubmitStatus('idle');

    try {
      const leadData = {
        fullName: formData.fullName,
        email: formData.email,
        phone: formData.phone || undefined,
        firm: formData.firm || undefined,
        website: formData.website || undefined,
        city: formData.city || undefined,
        state: formData.state || undefined,
        zipCode: formData.zipCode || undefined,
        message: formData.message || undefined,
        practiceAreas: formData.practiceAreas ? [formData.practiceAreas] : undefined
      };

      await apiService.submitLead(leadData);
      setSubmitStatus('success');
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
        practiceAreas: 'SEO Services'
      });
    } catch (error) {
      console.error('Error submitting lead:', error);
      setSubmitStatus('error');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-4xl mx-auto px-6 py-12">
          <h2 className="section-title">Get SEO Help</h2>
          <p className="section-subtitle">Submit your SEO inquiry and get connected with the right expert</p>
          
          {submitStatus === 'success' && (
            <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
              Lead submitted successfully!
            </div>
          )}
          
          {submitStatus === 'error' && (
            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
              Error submitting lead. Please try again.
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <input 
              type="text" 
              name="fullName"
              placeholder="Your Name" 
              value={formData.fullName}
              onChange={handleInputChange}
              required 
              className="input-field" 
            />
            <input 
              type="email" 
              name="email"
              placeholder="Your Email" 
              value={formData.email}
              onChange={handleInputChange}
              required 
              className="input-field" 
            />
            <input 
              type="tel" 
              name="phone"
              placeholder="Your Phone (optional)" 
              value={formData.phone}
              onChange={handleInputChange}
              className="input-field" 
            />
            <input 
              type="text" 
              name="firm"
              placeholder="Company/Firm (optional)" 
              value={formData.firm}
              onChange={handleInputChange}
              className="input-field" 
            />
            <input 
              type="url" 
              name="website"
              placeholder="Website (optional)" 
              value={formData.website}
              onChange={handleInputChange}
              className="input-field" 
            />
            <div className="grid grid-cols-3 gap-4">
              <input 
                type="text" 
                name="city"
                placeholder="City (optional)" 
                value={formData.city}
                onChange={handleInputChange}
                className="input-field" 
              />
              <input 
                type="text" 
                name="state"
                placeholder="State (optional)" 
                value={formData.state}
                onChange={handleInputChange}
                className="input-field" 
              />
              <input 
                type="text" 
                name="zipCode"
                placeholder="ZIP Code (optional)" 
                value={formData.zipCode}
                onChange={handleInputChange}
                className="input-field" 
              />
            </div>
            <textarea 
              name="message"
              placeholder="Describe your SEO needs" 
              rows={5} 
              value={formData.message}
              onChange={handleInputChange}
              required 
              className="input-field" 
            />
            <button 
              type="submit" 
              disabled={isSubmitting}
              className="btn-primary"
            >
              {isSubmitting ? 'Submitting...' : 'Submit Inquiry'}
            </button>
          </form>
        </div>
      </main>
    </div>
  );
}
