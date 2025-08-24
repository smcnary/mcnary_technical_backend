# Technical Specification — McNary Tech Blog

**Product Name:** McNary Tech Blog  
**Owner:** McNary Technical Backend Team  
**Tech Stack:** Next.js 14+ (React 18, TypeScript), Symfony 7.3 Backend API, PostgreSQL 16, Redis, Docker  
**Architecture:** Headless CMS with JAMstack frontend, Multi-tenant SaaS platform  

---

## 1) Purpose & Goals

Create a high-performance, SEO-optimized blog platform that leverages the existing McNary backend infrastructure. The blog must be:

* **SEO-First**: Built with search engine optimization as a primary concern
* **Performance-Optimized**: Lighthouse scores >90 across all metrics
* **Multi-Tenant**: Support multiple blog instances with tenant isolation
* **Developer-Friendly**: Modern React/TypeScript with excellent DX
* **Scalable**: Handle high traffic with CDN and caching strategies
* **Accessible**: WCAG 2.1 AA compliance

**Primary Outputs:** Blog posts, category pages, tag pages, author profiles, search functionality, RSS feeds, sitemaps.

---

## 2) High-Level Architecture

### Frontend (Next.js 14+)
* **App Router**: Using Next.js 14 App Router with React Server Components
* **Static Generation**: Pre-render blog content at build time for optimal SEO
* **Incremental Static Regeneration**: Update content without full rebuilds
* **Edge Runtime**: Deploy to edge locations for global performance

### Backend Integration
* **API Platform**: Leverage existing Symfony API Platform for content management
* **JWT Authentication**: Secure admin access with existing Lexik JWT bundle
* **Multi-Tenancy**: Tenant isolation through existing backend architecture
* **Content API**: RESTful endpoints for posts, categories, tags, and SEO metadata

### Data Layer
* **PostgreSQL**: Primary content storage with existing entities
* **Redis**: Caching layer for API responses and session management
* **CDN**: Static asset delivery and edge caching

---

## 3) Database Schema & API Integration

### Existing Backend Entities (Leveraged)
```sql
-- Posts table (existing)
posts (
    id UUID PRIMARY KEY,
    tenant_id UUID NOT NULL,
    site_id UUID NOT NULL,
    author_id UUID,
    title VARCHAR NOT NULL,
    slug VARCHAR NOT NULL,
    status VARCHAR DEFAULT 'draft',
    excerpt TEXT,
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);

-- Categories table (existing)
categories (
    id UUID PRIMARY KEY,
    tenant_id UUID,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'active',
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);

-- Tags table (existing)
tags (
    id UUID PRIMARY KEY,
    tenant_id UUID,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    color VARCHAR(7),
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);

-- SEO Metadata table (existing)
seo_meta (
    id UUID PRIMARY KEY,
    tenant_id UUID NOT NULL,
    entity_type VARCHAR NOT NULL,
    entity_id UUID NOT NULL,
    title VARCHAR,
    meta_description TEXT,
    canonical_url VARCHAR,
    robots VARCHAR,
    open_graph JSONB,
    twitter_card JSONB,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);
```

### New Blog-Specific Entities (To Be Added)
```sql
-- Blog post content (rich text, markdown)
post_content (
    id UUID PRIMARY KEY,
    post_id UUID NOT NULL REFERENCES posts(id),
    content_type VARCHAR(20) DEFAULT 'markdown', -- markdown, html, rich_text
    content TEXT NOT NULL,
    word_count INTEGER,
    reading_time_minutes INTEGER,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);

-- Post tags relationship
post_tags (
    post_id UUID NOT NULL REFERENCES posts(id),
    tag_id UUID NOT NULL REFERENCES tags(id),
    PRIMARY KEY (post_id, tag_id)
);

-- Post categories relationship
post_categories (
    post_id UUID NOT NULL REFERENCES posts(id),
    category_id UUID NOT NULL REFERENCES categories(id),
    is_primary BOOLEAN DEFAULT false,
    PRIMARY KEY (post_id, category_id)
);

-- Blog authors
blog_authors (
    id UUID PRIMARY KEY,
    tenant_id UUID NOT NULL,
    user_id UUID REFERENCES users(id),
    display_name VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(500),
    social_links JSONB,
    seo_title VARCHAR(255),
    seo_description TEXT,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);

-- Blog comments
blog_comments (
    id UUID PRIMARY KEY,
    post_id UUID NOT NULL REFERENCES posts(id),
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) NOT NULL,
    author_website VARCHAR(500),
    content TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, spam
    parent_id UUID REFERENCES blog_comments(id),
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL
);
```

