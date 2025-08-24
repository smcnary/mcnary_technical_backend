import { getPosts, getCategories, getTags } from '@/lib/api';

export async function GET() {
  try {
    const [posts, categories, tags] = await Promise.all([
      getPosts({ status: 'published' }),
      getCategories(),
      getTags(),
    ]);
    
    const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://mcnarytech.com</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://mcnarytech.com/blog</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
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
  ${tags.map(tag => `
    <url>
      <loc>https://mcnarytech.com/blog/tag/${tag.slug}</loc>
      <changefreq>weekly</changefreq>
      <priority>0.5</priority>
    </url>
  `).join('')}
</urlset>`;
    
    return new Response(sitemap, {
      headers: {
        'Content-Type': 'application/xml',
        'Cache-Control': 'public, max-age=3600, s-maxage=3600',
      },
    });
  } catch (error) {
    console.error('Failed to generate sitemap:', error);
    
    // Return a basic sitemap with just the main pages
    const basicSitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://mcnarytech.com</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://mcnarytech.com/blog</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
</urlset>`;
    
    return new Response(basicSitemap, {
      headers: {
        'Content-Type': 'application/xml',
        'Cache-Control': 'public, max-age=3600, s-maxage=3600',
      },
    });
  }
}
