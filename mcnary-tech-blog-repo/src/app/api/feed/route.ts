import { getPosts } from '@/lib/api';

export async function GET() {
  try {
    const posts = await getPosts({ status: 'published', limit: 50 });
    
    const feed = `<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>McNary Tech Blog</title>
    <description>Latest insights on technology, development, and innovation</description>
    <link>https://mcnarytech.com</link>
    <atom:link href="https://mcnarytech.com/api/feed" rel="self" type="application/rss+xml" />
    <language>en</language>
    <lastBuildDate>${new Date().toUTCString()}</lastBuildDate>
    <ttl>60</ttl>
    ${posts.map(post => `
      <item>
        <title><![CDATA[${post.title}]]></title>
        <description><![CDATA[${post.excerpt || post.title}]]></description>
        <link>https://mcnarytech.com/blog/${post.slug}</link>
        <guid>https://mcnarytech.com/blog/${post.slug}</guid>
        <pubDate>${new Date(post.publishedAt || post.createdAt).toUTCString()}</pubDate>
        <author>${post.author.displayName}</author>
        ${post.primaryCategory ? `<category>${post.primaryCategory.name}</category>` : ''}
        ${post.tags.map(tag => `<category>${tag.name}</category>`).join('')}
      </item>
    `).join('')}
  </channel>
</rss>`;
    
    return new Response(feed, {
      headers: {
        'Content-Type': 'application/xml',
        'Cache-Control': 'public, max-age=3600, s-maxage=3600',
      },
    });
  } catch (error) {
    console.error('Failed to generate RSS feed:', error);
    
    // Return a basic RSS feed with just the channel info
    const basicFeed = `<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>McNary Tech Blog</title>
    <description>Latest insights on technology, development, and innovation</description>
    <link>https://mcnarytech.com</link>
    <atom:link href="https://mcnarytech.com/api/feed" rel="self" type="application/rss+xml" />
    <language>en</language>
    <lastBuildDate>${new Date().toUTCString()}</lastBuildDate>
    <ttl>60</ttl>
  </channel>
</rss>`;
    
    return new Response(basicFeed, {
      headers: {
        'Content-Type': 'application/xml',
        'Cache-Control': 'public, max-age=3600, s-maxage=3600',
      },
    });
  }
}