---

## 4) Frontend Architecture (Next.js 14+)

### Project Structure
```
frontend-mcnarytech-blog/
├── src/
│   ├── app/                    # App Router
│   │   ├── (blog)/            # Blog route group
│   │   │   ├── page.tsx       # Blog home
│   │   │   ├── [slug]/        # Individual post
│   │   │   │   └── page.tsx
│   │   │   ├── category/      # Category pages
│   │   │   │   └── [slug]/
│   │   │   │       └── page.tsx
│   │   │   ├── tag/           # Tag pages
│   │   │   │   └── [slug]/
│   │   │   │       └── page.tsx
│   │   │   └── author/        # Author pages
│   │   │       └── [slug]/
│   │   │           └── page.tsx
│   │   ├── (admin)/           # Admin route group
│   │   │   ├── dashboard/
│   │   │   ├── posts/
│   │   │   ├── categories/
│   │   │   └── tags/
│   │   ├── api/                # API routes
│   │   │   ├── auth/
│   │   │   ├── posts/
│   │   │   ├── search/
│   │   │   └── sitemap/
│   │   ├── globals.css
│   │   ├── layout.tsx
│   │   └── page.tsx
│   ├── components/
│   │   ├── ui/                 # Reusable UI components
│   │   │   ├── Button.tsx
│   │   │   ├── Card.tsx
│   │   │   ├── Input.tsx
│   │   │   └── ...
│   │   ├── blog/               # Blog-specific components
│   │   │   ├── PostCard.tsx
│   │   │   ├── PostList.tsx
│   │   │   ├── CategoryList.tsx
│   │   │   ├── TagCloud.tsx
│   │   │   ├── SearchBar.tsx
│   │   │   ├── Pagination.tsx
│   │   │   └── ...
│   │   ├── layout/             # Layout components
│   │   │   ├── Header.tsx
│   │   │   ├── Footer.tsx
│   │   │   ├── Sidebar.tsx
│   │   │   └── Navigation.tsx
│   │   └── admin/              # Admin components
│   │       ├── PostEditor.tsx
│   │       ├── CategoryManager.tsx
│   │       └── ...
│   ├── lib/
│   │   ├── api.ts              # API client
│   │   ├── auth.ts             # Authentication utilities
│   │   ├── seo.ts              # SEO utilities
│   │   ├── utils.ts            # Utility functions
│   │   └── types.ts            # TypeScript types
│   ├── hooks/
│   │   ├── usePosts.ts         # Posts data hook
│   │   ├── useCategories.ts    # Categories hook
│   │   ├── useSearch.ts        # Search functionality
│   │   └── ...
│   └── styles/
│       ├── components.css      # Component styles
│       └── utilities.css       # Utility classes
├── public/
│   ├── images/
│   ├── favicon.ico
│   └── robots.txt
├── next.config.js
├── tailwind.config.js
├── tsconfig.json
├── package.json
└── README.md
```

### Key Technologies & Dependencies
```json
{
  "dependencies": {
    "next": "^14.0.0",
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "typescript": "^5.0.0",
    "@types/node": "^20.0.0",
    "@types/react": "^18.2.0",
    "@types/react-dom": "^18.2.0",
    "tailwindcss": "^3.3.0",
    "autoprefixer": "^10.4.0",
    "postcss": "^8.4.0",
    "@tailwindcss/typography": "^0.5.0",
    "@tailwindcss/forms": "^0.5.0",
    "clsx": "^2.0.0",
    "date-fns": "^2.30.0",
    "react-markdown": "^9.0.0",
    "remark-gfm": "^4.0.0",
    "rehype-highlight": "^7.0.0",
    "rehype-slug": "^6.0.0",
    "rehype-autolink-headings": "^7.0.0",
    "next-seo": "^6.4.0",
    "next-sitemap": "^4.2.0",
    "next-rss": "^1.4.0",
    "framer-motion": "^10.16.0",
    "lucide-react": "^0.294.0",
    "axios": "^1.6.0",
    "swr": "^2.2.0"
  },
  "devDependencies": {
    "@typescript-eslint/eslint-plugin": "^6.0.0",
    "@typescript-eslint/parser": "^6.0.0",
    "eslint": "^8.0.0",
    "eslint-config-next": "^14.0.0",
    "prettier": "^3.0.0",
    "prettier-plugin-tailwindcss": "^0.5.0"
  }
}
```

