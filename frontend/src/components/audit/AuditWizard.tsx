"use client";
import { useEffect, useMemo, useState } from "react";
import { create } from "zustand";
import { useRouter } from "next/navigation";

// -----------------------------
// Zustand store (autosave + persistence)
// -----------------------------

type AuditTier = "Growth" | "Pro" | "Enterprise";

type Account = {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
};

type AuditForm = {
  companyName: string;
  website: string;
  industry: string;
  goals: string[];
  competitors: string;
  monthlyBudget: string;
  tier: AuditTier | "";
  notes: string;
};

type AuthState = {
  token: string | null;
  userId: string | null;
  isAuthenticated: boolean;
};

type AuditState = {
  // Meta
  auditId: string | null;
  isSaving: boolean;
  saveError: string | null;
  lastSavedAt: number | null;

  // Steps
  currentStep: number;
  maxStepVisited: number;

  // Data
  account: Account;
  form: AuditForm;

  // Validation
  validationErrors: ValidationErrors;

  // Actions
  setStep: (n: number) => void;
  next: () => void;
  back: () => void;
  updateAccount: (p: Partial<Account>) => void;
  updateForm: (p: Partial<AuditForm>) => void;
  setTier: (t: AuditTier) => void;
  markSaved: (auditId?: string) => void;
  setSaving: (flag: boolean) => void;
  setError: (msg: string | null) => void;
  setValidationErrors: (errors: ValidationErrors) => void;
  clearValidationErrors: () => void;
  validateCurrentStep: () => boolean;
  isStepComplete: (stepIndex: number) => boolean;
  arePreviousStepsComplete: () => boolean;
  reset: () => void;
};

type ValidationErrors = {
  account: {
    firstName?: string;
    lastName?: string;
    email?: string;
    password?: string;
  };
  form: {
    companyName?: string;
    website?: string;
    industry?: string;
    goals?: string;
    competitors?: string;
    monthlyBudget?: string;
    tier?: string;
    notes?: string;
  };
};

const defaultState: Pick<AuditState, "account" | "form" | "validationErrors"> = {
  account: { email: "", password: "", firstName: "", lastName: "" },
  form: {
    companyName: "",
    website: "",
    industry: "",
    goals: [],
    competitors: "",
    monthlyBudget: "",
    tier: "",
    notes: "",
  },
  validationErrors: { account: {}, form: {} },
};

const STORAGE_KEY = "tulsa-seo.audit-wizard.v1";
const AUTH_STORAGE_KEY = "auth_token";

const useAuthStore = create<AuthState>((_set) => ({
  token: null,
  userId: null,
  isAuthenticated: false,
}));

const useAuditStore = create<AuditState>((set, get) => ({
  auditId: null,
  isSaving: false,
  saveError: null,
  lastSavedAt: null,
  currentStep: 0,
  maxStepVisited: 0,
  ...defaultState,
  setStep: (n: number) => set({ currentStep: n, maxStepVisited: Math.max(get().maxStepVisited, n) }),
  next: () => set({ currentStep: Math.min(get().currentStep + 1, steps.length - 1), maxStepVisited: Math.max(get().maxStepVisited, get().currentStep + 1) }),
  back: () => set({ currentStep: Math.max(get().currentStep - 1, 0) }),
  updateAccount: (p: Partial<Account>) => set({ account: { ...get().account, ...p } }),
  updateForm: (p: Partial<AuditForm>) => set({ form: { ...get().form, ...p } }),
  setTier: (t: AuditTier) => set({ form: { ...get().form, tier: t } }),
  markSaved: (id?: string) => set({ isSaving: false, saveError: null, lastSavedAt: Date.now(), auditId: id ?? get().auditId }),
  setSaving: (flag: boolean) => set({ isSaving: flag }),
  setError: (msg: string | null) => set({ saveError: msg, isSaving: false }),
  setValidationErrors: (errors: ValidationErrors) => set({ validationErrors: errors }),
  clearValidationErrors: () => set({ validationErrors: { account: {}, form: {} } }),
  validateCurrentStep: () => {
    const errors = validateStep(get().currentStep, get().account, get().form);
    set({ validationErrors: errors });
    // Check if there are any validation errors
    const hasErrors = Object.values(errors.account).some(error => error) || 
                     Object.values(errors.form).some(error => error);
    return !hasErrors;
  },
  isStepComplete: (stepIndex: number) => {
    const errors = validateStep(stepIndex, get().account, get().form);
    const hasErrors = Object.values(errors.account).some(error => error) || 
                     Object.values(errors.form).some(error => error);
    return !hasErrors;
  },
  arePreviousStepsComplete: () => {
    for (let i = 0; i < get().currentStep; i++) {
      if (!get().isStepComplete(i)) {
        return false;
      }
    }
    return true;
  },
  reset: () => set({
    auditId: null,
    isSaving: false,
    saveError: null,
    lastSavedAt: null,
    currentStep: 0,
    maxStepVisited: 0,
    ...defaultState,
  })
}));

// Load from localStorage
const loadFromStorage = () => {
  if (typeof window === "undefined") return;
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;
    const parsed = JSON.parse(raw);
    useAuditStore.setState({ ...parsed });
  } catch (e) {
    // ignore
  }
};

