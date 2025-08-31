
import { motion } from "framer-motion";
import { ArrowRight, Gauge, Layers, ClipboardCheck } from "lucide-react";
import { Button } from "@/components/ui/button";

/**
 * OurServicesStacked
 * Stacked, hierarchy-first services section for CounselRank
 * Tech: React + Tailwind + shadcn/ui + framer-motion
 */
export type Tier = {
  name: string;
  tagline?: string;
  highlights: string[];
  ctaLabel?: string;
  href?: string;
};

interface OurServicesStackedProps {
  className?: string;
  onTierClick?: (tierName: string) => void;
  tiers?: Tier[]; // For the Tiered SEO Packages sub-section
  auditHref?: string;
  dashboardHref?: string;
}

const fadeUp = {
  hidden: { opacity: 0, y: 16 },
  visible: (i = 0) => ({ opacity: 1, y: 0, transition: { delay: 0.08 * i, duration: 0.4 } }),
};

export default function OurServicesStacked({
  className = "",
  onTierClick,
  tiers = [
    {
      name: "Growth",
      tagline: "Expand rankings & lead flow",
      highlights: [
        "Content engine + AEO",
        "GBP optimization & reviews",
        "Link earning & PR outreach",
      ],
      ctaLabel: "View Growth",
      href: "/pricing#growth",
    },
    {
      name: "Pro",
      tagline: "Advanced optimization & scaling",
      highlights: [
        "Advanced technical + schema",
        "Multi-location optimization",
        "Custom reporting & SLAs",
      ],
      ctaLabel: "View Pro",
      href: "/pricing#pro",
    },
    {
      name: "Enterprise",
      tagline: "Multi-location & aggressive scaling",
      highlights: [
        "Programmatic/at-scale content",
        "Advanced technical + schema",
        "Custom dashboards & SLAs",
      ],
      ctaLabel: "View Enterprise",
      href: "/pricing#enterprise",
    },
  ],
  auditHref = "/services/audit",
  dashboardHref = "/app/login",
}: OurServicesStackedProps) {
  return (
    <section className={`relative isolate w-full ${className}`} aria-labelledby="our-services-heading">
      {/* soft background glow */}
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute left-[10%] top-10 h-64 w-64 rounded-full bg-indigo-600/10 blur-2xl" />
        <div className="absolute right-[12%] bottom-10 h-48 w-48 rounded-full bg-emerald-500/10 blur-2xl" />
      </div>

      <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        {/* Section Heading */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
          className="mb-16 text-center"
        >
          <h2 id="our-services-heading" className="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
            Our Services
          </h2>
          <p className="mt-4 text-white/80 max-w-3xl mx-auto text-lg leading-relaxed">
            A transparent, outcome-focused stack: pick your tier, start with a comprehensive audit, and track everything in your client dashboard.
          </p>
        </motion.div>

        {/* Tiered SEO Packages */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
          className="mb-16"
        >
          <div className="flex items-center gap-3 mb-4">
            <div className="p-2 rounded-lg border border-white/10 bg-white/5">
              <Layers className="h-6 w-6 text-indigo-300" aria-hidden />
            </div>
            <h3 className="text-xl sm:text-2xl font-semibold text-white">Tiered SEO Packages</h3>
          </div>
          <p className="text-white/70 max-w-3xl text-base leading-relaxed mb-8">
            Choose the right plan for your stage of growth—Growth, Pro, or Enterprise. Each tier layers on strategy,
            content, technical improvements, and authority building.
          </p>

          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {tiers.map((tier, idx) => (
              <motion.div key={tier.name} custom={idx} initial="hidden" whileInView="visible" viewport={{ once: true }} variants={fadeUp}>
                <div className="h-full rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl hover:shadow-2xl hover:bg-white/10 transition-all duration-200 hover:-translate-y-1">
                  <div className="flex flex-col h-full">
                    <div className="flex items-start justify-between">
                      <div>
                        <h4 className="text-xl font-semibold text-white">{tier.name}</h4>
                        {tier.tagline && (
                          <p className="mt-2 text-sm text-white/70">{tier.tagline}</p>
                        )}
                      </div>
                    </div>

                    <ul className="mt-6 space-y-3 text-sm flex-1">
                      {tier.highlights.map((h) => (
                        <li key={h} className="flex items-start gap-3">
                          <span className="mt-1.5 inline-block h-2 w-2 rounded-full bg-indigo-400" />
                          <span className="text-white/80 leading-relaxed">{h}</span>
                        </li>
                      ))}
                    </ul>

                    <div className="mt-6 pt-4">
                      {(tier.href || tier.ctaLabel) && (
                        <Button
                          onClick={() => onTierClick?.(tier.name)}
                          asChild={Boolean(tier.href)}
                          className="w-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/25 hover:shadow-xl hover:shadow-indigo-600/30 transition-all duration-200"
                          variant="default"
                        >
                          {tier.href ? (
                            <a href={tier.href} aria-label={`${tier.ctaLabel ?? "View"} ${tier.name} plan`}>
                              {tier.ctaLabel ?? "View plan"}
                              <ArrowRight className="ml-2 h-4 w-4" />
                            </a>
                          ) : (
                            <span>
                              {tier.ctaLabel ?? "View plan"}
                              <ArrowRight className="ml-2 inline h-4 w-4" />
                            </span>
                          )}
                        </Button>
                      )}
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </motion.div>

        {/* Comprehensive SEO Audit */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
          className="mb-16"
        >
          <div className="flex items-center gap-3 mb-4">
            <div className="p-2 rounded-lg border border-white/10 bg-white/5">
              <ClipboardCheck className="h-6 w-6 text-emerald-300" aria-hidden />
            </div>
            <h3 className="text-xl sm:text-2xl font-semibold text-white">Comprehensive SEO Audit</h3>
          </div>
          <div className="text-white/70 max-w-3xl text-base leading-relaxed mb-6">
            A top-to-bottom evaluation of your site, competitors, and search performance. Delivered with a prioritized
            roadmap—technical fixes, content gaps, and authority wins—so you know exactly what to do next.
          </div>

          <div className="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-xl">
            <div className="grid gap-6 sm:grid-cols-2">
              <ul className="space-y-3 text-sm">
                {[
                  "Core Web Vitals & crawl health",
                  "Indexation, sitemaps, robots & logs",
                  "Schema/structured data & AEO",
                  "Content quality, intent mapping & gaps",
                ].map((t) => (
                  <li key={t} className="flex items-start gap-3">
                    <span className="mt-1.5 inline-block h-2 w-2 rounded-full bg-emerald-400" />
                    <span className="text-white/80 leading-relaxed">{t}</span>
                  </li>
                ))}
              </ul>
              <ul className="space-y-3 text-sm">
                {[
                  "Internal linking & information architecture",
                  "Backlink profile & competitive insights",
                  "Local SEO & GBP signals",
                  "Prioritized roadmap with effort & impact",
                ].map((t) => (
                  <li key={t} className="flex items-start gap-3">
                    <span className="mt-1.5 inline-block h-2 w-2 rounded-full bg-emerald-400" />
                    <span className="text-white/80 leading-relaxed">{t}</span>
                  </li>
                ))}
              </ul>
            </div>
            <div className="mt-8 flex flex-wrap items-center gap-4">
              <Button asChild className="bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/25 hover:shadow-xl hover:shadow-indigo-600/30 transition-all duration-200">
                <a href={auditHref} aria-label="See sample audit">
                  See Sample Audit
                  <ArrowRight className="ml-2 h-4 w-4" />
                </a>
              </Button>
              <Button variant="outline" asChild className="border-white/20 bg-white/5 text-white hover:bg-white/10 hover:border-white/30 transition-all duration-200">
                <a href="/contact" aria-label="Book audit call">Book Audit Call</a>
              </Button>
            </div>
          </div>
        </motion.div>

        {/* Client Dashboard */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
        >
          <div className="flex items-center gap-3 mb-4">
            <div className="p-2 rounded-lg border border-white/10 bg-white/5">
              <Gauge className="h-6 w-6 text-indigo-300" aria-hidden />
            </div>
            <h3 className="text-xl sm:text-2xl font-semibold text-white">Client Dashboard</h3>
          </div>
          <p className="text-white/70 max-w-3xl text-base leading-relaxed mb-6">
            Real-time visibility into rankings, leads, reviews, and campaign progress—centralized and exportable. Transparent reporting with no surprises.
          </p>

          <div className="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-xl">
            <div className="grid gap-6 sm:grid-cols-3">
              {[
                {
                  title: "Rankings & Visibility",
                  desc: "Keyword positions, share of voice, and local pack tracking.",
                },
                {
                  title: "Leads & Conversions",
                  desc: "Form fills, calls, and attribution across pages/campaigns.",
                },
                {
                  title: "GBP & Reviews",
                  desc: "Review velocity, response rate, and profile change log.",
                },
              ].map((b) => (
                <div key={b.title} className="rounded-2xl border border-white/10 bg-white/5 p-6 hover:bg-white/10 transition-all duration-200">
                  <div className="font-semibold text-white text-lg mb-2">{b.title}</div>
                  <p className="text-sm text-white/70 leading-relaxed">{b.desc}</p>
                </div>
              ))}
            </div>
            <div className="mt-8 flex flex-wrap items-center gap-4">
              <Button asChild className="bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/25 hover:shadow-xl hover:shadow-indigo-600/30 transition-all duration-200">
                <a href={dashboardHref} aria-label="Open dashboard">
                  Open Dashboard
                  <ArrowRight className="ml-2 h-4 w-4" />
                </a>
              </Button>
              <Button variant="outline" asChild className="border-white/20 bg-white/5 text-white hover:bg-white/10 hover:border-white/30 transition-all duration-200">
                <a href="/features/dashboard" aria-label="Explore dashboard features">Explore Features</a>
              </Button>
            </div>
          </div>
        </motion.div>
      </div>

      {/* subtle top separator to blend with adjoining sections */}
      <div className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />
    </section>
  );
}

/**
 * Example usage (Next.js/React):
 *
 * <OurServicesStacked className="py-12" />
 *
 * // With custom tiers/links
 * <OurServicesStacked
 *   className="py-16"
 *   tiers={[
 *     { name: 'Growth', tagline: 'Scale content', highlights: ['AEO', 'Reviews', 'Digital PR'], href: '/pricing#growth' },
 *     { name: 'Pro', tagline: 'Advanced optimization', highlights: ['Advanced schema', 'Multi-location', 'Custom SLAs'], href: '/pricing#pro' },
 *     { name: 'Enterprise', tagline: 'Multi-location', highlights: ['Programmatic', 'Advanced schema', 'Custom SLAs'], href: '/pricing#enterprise' },
 *   ]}
 *   auditHref="/services/audit"
 *   dashboardHref="/app/login"
 * />
 */