---

## 5) SEO Implementation Strategy

### Meta Tags & Structured Data
```typescript
// lib/seo.ts
export interface SEOConfig {
  title: string;
  description: string;
  canonical?: string;
  openGraph?: {
    title?: string;
    description?: string;
    image?: string;
    type?: string;
    url?: string;
  };
  twitter?: {
    card?: string;
    title?: string;
    description?: string;
    image?: string;
  };
  structuredData?: object;
}

export function generateSEO(config: SEOConfig): JSX.Element {
  return (
    <>
      <title>{config.title}</title>
      <meta name="description" content={config.description} />
      {config.canonical && <link rel="canonical" href={config.canonical} />}
      
      {/* Open Graph */}
      <meta property="og:title" content={config.openGraph?.title || config.title} />
      <meta property="og:description" content={config.openGraph?.description || config.description} />
      <meta property="og:type" content={config.openGraph?.type || 'website'} />
      {config.openGraph?.image && <meta property="og:image" content={config.openGraph.image} />}
      
      {/* Twitter Card */}
      <meta name="twitter:card" content={config.twitter?.card || 'summary_large_image'} />
      <meta name="twitter:title" content={config.twitter?.title || config.title} />
      <meta name="twitter:description" content={config.twitter?.description || config.description} />
      
      {/* Structured Data */}
      {config.structuredData && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(config.structuredData),
          }}
        />
      )}
    </>
  );
}
```

### Blog Post Structured Data
```typescript
export function generateBlogPostStructuredData(post: BlogPost): object {
  return {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": post.title,
    "description": post.excerpt,
    "author": {
      "@type": "Person",
      "name": post.author.displayName,
      "url": `/author/${post.author.slug}`
    },
    "publisher": {
      "@type": "Organization",
      "name": "McNary Tech",
      "logo": {
        "@type": "ImageObject",
        "url": "https://mcnarytech.com/logo.png"
      }
    },
    "datePublished": post.publishedAt,
    "dateModified": post.updatedAt,
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": `https://mcnarytech.com/blog/${post.slug}`
    },
    "image": post.featuredImage,
    "articleSection": post.primaryCategory?.name,
    "keywords": post.tags.map(tag => tag.name).join(', ')
  };
}
```

### Sitemap Generation
```typescript
// app/api/sitemap/route.ts
import { getPosts, getCategories, getTags } from '@/lib/api';

export async function GET() {
  const posts = await getPosts({ status: 'published' });
  const categories = await getCategories();
  const tags = await getTags();
  
  const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
        <loc>https://mcnarytech.com/blog</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
      </url>
      ${posts.map(post => `
        <url>
          <loc>https://mcnarytech.com/blog/${post.slug}</loc>
          <lastmod>${post.updatedAt}</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.8</priority>
        </url>
      `).join('')}
      ${categories.map(category => `
        <url>
          <loc>https://mcnarytech.com/blog/category/${category.slug}</loc>
          <changefreq>weekly</changefreq>
          <priority>0.6</priority>
        </url>
      `).join('')}
    </urlset>`;
  
  return new Response(sitemap, {
    headers: {
      'Content-Type': 'application/xml',
    },
  });
}
```

### RSS Feed Generation
```typescript
// app/api/feed/route.ts
import { getPosts } from '@/lib/api';
import RSS from 'rss';

export async function GET() {
  const posts = await getPosts({ status: 'published', limit: 50 });
  
  const feed = new RSS({
    title: 'McNary Tech Blog',
    description: 'Latest insights on technology, development, and innovation',
    feed_url: 'https://mcnarytech.com/api/feed',
    site_url: 'https://mcnarytech.com',
    image_url: 'https://mcnarytech.com/logo.png',
    managingEditor: 'editor@mcnarytech.com',
    webMaster: 'webmaster@mcnarytech.com',
    copyright: '2024 McNary Tech',
    language: 'en',
    pubDate: new Date().toUTCString(),
    ttl: '60'
  });
  
  posts.forEach(post => {
    feed.item({
      title: post.title,
      description: post.excerpt,
      url: `https://mcnarytech.com/blog/${post.slug}`,
      guid: post.id,
      categories: post.categories.map(cat => cat.name),
      author: post.author.displayName,
      date: post.publishedAt
    });
  });
  
  return new Response(feed.xml(), {
    headers: {
      'Content-Type': 'application/xml',
    },
  });
}
```

---

## 6) Performance Optimization

### Static Generation Strategy
```typescript
// app/blog/[slug]/page.tsx
export async function generateStaticParams() {
  const posts = await getPosts({ status: 'published' });
  
  return posts.map((post) => ({
    slug: post.slug,
  }));
}

