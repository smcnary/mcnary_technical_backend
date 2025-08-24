import Link from 'next/link';
import { Card } from '@/components/ui/Card';
import type { Category } from '@/lib/types';

interface CategoryListProps {
  categories?: Category[];
  limit?: number;
}

export function CategoryList({ categories = [], limit }: CategoryListProps) {
  const displayCategories = limit ? categories.slice(0, limit) : categories;

  if (displayCategories.length === 0) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500">No categories available.</p>
      </div>
    );
  }

  return (
    <div className="space-y-3">
      {displayCategories.map((category) => (
        <Card key={category.id} className="p-4 hover:shadow-md transition-shadow duration-200">
          <Link href={`/blog/category/${category.slug}`} className="block">
            <div className="flex items-center justify-between">
              <div>
                <h4 className="font-medium text-gray-900 hover:text-primary-600 transition-colors duration-200">
                  {category.name}
                </h4>
                {category.description && (
                  <p className="text-sm text-gray-500 mt-1 line-clamp-2">
                    {category.description}
                  </p>
                )}
              </div>
              <div className="text-primary-600">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </Link>
        </Card>
      ))}
      
      {limit && categories.length > limit && (
        <div className="text-center pt-4">
          <Link
            href="/categories"
            className="text-primary-600 hover:text-primary-700 font-medium text-sm"
          >
            View all categories →
          </Link>
        </div>
      )}
    </div>
  );
}
