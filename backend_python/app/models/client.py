"""
Client model - migrated from Symfony Client entity
"""

from sqlalchemy import Column, String, Text, ForeignKey
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class Client(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "clients"
    
    # Agency relationship
    agency_id = Column(UUID(as_uuid=True), ForeignKey("agencies.id"), nullable=False)
    agency = relationship("Agency", back_populates="clients")
    
    # Client information
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=True, index=True)
    description = Column(Text, nullable=True)
    website_url = Column(String(255), nullable=True)
    phone = Column(String(50), nullable=True)
    email = Column(String(255), nullable=True)
    address = Column(String(255), nullable=True)
    city = Column(String(100), nullable=True)
    state = Column(String(100), nullable=True)
    postal_code = Column(String(20), nullable=True)
    country = Column(String(100), nullable=True)
    industry = Column(String(100), default="law", nullable=False)
    status = Column(String(50), default="active", nullable=False)
    
    # Google integrations
    google_business_profile = Column(JSONB, nullable=True, default=dict)
    google_search_console = Column(JSONB, nullable=True, default=dict)
    google_analytics = Column(JSONB, nullable=True, default=dict)
    
    # Additional metadata
    metadata_json = Column(JSONB, nullable=True, default=dict)
    
    # Relationships
    leads = relationship("Lead", back_populates="client")
    keywords = relationship("Keyword", back_populates="client")
    rankings = relationship("Ranking", back_populates="client")
    reviews = relationship("Review", back_populates="client")
    citations = relationship("Citation", back_populates="client")
    projects = relationship("Project", back_populates="client")
    
    def __repr__(self):
        return f"<Client(id={self.id}, name='{self.name}')>"