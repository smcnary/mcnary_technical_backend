"""
SEO tracking API endpoints
"""

from typing import List, Optional
from uuid import UUID
from datetime import date
from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_user, get_current_tenant
from app.services.seo_service import SeoService
from app.schemas.seo import (
    Keyword, KeywordCreate, KeywordUpdate, KeywordResponse,
    Ranking, RankingCreate, RankingUpdate, RankingResponse,
    Review, ReviewCreate, ReviewUpdate, ReviewResponse,
    Citation, CitationCreate, CitationUpdate, CitationResponse,
    SeoMeta, SeoMetaCreate, SeoMetaUpdate, SeoMetaResponse
)
from app.models import User, Tenant

router = APIRouter()


# Keyword endpoints
@router.post("/keywords", response_model=KeywordResponse, status_code=status.HTTP_201_CREATED)
async def create_keyword(
    keyword_data: KeywordCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new keyword"""
    seo_service = SeoService(db)
    return seo_service.create_keyword(current_tenant.id, keyword_data)


@router.get("/keywords", response_model=List[KeywordResponse])
async def get_keywords(
    client_id: Optional[UUID] = Query(None),
    status: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get keywords with optional filtering"""
    seo_service = SeoService(db)
    
    # Convert status string to enum if provided
    status_enum = None
    if status:
        try:
            from app.models.seo import KeywordStatus
            status_enum = KeywordStatus(status)
        except ValueError:
            raise HTTPException(status_code=400, detail="Invalid status value")
    
    return seo_service.get_keywords(current_tenant.id, client_id, status_enum, skip, limit)


@router.get("/keywords/{keyword_id}", response_model=KeywordResponse)
async def get_keyword(
    keyword_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get a keyword by ID"""
    seo_service = SeoService(db)
    keyword = seo_service.get_keyword(current_tenant.id, keyword_id)
    
    if not keyword:
        raise HTTPException(status_code=404, detail="Keyword not found")
    
    return keyword


@router.put("/keywords/{keyword_id}", response_model=KeywordResponse)
async def update_keyword(
    keyword_id: UUID,
    keyword_data: KeywordUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update a keyword"""
    seo_service = SeoService(db)
    keyword = seo_service.update_keyword(current_tenant.id, keyword_id, keyword_data)
    
    if not keyword:
        raise HTTPException(status_code=404, detail="Keyword not found")
    
    return keyword


@router.delete("/keywords/{keyword_id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_keyword(
    keyword_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Delete a keyword"""
    seo_service = SeoService(db)
    success = seo_service.delete_keyword(current_tenant.id, keyword_id)
    
    if not success:
        raise HTTPException(status_code=404, detail="Keyword not found")


# Ranking endpoints
@router.post("/rankings", response_model=RankingResponse, status_code=status.HTTP_201_CREATED)
async def create_ranking(
    ranking_data: RankingCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new ranking record"""
    seo_service = SeoService(db)
    return seo_service.create_ranking(current_tenant.id, ranking_data)


@router.get("/rankings", response_model=List[RankingResponse])
async def get_rankings(
    keyword_id: Optional[UUID] = Query(None),
    client_id: Optional[UUID] = Query(None),
    start_date: Optional[date] = Query(None),
    end_date: Optional[date] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get rankings with optional filtering"""
    seo_service = SeoService(db)
    return seo_service.get_rankings(
        current_tenant.id, keyword_id, client_id, start_date, end_date, skip, limit
    )


@router.put("/rankings/{ranking_id}", response_model=RankingResponse)
async def update_ranking(
    ranking_id: UUID,
    ranking_data: RankingUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update a ranking record"""
    seo_service = SeoService(db)
    ranking = seo_service.update_ranking(current_tenant.id, ranking_id, ranking_data)
    
    if not ranking:
        raise HTTPException(status_code=404, detail="Ranking not found")
    
    return ranking


# Review endpoints
@router.post("/reviews", response_model=ReviewResponse, status_code=status.HTTP_201_CREATED)
async def create_review(
    review_data: ReviewCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new review"""
    seo_service = SeoService(db)
    return seo_service.create_review(current_tenant.id, review_data)


@router.get("/reviews", response_model=List[ReviewResponse])
async def get_reviews(
    client_id: Optional[UUID] = Query(None),
    status: Optional[str] = Query(None),
    source: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get reviews with optional filtering"""
    seo_service = SeoService(db)
    
    # Convert status string to enum if provided
    status_enum = None
    if status:
        try:
            from app.models.seo import ReviewStatus
            status_enum = ReviewStatus(status)
        except ValueError:
            raise HTTPException(status_code=400, detail="Invalid status value")
    
    return seo_service.get_reviews(current_tenant.id, client_id, status_enum, source, skip, limit)


@router.put("/reviews/{review_id}", response_model=ReviewResponse)
async def update_review(
    review_id: UUID,
    review_data: ReviewUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update a review"""
    seo_service = SeoService(db)
    review = seo_service.update_review(current_tenant.id, review_id, review_data)
    
    if not review:
        raise HTTPException(status_code=404, detail="Review not found")
    
    return review


# Citation endpoints
@router.post("/citations", response_model=CitationResponse, status_code=status.HTTP_201_CREATED)
async def create_citation(
    citation_data: CitationCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new citation"""
    seo_service = SeoService(db)
    return seo_service.create_citation(current_tenant.id, citation_data)


@router.get("/citations", response_model=List[CitationResponse])
async def get_citations(
    client_id: Optional[UUID] = Query(None),
    status: Optional[str] = Query(None),
    platform_type: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get citations with optional filtering"""
    seo_service = SeoService(db)
    
    # Convert status string to enum if provided
    status_enum = None
    if status:
        try:
            from app.models.seo import CitationStatus
            status_enum = CitationStatus(status)
        except ValueError:
            raise HTTPException(status_code=400, detail="Invalid status value")
    
    return seo_service.get_citations(current_tenant.id, client_id, status_enum, platform_type, skip, limit)


@router.put("/citations/{citation_id}", response_model=CitationResponse)
async def update_citation(
    citation_id: UUID,
    citation_data: CitationUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update a citation"""
    seo_service = SeoService(db)
    citation = seo_service.update_citation(current_tenant.id, citation_id, citation_data)
    
    if not citation:
        raise HTTPException(status_code=404, detail="Citation not found")
    
    return citation


# SEO Meta endpoints
@router.post("/seo-meta", response_model=SeoMetaResponse, status_code=status.HTTP_201_CREATED)
async def create_seo_meta(
    seo_meta_data: SeoMetaCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create SEO metadata"""
    seo_service = SeoService(db)
    return seo_service.create_seo_meta(current_tenant.id, seo_meta_data)


@router.get("/seo-meta/{entity_type}/{entity_id}", response_model=SeoMetaResponse)
async def get_seo_meta(
    entity_type: str,
    entity_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get SEO metadata for an entity"""
    seo_service = SeoService(db)
    seo_meta = seo_service.get_seo_meta(current_tenant.id, entity_type, entity_id)
    
    if not seo_meta:
        raise HTTPException(status_code=404, detail="SEO metadata not found")
    
    return seo_meta


@router.put("/seo-meta/{entity_type}/{entity_id}", response_model=SeoMetaResponse)
async def update_seo_meta(
    entity_type: str,
    entity_id: UUID,
    seo_meta_data: SeoMetaUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update SEO metadata"""
    seo_service = SeoService(db)
    seo_meta = seo_service.update_seo_meta(current_tenant.id, entity_type, entity_id, seo_meta_data)
    
    if not seo_meta:
        raise HTTPException(status_code=404, detail="SEO metadata not found")
    
    return seo_meta


# Analytics endpoints
@router.get("/analytics/keyword-performance/{client_id}")
async def get_keyword_performance(
    client_id: UUID,
    start_date: date = Query(...),
    end_date: date = Query(...),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get keyword performance analytics"""
    seo_service = SeoService(db)
    return seo_service.get_keyword_performance(current_tenant.id, client_id, start_date, end_date)


@router.get("/analytics/review-summary/{client_id}")
async def get_review_summary(
    client_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get review summary analytics"""
    seo_service = SeoService(db)
    return seo_service.get_review_summary(current_tenant.id, client_id)


@router.get("/analytics/citation-summary/{client_id}")
async def get_citation_summary(
    client_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get citation summary analytics"""
    seo_service = SeoService(db)
    return seo_service.get_citation_summary(current_tenant.id, client_id)
