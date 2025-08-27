"use client";
import React, { useMemo, useState } from "react";
import FeaturesSection from "./FeaturesSection";

type Tier = "Starter" | "Growth" | "Pro" | "Enterprise";
const TIERS: Tier[] = ["Starter", "Growth", "Pro", "Enterprise"];

export default function TulsaSEOHero() {
  const [tier, setTier] = useState<Tier>("Growth");

  const auditHref = useMemo(() => `/services/audit?tier=${encodeURIComponent(tier)}`, [tier]);

  return (
    <>
      <section className="relative overflow-hidden bg-gradient-to-b from-[#0c0a17] to-black text-white">
        {/* soft grid + glow */}
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_70%_10%,rgba(99,102,241,0.15),transparent_60%),radial-gradient(50%_50%_at_20%_20%,rgba(16,185,129,0.10),transparent_60%)]" />
        <div className="mx-auto max-w-7xl px-6 pt-16 pb-10">
          {/* Eyebrow */}
          <div className="mb-3 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-3 py-1 text-xs text-indigo-200">
            <span className="h-1.5 w-1.5 rounded-full bg-indigo-400" /> AI‑First SEO Platform
          </div>

          {/* Headline */}
          <h1 className="text-5xl font-bold leading-tight md:text-6xl lg:text-7xl">
            <span className="text-white">Tulsa‑SEO:</span>{" "}
            <span className="text-white">Start with an Audit.</span>{" "}
            <span className="text-indigo-300">Scale with a Plan.</span>
          </h1>
          <p className="mt-3 max-w-2xl text-white/70">
            Fill in your details, pick a tier, and watch your rankings improve with an AI‑assisted roadmap that
            auto‑saves as you go.
          </p>

          {/* Wizard preview breadcrumb */}
          <div className="mt-6 grid grid-cols-1 gap-2 sm:grid-cols-5">
            {["Create Account","Business Details","Goals","Pick Tier","Review"].map((label, i) => (
              <div key={label} className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/80">
                <span className={`flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-semibold ${i === 0 ? "bg-indigo-600" : "bg-white/10"}`}>{i+1}</span>
                <span className="truncate">{label}</span>
              </div>
            ))}
          </div>

          {/* Controls */}
          <div className="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
            <a
              href={auditHref}
              className="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-500"
            >
              Start Your {tier} Audit <span aria-hidden>→</span>
            </a>
            <a
              href="/pricing"
              className="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-6 py-3 font-medium text-white/90 hover:bg-white/10"
            >
              View Pricing
            </a>
            <div className="sm:ml-3">
              <label className="block text-xs text-white/60 mb-1">Choose tier</label>
              <div className="relative">
                <select
                  value={tier}
                  onChange={(e) => setTier(e.target.value as Tier)}
                  className="w-full appearance-none rounded-xl border border-white/10 bg-black/40 px-4 py-3 pr-10 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  {TIERS.map((t) => (
                    <option key={t} value={t} className="bg-[#0c0a17]">
                      {t}
                    </option>
                  ))}
                </select>
                <span className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white/50">▾</span>
              </div>
            </div>
          </div>

          {/* Trust bar */}
          <div className="mt-10 grid gap-4 sm:grid-cols-3">
            <div className="rounded-2xl border border-white/10 bg-white/5 p-4 text-center">
              <div className="text-2xl font-semibold">+38%</div>
              <div className="text-xs text-white/60">Avg. 90‑day organic uplift</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-4 text-center">
              <div className="text-2xl font-semibold">500+</div>
              <div className="text-xs text-white/60">Issues detected & prioritized</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-4 text-center">
              <div className="text-2xl font-semibold">Autosave</div>
              <div className="text-xs text-white/60">Progress kept while you explore</div>
            </div>
          </div>
        </div>

        {/* bottom illustration divider */}
        <div className="pointer-events-none h-24 w-full bg-gradient-to-b from-transparent to-black" />
      </section>

      {/* Features Section */}
      <FeaturesSection />
    </>
  );
}
