"""
Web crawling service for SEO audits
"""

import asyncio
import aiohttp
import time
from typing import List, Dict, Any, Optional, Set, Tuple
from urllib.parse import urljoin, urlparse, parse_qs
from urllib.robotparser import RobotFileParser
import logging
from bs4 import BeautifulSoup
import re
from dataclasses import dataclass
from datetime import datetime

logger = logging.getLogger(__name__)


@dataclass
class CrawlResult:
    """Result of crawling a single URL"""
    url: str
    status_code: int
    response_time: float
    content_length: int
    content_type: str
    title: Optional[str] = None
    meta_description: Optional[str] = None
    meta_keywords: Optional[str] = None
    h1_tags: List[str] = None
    h2_tags: List[str] = None
    h3_tags: List[str] = None
    images: List[Dict[str, Any]] = None
    links: List[Dict[str, Any]] = None
    scripts: List[Dict[str, Any]] = None
    stylesheets: List[Dict[str, Any]] = None
    word_count: int = 0
    reading_time: int = 0
    error_message: Optional[str] = None
    redirect_chain: List[str] = None
    canonical_url: Optional[str] = None
    
    def __post_init__(self):
        if self.h1_tags is None:
            self.h1_tags = []
        if self.h2_tags is None:
            self.h2_tags = []
        if self.h3_tags is None:
            self.h3_tags = []
        if self.images is None:
            self.images = []
        if self.links is None:
            self.links = []
        if self.scripts is None:
            self.scripts = []
        if self.stylesheets is None:
            self.stylesheets = []
        if self.redirect_chain is None:
            self.redirect_chain = []


class RobotsTxtParser:
    """Parser for robots.txt files"""
    
    def __init__(self):
        self.parsers: Dict[str, RobotFileParser] = {}
    
    async def can_fetch(self, url: str, user_agent: str = "*") -> bool:
        """Check if URL can be fetched according to robots.txt"""
        parsed_url = urlparse(url)
        domain = f"{parsed_url.scheme}://{parsed_url.netloc}"
        
        if domain not in self.parsers:
            await self._load_robots_txt(domain, user_agent)
        
        parser = self.parsers.get(domain)
        if not parser:
            return True  # Allow if robots.txt couldn't be loaded
        
        return parser.can_fetch(user_agent, url)
    
    async def _load_robots_txt(self, domain: str, user_agent: str):
        """Load robots.txt for a domain"""
        try:
            robots_url = f"{domain}/robots.txt"
            async with aiohttp.ClientSession() as session:
                async with session.get(robots_url, timeout=10) as response:
                    if response.status == 200:
                        content = await response.text()
                        parser = RobotFileParser()
                        parser.set_url(robots_url)
                        parser.read()
                        self.parsers[domain] = parser
        except Exception as e:
            logger.warning(f"Could not load robots.txt for {domain}: {e}")


