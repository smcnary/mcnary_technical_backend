import { unstable_cache } from 'next/cache';
import type { 
  BlogPost, 
  Post, 
  Category, 
  Tag, 
  User, 
  GetPostsParams, 
  SearchParams, 
  SearchResults,
  ApiResponse 
} from './types';

const API_BASE_URL = process.env.API_URL || 'http://localhost:8000';

class ApiError extends Error {
  constructor(public status: number, message: string) {
    super(message);
    this.name = 'ApiError';
  }
}

async function apiRequest<T>(
  endpoint: string, 
  options: RequestInit = {}
): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const response = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
  });

  if (!response.ok) {
    throw new ApiError(response.status, `API request failed: ${response.statusText}`);
  }

  return response.json();
}

// Cached API functions
export const getPosts = unstable_cache(
  async (params: GetPostsParams = {}): Promise<Post[]> => {
    const searchParams = new URLSearchParams();
    
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined) {
        searchParams.append(key, String(value));
      }
    });
    
    const endpoint = `/posts?${searchParams.toString()}`;
    return apiRequest<Post[]>(endpoint);
  },
  ['posts'],
  {
    revalidate: 3600, // 1 hour
    tags: ['posts'],
  }
);

export const getPost = unstable_cache(
  async (slug: string): Promise<BlogPost | null> => {
    try {
      const posts = await apiRequest<BlogPost[]>(`/posts?slug=${slug}`);
      return posts[0] || null;
    } catch (error) {
      console.error('Failed to fetch post:', error);
      return null;
    }
  },
  ['post'],
  {
    revalidate: 7200, // 2 hours
    tags: ['post'],
  }
);

export const getCategories = unstable_cache(
  async (): Promise<Category[]> => {
    return apiRequest<Category[]>('/categories?status=active');
  },
  ['categories'],
  {
    revalidate: 7200, // 2 hours
    tags: ['categories'],
  }
);

export const getTags = unstable_cache(
  async (): Promise<Tag[]> => {
    return apiRequest<Tag[]>('/tags?status=active');
  },
  ['tags'],
  {
    revalidate: 7200, // 2 hours
    tags: ['tags'],
  }
);

export const getUsers = unstable_cache(
  async (): Promise<User[]> => {
    return apiRequest<User[]>('/users');
  },
  ['users'],
  {
    revalidate: 3600, // 1 hour
    tags: ['users'],
  }
);

export const searchPosts = async (params: SearchParams): Promise<SearchResults> => {
  const searchParams = new URLSearchParams();
  
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined) {
      searchParams.append(key, String(value));
    }
  });
  
  const endpoint = `/search?${searchParams.toString()}`;
  return apiRequest<SearchResults>(endpoint);
};

// Admin functions (not cached)
export const createPost = async (postData: Partial<BlogPost>): Promise<BlogPost> => {
  return apiRequest<BlogPost>('/posts', {
    method: 'POST',
    body: JSON.stringify(postData),
  });
};

export const updatePost = async (id: string, postData: Partial<BlogPost>): Promise<BlogPost> => {
  return apiRequest<BlogPost>(`/posts/${id}`, {
    method: 'PUT',
    body: JSON.stringify(postData),
  });
};

export const deletePost = async (id: string): Promise<void> => {
  return apiRequest<void>(`/posts/${id}`, {
    method: 'DELETE',
  });
};

export const createCategory = async (categoryData: Partial<Category>): Promise<Category> => {
  return apiRequest<Category>('/categories', {
    method: 'POST',
    body: JSON.stringify(categoryData),
  });
};

export const updateCategory = async (id: string, categoryData: Partial<Category>): Promise<Category> => {
  return apiRequest<Category>(`/categories/${id}`, {
    method: 'PUT',
    body: JSON.stringify(categoryData),
  });
};

export const deleteCategory = async (id: string): Promise<void> => {
  return apiRequest<void>(`/categories/${id}`, {
    method: 'DELETE',
  });
};

export const createTag = async (tagData: Partial<Tag>): Promise<Tag> => {
  return apiRequest<Tag>('/tags', {
    method: 'POST',
    body: JSON.stringify(tagData),
  });
};

export const updateTag = async (id: string, tagData: Partial<Tag>): Promise<Tag> => {
  return apiRequest<Tag>(`/tags/${id}`, {
    method: 'PUT',
    body: JSON.stringify(tagData),
  });
};

export const deleteTag = async (id: string): Promise<void> => {
  return apiRequest<void>(`/tags/${id}`, {
    method: 'DELETE',
  });
};