export async function generateMetadata({ params }: { params: { slug: string } }) {
  const post = await getPost(params.slug);
  
  return {
    title: post.title,
    description: post.excerpt,
    openGraph: {
      title: post.title,
      description: post.excerpt,
      image: post.featuredImage,
      type: 'article',
    },
    twitter: {
      card: 'summary_large_image',
      title: post.title,
      description: post.excerpt,
      image: post.featuredImage,
    },
  };
}

export default async function BlogPost({ params }: { params: { slug: string } }) {
  const post = await getPost(params.slug);
  
  return (
    <article className="prose prose-lg max-w-4xl mx-auto">
      <h1>{post.title}</h1>
      <div className="meta">
        <span>By {post.author.displayName}</span>
        <span>{formatDate(post.publishedAt)}</span>
        <span>{post.readingTime} min read</span>
      </div>
      <div dangerouslySetInnerHTML={{ __html: post.content }} />
    </article>
  );
}
```

### Image Optimization
```typescript
// components/blog/PostImage.tsx
import Image from 'next/image';

interface PostImageProps {
  src: string;
  alt: string;
  width: number;
  height: number;
  priority?: boolean;
}

export function PostImage({ src, alt, width, height, priority = false }: PostImageProps) {
  return (
    <Image
      src={src}
      alt={alt}
      width={width}
      height={height}
      priority={priority}
      className="rounded-lg shadow-lg"
      sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
    />
  );
}
```

### Caching Strategy
```typescript
// lib/api.ts
import { unstable_cache } from 'next/cache';

export const getPosts = unstable_cache(
  async (params: GetPostsParams = {}) => {
    const response = await fetch(`${process.env.API_URL}/posts?${new URLSearchParams(params)}`);
    return response.json();
  },
  ['posts'],
  {
    revalidate: 3600, // 1 hour
    tags: ['posts'],
  }
);

export const getPost = unstable_cache(
  async (slug: string) => {
    const response = await fetch(`${process.env.API_URL}/posts?slug=${slug}`);
    const posts = await response.json();
    return posts[0];
  },
  ['post'],
  {
    revalidate: 7200, // 2 hours
    tags: ['post'],
  }
);
```

---

## 7) Multi-Tenancy Implementation

### Tenant Context
```typescript
// lib/tenant.ts
export interface Tenant {
  id: string;
  name: string;
  domain: string;
  subdomain?: string;
  settings: {
    blogTitle: string;
    blogDescription: string;
    logo: string;
    primaryColor: string;
    allowComments: boolean;
    moderationRequired: boolean;
  };
}

export function getTenantFromHost(host: string): Tenant {
  // Extract tenant from hostname
  const subdomain = host.split('.')[0];
  
  // In production, this would query the database
  // For now, return a default tenant
  return {
    id: 'default',
    name: 'McNary Tech',
    domain: 'mcnarytech.com',
    subdomain,
    settings: {
      blogTitle: 'McNary Tech Blog',
      blogDescription: 'Technology insights and development tips',
      logo: '/logo.png',
      primaryColor: '#3B82F6',
      allowComments: true,
      moderationRequired: true,
    },
  };
}
```

### Tenant-Aware API Client
```typescript
// lib/api.ts
import { getTenantFromHost } from './tenant';

