"""
Review model for reputation management
"""

from sqlalchemy import Column, String, Text, Integer, Float, Boolean, ForeignKey, Enum as SQLEnum
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum
from datetime import datetime

from app.models.base import Base, TimestampMixin


class ReviewSource(enum.Enum):
    """Review platform sources"""
    GOOGLE = "google"
    YELP = "yelp"
    FACEBOOK = "facebook"
    TRIPADVISOR = "tripadvisor"
    BBB = "bbb"
    GLASSDOOR = "glassdoor"
    INDEED = "indeed"
    ANGIES_LIST = "angies_list"
    HOMESTARS = "homestars"
    MANUAL = "manual"


class ReviewStatus(enum.Enum):
    """Review status"""
    PENDING = "pending"
    APPROVED = "approved"
    RESPONDED = "responded"
    HIDDEN = "hidden"
    FLAGGED = "flagged"


class Review(Base, TimestampMixin):
    """Review tracking entity"""
    
    __tablename__ = "reviews"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    client_id = Column(UUID(as_uuid=True), ForeignKey("clients.id"), nullable=False, index=True)
    
    # Review details
    source = Column(SQLEnum(ReviewSource), nullable=False)
    external_id = Column(String(255), nullable=True)  # ID from the review platform
    url = Column(String(500), nullable=True)  # Link to the review
    
    # Review content
    rating = Column(Integer, nullable=False)  # 1-5 stars
    title = Column(String(255), nullable=True)
    content = Column(Text, nullable=True)
    author_name = Column(String(255), nullable=True)
    author_photo_url = Column(String(500), nullable=True)
    
    # Review metadata
    review_date = Column(String(50), nullable=True)  # Original review date from platform
    helpful_votes = Column(Integer, nullable=True, default=0)
    verified_purchase = Column(Boolean, nullable=True, default=False)
    
    # Management
    status = Column(SQLEnum(ReviewStatus), nullable=False, default=ReviewStatus.PENDING)
    is_featured = Column(Boolean, nullable=False, default=False)
    
    # Response tracking
    response_content = Column(Text, nullable=True)
    response_date = Column(String(50), nullable=True)
    response_author = Column(String(255), nullable=True)
    
    # Analytics
    sentiment_score = Column(Float, nullable=True)  # -1 to 1 sentiment analysis
    keywords = Column(JSONB, nullable=True)  # Extracted keywords
    categories = Column(JSONB, nullable=True)  # Review categories
    
    # Additional metadata
    metadata_json = Column(JSONB, nullable=True)
    tags = Column(JSONB, nullable=True)
    
    # Relationships
    client = relationship("Client", back_populates="reviews")
    
    def __repr__(self):
        return f"<Review(source={self.source.value}, rating={self.rating}, author='{self.author_name}')>"
