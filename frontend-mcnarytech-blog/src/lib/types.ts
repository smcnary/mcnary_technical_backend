export interface User {
  id: string;
  email: string;
  displayName: string;
  avatarUrl?: string;
  bio?: string;
  socialLinks?: Record<string, string>;
  createdAt: string;
  updatedAt: string;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  description?: string;
  status: 'active' | 'inactive';
  sortOrder: number;
  createdAt: string;
  updatedAt: string;
}

export interface Tag {
  id: string;
  name: string;
  slug: string;
  color?: string;
  status: 'active' | 'inactive';
  createdAt: string;
  updatedAt: string;
}

export interface Post {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  status: 'draft' | 'published' | 'archived';
  publishedAt?: string;
  createdAt: string;
  updatedAt: string;
  author: User;
  primaryCategory?: Category;
  categories: Category[];
  tags: Tag[];
  featuredImage?: string;
  readingTime: number;
  wordCount: number;
  seoMeta?: SeoMeta;
}

export interface SeoMeta {
  id: string;
  title?: string;
  metaDescription?: string;
  canonicalUrl?: string;
  robots?: string;
  openGraph?: {
    title?: string;
    description?: string;
    image?: string;
    type?: string;
    url?: string;
  };
  twitterCard?: {
    card?: string;
    title?: string;
    description?: string;
    image?: string;
  };
}

export interface BlogPost extends Post {
  content: string;
  contentType: 'markdown' | 'html' | 'rich_text';
}

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

export interface SearchResults {
  posts: BlogPost[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
}

export interface GetPostsParams {
  status?: 'draft' | 'published' | 'archived';
  category?: string;
  tag?: string;
  author?: string;
  featured?: boolean;
  limit?: number;
  offset?: number;
  sortBy?: 'date' | 'title' | 'readingTime';
  sortOrder?: 'asc' | 'desc';
}

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

export interface ApiResponse<T> {
  data: T;
  meta?: {
    total?: number;
    page?: number;
    limit?: number;
    totalPages?: number;
  };
  error?: string;
}
