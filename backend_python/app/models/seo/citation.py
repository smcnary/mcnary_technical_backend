"""
Citation model for local SEO tracking
"""

from sqlalchemy import Column, String, Text, Boolean, ForeignKey, Enum as SQLEnum, Integer
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class CitationType(enum.Enum):
    """Citation platform types"""
    BUSINESS_DIRECTORY = "business_directory"
    SOCIAL_MEDIA = "social_media"
    REVIEW_SITE = "review_site"
    NEWS_SITE = "news_site"
    INDUSTRY_SITE = "industry_site"
    LOCAL_SITE = "local_site"
    OTHER = "other"


class CitationStatus(enum.Enum):
    """Citation status"""
    PENDING = "pending"
    VERIFIED = "verified"
    CLAIMED = "claimed"
    NEEDS_UPDATE = "needs_update"
    REMOVED = "removed"
    ERROR = "error"


class Citation(Base, TimestampMixin):
    """Citation tracking entity for local SEO"""
    
    __tablename__ = "citations"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=False, index=True)
    
    # Citation details
    platform_name = Column(String(255), nullable=False)
    platform_type = Column(SQLEnum(CitationType), nullable=False)
    url = Column(String(500), nullable=True)
    
    # Business information consistency
    business_name = Column(String(255), nullable=True)
    address = Column(Text, nullable=True)
    city = Column(String(100), nullable=True)
    state = Column(String(50), nullable=True)
    zip_code = Column(String(20), nullable=True)
    country = Column(String(50), nullable=True)
    phone = Column(String(50), nullable=True)
    website = Column(String(500), nullable=True)
    email = Column(String(255), nullable=True)
    
    # Citation management
    status = Column(SQLEnum(CitationStatus), nullable=False, default=CitationStatus.PENDING)
    is_verified = Column(Boolean, nullable=False, default=False)
    is_claimed = Column(Boolean, nullable=False, default=False)
    
    # Quality metrics
    domain_authority = Column(Integer, nullable=True)  # Moz DA score
    traffic_estimate = Column(Integer, nullable=True)  # Estimated monthly traffic
    citation_flow = Column(Integer, nullable=True)  # Citation flow score
    
    # Additional data
    categories = Column(JSONB, nullable=True)  # Business categories
    hours = Column(JSONB, nullable=True)  # Business hours
    amenities = Column(JSONB, nullable=True)  # Business amenities
    photos = Column(JSONB, nullable=True)  # Photo URLs
    
    # Notes and metadata
    notes = Column(Text, nullable=True)
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    client = relationship("Client", back_populates="citations")
    
    def __repr__(self):
        return f"<Citation(platform='{self.platform_name}', status={self.status.value}, url='{self.url}')>"
