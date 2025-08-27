"use client";

import { useState, useRef, useEffect } from "react";
import Link from "next/link";
import { RegistrationLoadingModal } from "../../components/LoadingModal";

const pwScore = (v: string) => {
  let s = 0;
  if (v.length >= 8) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[a-z]/.test(v)) s++;
  if (/\d/.test(v)) s++;
  if (/[^\w]/.test(v)) s++;
  return Math.min(s, 4);
};

export default function RegisterPage() {
  const firstRef = useRef<HTMLInputElement>(null);

  const [firm, setFirm] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [agree, setAgree] = useState(true);
  const [marketing, setMarketing] = useState(true);

  const [loading, setLoading] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
  const [successMsg, setSuccessMsg] = useState<string | null>(null);

  const strength = pwScore(password);

  // read ?tier= to preserve flow after registration
  const search = typeof window !== "undefined" ? new URLSearchParams(window.location.search) : null;
  const tierParam = search?.get("tier");

  useEffect(() => {
    firstRef.current?.focus();
  }, []);

  const validate = () => {
    const fe: Record<string, string> = {};
    if (!firm) fe.firm = "Firm name is required.";
    if (!name) fe.name = "Your name is required.";
    if (!email) fe.email = "Email is required.";
    else if (!/^\S+@\S+\.\S+$/.test(email)) fe.email = "Enter a valid email.";
    if (!password) fe.password = "Password is required.";
    else if (password.length < 8) fe.password = "Use at least 8 characters.";
    if (confirm !== password) fe.confirm = "Passwords do not match.";
    if (!agree) fe.agree = "Please accept the Terms & Privacy.";
    setFieldErrors(fe);
    return Object.keys(fe).length === 0;
  };

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSuccessMsg(null);
    if (!validate()) return;

    setLoading(true);
    setFormError(null);

    try {
      // Map the form fields to match the backend API expectations
      const registrationData = {
        organization_name: firm,
        client_name: firm,
        admin_email: email,
        admin_password: password,
        admin_first_name: name.split(' ')[0] || '',
        admin_last_name: name.split(' ').slice(1).join(' ') || '',
        // Optional fields - only include if they have values
        client_country: 'USA',
        client_industry: 'law'
      };

      const res = await fetch("http://localhost:8000/api/v1/clients/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(registrationData),
      });

      if (!res.ok) {
        let msg = "Registration failed. Please try again.";
        try { 
          const j = await res.json(); 
          if (j?.error) msg = j.error; 
        } catch {
          // Ignore JSON parsing errors
        }
        setFormError(msg);
      } else {
        await res.json(); // Just consume the response
        setSuccessMsg("Account created successfully! Logging you in...");
        
        // Automatically log the user in after successful registration
        try {
          const loginResponse = await fetch("http://localhost:8000/api/v1/auth/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password }),
          });

          if (loginResponse.ok) {
            const loginData = await loginResponse.json();
            
            // Store the authentication token
            if (typeof window !== 'undefined') {
              localStorage.setItem('auth_token', loginData.token);
              if (loginData.user) {
                localStorage.setItem('userData', JSON.stringify(loginData.user));
              }
            }
            
            // Redirect to the client dashboard (preserve tier if available)
            const redirectUrl = tierParam ? `/client?tier=${encodeURIComponent(tierParam)}` : "/client";
            setTimeout(() => {
              window.location.href = redirectUrl;
            }, 1000);
          } else {
            // If auto-login fails, redirect to login page
            setSuccessMsg("Account created successfully! You can now log in with your credentials.");
            setTimeout(() => {
              const loginUrl = tierParam ? `/login?tier=${encodeURIComponent(tierParam)}` : "/login";
              window.location.href = loginUrl;
            }, 2000);
          }
        } catch (error) {
          console.error('Auto-login failed:', error);
          // If auto-login fails, redirect to login page
          setSuccessMsg("Account created successfully! You can now log in with your credentials.");
          setTimeout(() => {
            const loginUrl = tierParam ? `/login?tier=${encodeURIComponent(tierParam)}` : "/login";
            window.location.href = loginUrl;
          }, 2000);
        }
      }
    } catch {
      setFormError("Network error. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleGoogleSSO = () => {
    const qp = new URLSearchParams();
    if (tierParam) qp.set("tier", tierParam);
    window.location.href = `/api/auth/google?${qp.toString()}`;
  };

  const handleMicrosoftSSO = () => {
    const qp = new URLSearchParams();
    if (tierParam) qp.set("tier", tierParam);
    window.location.href = `/api/auth/microsoft?${qp.toString()}`;
  };

  return (
    <>
      {/* Loading Modal */}
      <RegistrationLoadingModal isOpen={loading} />
      
      <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white">
        {/* Header / intro matches hero */}
        <section className="relative">
          <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_70%_10%,rgba(99,102,241,0.12),transparent_60%),radial-gradient(50%_50%_at_20%_20%,rgba(16,185,129,0.10),transparent_60%)]" />
          <div className="mx-auto max-w-7xl px-6 py-16 text-center">
            <h1 className="text-3xl md:text-5xl font-semibold">Create your client account</h1>
            <p className="mt-4 text-white/70 max-w-2xl mx-auto">
              Access performance dashboards, rankings, and monthly reports. It takes less than 2 minutes.
            </p>
            <div className="mt-6 text-sm text-white/70">
              Already have an account?{" "}
              <Link href={`/login${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-indigo-300 hover:text-indigo-200 underline underline-offset-4">
                Log in
              </Link>
            </div>
          </div>
        </section>

        {/* Content */}
        <section className="mx-auto max-w-7xl px-6 pb-16 grid lg:grid-cols-5 gap-8">
          {/* Benefits / social proof */}
          <aside className="lg:col-span-2 space-y-6">
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl">
              <h3 className="text-lg font-semibold">Why join tulsa-seo.com?</h3>
              <ul className="mt-4 space-y-3 text-sm text-white/80">
                <li className="flex gap-3">
                  <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />
                  Real‑time keyword rankings and local map pack visibility.
                </li>
                <li className="flex gap-3">
                  <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />
                  Transparent monthly reports and lead attribution.
                </li>
                <li className="flex gap-3">
                  <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />
                  Secure client portal with role‑based access for your team.
                </li>
              </ul>
              <div className="mt-6">
                <p className="text-xs text-white/60">Trusted by leading firms</p>
                <div className="mt-2 flex flex-wrap gap-2">
                  <span className="px-3 py-1 rounded-full bg-white/10 text-white/80 text-xs border border-white/20">Google</span>
                  <span className="px-3 py-1 rounded-full bg-white/10 text-white/80 text-xs border border-white/20">Clutch</span>
                  <span className="px-3 py-1 rounded-full bg-white/10 text-white/80 text-xs border border-white/20">Avvo</span>
                  <span className="px-3 py-1 rounded-full bg-white/10 text-white/80 text-xs border border-white/20">BBB</span>
                </div>
              </div>
            </div>

            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl">
              <h3 className="text-lg font-semibold">Security</h3>
              <ul className="mt-3 list-disc list-inside text-sm text-white/80">
                <li>Encrypted in transit and at rest</li>
                <li>SSO available (Google / Microsoft)</li>
                <li>Role‑based permissions</li>
              </ul>
            </div>
          </aside>

          {/* Form */}
          <div className="lg:col-span-3">
            <div className="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-2xl">
              <div className="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <h2 className="text-lg font-semibold">Create account</h2>
                <Link href={`/login${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-sm text-indigo-300 hover:text-indigo-200">
                  Log in instead
                </Link>
              </div>

            {successMsg && (
              <div className="mx-6 mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-200">
                {successMsg}
              </div>
            )}
            {formError && (
              <div className="mx-6 mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                {formError}
              </div>
            )}

            <form onSubmit={submit} className="px-6 py-6 space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="text-sm text-white/80" htmlFor="firm">Firm name</label>
                  <input
                    ref={firstRef}
                    id="firm"
                    value={firm}
                    onChange={(e) => setFirm(e.target.value)}
                    placeholder="McNary & Associates"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.firm ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {fieldErrors.firm && <p className="text-xs text-rose-300">{fieldErrors.firm}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-sm text-white/80" htmlFor="name">Your name</label>
                  <input
                    id="name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Jane Doe"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.name ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {fieldErrors.name && <p className="text-xs text-rose-300">{fieldErrors.name}</p>}
                </div>
              </div>

              <div className="space-y-1.5">
                <label className="text-sm text-white/80" htmlFor="email">Work email</label>
                <input
                  id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)}
                  placeholder="you@lawfirm.com"
                  className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                    fieldErrors.email ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                  }`}
                />
                {fieldErrors.email && <p className="text-xs text-rose-300">{fieldErrors.email}</p>}
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <div className="flex items-center justify-between">
                    <label className="text-sm text-white/80" htmlFor="password">Password</label>
                    <span className="text-xs text-white/60">8+ chars, mix of cases &amp; symbol</span>
                  </div>
                  <input
                    id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.password ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {/* Strength meter */}
                  <div className="mt-1 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div
                      className={[
                        "h-full transition-all",
                        strength >= 1 ? "w-1/4" : "w-0",
                        strength >= 2 ? "w-2/4" : "",
                        strength >= 3 ? "w-3/4" : "",
                        strength >= 4 ? "w-full" : "",
                        strength <= 1 ? "bg-rose-500" : strength === 2 ? "bg-yellow-500" : strength === 3 ? "bg-indigo-500" : "bg-emerald-500",
                      ].join(" ")}
                    />
                  </div>
                  {fieldErrors.password && <p className="text-xs text-rose-300">{fieldErrors.password}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-sm text-white/80" htmlFor="confirm">Confirm password</label>
                  <input
                    id="confirm" type="password" value={confirm} onChange={(e) => setConfirm(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.confirm ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {fieldErrors.confirm && <p className="text-xs text-rose-300">{fieldErrors.confirm}</p>}
                </div>
              </div>

              <div className="flex items-start gap-3">
                <input
                  id="agree" type="checkbox" checked={agree} onChange={(e) => setAgree(e.target.checked)}
                  className="mt-1 h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-600 focus:ring-indigo-600"
                />
                <label htmlFor="agree" className="text-sm text-white/80">
                  I agree to the <Link className="text-indigo-300 hover:text-indigo-200" href="/terms">Terms</Link> and{" "}
                  <Link className="text-indigo-300 hover:text-indigo-200" href="/privacy">Privacy Policy</Link>.
                </label>
              </div>
              {fieldErrors.agree && <p className="text-xs text-rose-300">{fieldErrors.agree}</p>}

              <label className="flex items-center gap-2 text-sm text-white/80">
                <input
                  type="checkbox" checked={marketing} onChange={(e) => setMarketing(e.target.checked)}
                  className="h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-600 focus:ring-indigo-600"
                />
                Send me product updates (optional)
              </label>

              <button
                type="submit"
                disabled={loading}
                className="w-full rounded-xl bg-indigo-600 px-4 py-2.5 font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {loading ? "Creating account…" : "Create Account"}
              </button>

              {/* SSO Buttons */}
              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-white/10" />
                </div>
                <div className="relative flex justify-center text-sm">
                  <span className="bg-transparent px-2 text-white/60">Or continue with</span>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={handleGoogleSSO}
                  className="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-600/40"
                >
                  <svg className="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                  </svg>
                  Google
                </button>

                <button
                  type="button"
                  onClick={handleMicrosoftSSO}
                  className="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-600/40"
                >
                  <svg className="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#F25022" d="M1 1h10v10H1z"/>
                    <path fill="#7FBA00" d="M13 1h10v10H13z"/>
                    <path fill="#00A4EF" d="M1 13h10v10H1z"/>
                    <path fill="#FFB900" d="M13 13h10v10H13z"/>
                  </svg>
                  Microsoft
                </button>
              </div>

              <div className="flex items-center justify-center gap-2 text-sm text-white/70">
                <span>Already have an account?</span>
                <Link href={`/login${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-indigo-300 hover:text-indigo-200">Log in</Link>
              </div>
            </form>
          </div>
        </div>
      </section>
    </div>
    </>
  );
}
