"""
Ranking models for SEO tracking
"""

from sqlalchemy import Column, String, Integer, Date, ForeignKey, Enum as SQLEnum, Float
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum
from datetime import date

from app.models.base import Base, TimestampMixin


class SearchEngine(enum.Enum):
    """Search engine types"""
    GOOGLE = "google"
    BING = "bing"
    YAHOO = "yahoo"
    DUCKDUCKGO = "duckduckgo"


class DeviceType(enum.Enum):
    """Device types for ranking tracking"""
    DESKTOP = "desktop"
    MOBILE = "mobile"
    TABLET = "tablet"


class Ranking(Base, TimestampMixin):
    """Individual ranking tracking record"""
    
    __tablename__ = "rankings"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=False, index=True)
    keyword_id = Column(UUID(as_uuid=True), ForeignKey("keywords.id"), nullable=False, index=True)
    
    # Ranking details
    date = Column(Date, nullable=False, index=True)
    search_engine = Column(SQLEnum(SearchEngine), nullable=False, default=SearchEngine.GOOGLE)
    location = Column(String(100), nullable=True)  # City, State, Country
    device = Column(SQLEnum(DeviceType), nullable=True)
    
    # Ranking position and URL
    position = Column(Integer, nullable=False)  # 1-100, null if not in top 100
    url = Column(String(500), nullable=True)
    title = Column(String(255), nullable=True)
    snippet = Column(Text, nullable=True)
    
    # SERP features
    features = Column(JSONB, nullable=True)  # Featured snippets, knowledge panels, etc.
    metadata_json = Column(JSONB, nullable=True)  # Additional ranking metadata
    
    # Relationships
    client = relationship("Client", back_populates="rankings")
    keyword = relationship("Keyword", back_populates="rankings")
    
    def __repr__(self):
        return f"<Ranking(keyword='{self.keyword.keyword if self.keyword else 'Unknown'}', position={self.position}, date={self.date})>"


class RankingDaily(Base, TimestampMixin):
    """Daily aggregated ranking data"""
    
    __tablename__ = "ranking_daily"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    keyword_id = Column(UUID(as_uuid=True), ForeignKey("keywords.id"), nullable=False, index=True)
    
    # Date and metrics
    date = Column(Date, nullable=False, index=True)
    serp_position = Column(Integer, nullable=True)  # Average position
    url = Column(String(500), nullable=True)
    
    # Search Console metrics (if available)
    impressions = Column(Integer, nullable=True, default=0)
    clicks = Column(Integer, nullable=True, default=0)
    ctr = Column(Float, nullable=True)  # Click-through rate
    avg_position = Column(Float, nullable=True)  # Average position
    
    # SERP features and metadata
    serp_features = Column(JSONB, nullable=True)
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    keyword = relationship("Keyword")
    
    def __repr__(self):
        return f"<RankingDaily(keyword_id={self.keyword_id}, date={self.date}, position={self.serp_position})>"