// Save to localStorage (debounced)
let saveTimer: NodeJS.Timeout | null = null;
const persistToStorage = () => {
  if (typeof window === "undefined") return;
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const state = useAuditStore.getState();
    const minimal = {
      auditId: state.auditId,
      currentStep: state.currentStep,
      maxStepVisited: state.maxStepVisited,
      account: state.account,
      form: state.form,
      lastSavedAt: state.lastSavedAt,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(minimal));
  }, 350);
};

useAuditStore.subscribe(persistToStorage);

// -----------------------------
// API helpers (integrated with your Symfony backend)
// -----------------------------

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';
const GOOGLE_OAUTH_URL = `${API_BASE}/api/v1/auth/google`;
const MICROSOFT_OAUTH_URL = `${API_BASE}/api/v1/auth/microsoft`;

async function loginUser(email: string, password: string): Promise<{ token: string; userId: string }> {
  const response = await fetch(`${API_BASE}/api/v1/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password }),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.error || 'Login failed');
  }

  const data = await response.json();
  return { token: data.token, userId: data.user.id };
}

async function registerUser(account: Account, auditForm: AuditForm): Promise<{ token: string; userId: string }> {
  const response = await fetch(`${API_BASE}/api/v1/clients/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      organization_name: auditForm.companyName,
      organization_domain: auditForm.website,
      client_name: auditForm.companyName,
      client_website: auditForm.website,
      admin_email: account.email,
      admin_password: account.password,
      admin_first_name: account.firstName,
      admin_last_name: account.lastName,
    }),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.error || 'Registration failed');
  }

  const data = await response.json();
  // The client registration endpoint doesn't return a token, so we need to login after registration
  return { token: 'temp-token', userId: data.admin_user?.id || 'temp-user' };
}

type AuditPayload = {
  id?: string | null;
  form: AuditForm;
  account: Account;
};

