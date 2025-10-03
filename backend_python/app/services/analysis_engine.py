"""
SEO analysis engine with rule-based checks
"""

from typing import List, Dict, Any, Optional, Tuple
from dataclasses import dataclass
from abc import ABC, abstractmethod
import re
from urllib.parse import urlparse
import logging

from app.models.audit import FindingSeverity, FindingCategory
from app.services.crawler_service import CrawlResult

logger = logging.getLogger(__name__)


@dataclass
class FindingResult:
    """Result of an SEO check"""
    check_code: str
    check_name: str
    category: FindingCategory
    severity: FindingSeverity
    title: str
    description: str
    recommendation: str
    impact: str
    element: Optional[str] = None
    attribute: Optional[str] = None
    value: Optional[str] = None
    expected_value: Optional[str] = None
    score_impact: float = 0.0
    difficulty: str = "medium"


class SEOCheck(ABC):
    """Abstract base class for SEO checks"""
    
    @abstractmethod
    def check(self, result: CrawlResult) -> Optional[FindingResult]:
        """Perform the SEO check and return a finding if issues are found"""
        pass
    
    @property
    @abstractmethod
    def check_code(self) -> str:
        """Unique identifier for this check"""
        pass
    
    @property
    @abstractmethod
    def check_name(self) -> str:
        """Human-readable name for this check"""
        pass
    
    @property
    @abstractmethod
    def category(self) -> FindingCategory:
        """Category this check belongs to"""
        pass


class TechnicalSEOChecks:
    """Technical SEO checks"""
    
    class TitleTagCheck(SEOCheck):
        """Check for title tag presence and quality"""
        
        @property
        def check_code(self) -> str:
            return "TECH_TITLE_TAG"
        
        @property
        def check_name(self) -> str:
            return "Title Tag Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.TECHNICAL_SEO
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if not result.title:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.CRITICAL,
                    title="Missing Title Tag",
                    description="The page is missing a title tag, which is essential for SEO.",
                    recommendation="Add a descriptive title tag between 30-60 characters.",
                    impact="Search engines use title tags as the primary headline in search results.",
                    element="title",
                    score_impact=-15.0,
                    difficulty="easy"
                )
            
            title_length = len(result.title)
            if title_length < 30:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Title Tag Too Short",
                    description=f"Title tag is {title_length} characters long, which is below the recommended 30-60 characters.",
                    recommendation="Expand the title tag to be more descriptive and informative.",
                    impact="Short titles may not provide enough context for search engines and users.",
                    element="title",
                    value=result.title,
                    expected_value="30-60 characters",
                    score_impact=-5.0,
                    difficulty="easy"
                )
            elif title_length > 60:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Title Tag Too Long",
                    description=f"Title tag is {title_length} characters long, which exceeds the recommended 30-60 characters.",
                    recommendation="Shorten the title tag to ensure it displays fully in search results.",
                    impact="Long titles may be truncated in search results, reducing click-through rates.",
                    element="title",
                    value=result.title,
                    expected_value="30-60 characters",
                    score_impact=-3.0,
                    difficulty="easy"
                )
            
            return None
    
    class MetaDescriptionCheck(SEOCheck):
        """Check for meta description presence and quality"""
        
        @property
        def check_code(self) -> str:
            return "TECH_META_DESCRIPTION"
        
        @property
        def check_name(self) -> str:
            return "Meta Description Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.TECHNICAL_SEO
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if not result.meta_description:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.HIGH,
                    title="Missing Meta Description",
                    description="The page is missing a meta description tag.",
                    recommendation="Add a compelling meta description between 120-160 characters.",
                    impact="Meta descriptions appear in search results and influence click-through rates.",
                    element="meta[name='description']",
                    score_impact=-10.0,
                    difficulty="easy"
                )
            
            desc_length = len(result.meta_description)
            if desc_length < 120:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Meta Description Too Short",
                    description=f"Meta description is {desc_length} characters long, which is below the recommended 120-160 characters.",
                    recommendation="Expand the meta description to provide more compelling information.",
                    impact="Short meta descriptions may not provide enough incentive for users to click.",
                    element="meta[name='description']",
                    value=result.meta_description,
                    expected_value="120-160 characters",
                    score_impact=-3.0,
                    difficulty="easy"
                )
            elif desc_length > 160:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.LOW,
                    title="Meta Description Too Long",
                    description=f"Meta description is {desc_length} characters long, which exceeds the recommended 120-160 characters.",
                    recommendation="Shorten the meta description to ensure it displays fully in search results.",
                    impact="Long meta descriptions may be truncated in search results.",
                    element="meta[name='description']",
                    value=result.meta_description,
                    expected_value="120-160 characters",
                    score_impact=-1.0,
                    difficulty="easy"
                )
            
            return None
    
    class HeadingStructureCheck(SEOCheck):
        """Check heading structure and hierarchy"""
        
        @property
        def check_code(self) -> str:
            return "TECH_HEADING_STRUCTURE"
        
        @property
        def check_name(self) -> str:
            return "Heading Structure Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.TECHNICAL_SEO
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            issues = []
            
            # Check for missing H1 tag
            if not result.h1_tags:
                issues.append("Missing H1 tag")
            
            # Check for multiple H1 tags
            elif len(result.h1_tags) > 1:
                issues.append(f"Multiple H1 tags found ({len(result.h1_tags)})")
            
            # Check heading hierarchy
            if result.h3_tags and not result.h2_tags:
                issues.append("H3 tags found without H2 tags")
            
            if issues:
                severity = FindingSeverity.HIGH if "Missing H1 tag" in issues else FindingSeverity.MEDIUM
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=severity,
                    title="Heading Structure Issues",
                    description=f"Heading structure problems found: {'; '.join(issues)}",
                    recommendation="Ensure proper heading hierarchy with a single H1 tag and logical H2/H3 structure.",
                    impact="Proper heading structure helps search engines understand page content and improves accessibility.",
                    element="h1, h2, h3",
                    score_impact=-8.0 if severity == FindingSeverity.HIGH else -4.0,
                    difficulty="medium"
                )
            
            return None
    
    class ImageAltTextCheck(SEOCheck):
        """Check for image alt text"""
        
        @property
        def check_code(self) -> str:
            return "TECH_IMAGE_ALT"
        
        @property
        def check_name(self) -> str:
            return "Image Alt Text Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.TECHNICAL_SEO
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if not result.images:
                return None
            
            missing_alt = []
            for img in result.images:
                if not img.get('alt'):
                    missing_alt.append(img.get('src', 'Unknown'))
            
            if missing_alt:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Images Missing Alt Text",
                    description=f"{len(missing_alt)} images are missing alt text attributes.",
                    recommendation="Add descriptive alt text to all images for better accessibility and SEO.",
                    impact="Alt text helps search engines understand images and improves accessibility for screen readers.",
                    element="img",
                    attribute="alt",
                    score_impact=-5.0,
                    difficulty="easy"
                )
            
            return None
    
    class CanonicalUrlCheck(SEOCheck):
        """Check for canonical URL implementation"""
        
        @property
        def check_code(self) -> str:
            return "TECH_CANONICAL_URL"
        
        @property
        def check_name(self) -> str:
            return "Canonical URL Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.TECHNICAL_SEO
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if not result.canonical_url:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Missing Canonical URL",
                    description="The page is missing a canonical URL tag.",
                    recommendation="Add a canonical URL tag to prevent duplicate content issues.",
                    impact="Canonical URLs help search engines understand the preferred version of a page.",
                    element="link[rel='canonical']",
                    score_impact=-5.0,
                    difficulty="easy"
                )
            
            return None


