
import { motion } from "framer-motion";
import { ArrowRight, Gauge, Layers, ClipboardCheck } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
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
      name: "Starter",
      tagline: "Best for new sites and local focus",
      highlights: [
        "Local SEO setup & citations",
        "Technical fixes & speed pass",
        "Foundational content plan",
      ],
      ctaLabel: "View Starter",
      href: "/pricing#starter",
    },
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
    <section className={`w-full ${className}`} aria-labelledby="our-services-heading">
      <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        {/* Section Heading */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
          className="mb-10 text-center"
        >
          <h2 id="our-services-heading" className="text-3xl font-bold tracking-tight sm:text-4xl">
            Our Services
          </h2>
          <p className="mt-3 text-muted-foreground max-w-2xl mx-auto">
            A transparent, outcome-focused stack: pick your tier, start with a comprehensive audit, and track everything in your client dashboard.
          </p>
        </motion.div>

        {/* Tiered SEO Packages */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
          className="mb-8"
        >
          <div className="flex items-center gap-3">
            <Layers className="h-6 w-6" aria-hidden />
            <h3 className="text-xl sm:text-2xl font-semibold">Tiered SEO Packages</h3>
          </div>
          <p className="mt-2 text-muted-foreground max-w-3xl">
            Choose the right plan for your stage of growth—Starter, Growth, or Enterprise. Each tier layers on strategy,
            content, technical improvements, and authority building.
          </p>

          <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {tiers.map((tier, idx) => (
              <motion.div key={tier.name} custom={idx} initial="hidden" whileInView="visible" viewport={{ once: true }} variants={fadeUp}>
                <Card className="h-full border-muted/60 shadow-sm hover:shadow-md transition-shadow">
                  <CardContent className="p-5 flex flex-col h-full">
                    <div className="flex items-start justify-between">
                      <div>
                        <h4 className="text-lg font-semibold">{tier.name}</h4>
                        {tier.tagline && (
                          <p className="mt-1 text-sm text-muted-foreground">{tier.tagline}</p>
                        )}
                      </div>
                    </div>

                    <ul className="mt-4 space-y-2 text-sm">
                      {tier.highlights.map((h) => (
                        <li key={h} className="flex items-start gap-2">
                          <span className="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-foreground/60" />
                          <span>{h}</span>
                        </li>
                      ))}
                    </ul>

                    <div className="mt-auto pt-5">
                      {(tier.href || tier.ctaLabel) && (
                        <Button
                          onClick={() => onTierClick?.(tier.name)}
                          asChild={Boolean(tier.href)}
                          className="w-full"
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
                  </CardContent>
                </Card>
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
          className="mb-8"
        >
          <div className="flex items-center gap-3">
            <ClipboardCheck className="h-6 w-6" aria-hidden />
            <h3 className="text-xl sm:text-2xl font-semibold">Comprehensive SEO Audit</h3>
          </div>
          <div className="mt-2 text-muted-foreground max-w-3xl">
            A top-to-bottom evaluation of your site, competitors, and search performance. Delivered with a prioritized
            roadmap—technical fixes, content gaps, and authority wins—so you know exactly what to do next.
          </div>

          <Card className="mt-5 border-muted/60 shadow-sm">
            <CardContent className="p-5">
              <div className="grid gap-4 sm:grid-cols-2">
                <ul className="space-y-2 text-sm">
                  {[
                    "Core Web Vitals & crawl health",
                    "Indexation, sitemaps, robots & logs",
                    "Schema/structured data & AEO",
                    "Content quality, intent mapping & gaps",
                  ].map((t) => (
                    <li key={t} className="flex items-start gap-2">
                      <span className="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-foreground/60" />
                      <span>{t}</span>
                    </li>
                  ))}
                </ul>
                <ul className="space-y-2 text-sm">
                  {[
                    "Internal linking & information architecture",
                    "Backlink profile & competitive insights",
                    "Local SEO & GBP signals",
                    "Prioritized roadmap with effort & impact",
                  ].map((t) => (
                    <li key={t} className="flex items-start gap-2">
                      <span className="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-foreground/60" />
                      <span>{t}</span>
                    </li>
                  ))}
                </ul>
              </div>
              <div className="mt-5 flex flex-wrap items-center gap-3">
                <Button asChild>
                  <a href={auditHref} aria-label="See sample audit">
                    See Sample Audit
                    <ArrowRight className="ml-2 h-4 w-4" />
                  </a>
                </Button>
                <Button variant="outline" asChild>
                  <a href="/contact" aria-label="Book audit call">Book Audit Call</a>
                </Button>
              </div>
            </CardContent>
          </Card>
        </motion.div>

        {/* Client Dashboard */}
        <motion.div
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, amount: 0.3 }}
          variants={fadeUp}
        >
          <div className="flex items-center gap-3">
            <Gauge className="h-6 w-6" aria-hidden />
            <h3 className="text-xl sm:text-2xl font-semibold">Client Dashboard</h3>
          </div>
          <p className="mt-2 text-muted-foreground max-w-3xl">
            Real-time visibility into rankings, leads, reviews, and campaign progress—centralized and exportable. Transparent reporting with no surprises.
          </p>

          <Card className="mt-5 border-muted/60 shadow-sm">
            <CardContent className="p-5">
              <div className="grid gap-4 sm:grid-cols-3">
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
                  <div key={b.title} className="rounded-2xl border bg-card p-4">
                    <div className="font-medium">{b.title}</div>
                    <p className="mt-1 text-sm text-muted-foreground">{b.desc}</p>
                  </div>
                ))}
              </div>
              <div className="mt-5 flex flex-wrap items-center gap-3">
                <Button asChild>
                  <a href={dashboardHref} aria-label="Open dashboard">
                    Open Dashboard
                    <ArrowRight className="ml-2 h-4 w-4" />
                  </a>
                </Button>
                <Button variant="outline" asChild>
                  <a href="/features/dashboard" aria-label="Explore dashboard features">Explore Features</a>
                </Button>
              </div>
            </CardContent>
          </Card>
        </motion.div>
      </div>
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
 *     { name: 'Starter', tagline: 'Local-first', highlights: ['Citations', 'Speed pass', 'Content plan'], href: '/pricing#starter' },
 *     { name: 'Growth', tagline: 'Scale content', highlights: ['AEO', 'Reviews', 'Digital PR'], href: '/pricing#starter' },
 *     { name: 'Enterprise', tagline: 'Multi-location', highlights: ['Programmatic', 'Advanced schema', 'Custom SLAs'], href: '/pricing#enterprise' },
 *   ]}
 *   auditHref="/services/audit"
 *   dashboardHref="/app/login"
 * />
 */
