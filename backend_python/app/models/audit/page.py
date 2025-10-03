"""
Page model for audit service
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum, Integer, Boolean
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class PageStatus(enum.Enum):
    """Page crawl status"""
    PENDING = "pending"
    CRAWLING = "crawling"
    CRAWLED = "crawled"
    ANALYZING = "analyzing"
    ANALYZED = "analyzed"
    FAILED = "failed"
    SKIPPED = "skipped"


class Page(Base, TimestampMixin):
    """Page entity for crawled web pages"""
    
    __tablename__ = "pages"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    audit_run_id = Column(UUID(as_uuid=True), ForeignKey("audit_runs.id"), nullable=False)
    
    # Page identification
    url = Column(String(500), nullable=False, index=True)
    canonical_url = Column(String(500), nullable=True)
    status = Column(SQLEnum(PageStatus), nullable=False, default=PageStatus.PENDING)
    
    # Page metadata
    title = Column(String(500), nullable=True)
    meta_description = Column(Text, nullable=True)
    meta_keywords = Column(Text, nullable=True)
    language = Column(String(10), nullable=True)
    content_type = Column(String(100), nullable=True)
    
    # Technical details
    status_code = Column(Integer, nullable=True)
    response_time = Column(Integer, nullable=True)  # Milliseconds
    content_length = Column(Integer, nullable=True)
    file_size = Column(Integer, nullable=True)  # Bytes
    
    # SEO elements
    h1_tags = Column(JSONB, nullable=True)  # Array of H1 tags
    h2_tags = Column(JSONB, nullable=True)  # Array of H2 tags
    h3_tags = Column(JSONB, nullable=True)  # Array of H3 tags
    images = Column(JSONB, nullable=True)  # Array of image data
    links = Column(JSONB, nullable=True)  # Array of link data
    scripts = Column(JSONB, nullable=True)  # Array of script data
    stylesheets = Column(JSONB, nullable=True)  # Array of CSS data
    
    # Content analysis
    word_count = Column(Integer, nullable=True)
    reading_time = Column(Integer, nullable=True)  # Minutes
    keyword_density = Column(JSONB, nullable=True)
    readability_score = Column(Integer, nullable=True)
    
    # Performance metrics
    dom_size = Column(Integer, nullable=True)
    redirect_chain = Column(JSONB, nullable=True)
    load_time = Column(Integer, nullable=True)  # Milliseconds
    time_to_first_byte = Column(Integer, nullable=True)
    
    # Crawl information
    depth = Column(Integer, nullable=False, default=0)
    parent_url = Column(String(500), nullable=True)
    discovered_at = Column(String(50), nullable=True)
    crawled_at = Column(String(50), nullable=True)
    
    # Error handling
    error_message = Column(Text, nullable=True)
    retry_count = Column(Integer, nullable=False, default=0)
    
    # Additional data
    raw_html = Column(Text, nullable=True)  # Full HTML content (optional)
    screenshots = Column(JSONB, nullable=True)  # Screenshot URLs
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    audit_run = relationship("AuditRun", back_populates="pages")
    findings = relationship("AuditFinding", back_populates="page", cascade="all, delete-orphan")
    lighthouse_runs = relationship("LighthouseRun", back_populates="page", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<Page(id={self.id}, url='{self.url}', status={self.status.value})>"
    
    @property
    def is_crawled(self) -> bool:
        """Check if page has been crawled"""
        return self.status in [PageStatus.CRAWLED, PageStatus.ANALYZED]
    
    @property
    def is_analyzed(self) -> bool:
        """Check if page has been analyzed"""
        return self.status == PageStatus.ANALYZED
    
    @property
    def has_errors(self) -> bool:
        """Check if page has errors"""
        return self.status == PageStatus.FAILED or bool(self.error_message)
    
    @property
    def total_findings(self) -> int:
        """Get total number of findings for this page"""
        return len(self.findings)
    
    @property
    def critical_findings(self) -> int:
        """Get number of critical findings"""
        from app.models.audit import FindingSeverity
        return len([f for f in self.findings if f.severity == FindingSeverity.CRITICAL])
    
    @property
    def high_findings(self) -> int:
        """Get number of high severity findings"""
        from app.models.audit import FindingSeverity
        return len([f for f in self.findings if f.severity == FindingSeverity.HIGH])
    
    def get_heading_structure(self) -> dict:
        """Get heading structure analysis"""
        structure = {}
        
        if self.h1_tags:
            structure["h1"] = len(self.h1_tags)
        if self.h2_tags:
            structure["h2"] = len(self.h2_tags)
        if self.h3_tags:
            structure["h3"] = len(self.h3_tags)
            
        return structure
    
    def get_image_analysis(self) -> dict:
        """Get image analysis summary"""
        if not self.images:
            return {"total": 0, "with_alt": 0, "without_alt": 0, "large_images": 0}
        
        total = len(self.images)
        with_alt = len([img for img in self.images if img.get("alt_text")])
        without_alt = total - with_alt
        large_images = len([img for img in self.images if img.get("file_size", 0) > 1024 * 1024])  # > 1MB
        
        return {
            "total": total,
            "with_alt": with_alt,
            "without_alt": without_alt,
            "large_images": large_images
        }
    
    def get_link_analysis(self) -> dict:
        """Get link analysis summary"""
        if not self.links:
            return {"total": 0, "internal": 0, "external": 0, "broken": 0}
        
        total = len(self.links)
        internal = len([link for link in self.links if link.get("type") == "internal"])
        external = len([link for link in self.links if link.get("type") == "external"])
        broken = len([link for link in self.links if link.get("status") == "broken"])
        
        return {
            "total": total,
            "internal": internal,
            "external": external,
            "broken": broken
        }
