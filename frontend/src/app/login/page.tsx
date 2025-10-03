"use client";

import { useState, useRef, useEffect } from "react";
import Link from "next/link";
import { AuthLoadingModal } from "../../components/common/LoadingModal";

export default function LoginPage() {
  const firstRef = useRef<HTMLInputElement>(null);

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [remember, setRemember] = useState(true);

  const [loading, setLoading] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<{ email?: string; password?: string }>({});
  const [formError, setFormError] = useState<string | null>(null);

  // read ?next= and ?tier= to preserve flow after login
  const search = typeof window !== "undefined" ? new URLSearchParams(window.location.search) : null;
  const redirectNext = search?.get("next") || "/client";
  const tierParam = search?.get("tier");

  useEffect(() => {
    firstRef.current?.focus();
  }, []);

  const validate = () => {
    const fe: { email?: string; password?: string } = {};
    if (!email) fe.email = "Email is required.";
    else if (!/^\S+@\S+\.\S+$/.test(email)) fe.email = "Enter a valid email.";
    if (!password) fe.password = "Password is required.";
    setFieldErrors(fe);
    return Object.keys(fe).length === 0;
  };

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) return;

    setLoading(true);
    setFormError(null);

    try {
      const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';
      const res = await fetch(`${API_BASE_URL}/api/v1/auth/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      if (!res.ok) {
        let msg = "Login failed. Please check your credentials.";
        try {
          const j = await res.json();
          if (j?.detail) msg = j.detail;
        } catch {
          /* ignore */
        }
        setFormError(msg);
      } else {
        const data = await res.json();
        
        // Store the JWT token
        if (data.access_token) {
          localStorage.setItem('auth_token', data.access_token);
        }
        
        // Redirect to next (preserve tier if heading back to audit)
        const url = new URL(redirectNext, window.location.origin);
        if (tierParam && (redirectNext.startsWith("/services/audit") || redirectNext === "/client")) {
          url.searchParams.set("tier", tierParam);
        }
        window.location.href = url.toString();
      }
    } catch {
      setFormError("Network error. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleGoogleSSO = () => {
    const qp = new URLSearchParams();
    if (redirectNext) qp.set("next", redirectNext);
    if (tierParam) qp.set("tier", tierParam);
    window.location.href = `/api/auth/google?${qp.toString()}`;
  };

  const handleMicrosoftSSO = () => {
    const qp = new URLSearchParams();
    if (redirectNext) qp.set("next", redirectNext);
    if (tierParam) qp.set("tier", tierParam);
    window.location.href = `/api/auth/microsoft?${qp.toString()}`;
  };

  return (
    <>
      {/* Loading Modal */}
      <AuthLoadingModal isOpen={loading} action="signing in" />

      <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white">
        {/* Header / intro matches hero */}
        <section className="relative">
          <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_70%_10%,rgba(99,102,241,0.12),transparent_60%),radial-gradient(50%_50%_at_20%_20%,rgba(16,185,129,0.10),transparent_60%)]" />
          <div className="mx-auto max-w-7xl px-6 py-16 text-center">
            <h1 className="text-3xl md:text-5xl font-semibold">Welcome back</h1>
            <p className="mt-4 text-white/70 max-w-2xl mx-auto">
              Access your performance dashboards, rankings, and monthly reports.
            </p>
            <div className="mt-6 text-sm text-white/70">
              Don&apos;t have an account?{" "}
              <Link href={`/register${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-indigo-300 hover:text-indigo-200 underline underline-offset-4">
                Create one
              </Link>
            </div>
          </div>
        </section>

        {/* Content */}
        <section className="mx-auto max-w-7xl px-6 pb-16 grid lg:grid-cols-5 gap-8">
          {/* Benefits / info */}
          <aside className="lg:col-span-2 space-y-6">
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl">
              <h3 className="text-lg font-semibold">Your portal includes:</h3>
              <ul className="mt-4 space-y-3 text-sm text-white/80">
                <li className="flex gap-3"><span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />Real-time keyword rankings and performance metrics</li>
                <li className="flex gap-3"><span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />Monthly reports with lead attribution data</li>
                <li className="flex gap-3"><span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />Team collaboration tools and role-based access</li>
                <li className="flex gap-3"><span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-indigo-500" />Custom alerts and performance notifications</li>
              </ul>
            </div>

            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl">
              <h3 className="text-lg font-semibold">Need help?</h3>
              <p className="mt-2 text-sm text-white/80">Contact your account manager or reach out to our support team.</p>
              <div className="mt-4 space-y-2 text-sm text-white/70">
                <p><span className="font-medium text-white">Email:</span> support@tulsa-seo.com</p>
                <p><span className="font-medium text-white">Phone:</span> (555) 123-4567</p>
              </div>
            </div>
          </aside>

          {/* Form */}
          <div className="lg:col-span-3">
            <div className="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-2xl">
              <div className="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <h2 className="text-lg font-semibold">Sign in to your account</h2>
                <Link href={`/register${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-sm text-indigo-300 hover:text-indigo-200">
                  Create account
                </Link>
              </div>

              {formError && (
                <div className="mx-6 mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                  {formError}
                </div>
              )}

              <form onSubmit={submit} className="px-6 py-6 space-y-4">
                <div className="space-y-1.5">
                  <label className="text-sm text-white/80" htmlFor="email">Email address</label>
                  <input
                    ref={firstRef}
                    id="email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="you@lawfirm.com"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.email ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {fieldErrors.email && <p className="text-xs text-rose-300">{fieldErrors.email}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="text-sm text-white/80" htmlFor="password">Password</label>
                  <input
                    id="password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border bg-black/40 px-3.5 py-2.5 text-[15px] text-white placeholder:text-white/40 focus:outline-none focus:ring-2 ${
                      fieldErrors.password ? "border-rose-500/60 focus:ring-rose-400/40" : "border-white/10 focus:ring-indigo-500/40"
                    }`}
                  />
                  {fieldErrors.password && <p className="text-xs text-rose-300">{fieldErrors.password}</p>}
                </div>

                <div className="flex items-center justify-between">
                  <label className="flex items-center gap-2 text-sm text-white/80">
                    <input
                      id="remember"
                      type="checkbox"
                      checked={remember}
                      onChange={(e) => setRemember(e.target.checked)}
                      className="h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-600 focus:ring-indigo-600"
                    />
                    Remember me
                  </label>
                  <Link href="/forgot-password" className="text-sm text-indigo-300 hover:text-indigo-200">
                    Forgot password?
                  </Link>
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full rounded-xl bg-indigo-600 px-4 py-2.5 font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  {loading ? "Signing in…" : "Sign in"}
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
                  <span>Don&apos;t have an account?</span>
                  <Link href={`/register${tierParam ? `?tier=${encodeURIComponent(tierParam)}` : ""}`} className="text-indigo-300 hover:text-indigo-200">Create one</Link>
                </div>
              </form>
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
