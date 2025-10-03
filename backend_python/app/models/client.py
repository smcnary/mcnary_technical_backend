"""
Client model
"""

from sqlalchemy import Column, String, Text
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid

from app.models.base import TimestampMixin
from app.core.database import Base

class Client(Base, TimestampMixin):
    """Client model"""
    
    __tablename__ = "clients"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), nullable=False, unique=True)
    description = Column(Text, nullable=True)
    website = Column(String(500), nullable=True)
    phone = Column(String(50), nullable=True)
    address = Column(Text, nullable=True)
    city = Column(String(100), nullable=True)
    state = Column(String(50), nullable=True)
    zip_code = Column(String(20), nullable=True)
    
    # Relationships - commented out to avoid circular imports for now
    # leads = relationship("Lead", back_populates="client")
