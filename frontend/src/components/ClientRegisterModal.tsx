"use client";

import { useEffect, useRef, useState } from "react";
import { registerClient } from "../lib/api";
import { RegistrationLoadingModal } from "./LoadingModal";

const scorePassword = (v: string) => {
  let s = 0;
  if (v.length >= 8) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[a-z]/.test(v)) s++;
  if (/\d/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  return Math.min(s, 4); // 0..4 visual steps
};

export default function CreateAccountSection() {
  const firstRef = useRef<HTMLInputElement>(null);

  const [organizationName, setOrganizationName] = useState("");
  const [organizationDomain, setOrganizationDomain] = useState("");
  const [clientName, setClientName] = useState("");
  const [clientWebsite, setClientWebsite] = useState("");
  const [clientPhone, setClientPhone] = useState("");
  const [clientAddress, setClientAddress] = useState("");
  const [clientCity, setClientCity] = useState("");
  const [clientState, setClientState] = useState("");
  const [clientZipCode, setClientZipCode] = useState("");
  const [adminEmail, setAdminEmail] = useState("");
  const [adminPassword, setAdminPassword] = useState("");
  const [adminFirstName, setAdminFirstName] = useState("");
  const [adminLastName, setAdminLastName] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [agree, setAgree] = useState(true);
  const [marketing, setMarketing] = useState(true);

  const [formError, setFormError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);
  const [successMsg, setSuccessMsg] = useState<string | null>(null);

  useEffect(() => firstRef.current?.focus(), []);

  const validate = () => {
    const fe: Record<string, string> = {};
    if (!organizationName) fe.organizationName = "Organization name is required.";
    if (!clientName) fe.clientName = "Client name is required.";
    if (!adminEmail) fe.adminEmail = "Email is required.";
    else if (!/^\S+@\S+\.\S+$/.test(adminEmail)) fe.adminEmail = "Enter a valid email.";
    if (!adminPassword) fe.adminPassword = "Password is required.";
    else if (adminPassword.length < 8) fe.adminPassword = "Use at least 8 characters.";
    if (confirmPassword !== adminPassword) fe.confirmPassword = "Passwords do not match.";
    if (organizationDomain && !/^https?:\/\/.+/.test(organizationDomain)) fe.organizationDomain = "Enter a valid URL (include http:// or https://).";
    if (clientWebsite && !/^https?:\/\/.+/.test(clientWebsite)) fe.clientWebsite = "Enter a valid URL (include http:// or https://).";
    if (!agree) fe.agree = "Please accept the Terms & Privacy.";
    setFieldErrors(fe);
    return Object.keys(fe).length === 0;
  };

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSuccessMsg(null);
    setFormError(null);
    if (!validate()) return;

    setSubmitting(true);
    try {
      const registrationData = {
        organization_name: organizationName,
        organization_domain: organizationDomain || undefined,
        client_name: clientName,
        client_website: clientWebsite || undefined,
        client_phone: clientPhone || undefined,
        client_address: clientAddress || undefined,
        client_city: clientCity || undefined,
        client_state: clientState || undefined,
        client_zip_code: clientZipCode || undefined,
        admin_email: adminEmail,
        admin_password: adminPassword,
        admin_first_name: adminFirstName || undefined,
        admin_last_name: adminLastName || undefined,
      };

      await registerClient(registrationData);
      
      setSuccessMsg("Account created successfully! You can now log in with your credentials.");
      
      // Optionally auto-redirect to login or dashboard
      setTimeout(() => {
        // You might want to trigger a login modal here or redirect
        window.location.href = "/login";
      }, 2000);
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : "Registration failed. Please try again.";
      setFormError(errorMessage);
    } finally {
      setSubmitting(false);
    }
  };

  const strength = scorePassword(adminPassword);
  const strengthLabel = ["Weak", "Fair", "Good", "Strong"][Math.max(0, strength - 1)] ?? "";

  // helpers for input class states
  const ok = "border-gray-300 focus:border-blue-600 focus:ring-blue-600/30";
  const bad = "border-red-400 focus:ring-red-300";

  return (
    <>
      {/* Loading Modal */}
      <RegistrationLoadingModal isOpen={submitting} />
      
      <section className="bg-[#F5F7FB] py-16">
        <div className="max-w-3xl mx-auto px-6">
          <div className="rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
            {/* Header */}
            <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
              <h2 className="text-lg font-semibold text-gray-900">Create account</h2>
              <a href="/login" className="text-sm text-blue-600 hover:text-blue-700">Log in</a>
            </div>

          {/* Alerts */}
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

          {/* Form */}
          <form onSubmit={onSubmit} className="px-6 py-6 space-y-4">
            {/* Organization Info */}
            <div className="space-y-3">
              <h3 className="text-sm font-semibold text-gray-900">Organization Information</h3>
              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label htmlFor="organizationName" className="text-sm font-medium text-gray-800">Organization name *</label>
                  <input
                    ref={firstRef}
                    id="organizationName"
                    value={organizationName}
                    onChange={(e) => setOrganizationName(e.target.value)}
                    placeholder="McNary & Associates"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.organizationName ? bad : ok}`}
                    aria-invalid={!!fieldErrors.organizationName}
                    aria-describedby={fieldErrors.organizationName ? "organizationName-err" : undefined}
                  />
                  {fieldErrors.organizationName && <p id="organizationName-err" className="text-xs text-red-600">{fieldErrors.organizationName}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="organizationDomain" className="text-sm font-medium text-gray-800">Organization website</label>
                  <input
                    id="organizationDomain"
                    value={organizationDomain}
                    onChange={(e) => setOrganizationDomain(e.target.value)}
                    placeholder="https://mcnarylaw.com"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.organizationDomain ? bad : ok}`}
                    aria-invalid={!!fieldErrors.organizationDomain}
                    aria-describedby={fieldErrors.organizationDomain ? "organizationDomain-err" : undefined}
                  />
                  {fieldErrors.organizationDomain && <p id="organizationDomain-err" className="text-xs text-red-600">{fieldErrors.organizationDomain}</p>}
                </div>
              </div>
            </div>

            {/* Client Info */}
            <div className="space-y-3">
              <h3 className="text-sm font-semibold text-gray-900">Client Information</h3>
              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label htmlFor="clientName" className="text-sm font-medium text-gray-800">Client name *</label>
                  <input
                    id="clientName"
                    value={clientName}
                    onChange={(e) => setClientName(e.target.value)}
                    placeholder="McNary SEO Services"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientName ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientName}
                    aria-describedby={fieldErrors.clientName ? "clientName-err" : undefined}
                  />
                  {fieldErrors.clientName && <p id="clientName-err" className="text-xs text-red-600">{fieldErrors.clientName}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="clientWebsite" className="text-sm font-medium text-gray-800">Client website</label>
                  <input
                    id="clientWebsite"
                    value={clientWebsite}
                    onChange={(e) => setClientWebsite(e.target.value)}
                    placeholder="https://mcnarylaw.com"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientWebsite ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientWebsite}
                    aria-describedby={fieldErrors.clientWebsite ? "clientWebsite-err" : undefined}
                  />
                  {fieldErrors.clientWebsite && <p id="clientWebsite-err" className="text-xs text-red-600">{fieldErrors.clientWebsite}</p>}
                </div>
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label htmlFor="clientPhone" className="text-sm font-medium text-gray-800">Phone number</label>
                  <input
                    id="clientPhone"
                    value={clientPhone}
                    onChange={(e) => setClientPhone(e.target.value)}
                    placeholder="+1 (555) 123-4567"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientPhone ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientPhone}
                    aria-describedby={fieldErrors.clientPhone ? "clientPhone-err" : undefined}
                  />
                  {fieldErrors.clientPhone && <p id="clientPhone-err" className="text-xs text-red-600">{fieldErrors.clientPhone}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="clientAddress" className="text-sm font-medium text-gray-800">Address</label>
                  <input
                    id="clientAddress"
                    value={clientAddress}
                    onChange={(e) => setClientAddress(e.target.value)}
                    placeholder="123 Main Street"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientAddress ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientAddress}
                    aria-describedby={fieldErrors.clientAddress ? "clientAddress-err" : undefined}
                  />
                  {fieldErrors.clientAddress && <p id="clientAddress-err" className="text-xs text-red-600">{fieldErrors.clientAddress}</p>}
                </div>
              </div>

              <div className="grid md:grid-cols-3 gap-4">
                <div className="space-y-1.5">
                  <label htmlFor="clientCity" className="text-sm font-medium text-gray-800">City</label>
                  <input
                    id="clientCity"
                    value={clientCity}
                    onChange={(e) => setClientCity(e.target.value)}
                    placeholder="New York"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientCity ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientCity}
                    aria-describedby={fieldErrors.clientCity ? "clientCity-err" : undefined}
                  />
                  {fieldErrors.clientCity && <p id="clientCity-err" className="text-xs text-red-600">{fieldErrors.clientCity}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="clientState" className="text-sm font-medium text-gray-800">State</label>
                  <input
                    id="clientState"
                    value={clientState}
                    onChange={(e) => setClientState(e.target.value)}
                    placeholder="NY"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientState ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientState}
                    aria-describedby={fieldErrors.clientState ? "clientState-err" : undefined}
                  />
                  {fieldErrors.clientState && <p id="clientState-err" className="text-xs text-red-600">{fieldErrors.clientState}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="clientZipCode" className="text-sm font-medium text-gray-800">ZIP Code</label>
                  <input
                    id="clientZipCode"
                    value={clientZipCode}
                    onChange={(e) => setClientZipCode(e.target.value)}
                    placeholder="10001"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.clientZipCode ? bad : ok}`}
                    aria-invalid={!!fieldErrors.clientZipCode}
                    aria-describedby={fieldErrors.clientZipCode ? "clientZipCode-err" : undefined}
                  />
                  {fieldErrors.clientZipCode && <p id="clientZipCode-err" className="text-xs text-red-600">{fieldErrors.clientZipCode}</p>}
                </div>
              </div>
            </div>

            {/* Admin User Info */}
            <div className="space-y-3">
              <h3 className="text-sm font-semibold text-gray-900">Admin User Information</h3>
              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label htmlFor="adminFirstName" className="text-sm font-medium text-gray-800">First name</label>
                  <input
                    id="adminFirstName"
                    value={adminFirstName}
                    onChange={(e) => setAdminFirstName(e.target.value)}
                    placeholder="Jane"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.adminFirstName ? bad : ok}`}
                    aria-invalid={!!fieldErrors.adminFirstName}
                    aria-describedby={fieldErrors.adminFirstName ? "adminFirstName-err" : undefined}
                  />
                  {fieldErrors.adminFirstName && <p id="adminFirstName-err" className="text-xs text-red-600">{fieldErrors.adminFirstName}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="adminLastName" className="text-sm font-medium text-gray-800">Last name</label>
                  <input
                    id="adminLastName"
                    value={adminLastName}
                    onChange={(e) => setAdminLastName(e.target.value)}
                    placeholder="Doe"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.adminLastName ? bad : ok}`}
                    aria-invalid={!!fieldErrors.adminLastName}
                    aria-describedby={fieldErrors.adminLastName ? "adminLastName-err" : undefined}
                  />
                  {fieldErrors.adminLastName && <p id="adminLastName-err" className="text-xs text-red-600">{fieldErrors.adminLastName}</p>}
                </div>
              </div>

              <div className="space-y-1.5">
                <label htmlFor="adminEmail" className="text-sm font-medium text-gray-800">Work email *</label>
                <input
                  id="adminEmail"
                  type="email"
                  value={adminEmail}
                  onChange={(e) => setAdminEmail(e.target.value)}
                  placeholder="you@lawfirm.com"
                  className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.adminEmail ? bad : ok}`}
                  aria-invalid={!!fieldErrors.adminEmail}
                  aria-describedby={fieldErrors.adminEmail ? "adminEmail-err" : undefined}
                />
                {fieldErrors.adminEmail && <p id="adminEmail-err" className="text-xs text-red-600">{fieldErrors.adminEmail}</p>}
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <div className="flex items-center justify-between">
                    <label htmlFor="adminPassword" className="text-sm font-medium text-gray-800">Password *</label>
                    <span className="text-xs text-gray-500">8+ chars, mix of cases & symbol</span>
                  </div>
                  <input
                    id="adminPassword"
                    type="password"
                    value={adminPassword}
                    onChange={(e) => setAdminPassword(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.adminPassword ? bad : ok}`}
                    aria-invalid={!!fieldErrors.adminPassword}
                    aria-describedby={fieldErrors.adminPassword ? "adminPassword-err" : "adminPassword-help"}
                  />
                  {/* Strength meter */}
                  <div className="mt-1 flex items-center gap-3">
                    <div className="h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
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
                    <span className="min-w-[48px] text-xs text-gray-500">{strength ? strengthLabel : ""}</span>
                  </div>
                  <p id="adminPassword-help" className="sr-only">Password strength meter</p>
                  {fieldErrors.adminPassword && <p id="adminPassword-err" className="text-xs text-red-600">{fieldErrors.adminPassword}</p>}
                </div>

                <div className="space-y-1.5">
                  <label htmlFor="confirmPassword" className="text-sm font-medium text-gray-800">Confirm password *</label>
                  <input
                    id="confirmPassword"
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    placeholder="••••••••"
                    className={`w-full rounded-xl border px-3.5 py-2.5 text-[15px] shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 ${fieldErrors.confirmPassword ? bad : ok}`}
                    aria-invalid={!!fieldErrors.confirmPassword}
                    aria-describedby={fieldErrors.confirmPassword ? "confirmPassword-err" : undefined}
                  />
                  {fieldErrors.confirmPassword && <p id="confirmPassword-err" className="text-xs text-red-600">{fieldErrors.confirmPassword}</p>}
                </div>
              </div>
            </div>

            <div className="flex items-start gap-3">
              <input
                id="agree"
                type="checkbox"
                checked={agree}
                onChange={(e) => setAgree(e.target.checked)}
                className="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                aria-invalid={!!fieldErrors.agree}
                aria-describedby={fieldErrors.agree ? "agree-err" : undefined}
              />
              <label htmlFor="agree" className="text-sm text-gray-700">
                I agree to the{" "}
                <a href="/terms" className="text-blue-600 hover:text-blue-700">Terms</a> and{" "}
                <a href="/privacy" className="text-blue-600 hover:text-blue-700">Privacy Policy</a>.
              </label>
            </div>
            {fieldErrors.agree && <p id="agree-err" className="text-xs text-red-600">{fieldErrors.agree}</p>}

            <label className="flex items-center gap-2 text-sm text-gray-700">
              <input
                type="checkbox"
                checked={marketing}
                onChange={(e) => setMarketing(e.target.checked)}
                className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
              />
              Send me product updates (optional)
            </label>

            <button
              type="submit"
              disabled={submitting}
              className="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-white font-medium shadow-sm hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed"
            >
              {submitting ? "Creating account…" : "Create Account"}
            </button>

            <div className="flex items-center justify-center gap-2 text-sm text-gray-500">
              <span>Already have an account?</span>
              <a href="/login" className="text-blue-600 hover:text-blue-700">Log in</a>
            </div>
          </form>
        </div>
      </div>
    </section>
    </>
  );
}
