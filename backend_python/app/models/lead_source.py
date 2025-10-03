"""
Lead Source model
"""

from sqlalchemy import Column, String, Text
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid

from app.models.base import TimestampMixin
from app.core.database import Base

class LeadSource(Base, TimestampMixin):
    """Lead Source model"""
    
    __tablename__ = "lead_sources"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    name = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    url = Column(String(500), nullable=True)
    
    # Relationships - commented out to avoid circular imports for now
    # leads = relationship("Lead", back_populates="source")
