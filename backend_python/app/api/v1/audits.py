"""
Audit service API endpoints
"""

from typing import List, Optional
from uuid import UUID
from fastapi import APIRouter, Depends, HTTPException, Query, status, Response
from fastapi.responses import HTMLResponse, PlainTextResponse
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_user, get_current_tenant
from app.services.audit_service import AuditService
from app.schemas.audit import (
    Project, ProjectCreate, ProjectUpdate, ProjectResponse,
    AuditRun, AuditRunCreate, AuditRunUpdate, AuditRunResponse,
    AuditFinding, AuditFindingUpdate, AuditFindingResponse,
    Page, PageResponse,
    Credential, CredentialCreate, CredentialUpdate, CredentialResponse
)
from app.models import User, Tenant

router = APIRouter()


# Project endpoints
@router.post("/projects", response_model=ProjectResponse, status_code=status.HTTP_201_CREATED)
async def create_project(
    project_data: ProjectCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new project"""
    audit_service = AuditService(db)
    return audit_service.create_project(current_tenant.id, project_data)


@router.get("/projects", response_model=List[ProjectResponse])
async def get_projects(
    client_id: Optional[UUID] = Query(None),
    status: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get projects with optional filtering"""
    audit_service = AuditService(db)
    projects = audit_service.get_projects(current_tenant.id, client_id, status, skip, limit)
    
    # Add summary data to each project
    result = []
    for project in projects:
        project_dict = ProjectResponse.model_validate(project).model_dump()
        project_dict.update({
            "total_audit_runs": project.total_audit_runs,
            "successful_audit_runs": project.successful_audit_runs,
            "failed_audit_runs": project.failed_audit_runs
        })
        result.append(project_dict)
    
    return result


@router.get("/projects/{project_id}", response_model=ProjectResponse)
async def get_project(
    project_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get a project by ID"""
    audit_service = AuditService(db)
    project = audit_service.get_project(current_tenant.id, project_id)
    
    if not project:
        raise HTTPException(status_code=404, detail="Project not found")
    
    project_dict = ProjectResponse.model_validate(project).model_dump()
    project_dict.update({
        "total_audit_runs": project.total_audit_runs,
        "successful_audit_runs": project.successful_audit_runs,
        "failed_audit_runs": project.failed_audit_runs
    })
    
    return project_dict


@router.put("/projects/{project_id}", response_model=ProjectResponse)
async def update_project(
    project_id: UUID,
    project_data: ProjectUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update a project"""
    audit_service = AuditService(db)
    project = audit_service.update_project(current_tenant.id, project_id, project_data)
    
    if not project:
        raise HTTPException(status_code=404, detail="Project not found")
    
    return project


@router.delete("/projects/{project_id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_project(
    project_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Delete a project"""
    audit_service = AuditService(db)
    success = audit_service.delete_project(current_tenant.id, project_id)
    
    if not success:
        raise HTTPException(status_code=404, detail="Project not found")


# Audit Run endpoints
@router.post("/audit-runs", response_model=AuditRunResponse, status_code=status.HTTP_201_CREATED)
async def create_audit_run(
    audit_data: AuditRunCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new audit run"""
    audit_service = AuditService(db)
    return audit_service.create_audit_run(current_tenant.id, audit_data, current_user.id)


@router.get("/audit-runs", response_model=List[AuditRunResponse])
async def get_audit_runs(
    project_id: Optional[UUID] = Query(None),
    state: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit runs with optional filtering"""
    audit_service = AuditService(db)
    audit_runs = audit_service.get_audit_runs(current_tenant.id, project_id, state, skip, limit)
    
    # Add calculated fields to each audit run
    result = []
    for audit_run in audit_runs:
        audit_dict = AuditRunResponse.model_validate(audit_run).model_dump()
        audit_dict.update({
            "duration": audit_run.duration,
            "completion_percentage": audit_run.completion_percentage
        })
        result.append(audit_dict)
    
    return result


@router.get("/audit-runs/{audit_run_id}", response_model=AuditRunResponse)
async def get_audit_run(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get an audit run by ID"""
    audit_service = AuditService(db)
    audit_run = audit_service.get_audit_run(current_tenant.id, audit_run_id)
    
    if not audit_run:
        raise HTTPException(status_code=404, detail="Audit run not found")
    
    audit_dict = AuditRunResponse.model_validate(audit_run).model_dump()
    audit_dict.update({
        "duration": audit_run.duration,
        "completion_percentage": audit_run.completion_percentage
    })
    
    return audit_dict


@router.put("/audit-runs/{audit_run_id}", response_model=AuditRunResponse)
async def update_audit_run(
    audit_run_id: UUID,
    audit_data: AuditRunUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update an audit run"""
    audit_service = AuditService(db)
    audit_run = audit_service.update_audit_run(current_tenant.id, audit_run_id, audit_data)
    
    if not audit_run:
        raise HTTPException(status_code=404, detail="Audit run not found")
    
    return audit_run


@router.post("/audit-runs/{audit_run_id}/start", status_code=status.HTTP_202_ACCEPTED)
async def start_audit(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Start an audit run"""
    audit_service = AuditService(db)
    success = await audit_service.start_audit(current_tenant.id, audit_run_id)
    
    if not success:
        raise HTTPException(status_code=400, detail="Failed to start audit")
    
    return {"message": "Audit started successfully"}


@router.get("/audit-runs/{audit_run_id}/summary")
async def get_audit_summary(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit summary statistics"""
    audit_service = AuditService(db)
    summary = audit_service.get_audit_summary(current_tenant.id, audit_run_id)
    
    if not summary:
        raise HTTPException(status_code=404, detail="Audit run not found")
    
    return summary


# Finding endpoints
@router.get("/findings", response_model=List[AuditFindingResponse])
async def get_findings(
    audit_run_id: Optional[UUID] = Query(None),
    page_id: Optional[UUID] = Query(None),
    severity: Optional[str] = Query(None),
    category: Optional[str] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit findings with optional filtering"""
    audit_service = AuditService(db)
    findings = audit_service.get_findings(
        current_tenant.id, audit_run_id, page_id, severity, category, skip, limit
    )
    
    # Add calculated fields to each finding
    result = []
    for finding in findings:
        finding_dict = AuditFindingResponse.model_validate(finding).model_dump()
        finding_dict.update({
            "is_resolved": finding.is_resolved,
            "is_ignored": finding.is_ignored,
            "is_open": finding.is_open
        })
        result.append(finding_dict)
    
    return result


@router.put("/findings/{finding_id}", response_model=AuditFindingResponse)
async def update_finding(
    finding_id: UUID,
    status: Optional[str] = Query(None),
    assigned_to: Optional[UUID] = Query(None),
    notes: Optional[str] = Query(None),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Update audit finding"""
    audit_service = AuditService(db)
    finding = audit_service.update_finding(current_tenant.id, finding_id, status, assigned_to, notes)
    
    if not finding:
        raise HTTPException(status_code=404, detail="Finding not found")
    
    finding_dict = AuditFindingResponse.model_validate(finding).model_dump()
    finding_dict.update({
        "is_resolved": finding.is_resolved,
        "is_ignored": finding.is_ignored,
        "is_open": finding.is_open
    })
    
    return finding_dict


# Page endpoints
@router.get("/pages", response_model=List[PageResponse])
async def get_pages(
    audit_run_id: Optional[UUID] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get pages with optional filtering"""
    audit_service = AuditService(db)
    
    # Get audit run to verify tenant access
    if audit_run_id:
        audit_run = audit_service.get_audit_run(current_tenant.id, audit_run_id)
        if not audit_run:
            raise HTTPException(status_code=404, detail="Audit run not found")
    
    # Query pages
    from app.models.audit import Page
    query = db.query(Page).filter(Page.tenant_id == current_tenant.id)
    
    if audit_run_id:
        query = query.filter(Page.audit_run_id == audit_run_id)
    
    pages = query.offset(skip).limit(limit).all()
    
    # Add calculated fields to each page
    result = []
    for page in pages:
        page_dict = PageResponse.model_validate(page).model_dump()
        page_dict.update({
            "is_crawled": page.is_crawled,
            "is_analyzed": page.is_analyzed,
            "has_errors": page.has_errors,
            "total_findings": page.total_findings,
            "critical_findings": page.critical_findings,
            "high_findings": page.high_findings
        })
        result.append(page_dict)
    
    return result


# Report endpoints
@router.get("/audit-runs/{audit_run_id}/report", response_class=HTMLResponse)
async def get_audit_report_html(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit report in HTML format"""
    audit_service = AuditService(db)
    report = audit_service.generate_report(current_tenant.id, audit_run_id, 'html')
    
    if not report:
        raise HTTPException(status_code=404, detail="Audit run not found or report generation failed")
    
    return HTMLResponse(content=report)


@router.get("/audit-runs/{audit_run_id}/report.csv", response_class=PlainTextResponse)
async def get_audit_report_csv(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit report in CSV format"""
    audit_service = AuditService(db)
    report = audit_service.generate_report(current_tenant.id, audit_run_id, 'csv')
    
    if not report:
        raise HTTPException(status_code=404, detail="Audit run not found or report generation failed")
    
    return PlainTextResponse(content=report, media_type="text/csv")


@router.get("/audit-runs/{audit_run_id}/report.json")
async def get_audit_report_json(
    audit_run_id: UUID,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get audit report in JSON format"""
    audit_service = AuditService(db)
    report = audit_service.generate_report(current_tenant.id, audit_run_id, 'json')
    
    if not report:
        raise HTTPException(status_code=404, detail="Audit run not found or report generation failed")
    
    import json
    return json.loads(report)


# Credential endpoints (basic implementation)
@router.post("/credentials", response_model=CredentialResponse, status_code=status.HTTP_201_CREATED)
async def create_credential(
    credential_data: CredentialCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Create a new credential"""
    from app.models.audit import Credential
    
    credential = Credential(
        tenant_id=current_tenant.id,
        **credential_data.model_dump()
    )
    db.add(credential)
    db.commit()
    db.refresh(credential)
    
    credential_dict = CredentialResponse.model_validate(credential).model_dump()
    credential_dict.update({
        "is_expired": credential.is_expired,
        "needs_refresh": credential.needs_refresh
    })
    
    return credential_dict


@router.get("/credentials", response_model=List[CredentialResponse])
async def get_credentials(
    project_id: Optional[UUID] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=1000),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    current_tenant: Tenant = Depends(get_current_tenant)
):
    """Get credentials with optional filtering"""
    from app.models.audit import Credential
    
    query = db.query(Credential).filter(Credential.tenant_id == current_tenant.id)
    
    if project_id:
        query = query.filter(Credential.project_id == project_id)
    
    credentials = query.offset(skip).limit(limit).all()
    
    # Add calculated fields to each credential
    result = []
    for credential in credentials:
        credential_dict = CredentialResponse.model_validate(credential).model_dump()
        credential_dict.update({
            "is_expired": credential.is_expired,
            "needs_refresh": credential.needs_refresh
        })
        result.append(credential_dict)
    
    return result
