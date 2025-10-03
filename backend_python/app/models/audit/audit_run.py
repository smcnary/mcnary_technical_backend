"""
Audit Run model for SEO audit orchestration
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum, DateTime, Integer
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum
from datetime import datetime
from typing import Optional

from app.models.base import Base, TimestampMixin


class AuditRunState(enum.Enum):
    """Audit run state"""
    DRAFT = "draft"
    QUEUED = "queued"
    RUNNING = "running"
    FAILED = "failed"
    CANCELED = "canceled"
    COMPLETED = "completed"


class AuditRun(Base, TimestampMixin):
    """Audit run entity for tracking SEO audit execution"""
    
    __tablename__ = "audit_runs"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    project_id = Column(UUID(as_uuid=True), ForeignKey("projects.id"), nullable=False)
    
    # Audit configuration
    name = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    state = Column(SQLEnum(AuditRunState), nullable=False, default=AuditRunState.DRAFT)
    
    # Execution tracking
    started_at = Column(DateTime(timezone=True), nullable=True)
    finished_at = Column(DateTime(timezone=True), nullable=True)
    requested_by = Column(UUID(as_uuid=True), ForeignKey("users.id"), nullable=False)
    
    # Configuration
    seed_urls = Column(JSONB, nullable=False, default=list)  # Starting URLs to crawl
    max_pages = Column(Integer, nullable=False, default=1000)
    crawl_depth = Column(Integer, nullable=False, default=3)
    respect_robots_txt = Column(String(10), nullable=False, default="yes")
    crawl_delay = Column(Integer, nullable=False, default=1)  # Seconds between requests
    
    # Audit scope and settings
    audit_config = Column(JSONB, nullable=True)  # Audit-specific configuration
    include_external_links = Column(String(10), nullable=False, default="no")
    include_images = Column(String(10), nullable=False, default="yes")
    include_js = Column(String(10), nullable=False, default="yes")
    include_css = Column(String(10), nullable=False, default="yes")
    
    # Results and metrics
    pages_crawled = Column(Integer, nullable=False, default=0)
    pages_analyzed = Column(Integer, nullable=False, default=0)
    findings_count = Column(Integer, nullable=False, default=0)
    critical_findings = Column(Integer, nullable=False, default=0)
    high_findings = Column(Integer, nullable=False, default=0)
    medium_findings = Column(Integer, nullable=False, default=0)
    low_findings = Column(Integer, nullable=False, default=0)
    
    # Performance metrics
    total_crawl_time = Column(Integer, nullable=True)  # Seconds
    average_page_load_time = Column(Integer, nullable=True)  # Milliseconds
    lighthouse_score = Column(Integer, nullable=True)  # Overall Lighthouse score
    
    # Error handling
    error_message = Column(Text, nullable=True)
    retry_count = Column(Integer, nullable=False, default=0)
    max_retries = Column(Integer, nullable=False, default=3)
    
    # Version and metadata
    version = Column(String(20), nullable=False, default="1.0.0")
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    project = relationship("Project", back_populates="audit_runs")
    requested_by_user = relationship("User")
    pages = relationship("Page", back_populates="audit_run", cascade="all, delete-orphan")
    findings = relationship("AuditFinding", back_populates="audit_run", cascade="all, delete-orphan")
    lighthouse_runs = relationship("LighthouseRun", back_populates="audit_run", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<AuditRun(id={self.id}, name='{self.name}', state={self.state.value})>"
    
    @property
    def duration(self) -> Optional[int]:
        """Get audit duration in seconds"""
        if self.started_at and self.finished_at:
            return int((self.finished_at - self.started_at).total_seconds())
        return None
    
    @property
    def completion_percentage(self) -> float:
        """Get completion percentage based on pages crawled vs max pages"""
        if self.max_pages == 0:
            return 0.0
        return min(100.0, (self.pages_crawled / self.max_pages) * 100)
    
    def can_start(self) -> bool:
        """Check if audit can be started"""
        return self.state in [AuditRunState.DRAFT, AuditRunState.FAILED]
    
    def can_cancel(self) -> bool:
        """Check if audit can be canceled"""
        return self.state in [AuditRunState.QUEUED, AuditRunState.RUNNING]
    
    def can_retry(self) -> bool:
        """Check if audit can be retried"""
        return (self.state == AuditRunState.FAILED and 
                self.retry_count < self.max_retries)