async function upsertAudit(payload: AuditPayload): Promise<{ id: string }> {
  const authStore = useAuthStore.getState();
  
  if (!authStore.isAuthenticated) {
    throw new Error('Authentication required');
  }

  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${authStore.token}`,
  };

  if (payload.id) {
    // Update existing audit
    const response = await fetch(`${API_BASE}/api/v1/audit-intakes/${payload.id}`, {
      method: 'PATCH',
      headers: { ...headers, 'Content-Type': 'application/merge-patch+json' },
      body: JSON.stringify({
        websiteUrl: payload.form.website,
        contactName: `${payload.account.firstName} ${payload.account.lastName}`,
        contactEmail: payload.account.email,
        // Map other fields to AuditIntake entity structure
        techStack: {
          industry: payload.form.industry,
          goals: payload.form.goals,
          competitors: payload.form.competitors,
          budget: payload.form.monthlyBudget,
          tier: payload.form.tier,
          notes: payload.form.notes,
        },
      }),
    });

    if (!response.ok) {
      throw new Error('Failed to update audit');
    }

    return { id: payload.id };
  } else {
    // Create new audit
    const response = await fetch(`${API_BASE}/api/v1/audit-intakes`, {
      method: 'POST',
      headers,
      body: JSON.stringify({
        websiteUrl: payload.form.website,
        contactName: `${payload.account.firstName} ${payload.account.lastName}`,
        contactEmail: payload.account.email,
        cms: 'custom', // Default value
        techStack: {
          industry: payload.form.industry,
          goals: payload.form.goals,
          competitors: payload.form.competitors,
          budget: payload.form.monthlyBudget,
          tier: payload.form.tier,
          notes: payload.form.notes,
        },
      }),
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || 'Failed to create audit');
    }

    const data = await response.json();
    return { id: data.id };
  }
}

// -----------------------------
// UI Building Blocks
// -----------------------------

const Crumb = ({ active, done, onClick, children, index, isClickable }: { 
  active: boolean; 
  done: boolean; 
  onClick: () => void; 
  children: React.ReactNode; 
  index: number; 
  isClickable: boolean;
}) => (
  <button
    onClick={onClick}
    disabled={!isClickable}
    className={`group flex items-center gap-3 px-4 py-2 rounded-xl border transition ${
      active
        ? "border-indigo-500 bg-indigo-500/10 text-indigo-200"
        : done
        ? "border-emerald-600/40 bg-emerald-600/10 text-emerald-200 hover:bg-emerald-600/20"
        : isClickable
        ? "border-white/10 text-white/90 hover:bg-white/5"
        : "border-white/5 text-white/50 cursor-not-allowed opacity-50"
    }`}
  >
    <span className={`flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold ${
      active ? "bg-indigo-500 text-white" : done ? "bg-emerald-600/80 text-white" : isClickable ? "bg-white/10 text-white/90" : "bg-white/5 text-white/50"
    }`}>
      {index + 1}
    </span>
    <span className="text-left">
      <div className="text-xs uppercase tracking-wide opacity-90">Step {index + 1}</div>
      <div className="font-medium text-white">{children}</div>
    </span>
  </button>
);

const Field = ({ label, children }: { label: string; children: React.ReactNode }) => (
  <label className="block space-y-2">
    <span className="text-sm text-white/90">{label}</span>
    {children}
  </label>
);

const Card: React.FC<{ title?: string; right?: React.ReactNode; children: React.ReactNode; className?: string }>
  = ({ title, right, children, className = "" }) => (
  <div className={`rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl ${className}`}>
    {title && (
      <div className="mb-4 flex items-center justify-between">
        <h3 className="text-lg font-semibold text-white">{title}</h3>
        <div>{right}</div>
      </div>
    )}
    {children}
  </div>
);

const SaveBadge = () => {
  const { isSaving, lastSavedAt, saveError } = useAuditStore();
  const text = isSaving
    ? "Saving…"
    : saveError
    ? "Save failed"
    : lastSavedAt
    ? `Saved ${new Date(lastSavedAt).toLocaleTimeString()}`
    : "Not saved yet";
  return (
    <div className={`rounded-full px-3 py-1 text-xs font-medium ${
      isSaving ? "bg-yellow-500/20 text-yellow-200" : saveError ? "bg-rose-600/30 text-rose-200" : "bg-emerald-600/20 text-emerald-200"
    }`}>
      {text}
    </div>
  );
};

// -----------------------------
// Steps
// -----------------------------

const steps = [
  { key: "account", label: "Create Account" },
  { key: "business", label: "Business Details" },
  { key: "goals", label: "Goals & Competition" },
  { key: "plan", label: "Pick Your Tier" },
  { key: "review", label: "Confirm & Submit" },
  { key: "success", label: "Audit Submitted" },
] as const;

type StepKey = typeof steps[number]["key"];

function AccountStep() {
  const { account, updateAccount, validationErrors } = useAuditStore();
  const { isAuthenticated } = useAuthStore();
  const [isGoogleLoading, setIsGoogleLoading] = useState(false);
  const [isMicrosoftLoading, setIsMicrosoftLoading] = useState(false);
  return (
    <Card title="Create your account">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field label="First name">
          <input 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              validationErrors.account.firstName ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={account.firstName} 
            onChange={(e) => updateAccount({ firstName: e.target.value })} 
          />
          {validationErrors.account.firstName && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.account.firstName}</p>
          )}
        </Field>
        <Field label="Last name">
          <input 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              validationErrors.account.lastName ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={account.lastName} 
            onChange={(e) => updateAccount({ lastName: e.target.value })} 
          />
          {validationErrors.account.lastName && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.account.lastName}</p>
          )}
        </Field>
        <Field label="Email">
          <input 
            type="email" 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white ${
              validationErrors.account.email ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={account.email} 
            onChange={(e) => updateAccount({ email: e.target.value })} 
          />
          {validationErrors.account.email && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.account.email}</p>
          )}
        </Field>
        <Field label="Password">
          <input 
            type="password" 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white ${
              validationErrors.account.password ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={account.password} 
            onChange={(e) => updateAccount({ password: e.target.value })} 
            disabled={isAuthenticated}
          />
          {isAuthenticated && (
            <p className="text-xs text-white/60 mt-1">Authenticated via SSO — password entry disabled.</p>
          )}
          {validationErrors.account.password && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.account.password}</p>
          )}
        </Field>
      </div>
      <p className="mt-3 text-sm text-white/60">We&apos;ll create your portal login and connect this audit to your account.</p>
      
      {/* SSO Providers */}
      <div className="mt-6">
        <div className="relative">
          <div className="absolute inset-0 flex items-center">
            <span className="w-full border-t border-white/10" />
          </div>
          <div className="relative flex justify-center text-sm">
            <span className="bg-gray-900 px-2 text-white/60">Or continue with</span>
          </div>
        </div>
        <div className="mt-4 grid gap-3 md:grid-cols-2">
        <button
          type="button"
          onClick={() => {
            if (typeof window !== 'undefined') {
              setIsGoogleLoading(true);
              window.location.href = GOOGLE_OAUTH_URL + `?redirect=${encodeURIComponent(window.location.href)}`;
            }
          }}
          disabled={isGoogleLoading || isAuthenticated}
          className={`inline-flex items-center justify-center gap-3 rounded-xl border px-4 py-3 font-medium transition-all duration-200 ${
            isGoogleLoading || isAuthenticated 
              ? 'opacity-60 cursor-not-allowed bg-gray-800 border-gray-600 text-gray-400' 
              : 'bg-white border-white/20 text-gray-900 hover:bg-gray-100 hover:border-white/30 hover:shadow-lg'
          }`}
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" className="h-5 w-5" aria-hidden>
            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303C33.813,31.345,29.277,34,24,34c-6.627,0-12-5.373-12-12 s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.64,4.224,28.991,2,24,2C12.955,2,4,10.955,4,22 s8.955,20,20,20c11.045,0,20-8.955,20-20C44,21.329,43.861,20.691,43.611,20.083z"/>
            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.818C14.655,16.108,18.961,14,24,14c3.059,0,5.842,1.154,7.961,3.039 l5.657-5.657C33.64,4.224,28.991,2,24,2C16.318,2,9.656,6.337,6.306,14.691z"/>
            <path fill="#4CAF50" d="M24,42c5.166,0,9.86-1.977,13.409-5.197l-6.2-5.238C29.109,33.488,26.715,34,24,34 c-5.248,0-9.799-3.223-11.571-7.773l-6.56,5.049C7.201,37.556,15.017,42,24,42z"/>
            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-1.074,3.073-3.272,5.487-6.094,6.962 c0.002-0.001,0.003-0.001,0.005-0.002l6.2,5.238C34.955,40.338,44,34,44,22C44,21.329,43.861,20.691,43.611,20.083z"/>
          </svg>
          <span className="font-medium">{isGoogleLoading ? 'Redirecting…' : 'Continue with Google'}</span>
        </button>
        <button
          type="button"
          onClick={() => {
            if (typeof window !== 'undefined') {
              setIsMicrosoftLoading(true);
              window.location.href = MICROSOFT_OAUTH_URL + `?redirect=${encodeURIComponent(window.location.href)}`;
            }
          }}
          disabled={isMicrosoftLoading || isAuthenticated}
          className={`inline-flex items-center justify-center gap-3 rounded-xl border px-4 py-3 font-medium transition-all duration-200 ${
            isMicrosoftLoading || isAuthenticated 
              ? 'opacity-60 cursor-not-allowed bg-gray-800 border-gray-600 text-gray-400' 
              : 'bg-white border-white/20 text-gray-900 hover:bg-gray-100 hover:border-white/30 hover:shadow-lg'
          }`}
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23" className="h-5 w-5" aria-hidden>
            <rect width="10" height="10" x="1" y="1" fill="#F35325"/>
            <rect width="10" height="10" x="12" y="1" fill="#81BC06"/>
            <rect width="10" height="10" x="1" y="12" fill="#05A6F0"/>
            <rect width="10" height="10" x="12" y="12" fill="#FFBA08"/>
          </svg>
          <span>{isMicrosoftLoading ? 'Redirecting…' : 'Continue with Microsoft'}</span>
        </button>
        </div>
      </div>
    </Card>
  );
}

function BusinessStep() {
  const { form, updateForm, validationErrors } = useAuditStore();
  return (
    <Card title="Tell us about your business">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field label="Company name">
          <input 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white ${
              validationErrors.form.companyName ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={form.companyName} 
            onChange={(e) => updateForm({ companyName: e.target.value })} 
          />
          {validationErrors.form.companyName && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.form.companyName}</p>
          )}
        </Field>
        <Field label="Website URL">
          <input 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white ${
              validationErrors.form.website ? 'border-rose-500' : 'border-white/10'
            }`} 
            placeholder="https://" 
            value={form.website} 
            onChange={(e) => updateForm({ website: e.target.value })} 
          />
          {validationErrors.form.website && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.form.website}</p>
          )}
        </Field>
        <Field label="Industry/Niche">
          <input 
            className={`w-full rounded-xl border bg-black/40 p-3 text-white ${
              validationErrors.form.industry ? 'border-rose-500' : 'border-white/10'
            }`} 
            value={form.industry} 
            onChange={(e) => updateForm({ industry: e.target.value })} 
          />
          {validationErrors.form.industry && (
            <p className="text-xs text-rose-200 mt-1">{validationErrors.form.industry}</p>
          )}
        </Field>
        <Field label="Approx. monthly budget (optional)">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.monthlyBudget} onChange={(e) => updateForm({ monthlyBudget: e.target.value })} />
        </Field>
        <div className="md:col-span-2">
          <Field label="Top competitors (comma-separated)">
            <textarea rows={3} className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.competitors} onChange={(e) => updateForm({ competitors: e.target.value })} />
          </Field>
        </div>
      </div>
    </Card>
  );
}