class APIClient {
  private baseURL: string;
  private tenant: Tenant;
  
  constructor() {
    this.baseURL = process.env.API_URL || 'http://localhost:8000';
    this.tenant = getTenantFromHost(window.location.host);
  }
  
  private async request(endpoint: string, options: RequestInit = {}) {
    const url = `${this.baseURL}${endpoint}`;
    
    const response = await fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'X-Tenant-ID': this.tenant.id,
        ...options.headers,
      },
    });
    
    if (!response.ok) {
      throw new Error(`API request failed: ${response.statusText}`);
    }
    
    return response.json();
  }
  
  async getPosts(params: GetPostsParams = {}) {
    return this.request(`/posts?${new URLSearchParams(params)}`);
  }
  
  async getPost(slug: string) {
    return this.request(`/posts?slug=${slug}`);
  }
  
  async getCategories() {
    return this.request('/categories');
  }
  
  async getTags() {
    return this.request('/tags');
  }
}

export const apiClient = new APIClient();
```

---

## 8) Content Management System

### Admin Interface
```typescript
// app/(admin)/dashboard/page.tsx
export default function AdminDashboard() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div className="py-8">
        <h1 className="text-3xl font-bold text-gray-900">Blog Dashboard</h1>
        
        <div className="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <DashboardCard
            title="Total Posts"
            value={stats.totalPosts}
            change={stats.postsChange}
            href="/admin/posts"
          />
          <DashboardCard
            title="Published Posts"
            value={stats.publishedPosts}
            change={stats.publishedChange}
            href="/admin/posts?status=published"
          />
          <DashboardCard
            title="Draft Posts"
            value={stats.draftPosts}
            change={stats.draftChange}
            href="/admin/posts?status=draft"
          />
        </div>
        
        <div className="mt-8">
          <RecentPosts />
        </div>
      </div>
    </div>
  );
}
```

### Post Editor
```typescript
// components/admin/PostEditor.tsx
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import dynamic from 'next/dynamic';

const MarkdownEditor = dynamic(() => import('@/components/ui/MarkdownEditor'), {
  ssr: false,
});

interface PostEditorProps {
  post?: BlogPost;
  mode: 'create' | 'edit';
}

export function PostEditor({ post, mode }: PostEditorProps) {
  const [formData, setFormData] = useState({
    title: post?.title || '',
    excerpt: post?.excerpt || '',
    content: post?.content || '',
    status: post?.status || 'draft',
    categoryId: post?.primaryCategory?.id || '',
    tagIds: post?.tags.map(tag => tag.id) || [],
  });
  
  const router = useRouter();
  
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      const response = await fetch('/api/posts', {
        method: mode === 'create' ? 'POST' : 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      
      if (response.ok) {
        router.push('/admin/posts');
      }
    } catch (error) {
      console.error('Failed to save post:', error);
    }
  };
  
  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div>
        <label htmlFor="title" className="block text-sm font-medium text-gray-700">
          Title
        </label>
        <input
          type="text"
          id="title"
          value={formData.title}
          onChange={(e) => setFormData({ ...formData, title: e.target.value })}
          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      
      <div>
        <label htmlFor="excerpt" className="block text-sm font-medium text-gray-700">
          Excerpt
        </label>
        <textarea
          id="excerpt"
          value={formData.excerpt}
          onChange={(e) => setFormData({ ...formData, excerpt: e.target.value })}
          rows={3}
          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
      </div>
      
      <div>
        <label htmlFor="content" className="block text-sm font-medium text-gray-700">
          Content
        </label>
        <MarkdownEditor
          value={formData.content}
          onChange={(value) => setFormData({ ...formData, content: value })}
        />
      </div>
      
      <div className="flex justify-end space-x-3">
        <button
          type="button"
          onClick={() => router.back()}
          className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
        >
          {mode === 'create' ? 'Create Post' : 'Update Post'}
        </button>
      </div>
    </form>
  );
}
```

---

## 9) Search & Discovery

### Full-Text Search
```typescript
// lib/search.ts
export interface SearchParams {
  query: string;
  category?: string;
  tag?: string;
  author?: string;
  dateFrom?: string;
  dateTo?: string;
  sortBy?: 'relevance' | 'date' | 'title';
  page?: number;
  limit?: number;
}

