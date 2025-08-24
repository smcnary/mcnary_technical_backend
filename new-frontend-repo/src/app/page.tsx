import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { ArrowRight, Sparkles, Zap, Shield, Users } from 'lucide-react';

export default function HomePage() {
  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-hero py-20">
        <div className="absolute inset-0 bg-black/20" />
        <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm">
              <Sparkles className="mr-2 h-4 w-4" />
              Next Generation Frontend
            </div>
            
            <h1 className="mt-8 text-5xl font-bold tracking-tight text-white sm:text-6xl lg:text-7xl">
              Build Something
              <span className="block bg-gradient-to-r from-accent-400 to-primary-400 bg-clip-text text-transparent">
                Amazing
              </span>
            </h1>
            
            <p className="mt-6 text-xl text-gray-200 sm:text-2xl">
              A modern, scalable frontend application built with Next.js, TypeScript, and Tailwind CSS.
              Ready for production with best practices and modern tooling.
            </p>
            
            <div className="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
              <Button size="lg" className="group">
                Get Started
                <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
              </Button>
              <Button variant="outline" size="lg" className="bg-white/10 text-white border-white/20 hover:bg-white/20">
                Learn More
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
              Everything you need to build modern web applications
            </h2>
            <p className="mt-4 text-lg text-gray-600">
              Built with the latest technologies and best practices for optimal performance and developer experience.
            </p>
          </div>
          
          <div className="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <Card className="text-center">
              <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100">
                <Zap className="h-6 w-6 text-primary-600" />
              </div>
              <h3 className="mt-4 text-lg font-semibold">Lightning Fast</h3>
              <p className="mt-2 text-gray-600">
                Built with Next.js 14 and optimized for performance with automatic code splitting and lazy loading.
              </p>
            </Card>
            
            <Card className="text-center">
              <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100">
                <Shield className="h-6 w-6 text-secondary-600" />
              </div>
              <h3 className="mt-4 text-lg font-semibold">Type Safe</h3>
              <p className="mt-2 text-gray-600">
                Full TypeScript support with strict type checking and comprehensive type definitions.
              </p>
            </Card>
            
            <Card className="text-center">
              <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-accent-100">
                <Users className="h-6 w-6 text-accent-600" />
              </div>
              <h3 className="mt-4 text-lg font-semibold">Developer Experience</h3>
              <p className="mt-2 text-gray-600">
                Modern tooling with ESLint, Prettier, and Tailwind CSS for a delightful development experience.
              </p>
            </Card>
          </div>
        </div>
      </section>
    </div>
  );
}
