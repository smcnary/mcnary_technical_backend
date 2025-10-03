"""
Pydantic schemas for audit service
"""

from pydantic import BaseModel, Field, ConfigDict
from typing import Optional, List, Dict, Any
from datetime import date, datetime
from uuid import UUID

from app.models.audit import (
    AuditRunState, ProjectStatus, CredentialType, CredentialStatus,
    PageStatus, FindingSeverity, FindingCategory, LighthouseCategory
)


# Project Schemas
class ProjectBase(BaseModel):
    name: str = Field(..., max_length=255)
    description: Optional[str] = None
    website_url: str = Field(..., max_length=500)
    status: ProjectStatus = ProjectStatus.ACTIVE
    cms: Optional[str] = Field(default=None, max_length=64)
    cms_version: Optional[str] = Field(default=None, max_length=64)
    hosting_provider: Optional[str] = Field(default=None, max_length=128)
    tech_stack: Optional[Dict[str, Any]] = None
    has_google_analytics: bool = False
    has_search_console: bool = False
    has_google_business_profile: bool = False
    has_social_media: bool = False
    default_crawl_settings: Optional[Dict[str, Any]] = None
    notification_settings: Optional[Dict[str, Any]] = None
    scheduled_audits: Optional[List[str]] = None
    tags: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class ProjectCreate(ProjectBase):
    client_id: UUID


