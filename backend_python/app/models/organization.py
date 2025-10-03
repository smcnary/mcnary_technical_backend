"""
Organization model
"""

from sqlalchemy import Column, String
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid

from app.models.base import TimestampMixin
from app.core.database import Base

class Organization(Base, TimestampMixin):
    """Organization model"""
    
    __tablename__ = "organizations"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    name = Column(String(255), nullable=False)
    
    # Relationships - commented out to avoid circular imports for now
    # users = relationship("User", back_populates="organization")
