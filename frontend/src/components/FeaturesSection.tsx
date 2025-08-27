"use client";
import React from "react";
import { BarChart3, LineChart, Users, Target, FileBarChart, ShieldCheck } from "lucide-react";

// Matches the dark gradient + indigo/emerald highlights of the Hero & Wizard
export default function FeaturesSection() {
  const items: Array<{
    icon: React.ReactNode;
    title: string;
    desc: string;
  }> = [
    {
      icon: <LineChart className="h-5 w-5" aria-hidden />,
      title: "Real‑Time Rankings",
      desc: "Track keyword positions daily with alerts for wins, losses, and big swings.",
    },
    {
      icon: <BarChart3 className="h-5 w-5" aria-hidden />,
      title: "Performance Analytics",
      desc: "Dashboards showing traffic trends, conversions, and ROI from SEO efforts.",
    },
    {
      icon: <Users className="h-5 w-5" aria-hidden />,
      title: "Lead Attribution",
      desc: "See which pages and keywords drive calls, forms, chats, and booked consults.",
    },
    {
      icon: <Target className="h-5 w-5" aria-hidden />,
      title: "Competitor Insights",
      desc: "Monitor rankings, content gaps, and link velocity to outpace competitors.",
    },
    {
      icon: <FileBarChart className="h-5 w-5" aria-hidden />,
      title: "Automated Reporting",
      desc: "Monthly reports auto‑generated and sent to stakeholders with action items.",
    },
    {
      icon: <ShieldCheck className="h-5 w-5" aria-hidden />,
      title: "White‑Label Ready",
      desc: "Brandable client views and exports for agencies and multi‑location teams.",
    },
  ];

  return (
    <section className="relative isolate bg-gradient-to-b from-[#0c0a17] to-black py-14 text-white">
      {/* soft background glow */}
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute left-[10%] top-10 h-64 w-64 rounded-full bg-indigo-600/10 blur-2xl" />
        <div className="absolute right-[12%] bottom-10 h-48 w-48 rounded-full bg-emerald-500/10 blur-2xl" />
      </div>

      <div className="mx-auto max-w-6xl px-6">
        <div className="text-center">
          <h2 className="text-2xl md:text-3xl font-semibold">Everything You Need to Dominate Local Search</h2>
          <p className="mt-2 text-white/70 max-w-2xl mx-auto">
            From rank tracking to attribution, your AI‑assisted audit turns into an ongoing plan you can actually ship.
          </p>
        </div>

        <div className="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
          {items.map((item) => (
            <div
              key={item.title}
              className="group rounded-2xl border border-white/10 bg-white/5 p-5 shadow-xl transition hover:-translate-y-0.5 hover:bg-white/[0.08]"
            >
              <div className="mb-3 inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-2.5 py-2 text-indigo-200">
                <span className="grid h-8 w-8 place-items-center rounded-md bg-indigo-600/20">
                  {item.icon}
                </span>
                <span className="text-sm font-medium">{item.title}</span>
              </div>
              <p className="text-sm text-white/80 leading-relaxed">{item.desc}</p>
            </div>
          ))}
        </div>

        <div className="mt-10 text-center">
          <a
            href="/services/audit"
            className="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 font-medium shadow-lg shadow-indigo-600/20 hover:bg-indigo-500"
          >
            Start an Audit <span aria-hidden>→</span>
          </a>
          <p className="mt-2 text-xs text-white/60">Your progress autosaves. Switch tiers anytime in the wizard.</p>
        </div>
      </div>

      {/* subtle top/bottom separators to blend with adjoining sections */}
      <div className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />
      <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />
    </section>
  );
}
