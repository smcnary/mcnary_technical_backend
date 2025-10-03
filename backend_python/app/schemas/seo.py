"""
Pydantic schemas for SEO tracking
"""

from pydantic import BaseModel, Field, ConfigDict
from typing import Optional, List, Dict, Any
from datetime import date, datetime
from uuid import UUID

from app.models.seo import KeywordStatus, KeywordType, SearchEngine, DeviceType, ReviewSource, ReviewStatus, CitationType, CitationStatus


# Keyword Schemas
class KeywordBase(BaseModel):
    keyword: str = Field(..., max_length=255)
    keyword_type: KeywordType = KeywordType.PRIMARY
    status: KeywordStatus = KeywordStatus.ACTIVE
    search_volume: Optional[int] = None
    difficulty: Optional[int] = Field(None, ge=1, le=100)
    cpc: Optional[int] = None
    target_url: Optional[str] = Field(None, max_length=500)
    target_location: Optional[str] = Field(None, max_length=100)
    target_device: Optional[str] = Field(None, max_length=20)
    target_search_engines: Optional[List[str]] = None
    tags: Optional[Dict[str, Any]] = None
    notes: Optional[str] = None


class KeywordCreate(KeywordBase):
    client_id: UUID


class KeywordUpdate(BaseModel):
    keyword: Optional[str] = Field(None, max_length=255)
    keyword_type: Optional[KeywordType] = None
    status: Optional[KeywordStatus] = None
    search_volume: Optional[int] = None
    difficulty: Optional[int] = Field(None, ge=1, le=100)
    cpc: Optional[int] = None
    target_url: Optional[str] = Field(None, max_length=500)
    target_location: Optional[str] = Field(None, max_length=100)
    target_device: Optional[str] = Field(None, max_length=20)
    target_search_engines: Optional[List[str]] = None
    tags: Optional[Dict[str, Any]] = None
    notes: Optional[str] = None


