"use client";
import React, { useEffect, useMemo, useState } from "react";
import { create } from "zustand";

// -----------------------------
// Zustand store (autosave + persistence)
// -----------------------------

type AuditTier = "Starter" | "Growth" | "Pro" | "Enterprise";

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
  reset: () => void;
};

const defaultState: Pick<AuditState, "account" | "form"> = {
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
};

const STORAGE_KEY = "tulsa-seo.audit-wizard.v1";
const AUTH_STORAGE_KEY = "tulsa-seo.auth.v1";

const useAuthStore = create<AuthState>((_set: any) => ({
  token: null,
  userId: null,
  isAuthenticated: false,
}));

const useAuditStore = create<AuditState>((set: any, get: any) => ({
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

async function loginUser(email: string, password: string): Promise<{ token: string; userId: string }> {
  const response = await fetch(`${API_BASE}/api/auth/login`, {
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

async function registerUser(account: Account): Promise<{ token: string; userId: string }> {
  const response = await fetch(`${API_BASE}/api/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      email: account.email,
      password: account.password,
      firstName: account.firstName,
      lastName: account.lastName,
    }),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.error || 'Registration failed');
  }

  const data = await response.json();
  return { token: data.token || 'temp-token', userId: data.id };
}

async function upsertAudit(payload: any): Promise<{ id: string }> {
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
    const response = await fetch(`${API_BASE}/api/v1/audits/intakes/${payload.id}`, {
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
    const response = await fetch(`${API_BASE}/api/v1/audits/intakes`, {
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

const Crumb = ({ active, done, onClick, children, index }: { 
  active: boolean; 
  done: boolean; 
  onClick: () => void; 
  children: React.ReactNode; 
  index: number; 
}) => (
  <button
    onClick={onClick}
    className={`group flex items-center gap-3 px-4 py-2 rounded-xl border transition ${
      active
        ? "border-indigo-500 bg-indigo-500/10 text-indigo-200"
        : done
        ? "border-emerald-600/40 bg-emerald-600/10 text-emerald-200 hover:bg-emerald-600/20"
        : "border-white/10 text-white/60 hover:bg-white/5"
    }`}
  >
    <span className={`flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold ${
      active ? "bg-indigo-500 text-white" : done ? "bg-emerald-600/80 text-white" : "bg-white/10 text-white/70"
    }`}>
      {index + 1}
    </span>
    <span className="text-left">
      <div className="text-xs uppercase tracking-wide opacity-70">Step {index + 1}</div>
      <div className="font-medium">{children}</div>
    </span>
  </button>
);

const Field = ({ label, children }: { label: string; children: React.ReactNode }) => (
  <label className="block space-y-2">
    <span className="text-sm text-white/80">{label}</span>
    {children}
  </label>
);

const Card: React.FC<{ title?: string; right?: React.ReactNode; children: React.ReactNode }>
  = ({ title, right, children }) => (
  <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl">
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
] as const;

type StepKey = typeof steps[number]["key"];

function AccountStep() {
  const { account, updateAccount } = useAuditStore();
  return (
    <Card title="Create your account">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field label="First name">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" value={account.firstName} onChange={(e) => updateAccount({ firstName: e.target.value })} />
        </Field>
        <Field label="Last name">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" value={account.lastName} onChange={(e) => updateAccount({ lastName: e.target.value })} />
        </Field>
        <Field label="Email">
          <input type="email" className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={account.email} onChange={(e) => updateAccount({ email: e.target.value })} />
        </Field>
        <Field label="Password">
          <input type="password" className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={account.password} onChange={(e) => updateAccount({ password: e.target.value })} />
        </Field>
      </div>
      <p className="mt-3 text-sm text-white/60">We&apos;ll create your portal login and connect this audit to your account.</p>
    </Card>
  );
}

function BusinessStep() {
  const { form, updateForm } = useAuditStore();
  return (
    <Card title="Tell us about your business">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field label="Company name">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.companyName} onChange={(e) => updateForm({ companyName: e.target.value })} />
        </Field>
        <Field label="Website URL">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" placeholder="https://" value={form.website} onChange={(e) => updateForm({ website: e.target.value })} />
        </Field>
        <Field label="Industry/Niche">
          <input className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.industry} onChange={(e) => updateForm({ industry: e.target.value })} />
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
  const { form, updateForm } = useAuditStore();
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
            form.goals.includes(g) ? "bg-indigo-600 text-white border-indigo-500" : "bg-white/5 text-white/80 border-white/10 hover:bg-white/10"
          }`}>
            {g}
          </button>
        ))}
      </div>
      <div className="mt-4">
        <Field label="Notes (anything else we should know?)">
          <textarea rows={4} className="w-full rounded-xl border border-white/10 bg-black/40 p-3 text-white" value={form.notes} onChange={(e) => updateForm({ notes: e.target.value })} />
        </Field>
      </div>
    </Card>
  );
}

function PlanStep() {
  const { form, setTier } = useAuditStore();
  const [mode, setMode] = useState<"audit" | "subscription">("audit");
  
  const auditTiers: { key: AuditTier; price: string; features: string[] }[] = [
    { key: "Starter", price: "$199", features: ["One-time audit report", "10 fixes prioritized", "Quick wins list"] },
    { key: "Growth", price: "$499", features: ["Full audit + roadmap", "Rank-tracking setup", "On-page fixes"] },
    { key: "Pro", price: "$999", features: ["Everything in Growth", "Technical crawl & schema", "Content plan (3 mo)"] },
    { key: "Enterprise", price: "Custom", features: ["Multi-location", "Dedicated strategist", "Custom integrations"] },
  ];

  const subscriptionTiers: { key: AuditTier; price: string; features: string[] }[] = [
    { key: "Starter", price: "$299/mo", features: ["Implement audit fixes", "Basic monthly report", "Rank monitoring"] },
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
            className={`rounded-lg px-4 py-2 ${mode === "audit" ? "bg-indigo-600 text-white" : "text-white/80"}`}
            onClick={() => setMode("audit")}
          >
            Audit Pricing
          </button>
          <button
            className={`rounded-lg px-4 py-2 ${mode === "subscription" ? "bg-indigo-600 text-white" : "text-white/80"}`}
            onClick={() => setMode("subscription")}
          >
            Subscription Plans
          </button>
        </div>
      </div>

      {/* Tiers Grid */}
      <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        {tiers.map((t) => (
          <Card key={t.key} title={t.key} right={form.tier === t.key ? <span className="rounded-full bg-emerald-600/20 px-3 py-1 text-xs text-emerald-200">Selected</span> : null}>
            <div className="mb-2 text-2xl font-semibold">{t.price}<span className="text-sm text-white/60 ml-1">{mode === "audit" && t.key !== "Enterprise" ? "/ audit" : ""}</span></div>
            <ul className="mb-4 space-y-1 text-sm text-white/80">
              {t.features.map((f) => (
                <li key={f} className="flex items-start gap-2"><span className="mt-1 h-1.5 w-1.5 rounded-full bg-white/50" />{f}</li>
              ))}
            </ul>
            <button onClick={() => setTier(t.key)} className={`w-full rounded-xl border px-4 py-2 font-medium transition ${
              form.tier === t.key ? "border-emerald-500 bg-emerald-600/20 text-emerald-100" : "border-white/10 bg-white/5 text-white/80 hover:bg-white/10"
            }`}>
              {form.tier === t.key ? "Selected" : "Choose"}
            </button>
          </Card>
        ))}
      </div>
    </div>
  );
}

function ReviewStep() {
  const { account, form, auditId } = useAuditStore();
  
  const getTierPrice = (tier: string) => {
    const auditPrices: Record<string, string> = {
      "Starter": "$199",
      "Growth": "$499", 
      "Pro": "$999",
      "Enterprise": "Custom"
    };
    const subscriptionPrices: Record<string, string> = {
      "Starter": "$299/mo",
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
    default:
      return null;
  }
};

// -----------------------------
// Main Wizard Component
// -----------------------------

export default function AuditWizard() {
  const { currentStep, setStep, next, back, setSaving, markSaved, setError } = useAuditStore();
  const state = useAuditStore();
  const { isAuthenticated } = useAuthStore();

  // Handle URL parameters for pre-selecting tier
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const urlParams = new URLSearchParams(window.location.search);
      const tier = urlParams.get('tier') as AuditTier;
      const isSubscription = window.location.hash === '#subscribe';
      
      if (tier && ['Starter', 'Growth', 'Pro', 'Enterprise'].includes(tier)) {
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
  }, []);

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
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
              token: authResult.token,
              userId: authResult.userId,
              isAuthenticated: true,
            }));
          } catch (loginError) {
            // If login fails, try to register
            try {
              const authResult = await registerUser(state.account);
              useAuthStore.setState({
                token: authResult.token,
                userId: authResult.userId,
                isAuthenticated: true,
              });
              localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
                token: authResult.token,
                userId: authResult.userId,
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
      } catch (e: any) {
        setError(e?.message ?? "Failed to save");
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
            <h1 className="text-2xl font-semibold">SEO Audit Wizard</h1>
            <p className="text-white/70">Create your account, tell us about your business, and pick the audit tier. Autosaves as you type.</p>
          </div>
          <SaveBadge />
        </div>

        {/* Breadcrumb */}
        <div className="mb-8 grid gap-3 md:grid-cols-5">
          {steps.map((s, i) => (
            <Crumb
              key={s.key}
              index={i}
              active={i === currentStep}
              done={i < currentStep}
              onClick={() => setStep(Math.min(i, state.maxStepVisited))}
            >
              {s.label}
            </Crumb>
          ))}
        </div>

        {/* Step content */}
        <div className="mb-8">
          <StepBody stepKey={steps[currentStep].key} />
        </div>

        {/* Nav Buttons */}
        <div className="flex items-center justify-between">
          <button onClick={back} disabled={currentStep === 0} className="rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-white/90 disabled:opacity-40">Back</button>
          <div className="flex items-center gap-3">
            {currentStep < steps.length - 1 ? (
              <button onClick={next} className="rounded-xl bg-indigo-600 px-6 py-2.5 font-medium shadow-lg shadow-indigo-600/20 hover:bg-indigo-500">Continue</button>
            ) : (
              <button className="rounded-xl bg-emerald-600 px-6 py-2.5 font-medium shadow-lg shadow-emerald-600/20 hover:bg-emerald-500">Submit</button>
            )}
          </div>
        </div>

        {/* Footer helper */}
        <p className="mt-6 text-center text-xs text-white/50">Autosaving locally and to API. If you leave, your progress will be here when you return.</p>
      </div>
    </div>
  );
}
