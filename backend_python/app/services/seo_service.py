"""
SEO tracking service
"""

from typing import List, Optional, Dict, Any
from uuid import UUID
from datetime import date, datetime
from sqlalchemy.orm import Session
from sqlalchemy import and_, or_, func

from app.models.seo import Keyword, Ranking, Review, Citation, SeoMeta, KeywordStatus, ReviewStatus, CitationStatus
from app.schemas.seo import (
    KeywordCreate, KeywordUpdate, RankingCreate, RankingUpdate,
    ReviewCreate, ReviewUpdate, CitationCreate, CitationUpdate,
    SeoMetaCreate, SeoMetaUpdate
)


class SeoService:
    """Service for SEO tracking operations"""
    
    def __init__(self, db: Session):
        self.db = db
    
    # Keyword Management
    def create_keyword(self, tenant_id: UUID, keyword_data: KeywordCreate) -> Keyword:
        """Create a new keyword"""
        keyword = Keyword(
            tenant_id=tenant_id,
            **keyword_data.model_dump()
        )
        self.db.add(keyword)
        self.db.commit()
        self.db.refresh(keyword)
        return keyword
    
    def get_keyword(self, tenant_id: UUID, keyword_id: UUID) -> Optional[Keyword]:
        """Get a keyword by ID"""
        return self.db.query(Keyword).filter(
            and_(Keyword.id == keyword_id, Keyword.tenant_id == tenant_id)
        ).first()
    
    def get_keywords(self, tenant_id: UUID, client_id: Optional[UUID] = None, 
                    status: Optional[KeywordStatus] = None, skip: int = 0, limit: int = 100) -> List[Keyword]:
        """Get keywords with optional filtering"""
        query = self.db.query(Keyword).filter(Keyword.tenant_id == tenant_id)
        
        if client_id:
            query = query.filter(Keyword.client_id == client_id)
        if status:
            query = query.filter(Keyword.status == status)
            
        return query.offset(skip).limit(limit).all()
    
    def update_keyword(self, tenant_id: UUID, keyword_id: UUID, keyword_data: KeywordUpdate) -> Optional[Keyword]:
        """Update a keyword"""
        keyword = self.get_keyword(tenant_id, keyword_id)
        if not keyword:
            return None
            
        update_data = keyword_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(keyword, field, value)
            
        self.db.commit()
        self.db.refresh(keyword)
        return keyword
    
    def delete_keyword(self, tenant_id: UUID, keyword_id: UUID) -> bool:
        """Delete a keyword"""
        keyword = self.get_keyword(tenant_id, keyword_id)
        if not keyword:
            return False
            
        self.db.delete(keyword)
        self.db.commit()
        return True
    
    # Ranking Management
    def create_ranking(self, tenant_id: UUID, ranking_data: RankingCreate) -> Ranking:
        """Create a new ranking record"""
        ranking = Ranking(
            tenant_id=tenant_id,
            **ranking_data.model_dump()
        )
        self.db.add(ranking)
        self.db.commit()
        self.db.refresh(ranking)
        return ranking
    
    def get_rankings(self, tenant_id: UUID, keyword_id: Optional[UUID] = None,
                    client_id: Optional[UUID] = None, start_date: Optional[date] = None,
                    end_date: Optional[date] = None, skip: int = 0, limit: int = 100) -> List[Ranking]:
        """Get rankings with optional filtering"""
        query = self.db.query(Ranking).filter(Ranking.tenant_id == tenant_id)
        
        if keyword_id:
            query = query.filter(Ranking.keyword_id == keyword_id)
        if client_id:
            query = query.filter(Ranking.client_id == client_id)
        if start_date:
            query = query.filter(Ranking.date >= start_date)
        if end_date:
            query = query.filter(Ranking.date <= end_date)
            
        return query.order_by(Ranking.date.desc()).offset(skip).limit(limit).all()
    
    def update_ranking(self, tenant_id: UUID, ranking_id: UUID, ranking_data: RankingUpdate) -> Optional[Ranking]:
        """Update a ranking record"""
        ranking = self.db.query(Ranking).filter(
            and_(Ranking.id == ranking_id, Ranking.tenant_id == tenant_id)
        ).first()
        
        if not ranking:
            return None
            
        update_data = ranking_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(ranking, field, value)
            
        self.db.commit()
        self.db.refresh(ranking)
        return ranking
    
    # Review Management
    def create_review(self, tenant_id: UUID, review_data: ReviewCreate) -> Review:
        """Create a new review"""
        review = Review(
            tenant_id=tenant_id,
            **review_data.model_dump()
        )
        self.db.add(review)
        self.db.commit()
        self.db.refresh(review)
        return review
    
    def get_reviews(self, tenant_id: UUID, client_id: Optional[UUID] = None,
                   status: Optional[ReviewStatus] = None, source: Optional[str] = None,
                   skip: int = 0, limit: int = 100) -> List[Review]:
        """Get reviews with optional filtering"""
        query = self.db.query(Review).filter(Review.tenant_id == tenant_id)
        
        if client_id:
            query = query.filter(Review.client_id == client_id)
        if status:
            query = query.filter(Review.status == status)
        if source:
            query = query.filter(Review.source == source)
            
        return query.order_by(Review.created_at.desc()).offset(skip).limit(limit).all()
    
    def update_review(self, tenant_id: UUID, review_id: UUID, review_data: ReviewUpdate) -> Optional[Review]:
        """Update a review"""
        review = self.db.query(Review).filter(
            and_(Review.id == review_id, Review.tenant_id == tenant_id)
        ).first()
        
        if not review:
            return None
            
        update_data = review_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(review, field, value)
            
        self.db.commit()
        self.db.refresh(review)
        return review
    
    # Citation Management
    def create_citation(self, tenant_id: UUID, citation_data: CitationCreate) -> Citation:
        """Create a new citation"""
        citation = Citation(
            tenant_id=tenant_id,
            **citation_data.model_dump()
        )
        self.db.add(citation)
        self.db.commit()
        self.db.refresh(citation)
        return citation
    
    def get_citations(self, tenant_id: UUID, client_id: Optional[UUID] = None,
                     status: Optional[CitationStatus] = None, platform_type: Optional[str] = None,
                     skip: int = 0, limit: int = 100) -> List[Citation]:
        """Get citations with optional filtering"""
        query = self.db.query(Citation).filter(Citation.tenant_id == tenant_id)
        
        if client_id:
            query = query.filter(Citation.client_id == client_id)
        if status:
            query = query.filter(Citation.status == status)
        if platform_type:
            query = query.filter(Citation.platform_type == platform_type)
            
        return query.order_by(Citation.created_at.desc()).offset(skip).limit(limit).all()
    
    def update_citation(self, tenant_id: UUID, citation_id: UUID, citation_data: CitationUpdate) -> Optional[Citation]:
        """Update a citation"""
        citation = self.db.query(Citation).filter(
            and_(Citation.id == citation_id, Citation.tenant_id == tenant_id)
        ).first()
        
        if not citation:
            return None
            
        update_data = citation_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(citation, field, value)
            
        self.db.commit()
        self.db.refresh(citation)
        return citation
    
    # SEO Meta Management
    def create_seo_meta(self, tenant_id: UUID, seo_meta_data: SeoMetaCreate) -> SeoMeta:
        """Create SEO metadata"""
        seo_meta = SeoMeta(
            tenant_id=tenant_id,
            **seo_meta_data.model_dump()
        )
        self.db.add(seo_meta)
        self.db.commit()
        self.db.refresh(seo_meta)
        return seo_meta
    
    def get_seo_meta(self, tenant_id: UUID, entity_type: str, entity_id: UUID) -> Optional[SeoMeta]:
        """Get SEO metadata for an entity"""
        return self.db.query(SeoMeta).filter(
            and_(
                SeoMeta.tenant_id == tenant_id,
                SeoMeta.entity_type == entity_type,
                SeoMeta.entity_id == entity_id
            )
        ).first()
    
    def update_seo_meta(self, tenant_id: UUID, entity_type: str, entity_id: UUID, 
                       seo_meta_data: SeoMetaUpdate) -> Optional[SeoMeta]:
        """Update SEO metadata"""
        seo_meta = self.get_seo_meta(tenant_id, entity_type, entity_id)
        if not seo_meta:
            return None
            
        update_data = seo_meta_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(seo_meta, field, value)
            
        self.db.commit()
        self.db.refresh(seo_meta)
        return seo_meta
    
    # Analytics and Reporting
    def get_keyword_performance(self, tenant_id: UUID, client_id: UUID, 
                               start_date: date, end_date: date) -> Dict[str, Any]:
        """Get keyword performance analytics"""
        # Get keywords for client
        keywords = self.get_keywords(tenant_id, client_id=client_id)
        keyword_ids = [k.id for k in keywords]
        
        # Get ranking data for date range
        rankings = self.db.query(Ranking).filter(
            and_(
                Ranking.tenant_id == tenant_id,
                Ranking.keyword_id.in_(keyword_ids),
                Ranking.date >= start_date,
                Ranking.date <= end_date
            )
        ).all()
        
        # Calculate performance metrics
        performance_data = {}
        for keyword in keywords:
            keyword_rankings = [r for r in rankings if r.keyword_id == keyword.id]
            
            if keyword_rankings:
                positions = [r.position for r in keyword_rankings if r.position]
                avg_position = sum(positions) / len(positions) if positions else None
                best_position = min(positions) if positions else None
                worst_position = max(positions) if positions else None
                
                performance_data[keyword.keyword] = {
                    "keyword": keyword.keyword,
                    "average_position": avg_position,
                    "best_position": best_position,
                    "worst_position": worst_position,
                    "total_rankings": len(keyword_rankings),
                    "ranking_trend": self._calculate_trend(positions)
                }
        
        return performance_data
    
    def get_review_summary(self, tenant_id: UUID, client_id: UUID) -> Dict[str, Any]:
        """Get review summary analytics"""
        reviews = self.get_reviews(tenant_id, client_id=client_id)
        
        if not reviews:
            return {"total_reviews": 0, "average_rating": 0, "rating_distribution": {}}
        
        ratings = [r.rating for r in reviews if r.rating]
        avg_rating = sum(ratings) / len(ratings) if ratings else 0
        
        # Rating distribution
        rating_dist = {}
        for rating in range(1, 6):
            rating_dist[rating] = len([r for r in reviews if r.rating == rating])
        
        return {
            "total_reviews": len(reviews),
            "average_rating": round(avg_rating, 2),
            "rating_distribution": rating_dist,
            "sources": list(set([r.source.value for r in reviews]))
        }
    
    def get_citation_summary(self, tenant_id: UUID, client_id: UUID) -> Dict[str, Any]:
        """Get citation summary analytics"""
        citations = self.get_citations(tenant_id, client_id=client_id)
        
        if not citations:
            return {"total_citations": 0, "verified_citations": 0, "platforms": []}
        
        verified_count = len([c for c in citations if c.is_verified])
        platforms = list(set([c.platform_name for c in citations]))
        
        return {
            "total_citations": len(citations),
            "verified_citations": verified_count,
            "platforms": platforms,
            "verification_rate": round(verified_count / len(citations) * 100, 2) if citations else 0
        }
    
    def _calculate_trend(self, positions: List[int]) -> str:
        """Calculate ranking trend"""
        if len(positions) < 2:
            return "stable"
        
        # Simple trend calculation - compare first half vs second half
        mid = len(positions) // 2
        first_half_avg = sum(positions[:mid]) / mid
        second_half_avg = sum(positions[mid:]) / (len(positions) - mid)
        
        if second_half_avg < first_half_avg - 2:
            return "improving"
        elif second_half_avg > first_half_avg + 2:
            return "declining"
        else:
            return "stable"