class PerformanceChecks:
    """Performance-related SEO checks"""
    
    class PageLoadTimeCheck(SEOCheck):
        """Check page load time"""
        
        @property
        def check_code(self) -> str:
            return "PERF_PAGE_LOAD_TIME"
        
        @property
        def check_name(self) -> str:
            return "Page Load Time Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.PERFORMANCE
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if result.response_time > 3000:  # 3 seconds
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.HIGH,
                    title="Slow Page Load Time",
                    description=f"Page load time is {result.response_time:.0f}ms, which is above the recommended 3 seconds.",
                    recommendation="Optimize page loading speed through image compression, code minification, and CDN usage.",
                    impact="Slow loading pages negatively impact user experience and search rankings.",
                    value=f"{result.response_time:.0f}ms",
                    expected_value="< 3000ms",
                    score_impact=-10.0,
                    difficulty="hard"
                )
            elif result.response_time > 1500:  # 1.5 seconds
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Moderate Page Load Time",
                    description=f"Page load time is {result.response_time:.0f}ms, which could be improved.",
                    recommendation="Consider optimizing page loading speed for better user experience.",
                    impact="Faster loading pages provide better user experience and may rank higher.",
                    value=f"{result.response_time:.0f}ms",
                    expected_value="< 1500ms",
                    score_impact=-3.0,
                    difficulty="medium"
                )
            
            return None
    
    class ContentLengthCheck(SEOCheck):
        """Check content length"""
        
        @property
        def check_code(self) -> str:
            return "PERF_CONTENT_LENGTH"
        
        @property
        def check_name(self) -> str:
            return "Content Length Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.CONTENT
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if result.word_count < 300:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Thin Content",
                    description=f"Page has only {result.word_count} words, which is below the recommended 300 words.",
                    recommendation="Add more valuable, relevant content to improve SEO performance.",
                    impact="Thin content may not rank well in search results and provides less value to users.",
                    value=f"{result.word_count} words",
                    expected_value="> 300 words",
                    score_impact=-5.0,
                    difficulty="medium"
                )
            
            return None