function GoalsStep() {
  const { form, updateForm, validationErrors } = useAuditStore();
  const options = ["More calls/leads", "Rank locally", "E‑commerce sales", "Reputation/Reviews", "Content strategy", "Technical SEO"];
  const toggle = (g: string) => {
    const next = form.goals.includes(g) ? form.goals.filter((x: string) => x !== g) : [...form.goals, g];
    updateForm({ goals: next });
  };
  return (
    <Card title="What are your goals?">
      <div className="flex flex-wrap gap-2">
        {options.map((g) => (
          <button key={g} onClick={() => toggle(g)} className={`rounded-full px-4 py-2 text-sm transition border ${
            form.goals.includes(g) ? "bg-indigo-600 text-white border-indigo-500" : "bg-white/5 text-white/90 border-white/10 hover:bg-white/10"
          }`}>
            {g}
          </button>
        ))}
      </div>
      {validationErrors.form.goals && (
        <p className="text-xs text-rose-200 mt-2">{validationErrors.form.goals}</p>
      )}
      <div className="mt-4">
        <Field label="Notes (anything else we should know?)">
          <textarea rows={4} className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.notes} onChange={(e) => updateForm({ notes: e.target.value })} />
        </Field>
      </div>
    </Card>
  );
}

function PlanStep() {
  const { form, setTier, validationErrors } = useAuditStore();
  const [mode, setMode] = useState<"audit" | "subscription">("audit");
  
  const auditTiers: { key: AuditTier; price: string; features: string[] }[] = [
    { key: "Growth", price: "$499", features: ["Full audit + roadmap", "Rank-tracking setup", "On-page fixes"] },
    { key: "Pro", price: "$999", features: ["Everything in Growth", "Technical crawl & schema", "Content plan (3 mo)"] },
    { key: "Enterprise", price: "Custom", features: ["Multi-location", "Dedicated strategist", "Custom integrations"] },
  ];

  const subscriptionTiers: { key: AuditTier; price: string; features: string[] }[] = [
    { key: "Growth", price: "$799/mo", features: ["Content optimization", "Competitor tracking", "Monthly strategy call"] },
    { key: "Pro", price: "$1,499/mo", features: ["Technical monitoring", "Content production", "Bi-weekly strategy calls"] },
    { key: "Enterprise", price: "$3,000+/mo", features: ["Dedicated strategist", "PR + backlink campaigns", "Weekly reporting"] },
  ];

  const tiers = mode === "audit" ? auditTiers : subscriptionTiers;

  return (
    <div>
      {/* Mode Toggle */}
      <div className="mb-6 flex justify-center">
        <div className="inline-flex rounded-xl border border-white/10 bg-white/5 p-1 text-sm">
          <button
            className={`rounded-lg px-4 py-2 ${mode === "audit" ? "bg-indigo-600 text-white" : "text-white/90"}`}
            onClick={() => setMode("audit")}
          >
            Audit Pricing
          </button>
          <button
            className={`rounded-lg px-4 py-2 ${mode === "subscription" ? "bg-indigo-600 text-white" : "text-white/90"}`}
            onClick={() => setMode("subscription")}
          >
            Subscription Plans
          </button>
        </div>
      </div>

      {/* Tiers Grid */}
      <div className="grid gap-4 md:grid-cols-3">
        {tiers.map((t) => (
          <Card key={t.key} title={t.key} right={form.tier === t.key ? <span className="rounded-full bg-emerald-600/20 px-3 py-1 text-xs text-emerald-200">Selected</span> : null}>
            <div className="mb-2 text-2xl font-semibold text-white">{t.price}<span className="text-sm text-white/90 ml-1">{mode === "audit" && t.key !== "Enterprise" ? "/ audit" : ""}</span></div>
            <ul className="mb-4 space-y-1 text-sm text-white/90">
              {t.features.map((f) => (
                <li key={f} className="flex items-start gap-2"><span className="mt-1 h-1.5 w-1.5 rounded-full bg-white/50" />{f}</li>
              ))}
            </ul>
            <button onClick={() => setTier(t.key)} className={`w-full rounded-xl border px-4 py-2 font-medium transition ${
              form.tier === t.key ? "border-emerald-500 bg-emerald-600/20 text-emerald-100" : "border-white/10 bg-white/5 text-white/90 hover:bg-white/10"
            }`}>
              {form.tier === t.key ? "Selected" : "Choose"}
            </button>
          </Card>
        ))}
      </div>
      {validationErrors.form.tier && (
        <p className="text-xs text-rose-200 mt-2 text-center">{validationErrors.form.tier}</p>
      )}
    </div>
  );
}