export async function searchPosts(params: SearchParams): Promise<SearchResults> {
  const searchParams = new URLSearchParams();
  
  Object.entries(params).forEach(([key, value]) => {
    if (value) searchParams.append(key, value);
  });
  
  const response = await fetch(`/api/search?${searchParams}`);
  return response.json();
}
```

### Search API Route
```typescript
// app/api/search/route.ts
import { NextRequest } from 'next/server';
import { apiClient } from '@/lib/api';

export async function GET(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  
  const query = searchParams.get('query');
  const category = searchParams.get('category');
  const tag = searchParams.get('tag');
  const author = searchParams.get('author');
  const dateFrom = searchParams.get('dateFrom');
  const dateTo = searchParams.get('dateTo');
  const sortBy = searchParams.get('sortBy') || 'relevance';
  const page = parseInt(searchParams.get('page') || '1');
  const limit = parseInt(searchParams.get('limit') || '10');
  
  try {
    const results = await apiClient.searchPosts({
      query,
      category,
      tag,
      author,
      dateFrom,
      dateTo,
      sortBy,
      page,
      limit,
    });
    
    return Response.json(results);
  } catch (error) {
    return Response.json({ error: 'Search failed' }, { status: 500 });
  }
}
```

---

## 10) Deployment & Infrastructure

### Docker Configuration
```dockerfile
# Dockerfile
FROM node:18-alpine AS base

# Install dependencies only when needed
FROM base AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app

# Install dependencies based on the preferred package manager
COPY package.json package-lock.json* ./
RUN npm ci

# Rebuild the source code only when needed
FROM base AS builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
COPY . .

# Next.js collects completely anonymous telemetry data about general usage.
# Learn more here: https://nextjs.org/telemetry
# Uncomment the following line in case you want to disable telemetry during the build.
ENV NEXT_TELEMETRY_DISABLED 1

RUN npm run build

# Production image, copy all the files and run next
FROM base AS runner
WORKDIR /app

ENV NODE_ENV production
ENV NEXT_TELEMETRY_DISABLED 1

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /app/public ./public

# Set the correct permission for prerender cache
RUN mkdir .next
RUN chown nextjs:nodejs .next

# Automatically leverage output traces to reduce image size
# https://nextjs.org/docs/advanced-features/output-file-tracing
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs

EXPOSE 3000

ENV PORT 3000
ENV HOSTNAME "0.0.0.0"

CMD ["node", "server.js"]
```

### Docker Compose
```yaml
# docker-compose.yml
version: '3.8'

services:
  blog-frontend:
    build:
      context: ./frontend-mcnarytech-blog
      dockerfile: Dockerfile
    ports:
      - "3000:3000"
    environment:
      - NODE_ENV=production
      - API_URL=http://backend:8000
      - DATABASE_URL=postgresql://user:password@postgres:5432/blog
      - REDIS_URL=redis://redis:6379
    depends_on:
      - backend
      - postgres
      - redis
    networks:
      - blog-network

  backend:
    build: ./backend
    ports:
      - "8000:8000"
    environment:
      - DATABASE_URL=postgresql://user:password@postgres:5432/blog
      - REDIS_URL=redis://redis:6379
    depends_on:
      - postgres
      - redis
    networks:
      - blog-network

  postgres:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: blog
      POSTGRES_USER: user
      POSTGRES_PASSWORD: password
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - blog-network

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    networks:
      - blog-network

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - blog-frontend
    networks:
      - blog-network

volumes:
  postgres_data:
  redis_data:

networks:
  blog-network:
    driver: bridge
```

### Environment Configuration
```bash
# .env.local
NODE_ENV=development
API_URL=http://localhost:8000
DATABASE_URL=postgresql://user:password@localhost:5432/blog
REDIS_URL=redis://localhost:6379

# SEO
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_SITE_NAME="McNary Tech Blog"
NEXT_PUBLIC_SITE_DESCRIPTION="Technology insights and development tips"

# Analytics
NEXT_PUBLIC_GA_ID=G-XXXXXXXXXX
NEXT_PUBLIC_GTM_ID=GTM-XXXXXXX

