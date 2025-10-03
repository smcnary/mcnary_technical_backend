"""
Keyword model for SEO tracking
"""

from sqlalchemy import Column, String, Text, Integer, Boolean, ForeignKey, Enum as SQLEnum
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class KeywordStatus(enum.Enum):
    """Keyword tracking status"""
    ACTIVE = "active"
    PAUSED = "paused"
    ARCHIVED = "archived"


class KeywordType(enum.Enum):
    """Keyword type classification"""
    PRIMARY = "primary"
    SECONDARY = "secondary"
    LONG_TAIL = "long_tail"
    BRAND = "brand"
    COMPETITOR = "competitor"


class Keyword(Base, TimestampMixin):
    """Keyword tracking entity"""
    
    __tablename__ = "keywords"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=False, index=True)
    
    # Keyword details
    keyword = Column(String(255), nullable=False, index=True)
    keyword_type = Column(SQLEnum(KeywordType), nullable=False, default=KeywordType.PRIMARY)
    status = Column(SQLEnum(KeywordStatus), nullable=False, default=KeywordStatus.ACTIVE)
    
    # SEO metadata
    search_volume = Column(Integer, nullable=True)
    difficulty = Column(Integer, nullable=True)  # 1-100 scale
    cpc = Column(Integer, nullable=True)  # Cost per click in cents
    
    # Tracking settings
    target_url = Column(String(500), nullable=True)
    target_location = Column(String(100), nullable=True)  # City, State, Country
    target_device = Column(String(20), nullable=True)  # desktop, mobile, tablet
    target_search_engines = Column(JSONB, nullable=True)  # ["google", "bing", "yahoo"]
    
    # Performance tracking
    current_position = Column(Integer, nullable=True)
    best_position = Column(Integer, nullable=True)
    worst_position = Column(Integer, nullable=True)
    average_position = Column(Integer, nullable=True)
    
    # Analytics data
    impressions = Column(Integer, nullable=True, default=0)
    clicks = Column(Integer, nullable=True, default=0)
    ctr = Column(Integer, nullable=True)  # Click-through rate in basis points (0.01%)
    
    # Additional metadata
    tags = Column(JSONB, nullable=True)
    notes = Column(Text, nullable=True)
    
    # Relationships
    client = relationship("Client", back_populates="keywords")
    rankings = relationship("Ranking", back_populates="keyword", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<Keyword(id={self.id}, keyword='{self.keyword}', position={self.current_position})>"
