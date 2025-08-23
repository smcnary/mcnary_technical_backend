import { notFound } from 'next/navigation';
import { formatDistanceToNow } from 'date-fns';
import { getPost, getPosts } from '@/lib/api';
import { PostCard } from '@/components/blog/PostCard';
import type { Metadata } from 'next';

interface BlogPostPageProps {
  params: { slug: string };
}

export async function generateStaticParams() {
  try {
    const posts = await getPosts({ status: 'published' });
    
    return posts.map((post) => ({
      slug: post.slug,
    }));
  } catch (error) {
    // During build time, if the API is not available, return empty array
    console.warn('API not available during build, skipping static generation:', error);
    return [];
  }
}

export async function generateMetadata({ params }: BlogPostPageProps): Promise<Metadata> {
  try {
    const post = await getPost(params.slug);
    
    if (!post) {
      return {
        title: 'Post Not Found',
      };
    }

    return {
      title: post.title,
      description: post.excerpt || post.title,
      openGraph: {
        title: post.title,
        description: post.excerpt || post.title,
        type: 'article',
        publishedTime: post.publishedAt,
        modifiedTime: post.updatedAt,
        authors: [post.author.displayName],
        images: post.featuredImage ? [post.featuredImage] : [],
      },
      twitter: {
        card: 'summary_large_image',
        title: post.title,
        description: post.excerpt || post.title,
        images: post.featuredImage ? [post.featuredImage] : [],
      },
    };
  } catch (error) {
    console.warn('Failed to generate metadata:', error);
    return {
      title: 'Blog Post',
      description: 'Blog post from McNary Tech',
    };
  }
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  try {
    const post = await getPost(params.slug);
    
    if (!post) {
      notFound();
    }

    // Get related posts
    const relatedPosts = await getPosts({
      status: 'published',
      category: post.primaryCategory?.slug,
      limit: 3,
    });

    return (
      <article className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {/* Header */}
        <header className="mb-8">
          <div className="mb-4">
            {post.primaryCategory && (
              <a
                href={`/blog/category/${post.primaryCategory.slug}`}
                className="text-primary-600 hover:text-primary-700 font-medium text-sm"
              >
                {post.primaryCategory.name}
              </a>
            )}
          </div>
          
          <h1 className="text-4xl font-bold text-gray-900 mb-4 leading-tight">
            {post.title}
          </h1>
          
          {post.excerpt && (
            <p className="text-xl text-gray-600 mb-6 leading-relaxed">
              {post.excerpt}
            </p>
          )}
          
          <div className="flex items-center gap-4 text-sm text-gray-500 mb-6">
            <div className="flex items-center gap-2">
              <span>By {post.author.displayName}</span>
            </div>
            <span>•</span>
            <span>
              {formatDistanceToNow(new Date(post.publishedAt || post.createdAt), { addSuffix: true })}
            </span>
            {post.readingTime > 0 && (
              <>
                <span>•</span>
                <span>{post.readingTime} min read</span>
              </>
            )}
          </div>
          
          {post.featuredImage && (
            <div className="aspect-video relative overflow-hidden rounded-lg mb-8">
              <img
                src={post.featuredImage}
                alt={post.title}
                className="w-full h-full object-cover"
              />
            </div>
          )}
        </header>

        {/* Content */}
        <div className="prose prose-lg max-w-none mb-12">
          <div dangerouslySetInnerHTML={{ __html: post.content }} />
        </div>

        {/* Tags */}
        {post.tags.length > 0 && (
          <div className="mb-8">
            <h3 className="text-sm font-semibold text-gray-900 mb-3">Tags:</h3>
            <div className="flex flex-wrap gap-2">
              {post.tags.map((tag) => (
                <a
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
                </a>
              ))}
            </div>
          </div>
        )}

        {/* Author Bio */}
        <div className="border-t border-gray-200 pt-8 mb-12">
          <div className="flex items-start gap-4">
            {post.author.avatarUrl && (
              <img
                src={post.author.avatarUrl}
                alt={post.author.displayName}
                className="w-16 h-16 rounded-full"
              />
            )}
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">
                About {post.author.displayName}
              </h3>
              {post.author.bio && (
                <p className="text-gray-600 mb-3">{post.author.bio}</p>
              )}
              {post.author.socialLinks && (
                <div className="flex gap-3">
                  {Object.entries(post.author.socialLinks).map(([platform, url]) => (
                    <a
                      key={platform}
                      href={url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-gray-400 hover:text-primary-600 transition-colors duration-200"
                    >
                      {platform}
                    </a>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Related Posts */}
        {relatedPosts.length > 0 && (
          <div className="border-t border-gray-200 pt-8">
            <h3 className="text-2xl font-bold text-gray-900 mb-6">Related Posts</h3>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {relatedPosts
                .filter((relatedPost) => relatedPost.id !== post.id)
                .slice(0, 3)
                .map((relatedPost) => (
                  <PostCard key={relatedPost.id} post={relatedPost} />
                ))}
            </div>
          </div>
        )}
      </article>
    );
  } catch (error) {
    console.error('Failed to load blog post:', error);
    notFound();
  }
}
