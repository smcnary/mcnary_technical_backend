"use client";
import React, { useState } from "react";
import Link from "next/link";

// If you have Next.js, replace <a> with Link from "next/link"

type TierKey = "Starter" | "Growth" | "Pro" | "Enterprise";

const AUDIT_TIERS: Array<{
  key: TierKey;
  price: string;
  blurb: string;
  features: string[];
}> = [
  {
    key: "Starter",
    price: "$199",
    blurb: "Quick scan + 10 prioritized fixes.",
    features: ["On‑page scan", "10 prioritized fixes", "Quick wins checklist"],
  },
  {
    key: "Growth",
    price: "$499",
    blurb: "Full on‑page + technical + competitor snapshot.",
    features: [
      "On‑page + technical audit",
      "Rank tracking setup",
      "Competitor landscape",
      "20 prioritized fixes + roadmap",
    ],
  },
  {
    key: "Pro",
    price: "$999",
    blurb: "Technical crawl, schema, and a 3‑month content plan.",
    features: [
      "Everything in Growth",
      "Full technical crawl",
      "Schema + site structure",
      "3‑month content plan",
      "Backlink quality analysis",
    ],
  },
  {
    key: "Enterprise",
    price: "Custom",
    blurb: "Multi‑location, integrations, dedicated strategist.",
    features: [
      "Local + multi‑location",
      "Advanced architecture & schema",
      "Custom integrations",
      "Dedicated strategist Q&A",
    ],
  },
];

const SUB_TIERS: Array<{
  key: TierKey;
  price: string;
  blurb: string;
  features: string[];
}> = [
  {
    key: "Starter",
    price: "$299/mo",
    blurb: "Implement audit fixes + basic reporting.",
    features: ["1–2 fixes/week", "Basic monthly report", "Rank monitoring"],
  },
  {
    key: "Growth",
    price: "$799/mo",
    blurb: "Content optimization + competitor tracking.",
    features: [
      "2–4 page optimizations/mo",
      "Basic link outreach",
      "Competitor tracking",
      "Monthly strategy call",
    ],
  },
  {
    key: "Pro",
    price: "$1,499/mo",
    blurb: "Technical monitoring + content production.",
    features: [
      "Everything in Growth",
      "Technical monitoring",
      "2–3 blog posts/mo",
      "Reputation monitoring",
      "Bi‑weekly strategy calls",
    ],
  },
  {
    key: "Enterprise",
    price: "$3,000+/mo",
    blurb: "National/multi‑location SEO with PR & campaigns.",
    features: [
      "Dedicated strategist",
      "PR + backlink campaigns",
      "Full content strategy",
      "Weekly reporting & calls",
    ],
  },
];

const Section = ({ title, subtitle }: { title: string; subtitle?: string }) => (
  <div className="text-center">
    <h2 className="text-2xl md:text-3xl font-semibold text-white">{title}</h2>
    {subtitle && <p className="mt-2 text-white/70 max-w-2xl mx-auto">{subtitle}</p>}
  </div>
);

const Card = ({
  title,
  price,
  blurb,
  children,
  highlight = false,
  cta,
}: {
  title: string;
  price: string;
  blurb: string;
  children: React.ReactNode;
  highlight?: boolean;
  cta: React.ReactNode;
}) => (
  <div
    className={`relative rounded-2xl border p-6 shadow-xl transition hover:-translate-y-0.5 ${
      highlight
        ? "border-indigo-500/30 bg-gradient-to-b from-indigo-500/10 to-black"
        : "border-white/10 bg-white/5"
    }`}
  >
    {highlight && (
      <div className="absolute -top-3 right-4 rounded-full bg-indigo-600 px-3 py-1 text-xs font-medium text-white shadow">
        Best Value
      </div>
    )}
    <div className="mb-3">
      <div className="text-lg font-semibold text-white">{title}</div>
      <div className="text-3xl font-bold text-white mt-1">{price}</div>
      <p className="text-white/70 mt-1">{blurb}</p>
    </div>
    <ul className="text-sm text-white/80 space-y-2 mb-6">
      {children}
    </ul>
    {cta}
  </div>
);

const Feature = ({ children }: { children: React.ReactNode }) => (
  <li className="flex items-start gap-3"><span className="mt-2 h-1.5 w-1.5 rounded-full bg-white/60"/>{children}</li>
);

const Toggle = ({ mode, setMode }: { mode: "audit" | "subscription"; setMode: (m: "audit" | "subscription") => void }) => (
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
);

