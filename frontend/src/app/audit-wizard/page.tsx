"use client";
import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { apiService, AuditSubmission } from '@/services/api';

export default function AuditWizardPage() {
  const router = useRouter();
  const [currentStep, setCurrentStep] = useState(0);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    companyName: '',
    website: '',
    industry: '',
    goals: [] as string[],
    competitors: '',
    monthlyBudget: '',
    tier: '',
    notes: ''
  });

  const steps = [
    { key: "account", label: "Create Account" },
    { key: "business", label: "Business Details" },
    { key: "goals", label: "Goals & Competition" },
    { key: "plan", label: "Choose Your Package" },
    { key: "review", label: "Confirm & Submit" },
  ];

  const updateFormData = (field: string, value: any) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const next = () => setCurrentStep(Math.min(currentStep + 1, steps.length - 1));
  const back = () => setCurrentStep(Math.max(currentStep - 1, 0));

  const handleSubmit = async () => {
    setIsSubmitting(true);
    setSubmitError(null);
    
    try {
      const submission: AuditSubmission = {
        account: {
          firstName: formData.firstName,
          lastName: formData.lastName,
          email: formData.email,
          password: formData.password,
        },
        audit: {
          companyName: formData.companyName,
          website: formData.website,
          industry: formData.industry,
          goals: formData.goals,
          competitors: formData.competitors,
          monthlyBudget: formData.monthlyBudget,
          tier: formData.tier,
          notes: formData.notes,
        },
      };

      const result = await apiService.submitAuditWizard(submission);
      
      // Store the authentication token
      apiService.setAuthToken(result.token);
      
      // Redirect to dashboard
      router.push('/client');
    } catch (error) {
      console.error('Submission error:', error);
      setSubmitError(error instanceof Error ? error.message : 'Failed to submit audit. Please try again.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const renderStep = () => {
    switch (currentStep) {
      case 0: // Account
        return (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold">Create your account</h2>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label className="block text-sm font-medium mb-2">First name</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={formData.firstName}
                  onChange={(e) => updateFormData('firstName', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Last name</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={formData.lastName}
                  onChange={(e) => updateFormData('lastName', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Email</label>
                <input 
                  type="email"
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.email}
                  onChange={(e) => updateFormData('email', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Password</label>
                <input 
                  type="password"
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.password}
                  onChange={(e) => updateFormData('password', e.target.value)}
                />
              </div>
            </div>
            <p className="text-sm text-white/60">We'll create your portal login and connect this audit to your account.</p>
          </div>
        );
      case 1: // Business
        return (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold">Tell us about your business</h2>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label className="block text-sm font-medium mb-2">Company name</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.companyName}
                  onChange={(e) => updateFormData('companyName', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Website URL</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  placeholder="https://"
                  value={formData.website}
                  onChange={(e) => updateFormData('website', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Industry/Niche</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.industry}
                  onChange={(e) => updateFormData('industry', e.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Approx. monthly budget (optional)</label>
                <input 
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.monthlyBudget}
                  onChange={(e) => updateFormData('monthlyBudget', e.target.value)}
                />
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium mb-2">Top competitors (comma-separated)</label>
                <textarea 
                  rows={3}
                  className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                  value={formData.competitors}
                  onChange={(e) => updateFormData('competitors', e.target.value)}
                />
              </div>
            </div>
          </div>
        );
      case 2: // Goals
        return (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold">What are your goals?</h2>
            <div className="flex flex-wrap gap-2">
              {["More calls/leads", "Rank locally", "E‑commerce sales", "Reputation/Reviews", "Content strategy", "Technical SEO"].map((goal) => (
                <button 
                  key={goal}
                  onClick={() => {
                    const newGoals = formData.goals.includes(goal) 
                      ? formData.goals.filter(g => g !== goal)
                      : [...formData.goals, goal];
                    updateFormData('goals', newGoals);
                  }}
                  className={`rounded-full px-4 py-2 text-sm transition border ${
                    formData.goals.includes(goal) 
                      ? "bg-indigo-600 text-white border-indigo-500" 
                      : "bg-white/5 text-white/80 border-white/10 hover:bg-white/10"
                  }`}
                >
                  {goal}
                </button>
              ))}
            </div>
            <div>
              <label className="block text-sm font-medium mb-2">Notes (anything else we should know?)</label>
              <textarea 
                rows={4}
                className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white"
                value={formData.notes}
                onChange={(e) => updateFormData('notes', e.target.value)}
              />
            </div>
          </div>
        );
      case 3: // Plan
        return (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold">Choose Your Package</h2>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              {[
                { key: "Audit", price: "$3,000", features: ["Comprehensive SEO audit", "Technical analysis", "Competitive research", "Actionable roadmap", "Priority support"] },
                { key: "Audit + Retainer", price: "$5,000", features: ["Full $3,000 audit", "$1,500 credit applied", "3-month implementation", "Ongoing optimization", "Monthly reporting"] },
              ].map((tier) => (
                <button
                  key={tier.key}
                  onClick={() => updateFormData('tier', tier.key)}
                  className={`p-6 rounded-xl border transition ${
                    formData.tier === tier.key
                      ? "border-indigo-500 bg-indigo-500/10"
                      : "border-white/10 bg-white/5 hover:bg-white/10"
                  }`}
                >
                  <h3 className="text-lg font-semibold mb-2">{tier.key}</h3>
                  <p className="text-2xl font-bold mb-4">{tier.price}</p>
                  <ul className="space-y-2 text-sm">
                    {tier.features.map((feature, i) => (
                      <li key={i} className="flex items-center gap-2">
                        <span className="text-green-400">✓</span>
                        {feature}
                      </li>
                    ))}
                  </ul>
                </button>
              ))}
            </div>
          </div>
        );
      case 4: // Review
        return (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold">Confirm & Submit</h2>
            <div className="bg-white/5 rounded-xl p-6 space-y-4">
              <div>
                <h3 className="font-medium mb-2">Account Details</h3>
                <p>Name: {formData.firstName} {formData.lastName}</p>
                <p>Email: {formData.email}</p>
              </div>
              <div>
                <h3 className="font-medium mb-2">Business Details</h3>
                <p>Company: {formData.companyName}</p>
                <p>Website: {formData.website}</p>
                <p>Industry: {formData.industry}</p>
                <p>Budget: {formData.monthlyBudget}</p>
                <p>Competitors: {formData.competitors}</p>
              </div>
              <div>
                <h3 className="font-medium mb-2">Goals</h3>
                <p>{formData.goals.join(', ')}</p>
              </div>
              <div>
                <h3 className="font-medium mb-2">Selected Tier</h3>
                <p>{formData.tier}</p>
              </div>
              {formData.notes && (
                <div>
                  <h3 className="font-medium mb-2">Notes</h3>
                  <p>{formData.notes}</p>
                </div>
              )}
            </div>
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white">
      <div className="mx-auto max-w-6xl px-6 py-8">
        {/* Header */}
        <div className="mb-6">
          <h1 className="text-2xl font-semibold">SEO Audit Wizard</h1>
          <p className="text-white/70">Create your account, tell us about your business, and choose your audit package.</p>
        </div>

        {/* Breadcrumb */}
        <div className="mb-8 grid gap-3 md:grid-cols-5">
          {steps.map((step, i) => (
            <button
              key={step.key}
              onClick={() => setCurrentStep(i)}
              className={`text-left p-3 rounded-lg transition ${
                i === currentStep
                  ? "bg-indigo-600 text-white"
                  : i < currentStep
                  ? "bg-green-600/20 text-green-400 border border-green-600/30"
                  : "bg-white/5 text-white/60 hover:bg-white/10"
              }`}
            >
              <div className="text-xs font-medium mb-1">Step {i + 1}</div>
              <div className="text-sm">{step.label}</div>
            </button>
          ))}
        </div>

        {/* Step content */}
        <div className="mb-8">
          {renderStep()}
        </div>

        {/* Nav Buttons */}
        <div className="flex items-center justify-between">
          <button 
            onClick={back} 
            disabled={currentStep === 0} 
            className="rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-white/90 disabled:opacity-40"
          >
            Back
          </button>
          <div className="flex items-center gap-3">
            {currentStep < steps.length - 1 ? (
              <button 
                onClick={next} 
                className="rounded-xl bg-indigo-600 px-6 py-2.5 font-medium shadow-lg shadow-indigo-600/20 hover:bg-indigo-500"
              >
                Continue
              </button>
            ) : (
              <button 
                className="rounded-xl bg-emerald-600 px-6 py-2.5 font-medium shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                onClick={handleSubmit}
                disabled={isSubmitting}
              >
                {isSubmitting ? 'Submitting...' : 'Submit'}
              </button>
            )}
          </div>
        </div>

        {/* Error message */}
        {submitError && (
          <div className="mt-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm">
            {submitError}
          </div>
        )}
      </div>
    </div>
  );
}