class Keyword(KeywordBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    client_id: UUID
    current_position: Optional[int] = None
    best_position: Optional[int] = None
    worst_position: Optional[int] = None
    average_position: Optional[int] = None
    impressions: Optional[int] = 0
    clicks: Optional[int] = 0
    ctr: Optional[int] = None
    created_at: datetime
    updated_at: datetime


class KeywordResponse(Keyword):
    pass


# Ranking Schemas
class RankingBase(BaseModel):
    date: date
    search_engine: SearchEngine = SearchEngine.GOOGLE
    location: Optional[str] = Field(None, max_length=100)
    device: Optional[DeviceType] = None
    position: int = Field(..., ge=1, le=100)
    url: Optional[str] = Field(None, max_length=500)
    title: Optional[str] = Field(None, max_length=255)
    snippet: Optional[str] = None
    features: Optional[Dict[str, Any]] = None
    metadata: Optional[Dict[str, Any]] = None


class RankingCreate(RankingBase):
    client_id: UUID
    keyword_id: UUID


class RankingUpdate(BaseModel):
    date: Optional[date] = None
    search_engine: Optional[SearchEngine] = None
    location: Optional[str] = Field(None, max_length=100)
    device: Optional[DeviceType] = None
    position: Optional[int] = Field(None, ge=1, le=100)
    url: Optional[str] = Field(None, max_length=500)
    title: Optional[str] = Field(None, max_length=255)
    snippet: Optional[str] = None
    features: Optional[Dict[str, Any]] = None
    metadata: Optional[Dict[str, Any]] = None


class Ranking(RankingBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    client_id: UUID
    keyword_id: UUID
    created_at: datetime
    updated_at: datetime


class RankingResponse(Ranking):
    pass


# Review Schemas
class ReviewBase(BaseModel):
    source: ReviewSource
    external_id: Optional[str] = Field(None, max_length=255)
    url: Optional[str] = Field(None, max_length=500)
    rating: int = Field(..., ge=1, le=5)
    title: Optional[str] = Field(None, max_length=255)
    content: Optional[str] = None
    author_name: Optional[str] = Field(None, max_length=255)
    author_photo_url: Optional[str] = Field(None, max_length=500)
    review_date: Optional[str] = Field(None, max_length=50)
    helpful_votes: Optional[int] = 0
    verified_purchase: Optional[bool] = False
    status: ReviewStatus = ReviewStatus.PENDING
    is_featured: Optional[bool] = False
    response_content: Optional[str] = None
    response_date: Optional[str] = Field(None, max_length=50)
    response_author: Optional[str] = Field(None, max_length=255)
    sentiment_score: Optional[float] = Field(None, ge=-1.0, le=1.0)
    keywords: Optional[List[str]] = None
    categories: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None
    tags: Optional[List[str]] = None


class ReviewCreate(ReviewBase):
    client_id: UUID


class ReviewUpdate(BaseModel):
    source: Optional[ReviewSource] = None
    external_id: Optional[str] = Field(None, max_length=255)
    url: Optional[str] = Field(None, max_length=500)
    rating: Optional[int] = Field(None, ge=1, le=5)
    title: Optional[str] = Field(None, max_length=255)
    content: Optional[str] = None
    author_name: Optional[str] = Field(None, max_length=255)
    author_photo_url: Optional[str] = Field(None, max_length=500)
    review_date: Optional[str] = Field(None, max_length=50)
    helpful_votes: Optional[int] = None
    verified_purchase: Optional[bool] = None
    status: Optional[ReviewStatus] = None
    is_featured: Optional[bool] = None
    response_content: Optional[str] = None
    response_date: Optional[str] = Field(None, max_length=50)
    response_author: Optional[str] = Field(None, max_length=255)
    sentiment_score: Optional[float] = Field(None, ge=-1.0, le=1.0)
    keywords: Optional[List[str]] = None
    categories: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None
    tags: Optional[List[str]] = None


class Review(ReviewBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    client_id: UUID
    created_at: datetime
    updated_at: datetime


class ReviewResponse(Review):
    pass


# Citation Schemas
class CitationBase(BaseModel):
    platform_name: str = Field(..., max_length=255)
    platform_type: CitationType
    url: Optional[str] = Field(None, max_length=500)
    business_name: Optional[str] = Field(None, max_length=255)
    address: Optional[str] = None
    city: Optional[str] = Field(None, max_length=100)
    state: Optional[str] = Field(None, max_length=50)
    zip_code: Optional[str] = Field(None, max_length=20)
    country: Optional[str] = Field(None, max_length=50)
    phone: Optional[str] = Field(None, max_length=50)
    website: Optional[str] = Field(None, max_length=500)
    email: Optional[str] = Field(None, max_length=255)
    status: CitationStatus = CitationStatus.PENDING
    is_verified: Optional[bool] = False
    is_claimed: Optional[bool] = False
    domain_authority: Optional[int] = None
    traffic_estimate: Optional[int] = None
    citation_flow: Optional[int] = None
    categories: Optional[List[str]] = None
    hours: Optional[Dict[str, Any]] = None
    amenities: Optional[List[str]] = None
    photos: Optional[List[str]] = None
    notes: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None


class CitationCreate(CitationBase):
    client_id: UUID


class CitationUpdate(BaseModel):
    platform_name: Optional[str] = Field(None, max_length=255)
    platform_type: Optional[CitationType] = None
    url: Optional[str] = Field(None, max_length=500)
    business_name: Optional[str] = Field(None, max_length=255)
    address: Optional[str] = None
    city: Optional[str] = Field(None, max_length=100)
    state: Optional[str] = Field(None, max_length=50)
    zip_code: Optional[str] = Field(None, max_length=20)
    country: Optional[str] = Field(None, max_length=50)
    phone: Optional[str] = Field(None, max_length=50)
    website: Optional[str] = Field(None, max_length=500)
    email: Optional[str] = Field(None, max_length=255)
    status: Optional[CitationStatus] = None
    is_verified: Optional[bool] = None
    is_claimed: Optional[bool] = None
    domain_authority: Optional[int] = None
    traffic_estimate: Optional[int] = None
    citation_flow: Optional[int] = None
    categories: Optional[List[str]] = None
    hours: Optional[Dict[str, Any]] = None
    amenities: Optional[List[str]] = None
    photos: Optional[List[str]] = None
    notes: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None


class Citation(CitationBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    client_id: UUID
    created_at: datetime
    updated_at: datetime


class CitationResponse(Citation):
    pass


# SEO Meta Schemas
class SeoMetaBase(BaseModel):
    entity_type: str = Field(..., max_length=50)
    entity_id: UUID
    title: Optional[str] = Field(None, max_length=255)
    meta_description: Optional[str] = None
    canonical_url: Optional[str] = Field(None, max_length=500)
    robots: Optional[str] = Field(None, max_length=100)
    open_graph: Optional[Dict[str, Any]] = None
    twitter_card: Optional[Dict[str, Any]] = None
    structured_data: Optional[Dict[str, Any]] = None
    keywords: Optional[str] = None
    author: Optional[str] = Field(None, max_length=255)
    language: Optional[str] = Field(None, max_length=10, default="en")
    title_length: Optional[str] = Field(None, max_length=10)
    description_length: Optional[str] = Field(None, max_length=10)
    keyword_density: Optional[Dict[str, Any]] = None


class SeoMetaCreate(SeoMetaBase):
    pass


class SeoMetaUpdate(BaseModel):
    entity_type: Optional[str] = Field(None, max_length=50)
    entity_id: Optional[UUID] = None
    title: Optional[str] = Field(None, max_length=255)
    meta_description: Optional[str] = None
    canonical_url: Optional[str] = Field(None, max_length=500)
    robots: Optional[str] = Field(None, max_length=100)
    open_graph: Optional[Dict[str, Any]] = None
    twitter_card: Optional[Dict[str, Any]] = None
    structured_data: Optional[Dict[str, Any]] = None
    keywords: Optional[str] = None
    author: Optional[str] = Field(None, max_length=255)
    language: Optional[str] = Field(None, max_length=10)
    title_length: Optional[str] = Field(None, max_length=10)
    description_length: Optional[str] = Field(None, max_length=10)
    keyword_density: Optional[Dict[str, Any]] = None


class SeoMeta(SeoMetaBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    created_at: datetime
    updated_at: datetime


class SeoMetaResponse(SeoMeta):
    pass