function ReviewStep() {
  const { account, form, auditId } = useAuditStore();
  
  const getTierPrice = (tier: string) => {
    const auditPrices: Record<string, string> = {
      "Growth": "$499", 
      "Pro": "$999",
      "Enterprise": "Custom"
    };
    const subscriptionPrices: Record<string, string> = {
      "Growth": "$799/mo",
      "Pro": "$1,499/mo", 
      "Enterprise": "$3,000+/mo"
    };
    
    // Determine if this is subscription based on URL hash
    const isSubscription = typeof window !== 'undefined' && window.location.hash === '#subscribe';
    return isSubscription ? subscriptionPrices[tier] : auditPrices[tier];
  };

  return (
    <div className="grid gap-4 md:grid-cols-2">
      <Card title="Account">
        <div className="space-y-1 text-white/80">
          <div><span className="text-white/60">Name:</span> {account.firstName} {account.lastName}</div>
          <div><span className="text-white/60">Email:</span> {account.email}</div>
        </div>
      </Card>
      <Card title="Business">
        <div className="space-y-1 text-white/80">
          <div><span className="text-white/60">Company:</span> {form.companyName}</div>
          <div><span className="text-white/60">Website:</span> {form.website}</div>
          <div><span className="text-white/60">Industry:</span> {form.industry}</div>
          <div><span className="text-white/60">Budget:</span> {form.monthlyBudget || "—"}</div>
        </div>
      </Card>
      <Card title="Goals & Notes">
        <div className="text-white/80">
          <div className="mb-2"><span className="text-white/60">Goals:</span> {form.goals.length ? form.goals.join(", ") : "—"}</div>
          <div className="whitespace-pre-wrap"><span className="text-white/60">Notes:</span> {form.notes || "—"}</div>
        </div>
      </Card>
      <Card title="Selected Plan">
        <div className="text-white/80">
          <div className="font-medium">{form.tier || "—"}</div>
          {form.tier && (
            <div className="text-lg font-semibold text-emerald-400 mt-1">
              {getTierPrice(form.tier)}
            </div>
          )}
          <div className="text-sm text-white/60 mt-2">
            {typeof window !== 'undefined' && window.location.hash === '#subscribe' ? 'Subscription Plan' : 'One-time Audit'}
          </div>
        </div>
        {auditId && <div className="mt-2 text-xs text-white/50">Audit ID: {auditId}</div>}
      </Card>
    </div>
  );
}

