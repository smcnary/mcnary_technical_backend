import { PostCard } from './PostCard';
import type { Post } from '@/lib/types';

interface PostListProps {
  posts: Post[];
  featured?: boolean;
  limit?: number;
  showPagination?: boolean;
}

export function PostList({ posts, featured = false, limit, showPagination = false }: PostListProps) {
  const displayPosts = limit ? posts.slice(0, limit) : posts;

  if (posts.length === 0) {
    return (
      <div className="text-center py-12">
        <h3 className="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
        <p className="text-gray-500">Check back later for new content.</p>
      </div>
    );
  }

  return (
    <div>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {displayPosts.map((post) => (
          <PostCard
            key={post.id}
            post={post}
            featured={featured}
          />
        ))}
      </div>
      
      {showPagination && posts.length > (limit || 0) && (
        <div className="mt-12 flex justify-center">
          <nav className="flex items-center space-x-2">
            <button className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
              Previous
            </button>
            <span className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">
              Page 1 of 1
            </span>
            <button className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
              Next
            </button>
          </nav>
        </div>
      )}
    </div>
  );
}
