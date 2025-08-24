import Link from 'next/link';
import type { Tag } from '@/lib/types';

interface TagCloudProps {
  tags?: Tag[];
  limit?: number;
  showCount?: boolean;
}

export function TagCloud({ tags = [], limit, showCount = false }: TagCloudProps) {
  const displayTags = limit ? tags.slice(0, limit) : tags;

  if (displayTags.length === 0) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500">No tags available.</p>
      </div>
    );
  }

  return (
    <div className="flex flex-wrap gap-2">
      {displayTags.map((tag) => (
        <Link
          key={tag.id}
          href={`/blog/tag/${tag.slug}`}
          className="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-200"
          style={{
            backgroundColor: tag.color ? `${tag.color}20` : '#f3f4f6',
            color: tag.color || '#374151',
            border: tag.color ? `1px solid ${tag.color}40` : '1px solid #e5e7eb',
          }}
        >
          #{tag.name}
          {showCount && (
            <span className="ml-1.5 text-xs opacity-75">
              {/* TODO: Add post count when available */}
            </span>
          )}
        </Link>
      ))}
      
      {limit && tags.length > limit && (
        <div className="w-full text-center pt-4">
          <Link
            href="/tags"
            className="text-primary-600 hover:text-primary-700 font-medium text-sm"
          >
            View all tags →
          </Link>
        </div>
      )}
    </div>
  );
}