# Social Media
NEXT_PUBLIC_TWITTER_HANDLE=@mcnarytech
NEXT_PUBLIC_FACEBOOK_PAGE=mcnarytech
```

---

## 11) Testing Strategy

### Unit Tests
```typescript
// __tests__/components/PostCard.test.tsx
import { render, screen } from '@testing-library/react';
import { PostCard } from '@/components/blog/PostCard';

const mockPost = {
  id: '1',
  title: 'Test Post',
  excerpt: 'This is a test post excerpt',
  slug: 'test-post',
  author: { displayName: 'John Doe', slug: 'john-doe' },
  publishedAt: '2024-01-01T00:00:00Z',
  readingTime: 5,
  featuredImage: '/test-image.jpg',
};

describe('PostCard', () => {
  it('renders post title', () => {
    render(<PostCard post={mockPost} />);
    expect(screen.getByText('Test Post')).toBeInTheDocument();
  });

  it('renders post excerpt', () => {
    render(<PostCard post={mockPost} />);
    expect(screen.getByText('This is a test post excerpt')).toBeInTheDocument();
  });

  it('renders author name', () => {
    render(<PostCard post={mockPost} />);
    expect(screen.getByText('John Doe')).toBeInTheDocument();
  });
});
```

### Integration Tests
```typescript
// __tests__/api/posts.test.ts
import { createMocks } from 'node-mocks-http';
import { GET } from '@/app/api/posts/route';

describe('/api/posts', () => {
  it('returns posts list', async () => {
    const { req, res } = createMocks({
      method: 'GET',
      query: { limit: '10' },
    });

    await GET(req, res);

    expect(res._getStatusCode()).toBe(200);
    const data = JSON.parse(res._getData());
    expect(Array.isArray(data)).toBe(true);
  });
});
```

### E2E Tests
```typescript
// e2e/blog.spec.ts
import { test, expect } from '@playwright/test';

test('blog navigation', async ({ page }) => {
  await page.goto('/blog');
  
  // Check if blog posts are displayed
  await expect(page.locator('[data-testid="post-card"]')).toHaveCount(10);
  
  // Click on first post
  await page.click('[data-testid="post-card"]:first-child');
  
  // Verify post page loads
  await expect(page.locator('h1')).toBeVisible();
  await expect(page.locator('[data-testid="post-content"]')).toBeVisible();
});
```

---

## 12) Performance Monitoring

### Core Web Vitals
```typescript
// lib/analytics.ts
export function reportWebVitals(metric: any) {
  if (metric.label === 'web-vital') {
    // Send to analytics service
    console.log(metric);
    
    // Example: Google Analytics 4
    if (typeof window !== 'undefined' && window.gtag) {
      window.gtag('event', metric.name, {
        event_category: 'Web Vitals',
        event_label: metric.id,
        value: Math.round(metric.name === 'CLS' ? metric.value * 1000 : metric.value),
        non_interaction: true,
      });
    }
  }
}
```

### Performance Monitoring
```typescript
// lib/performance.ts
export function measurePageLoad() {
  if (typeof window !== 'undefined') {
    window.addEventListener('load', () => {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      
      const metrics = {
        dns: navigation.domainLookupEnd - navigation.domainLookupStart,
        tcp: navigation.connectEnd - navigation.connectStart,
        ttfb: navigation.responseStart - navigation.requestStart,
        domLoad: navigation.domContentLoadedEventEnd - navigation.navigationStart,
        windowLoad: navigation.loadEventEnd - navigation.navigationStart,
      };
      
      console.log('Performance metrics:', metrics);
    });
  }
}
```

---

## 13) Security Considerations

### Content Security Policy
```typescript
// next.config.js
const nextConfig = {
  async headers() {
    return [
      {
        source: '/(.*)',
        headers: [
          {
            key: 'Content-Security-Policy',
            value: [
              "default-src 'self'",
              "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://www.googletagmanager.com",
              "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
              "font-src 'self' https://fonts.gstatic.com",
              "img-src 'self' data: https:",
              "connect-src 'self' https://www.google-analytics.com",
              "frame-src 'self'",
            ].join('; '),
          },
          {
            key: 'X-Frame-Options',
            value: 'DENY',
          },
          {
            key: 'X-Content-Type-Options',
            value: 'nosniff',
          },
          {
            key: 'Referrer-Policy',
            value: 'strict-origin-when-cross-origin',
          },
        ],
      },
    ];
  },
};
```

### Input Validation
```typescript
// lib/validation.ts
import { z } from 'zod';

