"use client";
import { useMemo, useState } from "react";

type Tier = "Growth" | "Pro" | "Enterprise";
const TIERS: Tier[] = ["Growth", "Pro", "Enterprise"];

export default function TulsaSEOHero() {
  const [tier, setTier] = useState<Tier>("Growth");

  const auditHref = useMemo(() => `/services/audit?tier=${encodeURIComponent(tier)}`, [tier]);

  return (
    <>
      <section className="relative overflow-hidden bg-gradient-to-b from-[#0c0a17] to-black text-white">
        {/* soft grid + glow */}
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_70%_10%,rgba(99,102,241,0.15),transparent_60%),radial-gradient(50%_50%_at_20%_20%,rgba(16,185,129,0.10),transparent_60%)]" />
        
        <div className="mx-auto max-w-7xl px-6 pt-20 pb-16">
          {/* Eyebrow */}
          <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-2 text-sm text-indigo-200">
            <span className="h-2 w-2 rounded-full bg-indigo-400" /> AI‑First SEO Platform
          </div>

          {/* Headline */}
          <h1 className="text-4xl font-bold leading-tight md:text-5xl lg:text-6xl xl:text-7xl mb-6">
            <span className="text-white">Tulsa‑SEO:</span>{" "}
            <span className="text-white">Start with an Audit.</span>{" "}
            <span className="text-indigo-300">Scale with a Plan.</span>
          </h1>
          
          <p className="text-lg md:text-xl text-white/80 max-w-3xl mb-8 leading-relaxed">
            Fill in your details, pick a tier, and watch your rankings improve with an AI‑assisted roadmap that
            auto‑saves as you go.
          </p>

          {/* Wizard preview breadcrumb */}
          <div className="mb-10 grid grid-cols-1 gap-3 sm:grid-cols-5">
            {["Create Account","Business Details","Goals","Pick Tier","Review"].map((label, i) => (
              <div key={label} className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/80 hover:bg-white/10 transition-colors duration-200">
                <span className={`flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold transition-all duration-200 ${
                  i === 0 
                    ? "bg-indigo-600 text-white shadow-lg shadow-indigo-600/25" 
                    : "bg-white/10 text-white/80"
                }`}>
                  {i+1}
                </span>
                <span className="truncate font-medium">{label}</span>
              </div>
            ))}
          </div>

          {/* Controls */}
          <div className="mb-12 flex flex-col gap-4 sm:flex-row sm:items-end">
            <a
              href={auditHref}
              className="inline-flex items-center justify-center gap-3 rounded-xl bg-indigo-600 px-8 py-4 text-lg font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 hover:shadow-xl hover:shadow-indigo-600/30 transition-all duration-200 transform hover:-translate-y-0.5"
            >
              Start Your {tier} Audit <span aria-hidden className="text-xl">→</span>
            </a>
            
            <a
              href="/pricing"
              className="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-4 text-base font-medium text-white/90 hover:bg-white/10 hover:border-white/30 transition-all duration-200"
            >
              View Pricing
            </a>
            
            <div className="sm:ml-4">
              <label className="block text-sm text-white/70 mb-2 font-medium">Choose tier</label>
              <div className="relative">
                <select
                  value={tier}
                  onChange={(e) => setTier(e.target.value as Tier)}
                  className="w-full appearance-none rounded-xl border border-white/20 bg-black/40 px-4 py-3 pr-10 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                >
                  {TIERS.map((t) => (
                    <option key={t} value={t} className="bg-[#0c0a17] text-white">
                      {t}
                    </option>
                  ))}
                </select>
                <span className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white/60">▾</span>
              </div>
            </div>
          </div>

          {/* Trust bar */}
          <div className="grid gap-6 sm:grid-cols-3">
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">+38%</div>
              <div className="text-sm text-white/70 leading-relaxed">Avg. 90‑day organic uplift</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">500+</div>
              <div className="text-sm text-white/70 leading-relaxed">Issues detected & prioritized</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">Autosave</div>
              <div className="text-sm text-white/70 leading-relaxed">Progress kept while you explore</div>
            </div>
          </div>
        </div>

        {/* bottom illustration divider */}
        <div className="pointer-events-none h-32 w-full bg-gradient-to-b from-transparent to-black" />
      </section>
    </>
  );
}
