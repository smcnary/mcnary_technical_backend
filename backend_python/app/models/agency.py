"""
Agency model - migrated from Symfony Agency entity
"""

from sqlalchemy import Column, String, Text
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class Agency(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "agencies"
    
    name = Column(String(255), nullable=False)
    domain = Column(String(255), nullable=True)
    description = Column(Text, nullable=True)
    website_url = Column(String(255), nullable=True)
    phone = Column(String(50), nullable=True)
    email = Column(String(255), nullable=True)
    address = Column(String(255), nullable=True)
    city = Column(String(100), nullable=True)
    state = Column(String(100), nullable=True)
    postal_code = Column(String(20), nullable=True)
    country = Column(String(100), nullable=True)
    status = Column(String(50), default="active", nullable=False)
    metadata_json = Column(JSONB, nullable=True, default=dict)
    
    # Relationships
    users = relationship("User", back_populates="agency")
    clients = relationship("Client", back_populates="agency")
    
    def __repr__(self):
        return f"<Agency(id={self.id}, name='{self.name}')>"
