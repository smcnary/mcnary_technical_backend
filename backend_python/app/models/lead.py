"""
Lead model - migrated from Symfony Lead entity
"""

from datetime import datetime
from sqlalchemy import Column, String, Text, ForeignKey, Boolean
from sqlalchemy.dialects.postgresql import UUID, JSONB, ARRAY
from sqlalchemy.orm import relationship

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class Lead(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "leads"
    
    # Client relationship
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=True)
    client = relationship("Client", back_populates="leads")
    
    # Lead source relationship (will be implemented later)
    # source_id = Column(UUID(as_uuid=True), ForeignKey("lead_sources.id"), nullable=True)
    # source = relationship("LeadSource", back_populates="leads")
    
    # Lead information
    full_name = Column(String(255), nullable=False)
    email = Column(String(255), nullable=False)
    phone = Column(String(50), nullable=True)
    firm = Column(String(255), nullable=True)
    website = Column(String(500), nullable=True)
    practice_areas = Column(ARRAY(String), nullable=True, default=list)
    city = Column(String(100), nullable=True)
    state = Column(String(100), nullable=True)
    zip_code = Column(String(20), nullable=True)
    message = Column(Text, nullable=True)
    
    # Lead status and tracking
    status = Column(String(50), default="new_lead", nullable=False)  # new_lead, contacted, qualified, etc.
    utm_json = Column(JSONB, nullable=True, default=dict)
    interview_scheduled = Column(String(255), nullable=True)  # Will be datetime in future
    follow_up_date = Column(String(255), nullable=True)  # Will be datetime in future
    notes = Column(Text, nullable=True)
    is_test = Column(Boolean, default=False, nullable=False)
    
    # Relationships
    # events = relationship("LeadEvent", back_populates="lead")
    
    def __repr__(self):
        return f"<Lead(id={self.id}, full_name='{self.full_name}', email='{self.email}')>"