class WebCrawler:
    """Polite web crawler for SEO audits"""
    
    def __init__(self, 
                 max_pages: int = 1000,
                 crawl_delay: float = 1.0,
                 respect_robots: bool = True,
                 max_depth: int = 3,
                 timeout: int = 30,
                 max_retries: int = 3,
                 user_agent: str = "SEO-Audit-Bot/1.0"):
        
        self.max_pages = max_pages
        self.crawl_delay = crawl_delay
        self.respect_robots = respect_robots
        self.max_depth = max_depth
        self.timeout = timeout
        self.max_retries = max_retries
        self.user_agent = user_agent
        
        self.robots_parser = RobotsTxtParser() if respect_robots else None
        self.crawled_urls: Set[str] = set()
        self.failed_urls: Set[str] = set()
        self.redirect_urls: Dict[str, str] = {}
        
        # Rate limiting
        self.last_request_time: Dict[str, float] = {}
        
        # Session configuration
        self.session_config = {
            'timeout': aiohttp.ClientTimeout(total=timeout),
            'headers': {
                'User-Agent': user_agent,
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language': 'en-US,en;q=0.5',
                'Accept-Encoding': 'gzip, deflate',
                'Connection': 'keep-alive',
            }
        }
    
    async def crawl(self, seed_urls: List[str], include_external: bool = False) -> List[CrawlResult]:
        """Main crawling method"""
        results = []
        urls_to_crawl = [(url, 0) for url in seed_urls]  # (url, depth)
        
        async with aiohttp.ClientSession(**self.session_config) as session:
            while urls_to_crawl and len(results) < self.max_pages:
                url, depth = urls_to_crawl.pop(0)
                
                # Skip if already crawled or failed
                if url in self.crawled_urls or url in self.failed_urls:
                    continue
                
                # Skip if depth exceeded
                if depth > self.max_depth:
                    continue
                
                # Check robots.txt
                if self.robots_parser and not await self.robots_parser.can_fetch(url):
                    logger.info(f"Skipping {url} due to robots.txt")
                    continue
                
                # Rate limiting
                await self._enforce_rate_limit(url)
                
                try:
                    result = await self._crawl_url(session, url, depth)
                    results.append(result)
                    self.crawled_urls.add(url)
                    
                    # Extract new URLs if not at max depth
                    if depth < self.max_depth and result.status_code == 200:
                        new_urls = await self._extract_urls(result, include_external)
                        for new_url in new_urls:
                            if new_url not in self.crawled_urls and new_url not in self.failed_urls:
                                urls_to_crawl.append((new_url, depth + 1))
                    
                    logger.info(f"Crawled {url} (depth: {depth}, total: {len(results)})")
                    
                except Exception as e:
                    logger.error(f"Failed to crawl {url}: {e}")
                    self.failed_urls.add(url)
                    results.append(CrawlResult(
                        url=url,
                        status_code=0,
                        response_time=0.0,
                        content_length=0,
                        content_type="",
                        error_message=str(e)
                    ))
        
        return results
    
    async def _crawl_url(self, session: aiohttp.ClientSession, url: str, depth: int) -> CrawlResult:
        """Crawl a single URL"""
        start_time = time.time()
        redirect_chain = []
        final_url = url
        
        for attempt in range(self.max_retries):
            try:
                async with session.get(url, allow_redirects=False) as response:
                    # Handle redirects
                    if response.status in [301, 302, 303, 307, 308]:
                        redirect_chain.append(url)
                        location = response.headers.get('Location')
                        if location:
                            final_url = urljoin(url, location)
                            url = final_url
                            continue
                    
                    # Read content for successful responses
                    content = ""
                    if response.status == 200:
                        content = await response.text()
                    
                    response_time = (time.time() - start_time) * 1000  # Convert to milliseconds
                    
                    # Parse HTML content
                    parsed_data = await self._parse_html(content, final_url)
                    
                    return CrawlResult(
                        url=final_url,
                        status_code=response.status,
                        response_time=response_time,
                        content_length=len(content),
                        content_type=response.headers.get('Content-Type', ''),
                        redirect_chain=redirect_chain,
                        **parsed_data
                    )
                    
            except asyncio.TimeoutError:
                if attempt == self.max_retries - 1:
                    raise Exception(f"Timeout after {self.max_retries} attempts")
                await asyncio.sleep(1)
            except Exception as e:
                if attempt == self.max_retries - 1:
                    raise e
                await asyncio.sleep(1)
        
        raise Exception("Max retries exceeded")
    
    async def _parse_html(self, html_content: str, base_url: str) -> Dict[str, Any]:
        """Parse HTML content and extract SEO-relevant data"""
        if not html_content:
            return {
                'title': None,
                'meta_description': None,
                'meta_keywords': None,
                'h1_tags': [],
                'h2_tags': [],
                'h3_tags': [],
                'images': [],
                'links': [],
                'scripts': [],
                'stylesheets': [],
                'word_count': 0,
                'reading_time': 0,
                'canonical_url': None
            }
        
        try:
            soup = BeautifulSoup(html_content, 'html.parser')
            
            # Extract title
            title_tag = soup.find('title')
            title = title_tag.get_text().strip() if title_tag else None
            
            # Extract meta tags
            meta_description = None
            meta_keywords = None
            canonical_url = None
            
            for meta in soup.find_all('meta'):
                name = meta.get('name', '').lower()
                property_attr = meta.get('property', '').lower()
                content = meta.get('content', '')
                
                if name == 'description':
                    meta_description = content
                elif name == 'keywords':
                    meta_keywords = content
                elif name == 'canonical' or property_attr == 'og:url':
                    canonical_url = urljoin(base_url, content)
            
            # Extract headings
            h1_tags = [h.get_text().strip() for h in soup.find_all('h1')]
            h2_tags = [h.get_text().strip() for h in soup.find_all('h2')]
            h3_tags = [h.get_text().strip() for h in soup.find_all('h3')]
            
            # Extract images
            images = []
            for img in soup.find_all('img'):
                src = img.get('src')
                if src:
                    images.append({
                        'src': urljoin(base_url, src),
                        'alt': img.get('alt', ''),
                        'title': img.get('title', ''),
                        'width': img.get('width'),
                        'height': img.get('height')
                    })
            
            # Extract links
            links = []
            for link in soup.find_all('a', href=True):
                href = link.get('href')
                if href:
                    full_url = urljoin(base_url, href)
                    parsed_href = urlparse(href)
                    parsed_base = urlparse(base_url)
                    
                    link_type = 'external' if parsed_href.netloc and parsed_href.netloc != parsed_base.netloc else 'internal'
                    
                    links.append({
                        'url': full_url,
                        'text': link.get_text().strip(),
                        'title': link.get('title', ''),
                        'type': link_type,
                        'rel': link.get('rel', [])
                    })
            
            # Extract scripts
            scripts = []
            for script in soup.find_all('script', src=True):
                src = script.get('src')
                if src:
                    scripts.append({
                        'src': urljoin(base_url, src),
                        'type': script.get('type', ''),
                        'async': script.has_attr('async'),
                        'defer': script.has_attr('defer')
                    })
            
            # Extract stylesheets
            stylesheets = []
            for link in soup.find_all('link', rel='stylesheet'):
                href = link.get('href')
                if href:
                    stylesheets.append({
                        'href': urljoin(base_url, href),
                        'media': link.get('media', ''),
                        'type': link.get('type', 'text/css')
                    })
            
            # Calculate word count and reading time
            text_content = soup.get_text()
            words = re.findall(r'\b\w+\b', text_content.lower())
            word_count = len(words)
            reading_time = max(1, word_count // 200)  # Assume 200 words per minute
            
            return {
                'title': title,
                'meta_description': meta_description,
                'meta_keywords': meta_keywords,
                'h1_tags': h1_tags,
                'h2_tags': h2_tags,
                'h3_tags': h3_tags,
                'images': images,
                'links': links,
                'scripts': scripts,
                'stylesheets': stylesheets,
                'word_count': word_count,
                'reading_time': reading_time,
                'canonical_url': canonical_url
            }
            
        except Exception as e:
            logger.error(f"Error parsing HTML for {base_url}: {e}")
            return {
                'title': None,
                'meta_description': None,
                'meta_keywords': None,
                'h1_tags': [],
                'h2_tags': [],
                'h3_tags': [],
                'images': [],
                'links': [],
                'scripts': [],
                'stylesheets': [],
                'word_count': 0,
                'reading_time': 0,
                'canonical_url': None
            }
    
    async def _extract_urls(self, result: CrawlResult, include_external: bool) -> List[str]:
        """Extract URLs from crawl result"""
        urls = []
        base_domain = urlparse(result.url).netloc
        
        for link in result.links:
            link_url = link['url']
            parsed_url = urlparse(link_url)
            
            # Skip non-HTTP URLs
            if parsed_url.scheme not in ['http', 'https']:
                continue
            
            # Skip external URLs unless allowed
            if not include_external and parsed_url.netloc != base_domain:
                continue
            
            # Skip URLs with fragments or query parameters for now
            if parsed_url.fragment or parsed_url.query:
                continue
            
            # Skip common file extensions
            path = parsed_url.path.lower()
            if any(path.endswith(ext) for ext in ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.zip', '.rar']):
                continue
            
            urls.append(link_url)
        
        return list(set(urls))  # Remove duplicates
    
    async def _enforce_rate_limit(self, url: str):
        """Enforce rate limiting per domain"""
        domain = urlparse(url).netloc
        current_time = time.time()
        
        if domain in self.last_request_time:
            time_since_last = current_time - self.last_request_time[domain]
            if time_since_last < self.crawl_delay:
                sleep_time = self.crawl_delay - time_since_last
                await asyncio.sleep(sleep_time)
        
        self.last_request_time[domain] = time.time()


class CrawlerService:
    """Service for managing web crawling operations"""
    
    def __init__(self):
        self.active_crawlers: Dict[str, WebCrawler] = {}
    
    async def start_crawl(self, 
                         audit_run_id: str,
                         seed_urls: List[str],
                         config: Dict[str, Any]) -> str:
        """Start a new crawling operation"""
        
        crawler = WebCrawler(
            max_pages=config.get('max_pages', 1000),
            crawl_delay=config.get('crawl_delay', 1.0),
            respect_robots=config.get('respect_robots', True),
            max_depth=config.get('max_depth', 3),
            timeout=config.get('timeout', 30),
            max_retries=config.get('max_retries', 3),
            user_agent=config.get('user_agent', 'SEO-Audit-Bot/1.0')
        )
        
        self.active_crawlers[audit_run_id] = crawler
        
        # Start crawling in background
        asyncio.create_task(self._run_crawl(audit_run_id, seed_urls, config))
        
        return audit_run_id
    
    async def _run_crawl(self, audit_run_id: str, seed_urls: List[str], config: Dict[str, Any]):
        """Run the actual crawling operation"""
        crawler = self.active_crawlers.get(audit_run_id)
        if not crawler:
            return
        
        try:
            results = await crawler.crawl(
                seed_urls,
                include_external=config.get('include_external_links', False)
            )
            
            # Store results (this would typically save to database)
            logger.info(f"Crawl completed for {audit_run_id}: {len(results)} pages crawled")
            
        except Exception as e:
            logger.error(f"Crawl failed for {audit_run_id}: {e}")
        
        finally:
            # Clean up
            if audit_run_id in self.active_crawlers:
                del self.active_crawlers[audit_run_id]
    
    async def stop_crawl(self, audit_run_id: str) -> bool:
        """Stop an active crawling operation"""
        if audit_run_id in self.active_crawlers:
            del self.active_crawlers[audit_run_id]
            return True
        return False
    
    def get_crawl_status(self, audit_run_id: str) -> Dict[str, Any]:
        """Get status of a crawling operation"""
        crawler = self.active_crawlers.get(audit_run_id)
        if not crawler:
            return {"status": "not_found"}
        
        return {
            "status": "running",
            "crawled_urls": len(crawler.crawled_urls),
            "failed_urls": len(crawler.failed_urls),
            "max_pages": crawler.max_pages,
            "progress": len(crawler.crawled_urls) / crawler.max_pages * 100
        }
