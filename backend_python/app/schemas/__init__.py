"""
Pydantic schemas for API requests and responses
"""

# User schemas
from app.schemas.user import UserCreate, UserUpdate, UserResponse

# Lead schemas  
from app.schemas.lead import LeadCreate, LeadUpdate, LeadResponse

# SEO schemas
from app.schemas.seo import (
    Keyword, KeywordCreate, KeywordUpdate, KeywordResponse,
    Ranking, RankingCreate, RankingUpdate, RankingResponse,
    Review, ReviewCreate, ReviewUpdate, ReviewResponse,
    Citation, CitationCreate, CitationUpdate, CitationResponse,
    SeoMeta, SeoMetaCreate, SeoMetaUpdate, SeoMetaResponse
)

# Audit schemas
from app.schemas.audit import (
    Project, ProjectCreate, ProjectUpdate, ProjectResponse,
    AuditRun, AuditRunCreate, AuditRunUpdate, AuditRunResponse,
    AuditFinding, AuditFindingCreate, AuditFindingUpdate, AuditFindingResponse,
    Page, PageCreate, PageUpdate, PageResponse,
    Credential, CredentialCreate, CredentialUpdate, CredentialResponse,
    LighthouseRun, LighthouseRunCreate, LighthouseRunResponse
)

__all__ = [
    # User schemas
    "User",
    "UserCreate",
    "UserUpdate", 
    "UserResponse",
    # Lead schemas
    "Lead",
    "LeadCreate",
    "LeadUpdate",
    "LeadResponse",
    # SEO schemas
    "Keyword",
    "KeywordCreate",
    "KeywordUpdate",
    "KeywordResponse",
    "Ranking",
    "RankingCreate",
    "RankingUpdate",
    "RankingResponse",
    "Review",
    "ReviewCreate",
    "ReviewUpdate",
    "ReviewResponse",
    "Citation",
    "CitationCreate",
    "CitationUpdate",
    "CitationResponse",
    "SeoMeta",
    "SeoMetaCreate",
    "SeoMetaUpdate",
    "SeoMetaResponse",
    # Audit schemas
    "Project",
    "ProjectCreate",
    "ProjectUpdate",
    "ProjectResponse",
    "AuditRun",
    "AuditRunCreate",
    "AuditRunUpdate",
    "AuditRunResponse",
    "AuditFinding",
    "AuditFindingCreate",
    "AuditFindingUpdate",
    "AuditFindingResponse",
    "Page",
    "PageCreate",
    "PageUpdate",
    "PageResponse",
    "Credential",
    "CredentialCreate",
    "CredentialUpdate",
    "CredentialResponse",
    "LighthouseRun",
    "LighthouseRunCreate",
    "LighthouseRunResponse"
]