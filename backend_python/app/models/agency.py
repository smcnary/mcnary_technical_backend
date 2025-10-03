"""
Agency model
"""

from sqlalchemy import Column, String, Text
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid

from app.models.base import TimestampMixin
from app.core.database import Base

class Agency(Base, TimestampMixin):
    """Agency model"""
    
    __tablename__ = "agencies"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), nullable=False, unique=True)
    description = Column(Text, nullable=True)
    
    # Relationships - commented out to avoid circular imports for now
    # users = relationship("User", back_populates="agency")
