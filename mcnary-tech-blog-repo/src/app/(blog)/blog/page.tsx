import { Suspense } from 'react';
import { PostList } from '@/components/blog/PostList';
import { CategoryList } from '@/components/blog/CategoryList';
import { TagCloud } from '@/components/blog/TagCloud';
import { getPosts, getCategories, getTags } from '@/lib/api';

export const metadata = {
  title: 'Blog',
  description: 'Latest insights and tutorials from the McNary Tech team',
};

async function BlogContent() {
  try {
    const [posts, categories, tags] = await Promise.all([
      getPosts({ status: 'published', sortBy: 'date', sortOrder: 'desc' }),
      getCategories(),
      getTags(),
    ]);

    return (
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Discover insights, tutorials, and best practices in technology, 
            development, and software engineering.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-3">
            <PostList posts={posts} showPagination={true} />
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-1 space-y-8">
            {/* Categories */}
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
              <CategoryList categories={categories} limit={8} />
            </div>

            {/* Tags */}
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Popular Tags</h3>
              <TagCloud tags={tags} limit={20} />
            </div>
          </div>
        </div>
      </div>
    );
  } catch (error) {
    console.warn('API not available during build, showing empty state:', error);
    
    return (
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Discover insights, tutorials, and best practices in technology, 
            development, and software engineering.
          </p>
        </div>

        <div className="text-center py-12">
          <h3 className="text-lg font-medium text-gray-900 mb-2">No posts available</h3>
          <p className="text-gray-500">Check back later for new content.</p>
        </div>
      </div>
    );
  }
}

export default function BlogPage() {
  return (
    <Suspense fallback={
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="text-center">
          <div className="animate-pulse">
            <div className="h-12 bg-gray-200 rounded mb-4"></div>
            <div className="h-6 bg-gray-200 rounded max-w-2xl mx-auto"></div>
          </div>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
          {[...Array(6)].map((_, i) => (
            <div key={i} className="animate-pulse">
              <div className="h-48 bg-gray-200 rounded mb-4"></div>
              <div className="h-4 bg-gray-200 rounded mb-2"></div>
              <div className="h-4 bg-gray-200 rounded mb-2"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
          ))}
        </div>
      </div>
    }>
      <BlogContent />
    </Suspense>
  );
}