function SuccessStep() {
  const { account, form, auditId } = useAuditStore();
  const router = useRouter();
  
  const handleGoToDashboard = () => {
    router.push('/client');
  };

  const handleConnectGBP = () => {
    // This would typically open a modal or redirect to GBP connection flow
    // For now, we'll show an alert with instructions
    alert('Google Business Profile connection will be implemented here. This will allow you to:\n\n• Claim your business listing\n• Manage reviews and photos\n• Track local search performance\n• Sync data with your audit results');
  };

  return (
    <div className="max-w-4xl mx-auto">
      {/* Success Header */}
      <div className="text-center mb-8">
        <div className="inline-flex items-center justify-center w-16 h-16 bg-emerald-600/20 rounded-full mb-4">
          <svg className="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 className="text-3xl font-bold text-white mb-2">Audit Submitted Successfully!</h2>
        <p className="text-white/80 text-lg">
          Your {form.tier} audit for <span className="font-semibold text-emerald-400">{form.companyName}</span> is now in progress.
        </p>
        {auditId && (
          <div className="mt-3 text-sm text-white/60">
            Audit ID: <span className="font-mono bg-white/10 px-2 py-1 rounded">{auditId}</span>
          </div>
        )}
      </div>

      {/* Timeline Card */}
      <Card title="What happens next?" className="mb-6">
        <div className="space-y-4">
          <div className="flex items-start gap-4">
            <div className="flex-shrink-0 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
              <span className="text-sm font-semibold text-white">1</span>
            </div>
            <div>
              <h4 className="font-semibold text-white mb-1">Initial Analysis (Day 1-2)</h4>
              <p className="text-white/80 text-sm">We&apos;ll crawl your website and analyze your current SEO performance, technical issues, and competitive landscape.</p>
            </div>
          </div>
          <div className="flex items-start gap-4">
            <div className="flex-shrink-0 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
              <span className="text-sm font-semibold text-white">2</span>
            </div>
            <div>
              <h4 className="font-semibold text-white mb-1">Deep Dive Research (Day 3-4)</h4>
              <p className="text-white/80 text-sm">Our team conducts keyword research, competitor analysis, and develops your customized optimization roadmap.</p>
            </div>
          </div>
          <div className="flex items-start gap-4">
            <div className="flex-shrink-0 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
              <span className="text-sm font-semibold text-white">3</span>
            </div>
            <div>
              <h4 className="font-semibold text-white mb-1">Final Report (Day 5)</h4>
              <p className="text-white/80 text-sm">You&apos;ll receive your comprehensive audit report with actionable recommendations and priority rankings.</p>
            </div>
          </div>
        </div>
        <div className="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl">
          <p className="text-blue-200 text-sm">
            <strong>Typical timeline:</strong> 3-5 business days. You&apos;ll receive email updates at <span className="font-semibold">{account.email}</span> throughout the process.
          </p>
        </div>
      </Card>

      {/* GBP Setup Card */}
      <Card title="While You Wait: Set Up Google Business Profile" className="mb-6">
        <div className="space-y-4">
          <p className="text-white/80">
            To maximize your local SEO results, we recommend setting up and optimizing your Google Business Profile now. 
            This will be integrated into your audit results and ongoing strategy.
          </p>
          
          <div className="grid gap-4 md:grid-cols-2">
            <div className="p-4 bg-white/5 rounded-xl border border-white/10">
              <h4 className="font-semibold text-white mb-2 flex items-center gap-2">
                <svg className="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                  <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Don&apos;t have a GBP?
              </h4>
              <p className="text-white/70 text-sm mb-3">Create a free Google Business Profile to get started with local SEO.</p>
              <a 
                href="https://business.google.com/create" 
                target="_blank" 
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
              >
                Create GBP Profile
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
            </div>
            
            <div className="p-4 bg-white/5 rounded-xl border border-white/10">
              <h4 className="font-semibold text-white mb-2 flex items-center gap-2">
                <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Already have a GBP?
              </h4>
              <p className="text-white/70 text-sm mb-3">Connect your existing Google Business Profile to your account.</p>
              <button 
                onClick={handleConnectGBP}
                className="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition"
              >
                Connect GBP
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </Card>

      {/* Action Buttons */}
      <div className="flex flex-col sm:flex-row gap-4 justify-center">
        <button 
          onClick={handleGoToDashboard}
          className="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition flex items-center justify-center gap-2"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z" />
          </svg>
          Go to Dashboard
        </button>
        
        <button 
          onClick={() => window.location.href = '/services/audit'}
          className="px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition flex items-center justify-center gap-2"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Learn More About Our Process
        </button>
      </div>
    </div>
  );
}

const StepBody = ({ stepKey }: { stepKey: StepKey }) => {
  switch (stepKey) {
    case "account":
      return <AccountStep />;
    case "business":
      return <BusinessStep />;
    case "goals":
      return <GoalsStep />;
    case "plan":
      return <PlanStep />;
    case "review":
      return <ReviewStep />;
    case "success":
      return <SuccessStep />;
    default:
      return null;
  }
};

// -----------------------------
// Main Wizard Component
// -----------------------------

