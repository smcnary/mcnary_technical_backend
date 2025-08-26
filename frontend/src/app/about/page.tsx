'use client'

import Header from '../../components/Header';

export default function AboutPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <AboutSection />
      </main>
    </div>
  );
}

function AboutSection() {
  return (
    <section className="relative w-full bg-neutral-950 text-neutral-100">
      <div className="mx-auto max-w-7xl px-6 py-16 sm:py-20 md:px-10">
        {/* Header */}
        <header className="mx-auto max-w-3xl text-center">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            About <span className="text-white/90">tulsa-seo.com</span>
          </h2>
          <p className="mt-3 text-base leading-relaxed text-neutral-300 sm:text-lg">
            Your trusted partner for local SEO that delivers results—built in Tulsa, optimized for your market.
          </p>
        </header>

        {/* Two-up original cards (fixed + polished) */}
        <div className="mt-10 grid gap-6 md:mt-14 md:grid-cols-2">
          <div className="rounded-2xl border border-white/10 bg-neutral-900/40 p-6 shadow-[0_0_0_1px_rgba(255,255,255,0.02)_inset]">
            <h3 className="text-xl font-semibold">Our Mission</h3>
            <p className="mt-3 text-neutral-300">
              Help Tulsa businesses dominate search and attract qualified customers through
              data‑driven, AI‑assisted SEO strategies—measured, repeatable, and transparent.
            </p>
          </div>

          <div className="rounded-2xl border border-white/10 bg-neutral-900/40 p-6 shadow-[0_0_0_1px_rgba(255,255,255,0.02)_inset]">
            <h3 className="text-xl font-semibold">Why Choose Us</h3>
            <ul className="mt-4 space-y-3 text-neutral-300">
              <li className="flex gap-3"><span className="mt-1 size-2.5 rounded-full bg-emerald-400/90" />Specialized in local business SEO</li>
              <li className="flex gap-3"><span className="mt-1 size-2.5 rounded-full bg-emerald-400/90" />Proven, case‑study backed results</li>
              <li className="flex gap-3"><span className="mt-1 size-2.5 rounded-full bg-emerald-400/90" />Deep expertise in geographic markets</li>
              <li className="flex gap-3"><span className="mt-1 size-2.5 rounded-full bg-emerald-400/90" />AI‑first audits & reporting dashboard</li>
            </ul>
          </div>
        </div>

        {/* New: Services & Process */}
        <div className="mt-6 grid gap-6 md:grid-cols-2">
          <div className="rounded-2xl border border-white/10 bg-neutral-900/40 p-6">
            <h3 className="text-xl font-semibold">Core Services</h3>
            <div className="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
              {[
                "Technical SEO + site health",
                "Local SEO (GBP, NAP, citations)",
                "On‑page optimization",
                "Content & topic clusters",
                "Reviews & reputation",
                "Link earning & PR",
              ].map((s) => (
                <div key={s} className="flex items-start gap-3">
                  <svg viewBox="0 0 24 24" className="mt-0.5 h-5 w-5" aria-hidden>
                    <path d="M9 12l2 2 4-4" stroke="currentColor" className="opacity-90" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" strokeWidth="2" fill="none" className="opacity-30"/>
                  </svg>
                  <span className="text-neutral-300">{s}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="rounded-2xl border border-white/10 bg-neutral-900/40 p-6">
            <h3 className="text-xl font-semibold">Our Process</h3>
            <ol className="mt-4 space-y-4">
              {[
                ["1. Discover", "Goals, competitors, ICP, and baseline metrics."],
                ["2. Audit", "Technical, content, local, and authority gaps with priority scoring."],
                ["3. Implement", "Quick wins first, then roadmap sprints with clear owners."],
                ["4. Measure", "Rankings, traffic, leads, and revenue in a single dashboard."],
                ["5. Improve", "Monthly refinement driven by data and experiments."],
              ].map(([title, desc]) => (
                <li key={title} className="rounded-xl border border-white/5 bg-neutral-900/50 p-3">
                  <p className="font-medium">{title}</p>
                  <p className="text-sm text-neutral-300">{desc}</p>
                </li>
              ))}
            </ol>
          </div>
        </div>

        {/* New: Stats */}
        <dl className="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
          {[
            ["+78%", "avg. YoY organic growth"],
            ["32", "priority keywords on page 1"],
            ["<45d", "to GBP visibility lift"],
            ["A+", "site health score targets"],
          ].map(([value, label]) => (
            <div key={label} className="rounded-2xl border border-white/10 bg-neutral-900/40 p-5 text-center">
              <dt className="text-2xl font-semibold">{value}</dt>
              <dd className="mt-1 text-sm text-neutral-300">{label}</dd>
            </div>
          ))}
        </dl>

        {/* Trust bar */}
        <div className="mt-10 flex flex-wrap items-center justify-center gap-x-10 gap-y-6 opacity-80">
          {["Google Partner", "AI Powered", "Clutch", "BBB Accredited"].map((t) => (
            <div key={t} className="flex items-center gap-3">
              <div className="h-6 w-6 rounded-md bg-white/10" aria-hidden />
              <span className="text-sm text-neutral-300">{t}</span>
            </div>
          ))}
        </div>

        {/* FAQ */}
        <div className="mt-14">
          <h3 className="text-xl font-semibold text-center">FAQ</h3>
          <div className="mx-auto mt-6 max-w-3xl divide-y divide-white/10 rounded-2xl border border-white/10 bg-neutral-900/40">
            {[
              ["How long until I see results?",
               "Most local businesses see leading indicators (improved impressions & map visibility) in 30–45 days, with meaningful traffic & lead lifts in 60–90 days."],
              ["Do you manage my Google Business Profile?",
               "Yes. We optimize categories, services, photos, posts, and review strategy—plus monitor for spam and suggest Q&A."],
              ["Can you work with my developer or CMS?",
               "Absolutely. We provide implementation notes, tickets, and QA for WordPress, Shopify, Webflow, custom stacks, and more."],
            ].map(([q, a]) => (
              <details key={q} className="group open:bg-neutral-900/60">
                <summary className="cursor-pointer list-none p-5 font-medium transition hover:bg-white/5">
                  {q}
                </summary>
                <div className="px-5 pb-5 text-neutral-300">{a}</div>
              </details>
            ))}
          </div>
        </div>

        {/* CTA */}
        <div className="mt-14 rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-emerald-400/5 p-6 text-center md:p-8">
          <h3 className="text-xl font-semibold">Ready to outrank Tulsa competitors?</h3>
          <p className="mt-2 text-neutral-300">
            Get a free mini‑audit and a prioritized roadmap customized to your business.
          </p>
          <div className="mt-5 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a
              href="/contact"
              className="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 font-medium text-neutral-900 hover:bg-emerald-400 transition"
            >
              Get My Free Mini‑Audit
            </a>
            <a
              href="/dashboard"
              className="inline-flex items-center justify-center rounded-xl border border-white/15 px-5 py-3 font-medium text-white/90 hover:bg-white/5 transition"
            >
              Explore the AI SEO Dashboard
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
