import { useEffect, useRef, useState } from "react";

type Props = {
  open: boolean;
  onClose: () => void;
  onSuccess?: (user?: any) => void; // optional user payload if your API returns one
};

type ApiError = { code?: string; message: string };

export default function ClientLoginModal({ open, onClose, onSuccess }: Props) {
  const firstFieldRef = useRef<HTMLInputElement>(null);

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [remember, setRemember] = useState(true);

  const [loading, setLoading] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<{ email?: string; password?: string }>({});
  const [formError, setFormError] = useState<string | null>(null);

  // ESC to close + autofocus
  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => e.key === "Escape" && onClose();
    document.addEventListener("keydown", onKey);
    const t = setTimeout(() => firstFieldRef.current?.focus(), 50);
    return () => {
      document.removeEventListener("keydown", onKey);
      clearTimeout(t);
    };
  }, [open, onClose]);

  if (!open) return null;

  const validate = () => {
    const errs: typeof fieldErrors = {};
    if (!email) errs.email = "Email is required.";
    else if (!/^\S+@\S+\.\S+$/.test(email)) errs.email = "Enter a valid email.";
    if (!password) errs.password = "Password is required.";
    setFieldErrors(errs);
    return Object.keys(errs).length === 0;
  };

  const login = async () => {
    setLoading(true);
    setFormError(null);

    try {
      const res = await fetch("/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include", // needed for cookie-based sessions
        body: JSON.stringify({ email, password, remember }),
      });

      // Cookie-session success (204/201/200 without token)
      if (res.ok && res.status !== 200) {
        onSuccess?.();
        onClose();
        return;
      }

      // JWT success (200 with access_token)
      if (res.ok && res.headers.get("content-type")?.includes("application/json")) {
        const json = await res.json();
        if (json?.access_token) {
          // Prefer httpOnly cookie; but if API returns token, store minimally
          // Better: exchange token for cookie on a secure endpoint
          sessionStorage.setItem("access_token", json.access_token);
        }
        onSuccess?.(json?.user);
        onClose();
        return;
      }

      // Error path
      let errMsg = "Unable to sign in. Please check your credentials.";
      try {
        const err: ApiError = await res.json();
        if (err?.message) errMsg = err.message;
      } catch {
        // Ignore JSON parsing errors, use default message
      }
      if (res.status === 401) errMsg = "Incorrect email or password.";
      if (res.status === 429) errMsg = "Too many attempts. Please try again later.";
      setFormError(errMsg);
    } catch (e) {
      setFormError("Network error. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) return;
    await login();
  };

  return (
    <div className="fixed inset-0 z-[60] animate-fade-in" role="dialog" aria-modal="true" aria-labelledby="login-title">
      {/* Overlay */}
      <button className="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-200" onClick={onClose} aria-label="Close" />

      {/* Panel */}
      <div className="absolute inset-0 flex items-center justify-center px-4">
        <div className="w-full max-w-md rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 animate-slide-up">
          <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 id="login-title" className="text-xl font-bold text-gray-900">Client Login</h2>
            <button
              onClick={onClose}
              className="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 transition-all duration-200"
              aria-label="Close"
            >
              <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l12 12M18 6L6 18" />
              </svg>
            </button>
          </div>

          <form onSubmit={onSubmit} className="px-6 py-6 space-y-5">
            {formError && (
              <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 animate-fade-in">
                {formError}
              </div>
            )}

            <div className="space-y-2">
              <label htmlFor="email" className="text-sm font-semibold text-gray-800">Email</label>
              <input
                ref={firstFieldRef}
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="you@lawfirm.com"
                className={`input-field ${fieldErrors.email ? "border-red-400 focus:ring-red-500/20 focus:border-red-500" : ""}`}
              />
              {fieldErrors.email && <p className="text-xs text-red-600 animate-fade-in">{fieldErrors.email}</p>}
            </div>

            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <label htmlFor="password" className="text-sm font-semibold text-gray-800">Password</label>
                <a href="/forgot-password" className="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200">Forgot?</a>
              </div>
              <input
                id="password"
                name="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className={`input-field ${fieldErrors.password ? "border-red-400 focus:ring-red-500/20 focus:border-red-500" : ""}`}
              />
              {fieldErrors.password && <p className="text-xs text-red-600 animate-fade-in">{fieldErrors.password}</p>}
            </div>

            <div className="flex items-center justify-between">
              <label className="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input
                  type="checkbox"
                  checked={remember}
                  onChange={(e) => setRemember(e.target.checked)}
                  className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600/20 focus:ring-2 transition-all duration-200"
                />
                Remember me
              </label>
              <a href="/register" className="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors duration-200">Need an account?</a>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none"
            >
              {loading ? (
                <span className="inline-flex items-center justify-center gap-2">
                  <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4A4 4 0 008 12H4z"></path>
                  </svg>
                  Logging in…
                </span>
              ) : (
                "Log In"
              )}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
