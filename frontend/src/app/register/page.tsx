"use client";

import { useState, useRef, useEffect } from "react";
import Link from "next/link";

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
        const data = await res.json();
        setSuccessMsg("Account created successfully! You can now log in with your credentials.");
        setTimeout(() => (window.location.href = "/login"), 2000);
      }
    } catch {
      setFormError("Network error. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleGoogleSSO = () => {
    // Redirect to Google OAuth endpoint
    window.location.href = "/api/auth/google";
  };

  const handleMicrosoftSSO = () => {
    // Redirect to Microsoft OAuth endpoint
    window.location.href = "/api/auth/microsoft";
  };

  return (
    <div className="min-h-screen bg-[#F5F7FB]">
      {/* Hero / Header */}
      <section className="bg-[#0F1724]">
        <div className="mx-auto max-w-7xl px-6 py-16 text-center">
          <h1 className="text-3xl md:text-5xl font-extrabold text-white">Create your client account</h1>
          <p className="mt-4 text-gray-300 max-w-2xl mx-auto">
            Access performance dashboards, rankings, and monthly reports. It takes less than 2 minutes.
          </p>
          <div className="mt-6 text-sm text-gray-400">
            Already have an account?{" "}
            <Link href="/login" className="text-blue-400 hover:text-blue-300 underline underline-offset-4">
              Log in
            </Link>
          </div>
        </div>
      </section>

      {/* Content */}
      <section className="mx-auto max-w-7xl px-6 py-12 grid lg:grid-cols-5 gap-8">
        {/* Benefits / social proof */}
        <aside className="lg:col-span-2 space-y-6">
          <div className="rounded-2xl bg-white shadow-md ring-1 ring-black/5 p-6">
            <h3 className="text-lg font-semibold text-gray-900">Why join CounselRank.legal?</h3>
            <ul className="mt-4 space-y-3 text-sm text-gray-700">
              <li className="flex gap-3">
                <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-blue-600" />
                Real‑time keyword rankings and local map pack visibility.
              </li>
              <li className="flex gap-3">
                <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-blue-600" />
                Transparent monthly reports and lead attribution.
              </li>
              <li className="flex gap-3">
                <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-blue-600" />
                Secure client portal with role‑based access for your team.
              </li>
            </ul>
            <div className="mt-6">
              <p className="text-xs text-gray-500">Trusted by leading firms</p>
              <div className="mt-2 flex flex-wrap gap-2">
                <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">Google</span>
                <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">Clutch</span>
                <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">Avvo</span>
                <span className="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">BBB</span>
              </div>
            </div>
          </div>

          <div className="rounded-2xl bg-white shadow-md ring-1 ring-black/5 p-6">
            <h3 className="text-lg font-semibold text-gray-900">Security</h3>
            <ul className="mt-3 list-disc list-inside text-sm text-gray-700">
              <li>Encrypted in transit and at rest</li>
              <li>SSO available (Google / Microsoft)</li>
              <li>Role‑based permissions</li>
            </ul>
          </div>
        </aside>

        {/* Form */}
        <div className="lg:col-span-3">
          <div className="rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
            <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
              <h2 className="text-lg font-semibold text-gray-900">Create account</h2>
              <Link href="/login" className="text-sm text-blue-600 hover:text-blue-700">
                Log in instead
              </Link>
            </div>

            {successMsg && (
              <div className="mx-6 mt-4 rounded-xl border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                {successMsg}
              </div>
            )}
            {formError && (
              <div className="mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {formError}
              </div>
            )}

            <form onSubmit={submit} className="px-6 py-6 space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="text-sm font-medium text-gray-800" htmlFor="firm">Firm name</label>
                  <input
                    ref={firstRef}
                    id="firm"
                    value={firm}
                    onChange={(e) => setFirm(e.target.value)}
                    placeholder="McNary & Associates"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${
                      fieldErrors.firm ? "border-red-400 focus:ring-red-300" : "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30"
                    }`}
                  />
                  {fieldErrors.firm && <p className="text-xs text-red-600">{fieldErrors.firm}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-sm font-medium text-gray-800" htmlFor="name">Your name</label>
                  <input
                    id="name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Jane Doe"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${
                      fieldErrors.name ? "border-red-400 focus:ring-red-300" : "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30"
                    }`}
                  />
                  {fieldErrors.name && <p className="text-xs text-red-600">{fieldErrors.name}</p>}
                </div>
              </div>

              <div className="space-y-1.5">
                <label className="text-sm font-medium text-gray-800" htmlFor="email">Work email</label>
                <input
                  id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)}
                  placeholder="you@lawfirm.com"
                  className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${
                    fieldErrors.email ? "border-red-400 focus:ring-red-300" : "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30"
                  }`}
                />
                {fieldErrors.email && <p className="text-xs text-red-600">{fieldErrors.email}</p>}
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <div className="flex items-center justify-between">
                    <label className="text-sm font-medium text-gray-800" htmlFor="password">Password</label>
                    <span className="text-xs text-gray-500">8+ chars, mix of cases &amp; symbol</span>
                  </div>
                  <input
                    id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${
                      fieldErrors.password ? "border-red-400 focus:ring-red-300" : "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30"
                    }`}
                  />
                  {/* Strength meter */}
                  <div className="mt-1 h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                    <div
                      className={[
                        "h-full transition-all",
                        strength >= 1 ? "w-1/4" : "w-0",
                        strength >= 2 ? "w-2/4" : "",
                        strength >= 3 ? "w-3/4" : "",
                        strength >= 4 ? "w-full" : "",
                        strength <= 1 ? "bg-red-500" : strength === 2 ? "bg-yellow-500" : strength === 3 ? "bg-blue-500" : "bg-green-500",
                      ].join(" ")}
                    />
                  </div>
                  {fieldErrors.password && <p className="text-xs text-red-600">{fieldErrors.password}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-sm font-medium text-gray-800" htmlFor="confirm">Confirm password</label>
                  <input
                    id="confirm" type="password" value={confirm} onChange={(e) => setConfirm(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${
                      fieldErrors.confirm ? "border-red-400 focus:ring-red-300" : "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30"
                    }`}
                  />
                  {fieldErrors.confirm && <p className="text-xs text-red-600">{fieldErrors.confirm}</p>}
                </div>
              </div>

              <div className="flex items-start gap-3">
                <input
                  id="agree" type="checkbox" checked={agree} onChange={(e) => setAgree(e.target.checked)}
                  className="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                />
                <label htmlFor="agree" className="text-sm text-gray-700">
                  I agree to the <Link className="text-blue-600 hover:text-blue-700" href="/terms">Terms</Link> and{" "}
                  <Link className="text-blue-600 hover:text-blue-700" href="/privacy">Privacy Policy</Link>.
                </label>
              </div>
              {fieldErrors.agree && <p className="text-xs text-red-600">{fieldErrors.agree}</p>}

              <label className="flex items-center gap-2 text-sm text-gray-700">
                <input
                  type="checkbox" checked={marketing} onChange={(e) => setMarketing(e.target.checked)}
                  className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                />
                Send me product updates (optional)
              </label>

              <button
                type="submit"
                disabled={loading}
                className="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-white font-medium shadow-sm hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {loading ? "Creating account…" : "Create Account"}
              </button>

              {/* SSO Buttons */}
              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-gray-300" />
                </div>
                <div className="relative flex justify-center text-sm">
                  <span className="bg-white px-2 text-gray-500">Or continue with</span>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={handleGoogleSSO}
                  className="flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
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
                  className="flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
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

              <div className="flex items-center justify-center gap-2 text-sm text-gray-500">
                <span>Already have an account?</span>
                <Link href="/login" className="text-blue-600 hover:text-blue-700">Log in</Link>
              </div>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}
