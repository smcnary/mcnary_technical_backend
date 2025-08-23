import Link from 'next/link';
import Image from 'next/image';
import { formatDistanceToNow } from 'date-fns';
import { Card } from '@/components/ui/Card';
import type { Post } from '@/lib/types';

interface PostCardProps {
  post: Post;
  featured?: boolean;
}

export function PostCard({ post, featured = false }: PostCardProps) {
  const isPublished = post.status === 'published';
  const hasImage = post.featuredImage;

  return (
    <Card className="group hover:shadow-lg transition-shadow duration-300">
      <Link href={`/blog/${post.slug}`} className="block">
        {hasImage && (
          <div className="aspect-video relative overflow-hidden">
            <Image
              src={post.featuredImage!}
              alt={post.title}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-300"
              sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
            />
            {featured && (
              <div className="absolute top-2 left-2 bg-primary-600 text-white text-xs font-medium px-2 py-1 rounded">
                Featured
              </div>
            )}
          </div>
        )}
        
        <div className="p-6">
          <div className="flex items-center gap-2 text-sm text-gray-500 mb-3">
            <span>By {post.author.displayName}</span>
            <span>•</span>
            <span>{formatDistanceToNow(new Date(post.publishedAt || post.createdAt), { addSuffix: true })}</span>
            {post.readingTime > 0 && (
              <>
                <span>•</span>
                <span>{post.readingTime} min read</span>
              </>
            )}
          </div>
          
          <h3 className="text-xl font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-200 mb-3 line-clamp-2">
            {post.title}
          </h3>
          
          {post.excerpt && (
            <p className="text-gray-600 mb-4 line-clamp-3">
              {post.excerpt}
            </p>
          )}
          
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              {post.primaryCategory && (
                <Link
                  href={`/blog/category/${post.primaryCategory.slug}`}
                  className="text-sm text-primary-600 hover:text-primary-700 font-medium"
                  onClick={(e) => e.stopPropagation()}
                >
                  {post.primaryCategory.name}
                </Link>
              )}
              {post.tags.slice(0, 2).map((tag) => (
                <Link
                  key={tag.id}
                  href={`/blog/tag/${tag.slug}`}
                  className="text-sm text-gray-500 hover:text-gray-700"
                  onClick={(e) => e.stopPropagation()}
                >
                  #{tag.name}
                </Link>
              ))}
            </div>
            
            {!isPublished && (
              <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                {post.status}
              </span>
            )}
          </div>
        </div>
      </Link>
    </Card>
  );
}
