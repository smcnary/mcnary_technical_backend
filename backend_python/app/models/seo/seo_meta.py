"""
SEO Meta model for content metadata tracking
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class SeoMeta(Base, TimestampMixin):
    """SEO metadata for pages and content"""
    
    __tablename__ = "seo_meta"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    
    # Entity reference
    entity_type = Column(String(50), nullable=False)  # 'page', 'post', 'product', etc.
    entity_id = Column(UUID(as_uuid=True), nullable=False)
    
    # Basic meta tags
    title = Column(String(255), nullable=True)
    meta_description = Column(Text, nullable=True)
    canonical_url = Column(String(500), nullable=True)
    robots = Column(String(100), nullable=True)  # noindex, nofollow, etc.
    
    # Open Graph tags
    open_graph = Column(JSONB, nullable=True)  # og:title, og:description, og:image, etc.
    
    # Twitter Card tags
    twitter_card = Column(JSONB, nullable=True)  # twitter:card, twitter:title, etc.
    
    # Structured data
    structured_data = Column(JSONB, nullable=True)  # JSON-LD structured data
    
    # Additional metadata
    keywords = Column(Text, nullable=True)  # meta keywords (legacy)
    author = Column(String(255), nullable=True)
    language = Column(String(10), nullable=True, default="en")
    
    # SEO analysis results
    title_length = Column(String(10), nullable=True)  # Character count
    description_length = Column(String(10), nullable=True)  # Character count
    keyword_density = Column(JSONB, nullable=True)  # Keyword analysis
    
    # Relationships
    # Note: entity_id references different tables based on entity_type
    # This would need to be handled in the application layer
    
    def __repr__(self):
        return f"<SeoMeta(entity_type='{self.entity_type}', entity_id={self.entity_id}, title='{self.title}')>"
