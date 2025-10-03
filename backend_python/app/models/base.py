"""
Base model with common fields and timestamps
"""

from datetime import datetime
from sqlalchemy import Column, DateTime, String
from sqlalchemy.dialects.postgresql import UUID
from sqlalchemy.sql import func
from sqlalchemy.ext.declarative import declared_attr
import uuid

class TimestampMixin:
    """Mixin for created_at and updated_at fields"""
    
    created_at = Column(DateTime(timezone=True), server_default=func.now(), nullable=False)
    updated_at = Column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now(), nullable=False)

class UUIDMixin:
    """Mixin for UUID primary key"""
    
    @declared_attr
    def id(cls):
        return Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4, nullable=False)

class BaseModel(TimestampMixin, UUIDMixin):
    """Base model with UUID and timestamps"""
    
    @declared_attr
    def __tablename__(cls):
        return cls.__name__.lower() + 's'
