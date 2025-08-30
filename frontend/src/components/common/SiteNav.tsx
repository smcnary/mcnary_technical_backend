"use client";
import { useEffect, useState } from "react";

// If using Next.js, you can swap <a> for next/link

export default function SiteNav() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 4);
    onScroll();
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const baseBar =
    "sticky top-0 z-50 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-black/30 bg-white/90 dark:bg-black/50";
  const shadow = scrolled ? "shadow-[0_1px_0_0_rgba(255,255,255,0.08)_inset] dark:shadow-[0_1px_0_0_rgba(255,255,255,0.08)_inset]" : "";

  return (
    <header className={`${baseBar} ${shadow}`}>
      <nav className="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-3 md:px-6">
        {/* Brand */}
        <a href="/" className="group flex items-center gap-3">
          <span className="flex h-8 w-8 items-center justify-center rounded-full bg-black text-white dark:bg-white dark:text-black text-sm font-bold ring-1 ring-black/10 dark:ring-white/20">TS</span>
          <span className="font-semibold tracking-tight text-black/90 dark:text-white">tulsa-seo.com</span>
        </a>

        {/* Desktop links */}
        <div className="hidden items-center gap-6 md:flex">
          <a href="#how" className="text-sm text-black/70 hover:text-black dark:text-white/70 dark:hover:text-white">How it works</a>
          <a href="/pricing" className="text-sm text-black/70 hover:text-black dark:text-white/70 dark:hover:text-white">Pricing</a>
          <a href="/client" className="text-sm text-black/70 hover:text-black dark:text-white/70 dark:hover:text-white">Client Login</a>
        </div>

        {/* CTA */}
        <div className="hidden md:block">
          <a
            href="/services/audit"
            className="inline-flex items-center gap-2 rounded-xl bg-black px-4 py-2 text-sm font-medium text-white shadow hover:bg-zinc-900 dark:bg-white dark:text-black dark:hover:bg-zinc-100"
          >
            Start Free Audit <span aria-hidden>→</span>
          </a>
        </div>

        {/* Mobile toggle */}
        <button
          className="md:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg border border-black/10 bg-white text-black dark:border-white/10 dark:bg-white/10 dark:text-white"
          onClick={() => setMobileOpen((v) => !v)}
          aria-label="Toggle menu"
        >
          {/* hamburger */}
          <span className="relative block h-4 w-4">
            <span className={`absolute inset-x-0 top-0 h-0.5 ${mobileOpen ? "translate-y-1.5 rotate-45" : ""} bg-current transition`}></span>
            <span className={`absolute inset-x-0 top-1.5 h-0.5 ${mobileOpen ? "opacity-0" : ""} bg-current transition`}></span>
            <span className={`absolute inset-x-0 top-3 h-0.5 ${mobileOpen ? "-translate-y-1.5 -rotate-45" : ""} bg-current transition`}></span>
          </span>
        </button>
      </nav>

      {/* Mobile sheet */}
      {mobileOpen && (
        <div className="border-t border-black/10 bg-white/95 px-4 py-4 backdrop-blur dark:border-white/10 dark:bg-black/80 md:hidden">
          <div className="flex flex-col gap-3">
            <a href="#how" className="rounded-lg px-3 py-2 text-black/80 hover:bg-black/5 dark:text-white/80 dark:hover:bg-white/5">How it works</a>
            <a href="/pricing" className="rounded-lg px-3 py-2 text-black/80 hover:bg-black/5 dark:text-white/80 dark:hover:bg-white/5">Pricing</a>
            <a href="/client" className="rounded-lg px-3 py-2 text-black/80 hover:bg-black/5 dark:text-white/80 dark:hover:bg-white/5">Client Login</a>
            <a href="/services/audit" className="mt-1 inline-flex items-center justify-center gap-2 rounded-xl bg-black px-4 py-2 text-sm font-medium text-white hover:bg-zinc-900 dark:bg-white dark:text-black dark:hover:bg-zinc-100">Start Free Audit →</a>
          </div>
        </div>
      )}

      {/* subtle separator over dark hero */}
      <div className="h-px w-full bg-gradient-to-r from-transparent via-black/10 to-transparent dark:via-white/10" />
    </header>
  );
}
