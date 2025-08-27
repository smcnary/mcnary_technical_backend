
import Link from "next/link";
import { Menu, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";

/**
 * NavAuditFirst
 * Simplified, audit-focused header for Tulsa-SEO
 * Tech: Next.js/React + Tailwind + shadcn/ui
 */
interface NavAuditFirstProps {
  logo?: React.ReactNode;
  auditHref?: string;      // primary CTA
  pricingHref?: string;    // secondary link
  howItWorksHref?: string; // secondary link
  loginHref?: string;      // client login
}

export default function NavAuditFirst({
  logo = (
    <div className="flex items-center gap-2">
      <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-primary/10 text-primary font-semibold">
        TS
      </div>
      <span className="text-base sm:text-lg font-semibold tracking-tight">tulsa-seo.com</span>
    </div>
  ),
  auditHref = "/services/audit",
  pricingHref = "/pricing",
  howItWorksHref = "#how-it-works",
  loginHref = "/app/login",
}: NavAuditFirstProps) {
  return (
    <header className="sticky top-0 z-40 w-full border-b bg-background/80 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:h-16 sm:px-6 lg:px-8">
        {/* Left: Logo */}
        <Link href="/" aria-label="Go to homepage" className="shrink-0">
          {logo}
        </Link>

        {/* Center: minimal links (desktop) */}
        <nav className="hidden items-center gap-6 md:flex">
          <Link href={howItWorksHref} className="text-sm text-muted-foreground hover:text-foreground">
            How it works
          </Link>
          <Link href={pricingHref} className="text-sm text-muted-foreground hover:text-foreground">
            Pricing
          </Link>
          <Link href={loginHref} className="text-sm text-muted-foreground hover:text-foreground">
            Client Login
          </Link>
        </nav>

        {/* Right: Primary CTA (desktop) */}
        <div className="hidden md:block">
          <Button asChild size="default" className="shadow-sm">
            <Link href={auditHref} aria-label="Start free SEO audit">
              Start Free Audit
              <ArrowRight className="ml-2 h-4 w-4" />
            </Link>
          </Button>
        </div>

        {/* Mobile: menu + CTA */}
        <div className="flex items-center gap-2 md:hidden">
          <Button asChild size="sm">
            <Link href={auditHref} aria-label="Start free SEO audit">
              Audit
              <ArrowRight className="ml-1.5 h-4 w-4" />
            </Link>
          </Button>

          <Sheet>
            <SheetTrigger asChild>
              <Button variant="ghost" size="icon" aria-label="Open menu">
                <Menu className="h-5 w-5" />
              </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-80">
              <SheetHeader>
                <SheetTitle>Menu</SheetTitle>
              </SheetHeader>
              <div className="mt-4 grid gap-3">
                <Link href={howItWorksHref} className="text-sm">How it works</Link>
                <Link href={pricingHref} className="text-sm">Pricing</Link>
                <Link href={loginHref} className="text-sm">Client Login</Link>
                <Button asChild className="mt-2">
                  <Link href={auditHref}>
                    Start Free Audit
                    <ArrowRight className="ml-2 h-4 w-4" />
                  </Link>
                </Button>
              </div>
            </SheetContent>
          </Sheet>
        </div>
      </div>
    </header>
  );
}

/**
 * Example usage:
 *
 * <NavAuditFirst
 *   auditHref="/services/audit"
 *   pricingHref="/pricing"
 *   howItWorksHref="#how-it-works"
 *   loginHref="/app/login"
 * />
 */
