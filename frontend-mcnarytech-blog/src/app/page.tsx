import Link from 'next/link';
import { Button } from '@/components/ui/Button';
import { PostList } from '@/components/blog/PostList';
import { CategoryList } from '@/components/blog/CategoryList';
import { TagCloud } from '@/components/blog/TagCloud';
import type { Post, Category, Tag } from '@/lib/types';

export default function HomePage() {
  // Mock data for now - in production this would come from the API
  const mockPosts: Post[] = [];
  const mockCategories: Category[] = [];
  const mockTags: Tag[] = [];

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-br from-primary-50 to-primary-100 py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
              Welcome to{' '}
              <span className="text-primary-600">McNary Tech Blog</span>
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-3xl mx-auto">
              Discover insights, tutorials, and best practices in technology, 
              development, and software engineering from our expert team.
            </p>
            <div className="mt-10 flex justify-center gap-4">
              <Button asChild>
                <Link href="/blog">Read Our Blog</Link>
              </Button>
              <Button variant="outline" asChild>
                <Link href="/about">Learn More</Link>
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Posts */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900">Featured Posts</h2>
            <p className="mt-4 text-lg text-gray-600">
              Our latest insights and tutorials
            </p>
          </div>
          <PostList posts={mockPosts} featured={true} limit={6} />
        </div>
      </section>

      {/* Categories & Tags */}
      <section className="py-16 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
              <h3 className="text-2xl font-bold text-gray-900 mb-6">Categories</h3>
              <CategoryList categories={mockCategories} />
            </div>
            <div>
              <h3 className="text-2xl font-bold text-gray-900 mb-6">Popular Tags</h3>
              <TagCloud tags={mockTags} />
            </div>
          </div>
        </div>
      </section>

      {/* Newsletter Signup */}
      <section className="py-16 bg-primary-600">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-bold text-white mb-4">
            Stay Updated
          </h2>
          <p className="text-xl text-primary-100 mb-8">
            Get the latest posts and insights delivered to your inbox
          </p>
          <div className="max-w-md mx-auto">
            <form className="flex gap-3">
              <input
                type="email"
                placeholder="Enter your email"
                className="flex-1 px-4 py-3 rounded-md border-0 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600"
                required
              />
              <Button type="submit" variant="secondary">
                Subscribe
              </Button>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
}