class AccessibilityChecks:
    """Accessibility-related SEO checks"""
    
    class ImageAccessibilityCheck(SEOCheck):
        """Check image accessibility"""
        
        @property
        def check_code(self) -> str:
            return "A11Y_IMAGE_ACCESSIBILITY"
        
        @property
        def check_name(self) -> str:
            return "Image Accessibility Check"
        
        @property
        def category(self) -> FindingCategory:
            return FindingCategory.ACCESSIBILITY
        
        def check(self, result: CrawlResult) -> Optional[FindingResult]:
            if not result.images:
                return None
            
            issues = []
            for img in result.images:
                if not img.get('alt'):
                    issues.append("Missing alt text")
                elif len(img.get('alt', '')) > 125:
                    issues.append("Alt text too long")
            
            if issues:
                return FindingResult(
                    check_code=self.check_code,
                    check_name=self.check_name,
                    category=self.category,
                    severity=FindingSeverity.MEDIUM,
                    title="Image Accessibility Issues",
                    description=f"Images have accessibility issues: {'; '.join(set(issues))}",
                    recommendation="Ensure all images have appropriate alt text (under 125 characters).",
                    impact="Proper image accessibility improves user experience and SEO.",
                    element="img",
                    attribute="alt",
                    score_impact=-3.0,
                    difficulty="easy"
                )
            
            return None


class SEOAnalysisEngine:
    """Main SEO analysis engine"""
    
    def __init__(self):
        self.checks: List[SEOCheck] = [
            # Technical SEO checks
            TechnicalSEOChecks.TitleTagCheck(),
            TechnicalSEOChecks.MetaDescriptionCheck(),
            TechnicalSEOChecks.HeadingStructureCheck(),
            TechnicalSEOChecks.ImageAltTextCheck(),
            TechnicalSEOChecks.CanonicalUrlCheck(),
            
            # Performance checks
            PerformanceChecks.PageLoadTimeCheck(),
            PerformanceChecks.ContentLengthCheck(),
            
            # Accessibility checks
            AccessibilityChecks.ImageAccessibilityCheck(),
        ]
    
    def analyze_page(self, result: CrawlResult) -> List[FindingResult]:
        """Analyze a single page and return findings"""
        findings = []
        
        for check in self.checks:
            try:
                finding = check.check(result)
                if finding:
                    findings.append(finding)
            except Exception as e:
                logger.error(f"Error running check {check.check_code}: {e}")
        
        return findings
    
    def get_available_checks(self) -> List[Dict[str, str]]:
        """Get list of available checks"""
        return [
            {
                "code": check.check_code,
                "name": check.check_name,
                "category": check.category.value
            }
            for check in self.checks
        ]
    
    def run_specific_check(self, check_code: str, result: CrawlResult) -> Optional[FindingResult]:
        """Run a specific check by code"""
        for check in self.checks:
            if check.check_code == check_code:
                return check.check(result)
        return None
    
    def calculate_overall_score(self, findings: List[FindingResult]) -> float:
        """Calculate overall SEO score based on findings"""
        base_score = 100.0
        
        for finding in findings:
            if finding.severity == FindingSeverity.CRITICAL:
                base_score += finding.score_impact * 1.5
            elif finding.severity == FindingSeverity.HIGH:
                base_score += finding.score_impact * 1.2
            else:
                base_score += finding.score_impact
        
        return max(0.0, min(100.0, base_score))
    
    def get_category_scores(self, findings: List[FindingResult]) -> Dict[str, float]:
        """Get scores by category"""
        category_scores = {}
        categories = set(finding.category for finding in findings)
        
        for category in categories:
            category_findings = [f for f in findings if f.category == category]
            category_score = 100.0
            
            for finding in category_findings:
                if finding.severity == FindingSeverity.CRITICAL:
                    category_score += finding.score_impact * 1.5
                elif finding.severity == FindingSeverity.HIGH:
                    category_score += finding.score_impact * 1.2
                else:
                    category_score += finding.score_impact
            
            category_scores[category.value] = max(0.0, min(100.0, category_score))
        
        return category_scores
