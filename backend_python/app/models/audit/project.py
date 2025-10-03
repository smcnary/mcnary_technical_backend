"""
Project model for audit service
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum, Boolean
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class ProjectStatus(enum.Enum):
    """Project status"""
    ACTIVE = "active"
    INACTIVE = "inactive"
    ARCHIVED = "archived"


class Project(Base, TimestampMixin):
    """Project entity for organizing audit runs"""
    
    __tablename__ = "projects"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=False)
    
    # Project details
    name = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    website_url = Column(String(500), nullable=False)
    status = Column(SQLEnum(ProjectStatus), nullable=False, default=ProjectStatus.ACTIVE)
    
    # Technical information
    cms = Column(String(64), nullable=True)  # wordpress, shopify, webflow, custom, etc.
    cms_version = Column(String(64), nullable=True)
    hosting_provider = Column(String(128), nullable=True)
    tech_stack = Column(JSONB, nullable=True)  # Framework, PHP version, database, etc.
    
    # Access and credentials
    has_google_analytics = Column(Boolean, nullable=False, default=False)
    has_search_console = Column(Boolean, nullable=False, default=False)
    has_google_business_profile = Column(Boolean, nullable=False, default=False)
    has_social_media = Column(Boolean, nullable=False, default=False)
    
    # Configuration
    default_crawl_settings = Column(JSONB, nullable=True)
    notification_settings = Column(JSONB, nullable=True)
    scheduled_audits = Column(JSONB, nullable=True)  # Cron expressions for scheduled audits
    
    # Additional metadata
    tags = Column(JSONB, nullable=True)
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    client = relationship("Client", back_populates="projects")
    audit_runs = relationship("AuditRun", back_populates="project", cascade="all, delete-orphan")
    credentials = relationship("Credential", back_populates="project", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<Project(id={self.id}, name='{self.name}', website='{self.website_url}')>"
    
    @property
    def latest_audit_run(self):
        """Get the most recent audit run"""
        if self.audit_runs:
            return max(self.audit_runs, key=lambda run: run.created_at)
        return None
    
    @property
    def total_audit_runs(self) -> int:
        """Get total number of audit runs"""
        return len(self.audit_runs)
    
    @property
    def successful_audit_runs(self) -> int:
        """Get number of successful audit runs"""
        from app.models.audit import AuditRunState
        return len([run for run in self.audit_runs if run.state == AuditRunState.COMPLETED])
    
    @property
    def failed_audit_runs(self) -> int:
        """Get number of failed audit runs"""
        from app.models.audit import AuditRunState
        return len([run for run in self.audit_runs if run.state == AuditRunState.FAILED])
