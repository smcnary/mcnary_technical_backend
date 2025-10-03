"""
Tenant model
"""

from sqlalchemy import Column, String
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid

from app.models.base import TimestampMixin
from app.core.database import Base

class Tenant(Base, TimestampMixin):
    """Tenant model"""
    
    __tablename__ = "tenants"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), nullable=False, unique=True)
    status = Column(String(50), default="trial", nullable=False)
    timezone = Column(String(50), default="UTC", nullable=False)
    
    # Relationships - commented out to avoid circular imports for now
    # users = relationship("User", back_populates="tenant")