export const createPostSchema = z.object({
  title: z.string().min(1).max(200),
  excerpt: z.string().max(500).optional(),
  content: z.string().min(1),
  status: z.enum(['draft', 'published', 'archived']),
  categoryId: z.string().uuid().optional(),
  tagIds: z.array(z.string().uuid()).optional(),
});

export const updatePostSchema = createPostSchema.partial().extend({
  id: z.string().uuid(),
});

export type CreatePostInput = z.infer<typeof createPostSchema>;
export type UpdatePostInput = z.infer<typeof updatePostSchema>;
```

---

## 14) Development Workflow

### Git Hooks
```json
// package.json
{
  "husky": {
    "hooks": {
      "pre-commit": "lint-staged",
      "commit-msg": "commitlint -E HUSKY_GIT_PARAMS"
    }
  },
  "lint-staged": {
    "*.{js,jsx,ts,tsx}": [
      "eslint --fix",
      "prettier --write"
    ],
    "*.{json,md}": [
      "prettier --write"
    ]
  }
}
```

### Code Quality Tools
```json
// .eslintrc.json
{
  "extends": [
    "next/core-web-vitals",
    "@typescript-eslint/recommended"
  ],
  "rules": {
    "@typescript-eslint/no-unused-vars": "error",
    "@typescript-eslint/no-explicit-any": "warn",
    "prefer-const": "error",
    "no-var": "error"
  }
}
```

---

## 15) Rollout Plan

### Phase 1: MVP (Weeks 1-4)
- [ ] Basic blog structure with Next.js
- [ ] Post listing and individual post pages
- [ ] Category and tag pages
- [ ] Basic SEO implementation
- [ ] Integration with existing backend API

### Phase 2: Content Management (Weeks 5-8)
- [ ] Admin dashboard
- [ ] Post editor with markdown support
- [ ] Category and tag management
- [ ] Media upload functionality
- [ ] User authentication and authorization

### Phase 3: Advanced Features (Weeks 9-12)
- [ ] Search functionality
- [ ] RSS feeds and sitemaps
- [ ] Social media integration
- [ ] Analytics and performance monitoring
- [ ] Multi-tenant support

### Phase 4: Optimization (Weeks 13-16)
- [ ] Performance optimization
- [ ] SEO enhancements
- [ ] Accessibility improvements
- [ ] Testing and bug fixes
- [ ] Documentation and deployment

---

## 16) Success Metrics

### Performance Targets
- **Lighthouse Score**: >90 across all metrics
- **First Contentful Paint**: <1.5s
- **Largest Contentful Paint**: <2.5s
- **Cumulative Layout Shift**: <0.1
- **Time to Interactive**: <3.5s

### SEO Targets
- **Core Web Vitals**: Pass all metrics
- **Page Speed**: <3s load time
- **Mobile Optimization**: 100% mobile-friendly
- **Structured Data**: Implement for all content types
- **Sitemap Coverage**: 100% of published content

### User Experience Targets
- **Accessibility**: WCAG 2.1 AA compliance
- **Cross-browser**: Support for Chrome, Firefox, Safari, Edge
- **Mobile-first**: Responsive design for all screen sizes
- **Performance**: Consistent experience across devices

---

## 17) Risk Mitigation

### Technical Risks
- **API Integration Complexity**: Start with simple endpoints, gradually add complexity
- **Performance Issues**: Implement performance monitoring from day one
- **SEO Challenges**: Follow Next.js best practices and implement proper meta tags

### Business Risks
- **Content Migration**: Plan for content import from existing systems
- **User Adoption**: Provide comprehensive admin training and documentation
- **Scalability**: Design with horizontal scaling in mind from the start

### Security Risks
- **Input Validation**: Implement strict validation for all user inputs
- **Authentication**: Leverage existing JWT infrastructure
- **Content Security**: Implement CSP and other security headers

---

This technical specification provides a comprehensive roadmap for building an SEO-friendly blog platform that integrates seamlessly with the existing McNary backend infrastructure while delivering a modern, performant user experience.