class ProjectUpdate(BaseModel):
    name: Optional[str] = Field(default=None, max_length=255)
    description: Optional[str] = None
    website_url: Optional[str] = Field(default=None, max_length=500)
    status: Optional[ProjectStatus] = None
    cms: Optional[str] = Field(default=None, max_length=64)
    cms_version: Optional[str] = Field(default=None, max_length=64)
    hosting_provider: Optional[str] = Field(default=None, max_length=128)
    tech_stack: Optional[Dict[str, Any]] = None
    has_google_analytics: Optional[bool] = None
    has_search_console: Optional[bool] = None
    has_google_business_profile: Optional[bool] = None
    has_social_media: Optional[bool] = None
    default_crawl_settings: Optional[Dict[str, Any]] = None
    notification_settings: Optional[Dict[str, Any]] = None
    scheduled_audits: Optional[List[str]] = None
    tags: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class Project(ProjectBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    client_id: UUID
    created_at: datetime
    updated_at: datetime


class ProjectResponse(Project):
    total_audit_runs: Optional[int] = None
    successful_audit_runs: Optional[int] = None
    failed_audit_runs: Optional[int] = None


# Audit Run Schemas
class AuditRunBase(BaseModel):
    name: str = Field(..., max_length=255)
    description: Optional[str] = None
    seed_urls: List[str] = Field(default_factory=list)
    max_pages: int = Field(default=1000, ge=1, le=10000)
    crawl_depth: int = Field(default=3, ge=1, le=10)
    respect_robots_txt: str = Field(default="yes", pattern="^(yes|no)$")
    crawl_delay: int = Field(default=1, ge=0, le=10)
    audit_config: Optional[Dict[str, Any]] = None
    include_external_links: str = Field(default="no", pattern="^(yes|no)$")
    include_images: str = Field(default="yes", pattern="^(yes|no)$")
    include_js: str = Field(default="yes", pattern="^(yes|no)$")
    include_css: str = Field(default="yes", pattern="^(yes|no)$")
    metadata: Optional[Dict[str, Any]] = None


class AuditRunCreate(AuditRunBase):
    project_id: UUID


class AuditRunUpdate(BaseModel):
    name: Optional[str] = Field(default=None, max_length=255)
    description: Optional[str] = None
    seed_urls: Optional[List[str]] = None
    max_pages: Optional[int] = Field(default=None, ge=1, le=10000)
    crawl_depth: Optional[int] = Field(default=None, ge=1, le=10)
    respect_robots_txt: Optional[str] = Field(default=None, pattern="^(yes|no)$")
    crawl_delay: Optional[int] = Field(default=None, ge=0, le=10)
    audit_config: Optional[Dict[str, Any]] = None
    include_external_links: Optional[str] = Field(default=None, pattern="^(yes|no)$")
    include_images: Optional[str] = Field(default=None, pattern="^(yes|no)$")
    include_js: Optional[str] = Field(default=None, pattern="^(yes|no)$")
    include_css: Optional[str] = Field(default=None, pattern="^(yes|no)$")
    metadata: Optional[Dict[str, Any]] = None


class AuditRun(AuditRunBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    project_id: UUID
    state: AuditRunState
    started_at: Optional[datetime] = None
    finished_at: Optional[datetime] = None
    requested_by: UUID
    pages_crawled: int = 0
    pages_analyzed: int = 0
    findings_count: int = 0
    critical_findings: int = 0
    high_findings: int = 0
    medium_findings: int = 0
    low_findings: int = 0
    total_crawl_time: Optional[int] = None
    average_page_load_time: Optional[int] = None
    lighthouse_score: Optional[int] = None
    error_message: Optional[str] = None
    retry_count: int = 0
    max_retries: int = 3
    version: str = "1.0.0"
    created_at: datetime
    updated_at: datetime


class AuditRunResponse(AuditRun):
    duration: Optional[int] = None
    completion_percentage: float = 0.0


# Audit Finding Schemas
class AuditFindingBase(BaseModel):
    check_code: str = Field(..., max_length=100)
    check_name: str = Field(..., max_length=255)
    category: FindingCategory
    severity: FindingSeverity
    title: str = Field(..., max_length=255)
    description: Optional[str] = None
    recommendation: Optional[str] = None
    impact: Optional[str] = Field(default=None, max_length=255)
    element: Optional[str] = Field(default=None, max_length=255)
    attribute: Optional[str] = Field(default=None, max_length=255)
    value: Optional[str] = None
    expected_value: Optional[str] = None
    url: Optional[str] = Field(default=None, max_length=500)
    line_number: Optional[int] = None
    column_number: Optional[int] = None
    xpath: Optional[str] = Field(default=None, max_length=500)
    css_selector: Optional[str] = Field(default=None, max_length=500)
    score_impact: Optional[float] = Field(default=None, ge=-100.0, le=100.0)
    difficulty: Optional[str] = Field(default=None, pattern="^(easy|medium|hard)$")
    status: str = Field(default="open", pattern="^(open|in_progress|fixed|ignored)$")
    assigned_to: Optional[UUID] = None
    due_date: Optional[str] = Field(default=None, max_length=50)
    screenshots: Optional[List[str]] = None
    code_snippets: Optional[Dict[str, Any]] = None
    references: Optional[List[str]] = None
    tags: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class AuditFindingCreate(AuditFindingBase):
    audit_run_id: UUID
    page_id: Optional[UUID] = None


class AuditFindingUpdate(BaseModel):
    check_code: Optional[str] = Field(default=None, max_length=100)
    check_name: Optional[str] = Field(default=None, max_length=255)
    category: Optional[FindingCategory] = None
    severity: Optional[FindingSeverity] = None
    title: Optional[str] = Field(default=None, max_length=255)
    description: Optional[str] = None
    recommendation: Optional[str] = None
    impact: Optional[str] = Field(default=None, max_length=255)
    element: Optional[str] = Field(default=None, max_length=255)
    attribute: Optional[str] = Field(default=None, max_length=255)
    value: Optional[str] = None
    expected_value: Optional[str] = None
    url: Optional[str] = Field(default=None, max_length=500)
    line_number: Optional[int] = None
    column_number: Optional[int] = None
    xpath: Optional[str] = Field(default=None, max_length=500)
    css_selector: Optional[str] = Field(default=None, max_length=500)
    score_impact: Optional[float] = Field(default=None, ge=-100.0, le=100.0)
    difficulty: Optional[str] = Field(default=None, pattern="^(easy|medium|hard)$")
    status: Optional[str] = Field(default=None, pattern="^(open|in_progress|fixed|ignored)$")
    assigned_to: Optional[UUID] = None
    due_date: Optional[str] = Field(default=None, max_length=50)
    resolved_at: Optional[str] = Field(default=None, max_length=50)
    screenshots: Optional[List[str]] = None
    code_snippets: Optional[Dict[str, Any]] = None
    references: Optional[List[str]] = None
    tags: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class AuditFinding(AuditFindingBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    audit_run_id: UUID
    page_id: Optional[UUID] = None
    priority_score: Optional[int] = None
    resolved_at: Optional[str] = None
    created_at: datetime
    updated_at: datetime


class AuditFindingResponse(AuditFinding):
    is_resolved: bool = False
    is_ignored: bool = False
    is_open: bool = True


# Page Schemas
class PageBase(BaseModel):
    url: str = Field(..., max_length=500)
    canonical_url: Optional[str] = Field(default=None, max_length=500)
    title: Optional[str] = Field(default=None, max_length=500)
    meta_description: Optional[str] = None
    meta_keywords: Optional[str] = None
    language: Optional[str] = Field(default=None, max_length=10)
    content_type: Optional[str] = Field(default=None, max_length=100)
    status_code: Optional[int] = None
    response_time: Optional[int] = None
    content_length: Optional[int] = None
    file_size: Optional[int] = None
    h1_tags: Optional[List[str]] = None
    h2_tags: Optional[List[str]] = None
    h3_tags: Optional[List[str]] = None
    images: Optional[List[Dict[str, Any]]] = None
    links: Optional[List[Dict[str, Any]]] = None
    scripts: Optional[List[Dict[str, Any]]] = None
    stylesheets: Optional[List[Dict[str, Any]]] = None
    word_count: Optional[int] = None
    reading_time: Optional[int] = None
    keyword_density: Optional[Dict[str, Any]] = None
    readability_score: Optional[int] = None
    dom_size: Optional[int] = None
    redirect_chain: Optional[List[str]] = None
    load_time: Optional[int] = None
    time_to_first_byte: Optional[int] = None
    depth: int = 0
    parent_url: Optional[str] = Field(default=None, max_length=500)
    discovered_at: Optional[str] = Field(default=None, max_length=50)
    crawled_at: Optional[str] = Field(default=None, max_length=50)
    error_message: Optional[str] = None
    screenshots: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class PageCreate(PageBase):
    audit_run_id: UUID


class PageUpdate(BaseModel):
    canonical_url: Optional[str] = Field(default=None, max_length=500)
    title: Optional[str] = Field(default=None, max_length=500)
    meta_description: Optional[str] = None
    meta_keywords: Optional[str] = None
    language: Optional[str] = Field(default=None, max_length=10)
    content_type: Optional[str] = Field(default=None, max_length=100)
    status_code: Optional[int] = None
    response_time: Optional[int] = None
    content_length: Optional[int] = None
    file_size: Optional[int] = None
    h1_tags: Optional[List[str]] = None
    h2_tags: Optional[List[str]] = None
    h3_tags: Optional[List[str]] = None
    images: Optional[List[Dict[str, Any]]] = None
    links: Optional[List[Dict[str, Any]]] = None
    scripts: Optional[List[Dict[str, Any]]] = None
    stylesheets: Optional[List[Dict[str, Any]]] = None
    word_count: Optional[int] = None
    reading_time: Optional[int] = None
    keyword_density: Optional[Dict[str, Any]] = None
    readability_score: Optional[int] = None
    dom_size: Optional[int] = None
    redirect_chain: Optional[List[str]] = None
    load_time: Optional[int] = None
    time_to_first_byte: Optional[int] = None
    depth: Optional[int] = None
    parent_url: Optional[str] = Field(default=None, max_length=500)
    discovered_at: Optional[str] = Field(default=None, max_length=50)
    crawled_at: Optional[str] = Field(default=None, max_length=50)
    error_message: Optional[str] = None
    screenshots: Optional[List[str]] = None
    metadata: Optional[Dict[str, Any]] = None


class Page(PageBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    audit_run_id: UUID
    status: PageStatus
    retry_count: int = 0
    created_at: datetime
    updated_at: datetime


class PageResponse(Page):
    is_crawled: bool = False
    is_analyzed: bool = False
    has_errors: bool = False
    total_findings: int = 0
    critical_findings: int = 0
    high_findings: int = 0


# Credential Schemas
class CredentialBase(BaseModel):
    name: str = Field(..., max_length=255)
    credential_type: CredentialType
    status: CredentialStatus = CredentialStatus.ACTIVE
    client_id: Optional[str] = Field(default=None, max_length=255)
    client_secret: Optional[str] = Field(default=None, max_length=255)
    account_id: Optional[str] = Field(default=None, max_length=255)
    property_id: Optional[str] = Field(default=None, max_length=255)
    view_id: Optional[str] = Field(default=None, max_length=255)
    profile_id: Optional[str] = Field(default=None, max_length=255)
    expires_at: Optional[str] = Field(default=None, max_length=50)
    token_type: Optional[str] = Field(default=None, max_length=50)
    scope: Optional[str] = None
    settings: Optional[Dict[str, Any]] = None
    webhook_url: Optional[str] = Field(default=None, max_length=500)
    rate_limit: Optional[int] = Field(default=None, ge=1, le=10000)
    notes: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None


class CredentialCreate(CredentialBase):
    project_id: UUID


class CredentialUpdate(BaseModel):
    name: Optional[str] = Field(default=None, max_length=255)
    credential_type: Optional[CredentialType] = None
    status: Optional[CredentialStatus] = None
    client_id: Optional[str] = Field(default=None, max_length=255)
    client_secret: Optional[str] = Field(default=None, max_length=255)
    account_id: Optional[str] = Field(default=None, max_length=255)
    property_id: Optional[str] = Field(default=None, max_length=255)
    view_id: Optional[str] = Field(default=None, max_length=255)
    profile_id: Optional[str] = Field(default=None, max_length=255)
    expires_at: Optional[str] = Field(default=None, max_length=50)
    token_type: Optional[str] = Field(default=None, max_length=50)
    scope: Optional[str] = None
    settings: Optional[Dict[str, Any]] = None
    webhook_url: Optional[str] = Field(default=None, max_length=500)
    rate_limit: Optional[int] = Field(default=None, ge=1, le=10000)
    notes: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None


class Credential(CredentialBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    project_id: UUID
    last_verified_at: Optional[str] = None
    last_test_at: Optional[str] = None
    test_result: Optional[Dict[str, Any]] = None
    error_message: Optional[str] = None
    created_at: datetime
    updated_at: datetime


class CredentialResponse(Credential):
    is_expired: bool = False
    needs_refresh: bool = False


# Lighthouse Run Schemas
class LighthouseRunBase(BaseModel):
    url: str = Field(..., max_length=500)
    run_at: str = Field(..., max_length=50)
    performance_score: Optional[int] = Field(default=None, ge=0, le=100)
    accessibility_score: Optional[int] = Field(default=None, ge=0, le=100)
    best_practices_score: Optional[int] = Field(default=None, ge=0, le=100)
    seo_score: Optional[int] = Field(default=None, ge=0, le=100)
    pwa_score: Optional[int] = Field(default=None, ge=0, le=100)
    first_contentful_paint: Optional[float] = None
    largest_contentful_paint: Optional[float] = None
    first_input_delay: Optional[float] = None
    cumulative_layout_shift: Optional[float] = None
    speed_index: Optional[float] = None
    time_to_interactive: Optional[float] = None
    total_blocking_time: Optional[float] = None
    total_requests: Optional[int] = None
    total_size: Optional[int] = None
    unused_css: Optional[int] = None
    unused_js: Optional[int] = None
    render_blocking_resources: Optional[List[Dict[str, Any]]] = None
    opportunities: Optional[List[Dict[str, Any]]] = None
    diagnostics: Optional[List[Dict[str, Any]]] = None
    audits: Optional[List[Dict[str, Any]]] = None
    device_type: Optional[str] = Field(default=None, max_length=20)
    browser: Optional[str] = Field(default=None, max_length=50)
    viewport_width: Optional[int] = None
    viewport_height: Optional[int] = None
    lighthouse_version: Optional[str] = Field(default=None, max_length=20)
    config: Optional[Dict[str, Any]] = None
    metadata: Optional[Dict[str, Any]] = None


class LighthouseRunCreate(LighthouseRunBase):
    audit_run_id: UUID
    page_id: Optional[UUID] = None


class LighthouseRun(LighthouseRunBase):
    model_config = ConfigDict(from_attributes=True)
    
    id: UUID
    tenant_id: UUID
    audit_run_id: UUID
    page_id: Optional[UUID] = None
    created_at: datetime
    updated_at: datetime


class LighthouseRunResponse(LighthouseRun):
    overall_score: float = 0.0
    performance_grade: str = "N/A"
    accessibility_grade: str = "N/A"
    seo_grade: str = "N/A"