export default function AuditWizard() {
  const { currentStep, setStep, next, back, setSaving, markSaved, setError, validateCurrentStep, clearValidationErrors } = useAuditStore();
  const state = useAuditStore();
  const { isAuthenticated } = useAuthStore();
  const [ssoError, setSsoError] = useState<string | null>(null);

  // Handle URL parameters for pre-selecting tier
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const urlParams = new URLSearchParams(window.location.search);
      const tier = urlParams.get('tier') as AuditTier;
      const isSubscription = window.location.hash === '#subscribe';
      
      if (tier && ['Growth', 'Pro', 'Enterprise'].includes(tier)) {
        useAuditStore.getState().setTier(tier);
      }
      
      // If subscription mode, skip to plan step
      if (isSubscription && currentStep < 3) {
        setStep(3); // Plan step
      }
    }
  }, [currentStep, setStep]);

  // Initial load
  useEffect(() => {
    loadFromStorage();
    
    // Load auth state from localStorage
    const authData = localStorage.getItem(AUTH_STORAGE_KEY);
    if (authData) {
      try {
        const parsed = JSON.parse(authData);
        useAuthStore.setState(parsed);
      } catch (e) {
        // ignore
      }
    }

    // Handle OAuth callback params (?token=...&email=...)
    try {
      if (typeof window !== 'undefined') {
        const url = new URL(window.location.href);
        const token = url.searchParams.get('token');
        const email = url.searchParams.get('email');
        const firstName = url.searchParams.get('firstName');
        const lastName = url.searchParams.get('lastName');
        const oauthError = url.searchParams.get('error') || url.searchParams.get('message') || url.searchParams.get('error_description');

        if (oauthError) {
          setSsoError(oauthError);
          const cleanUrl = `${url.origin}${url.pathname}`;
          window.history.replaceState({}, '', cleanUrl);
        } else if (token) {
          useAuthStore.setState({ token, userId: null, isAuthenticated: true });
          // Store token in the format expected by the main auth system
          localStorage.setItem('auth_token', token);
          localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({ token, userId: null, isAuthenticated: true }));

          // Pre-fill account info when available; set sentinel password to satisfy validation
          if (email) {
            useAuditStore.setState({ account: { ...useAuditStore.getState().account, email } });
          }
          if (firstName) {
            useAuditStore.setState({ account: { ...useAuditStore.getState().account, firstName } });
          }
          if (lastName) {
            useAuditStore.setState({ account: { ...useAuditStore.getState().account, lastName } });
          }
          useAuditStore.setState({ account: { ...useAuditStore.getState().account, password: 'SSO_AUTH' } });

          // Clean URL and move to next step
          const cleanUrl = `${url.origin}${url.pathname}`;
          window.history.replaceState({}, '', cleanUrl);
          clearValidationErrors();
          setStep(1);
        }
      }
    } catch (_) {
      // ignore
    }
  }, []);

  // Handle next step with validation
  const handleNext = () => {
    if (validateCurrentStep()) {
      clearValidationErrors();
      next();
    }
  };

  // Handle step navigation with validation
  const handleStepClick = (stepIndex: number) => {
    // Only allow navigation to steps that are complete or the current step
    // Check if all steps up to the target step are complete
    let canNavigate = true;
    for (let i = 0; i < stepIndex; i++) {
      if (!state.isStepComplete(i)) {
        canNavigate = false;
        break;
      }
    }
    
    if (canNavigate && stepIndex <= state.maxStepVisited) {
      clearValidationErrors();
      setStep(stepIndex);
    }
  };

  // Handle submit
  const handleSubmit = async () => {
    if (validateCurrentStep()) {
      try {
        setSaving(true);
        const res = await upsertAudit({
          id: state.auditId,
          account: state.account,
          form: state.form,
        });
        markSaved(res.id);
        // Navigate to success step
        next();
      } catch (e: unknown) {
        const errorMessage = e instanceof Error ? e.message : "Failed to submit";
        setError(errorMessage);
      }
    }
  };

  // Autosave (sync to backend) — debounced on any change
  const payload = useMemo(() => ({
    id: state.auditId,
    account: state.account,
    form: state.form,
    step: state.currentStep,
  }), [state.auditId, state.account, state.form, state.currentStep]);

  useEffect(() => {
    const t = setTimeout(async () => {
      try {
        // Ensure user is authenticated before saving
        if (!isAuthenticated) {
          // Try to login with existing credentials, or register if new
          try {
            const authResult = await loginUser(state.account.email, state.account.password);
            useAuthStore.setState({
              token: authResult.token,
              userId: authResult.userId,
              isAuthenticated: true,
            });
            // Store token in the format expected by the main auth system
            localStorage.setItem('auth_token', authResult.token);
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
              token: authResult.token,
              userId: authResult.userId,
              isAuthenticated: true,
            }));
          } catch (loginError) {
            // If login fails, try to register
            try {
              await registerUser(state.account, state.form);
              // After registration, login to get a proper token
              const loginResult = await loginUser(state.account.email, state.account.password);
              useAuthStore.setState({
                token: loginResult.token,
                userId: loginResult.userId,
                isAuthenticated: true,
              });
              // Store token in the format expected by the main auth system
              localStorage.setItem('auth_token', loginResult.token);
              localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
                token: loginResult.token,
                userId: loginResult.userId,
                isAuthenticated: true,
              }));
            } catch (registerError) {
              setError('Authentication failed. Please check your credentials.');
              return;
            }
          }
        }

        setSaving(true);
        const res = await upsertAudit(payload);
        markSaved(res.id);
      } catch (e: unknown) {
        const errorMessage = e instanceof Error ? e.message : "Failed to save";
        setError(errorMessage);
      }
    }, 600);
    return () => clearTimeout(t);
  }, [payload, setSaving, markSaved, setError, isAuthenticated, state.account]);

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white">
      <div className="mx-auto max-w-6xl px-6 py-8">
        {/* Header */}
        <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
          <div>
            <h1 className="text-2xl font-semibold text-white">SEO Audit Wizard</h1>
            <p className="text-white/90">Create your account, tell us about your business, and pick the audit tier. Autosaves as you type.</p>
          </div>
          <SaveBadge />
        </div>

        {/* Breadcrumb */}
        <div className="mb-8 grid gap-3 md:grid-cols-5">
          {steps.map((s, i) => {
            const isStepComplete = state.isStepComplete(i);
            // Check if all previous steps are complete
            let canNavigate = true;
            for (let j = 0; j < i; j++) {
              if (!state.isStepComplete(j)) {
                canNavigate = false;
                break;
              }
            }
            const isClickable = i <= state.maxStepVisited && canNavigate;
            return (
              <Crumb
                key={s.key}
                index={i}
                active={i === currentStep}
                done={i < currentStep && isStepComplete}
                isClickable={isClickable}
                onClick={() => handleStepClick(i)}
              >
                {s.label}
              </Crumb>
            );
          })}
        </div>

        {/* Toast: SSO error */}
        {ssoError ? (
          <div className="mb-4 rounded-xl border border-rose-500/30 bg-rose-500/10 p-3 text-rose-100 flex items-start justify-between">
            <div className="pr-3">
              <div className="font-medium">Single Sign-On failed</div>
              <div className="text-sm opacity-90">{ssoError}</div>
            </div>
            <button onClick={() => setSsoError(null)} className="rounded-lg border border-rose-500/30 px-2 py-1 text-xs hover:bg-rose-500/20">
              Dismiss
            </button>
          </div>
        ) : null}

        {/* Step content */}
        <div className="mb-8">
          <StepBody stepKey={steps[currentStep].key} />
        </div>

        {/* Validation error display */}
        {Object.values(state.validationErrors.account).some(error => error) || 
         Object.values(state.validationErrors.form).some(error => error) ? (
          <div className="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
            <h3 className="text-rose-200 font-medium mb-2">Please complete the following:</h3>
            <ul className="text-sm text-rose-200 space-y-1">
              {Object.values(state.validationErrors.account).map((error, index) => 
                error ? <li key={`account-${index}`}>• {error}</li> : null
              )}
              {Object.values(state.validationErrors.form).map((error, index) => 
                error ? <li key={`form-${index}`}>• {error}</li> : null
              )}
            </ul>
          </div>
        ) : null}

        {/* Nav Buttons - Hide on success step */}
        {currentStep !== steps.length - 1 && (
          <div className="flex items-center justify-between">
            <button onClick={back} disabled={currentStep === 0} className="rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-white/90 disabled:opacity-40">Back</button>
            <div className="flex items-center gap-3">
              {currentStep < steps.length - 2 ? (
                <button 
                  onClick={handleNext} 
                  disabled={!state.validateCurrentStep()}
                  className="rounded-xl px-6 py-2.5 font-medium shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed bg-indigo-600 shadow-indigo-600/20 hover:bg-indigo-500"
                >
                  Continue
                </button>
              ) : (
                <button 
                  onClick={handleSubmit} 
                  disabled={!state.validateCurrentStep()}
                  className="rounded-xl px-6 py-2.5 font-medium shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 shadow-emerald-600/20 hover:bg-emerald-500"
                >
                  Submit
                </button>
              )}
            </div>
          </div>
        )}

        {/* Footer helper - Hide on success step */}
        {currentStep !== steps.length - 1 && (
          <p className="mt-6 text-center text-xs text-white/80">Autosaving locally and to API. If you leave, your progress will be here when you return.</p>
        )}
      </div>
    </div>
  );
}