function tierGrid(
  items: typeof AUDIT_TIERS | typeof SUB_TIERS,
  mode: "audit" | "subscription"
) {
  const highlightKey: TierKey = "Growth";
  return (
    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mt-6">
      {items.map((t) => (
        <Card
          key={t.key}
          title={t.key}
          price={t.price}
          blurb={t.blurb}
          highlight={t.key === highlightKey}
          cta={
            mode === "audit" ? (
              <Link
                href={`/audit-wizard?tier=${encodeURIComponent(t.key)}`}
                className="block w-full text-center rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-500"
              >
                Start {t.key} Audit
              </Link>
            ) : (
              <Link
                href={`/audit-wizard?tier=${encodeURIComponent(t.key)}#subscribe`}
                className="block w-full text-center rounded-xl bg-emerald-600 px-4 py-2 font-medium text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-500"
              >
                Choose {t.key} Plan
              </Link>
            )
          }
        >
          {t.features.map((f) => (
            <Feature key={f}>{f}</Feature>
          ))}
        </Card>
      ))}
    </div>
  );
}

const Comparison = () => (
  <div className="mt-14 rounded-2xl border border-white/10 bg-white/5 p-6">
    <h3 className="text-lg font-semibold text-white mb-4">What continues from Audit → Subscription?</h3>
    <div className="grid md:grid-cols-3 gap-4 text-sm text-white/80">
      <div>
        <div className="font-medium text-white mb-2">Starter → Starter</div>
        <ul className="space-y-2">
          <li>Implement quick wins</li>
          <li>Basic rank tracking</li>
          <li>Monthly report</li>
        </ul>
      </div>
      <div>
        <div className="font-medium text-white mb-2">Growth → Growth</div>
        <ul className="space-y-2">
          <li>Roadmap execution</li>
          <li>Content optimization</li>
          <li>Competitor tracking + strategy call</li>
        </ul>
      </div>
      <div>
        <div className="font-medium text-white mb-2">Pro/Enterprise → Pro/Enterprise</div>
        <ul className="space-y-2">
          <li>Technical monitoring + schema updates</li>
          <li>Content production + PR/backlinks</li>
          <li>Frequent reporting & leadership sync</li>
        </ul>
      </div>
    </div>
  </div>
);

const FAQ = () => (
  <div className="mt-14 grid md:grid-cols-2 gap-6 text-white/80">
    <div className="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div className="font-medium text-white mb-1">Can I apply my audit fee to a subscription?</div>
      <p>Yes—subscribe within 14 days and your audit fee becomes a credit on month one.</p>
    </div>
    <div className="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div className="font-medium text-white mb-1">Do you require contracts?</div>
      <p>Month‑to‑month. Cancel anytime. For Enterprise, 3‑month minimum is typical.</p>
    </div>
    <div className="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div className="font-medium text-white mb-1">How do you report results?</div>
      <p>Live dashboard + monthly/bi‑weekly calls depending on plan.</p>
    </div>
    <div className="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div className="font-medium text-white mb-1">What if my site is new?</div>
      <p>We tailor the plan toward foundational content, local SEO, and technical setup first.</p>
    </div>
  </div>
);

export default function PricingPage() {
  const [mode, setMode] = useState<"audit" | "subscription">("audit");
  const title = mode === "audit" ? "Choose Your Audit" : "Choose Your Subscription";
  const subtitle =
    mode === "audit"
      ? "Start with a one‑time SEO audit. We'll reveal quick wins, critical issues, and a prioritized roadmap."
      : "Continue with an ongoing plan to implement recommendations and keep growing month over month.";

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white">
      <div className="mx-auto max-w-6xl px-6 py-10">
        {/* Hero */}
        <div className="mb-8 flex flex-wrap items-center justify-between gap-4">
          <div>
            <h1 className="text-3xl font-semibold">Pricing</h1>
            <p className="text-white/70">Audit first. Then subscribe to scale. Built to pair with your Audit Wizard flow.</p>
          </div>
          <Toggle mode={mode} setMode={setMode} />
        </div>

        {/* Section */}
        <Section title={title} subtitle={subtitle} />
        {mode === "audit" ? tierGrid(AUDIT_TIERS, "audit") : tierGrid(SUB_TIERS, "subscription")}

        {/* Comparison & FAQ */}
        <Comparison />
        <FAQ />

        {/* CTA */}
        <div className="mt-14 text-center">
          <Link
            href="/audit-wizard"
            className="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 font-medium shadow-lg shadow-indigo-600/20 hover:bg-indigo-500"
          >
            Start an Audit
            <span aria-hidden>→</span>
          </Link>
          <p className="text-xs text-white/60 mt-3">Your progress autosaves. You can switch tiers on the Review step.</p>
        </div>
      </div>
    </div>
  );
}
