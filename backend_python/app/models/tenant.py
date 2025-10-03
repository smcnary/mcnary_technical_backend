"""
Tenant model - migrated from Symfony Tenant entity
"""

from sqlalchemy import Column, String
from sqlalchemy.dialects.postgresql import UUID
from sqlalchemy.orm import relationship

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class Tenant(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "tenants"
    
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False, index=True)
    status = Column(String(50), default="trial", nullable=False)  # trial, active, suspended
    timezone = Column(String(50), default="UTC", nullable=False)
    
    # Relationships
    users = relationship("User", back_populates="tenant")
    
    def __repr__(self):
        return f"<Tenant(id={self.id}, name='{self.name}', slug='{self.slug}')>"