// -----------------------------
// Validation Functions
// -----------------------------

const validateEmail = (email: string): string | null => {
  if (!email) return "Email is required";
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) return "Please enter a valid email address";
  return null;
};

const validatePassword = (password: string): string | null => {
  if (!password) return "Password is required";
  if (password.length < 8) return "Password must be at least 8 characters long";
  if (!/(?=.*[a-z])/.test(password)) return "Password must contain at least one lowercase letter";
  if (!/(?=.*[A-Z])/.test(password)) return "Password must contain at least one uppercase letter";
  if (!/(?=.*\d)/.test(password)) return "Password must contain at least one number";
  return null;
};

const validateUrl = (url: string): string | null => {
  if (!url) return "Website URL is required";
  try {
    const urlObj = new URL(url.startsWith('http') ? url : `https://${url}`);
    if (!urlObj.hostname) return "Please enter a valid website URL";
  } catch {
    return "Please enter a valid website URL";
  }
  return null;
};

const validateRequired = (value: string, fieldName: string): string | null => {
  if (!value || value.trim() === "") return `${fieldName} is required`;
  return null;
};

const validateStep = (step: number, account: Account, form: AuditForm): ValidationErrors => {
  const errors: ValidationErrors = { account: {}, form: {} };

  switch (step) {
    case 0: // Account step
      errors.account.firstName = validateRequired(account.firstName, "First name") || undefined;
      errors.account.lastName = validateRequired(account.lastName, "Last name") || undefined;
      errors.account.email = validateEmail(account.email) || undefined;
      errors.account.password = validatePassword(account.password) || undefined;
      break;
    
    case 1: // Business step
      errors.form.companyName = validateRequired(form.companyName, "Company name") || undefined;
      errors.form.website = validateUrl(form.website) || undefined;
      errors.form.industry = validateRequired(form.industry, "Industry/Niche") || undefined;
      break;
    
    case 2: // Goals step
      if (form.goals.length === 0) {
        errors.form.goals = "Please select at least one goal";
      }
      break;
    
    case 3: // Plan step
      if (!form.tier) {
        errors.form.tier = "Please select a tier";
      }
      break;
  }

  return errors;
};
