"""
Organization model - migrated from Symfony Organization entity
"""

from sqlalchemy import Column, String
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class Organization(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "organizations"
    
    name = Column(String(255), nullable=False)
    domain = Column(String(255), nullable=True)
    status = Column(String(50), default="active", nullable=False)
    metadata_json = Column(JSONB, nullable=True, default=dict)
    
    # Relationships
    users = relationship("User", back_populates="organization")
    
    def __repr__(self):
        return f"<Organization(id={self.id}, name='{self.name}')>"