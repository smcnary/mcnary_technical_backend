"""
Audit orchestration service
"""

from typing import List, Optional, Dict, Any
from uuid import UUID
from datetime import datetime
from sqlalchemy.orm import Session
from sqlalchemy import and_
import asyncio
import logging

from app.models.audit import (
    AuditRun, AuditFinding, Project, Credential, Page, 
    AuditRunState, FindingSeverity, FindingCategory, PageStatus
)
from app.schemas.audit import (
    AuditRunCreate, AuditRunUpdate, ProjectCreate, ProjectUpdate,
    CredentialCreate, CredentialUpdate
)
from app.services.crawler_service import CrawlerService
from app.services.analysis_engine import SEOAnalysisEngine
from app.services.reporting_service import ReportingService

logger = logging.getLogger(__name__)


class AuditService:
    """Service for managing SEO audit operations"""
    
    def __init__(self, db: Session):
        self.db = db
        self.crawler_service = CrawlerService()
        self.analysis_engine = SEOAnalysisEngine()
        self.reporting_service = ReportingService()
    
    # Project Management
    def create_project(self, tenant_id: UUID, project_data: ProjectCreate) -> Project:
        """Create a new project"""
        project = Project(
            tenant_id=tenant_id,
            **project_data.model_dump()
        )
        self.db.add(project)
        self.db.commit()
        self.db.refresh(project)
        return project
    
    def get_project(self, tenant_id: UUID, project_id: UUID) -> Optional[Project]:
        """Get a project by ID"""
        return self.db.query(Project).filter(
            and_(Project.id == project_id, Project.tenant_id == tenant_id)
        ).first()
    
    def get_projects(self, tenant_id: UUID, client_id: Optional[UUID] = None,
                    status: Optional[str] = None, skip: int = 0, limit: int = 100) -> List[Project]:
        """Get projects with optional filtering"""
        query = self.db.query(Project).filter(Project.tenant_id == tenant_id)
        
        if client_id:
            query = query.filter(Project.client_id == client_id)
        if status:
            from app.models.audit import ProjectStatus
            try:
                status_enum = ProjectStatus(status)
                query = query.filter(Project.status == status_enum)
            except ValueError:
                pass  # Invalid status, ignore filter
        
        return query.offset(skip).limit(limit).all()
    
    def update_project(self, tenant_id: UUID, project_id: UUID, 
                      project_data: ProjectUpdate) -> Optional[Project]:
        """Update a project"""
        project = self.get_project(tenant_id, project_id)
        if not project:
            return None
        
        update_data = project_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(project, field, value)
        
        self.db.commit()
        self.db.refresh(project)
        return project
    
    def delete_project(self, tenant_id: UUID, project_id: UUID) -> bool:
        """Delete a project"""
        project = self.get_project(tenant_id, project_id)
        if not project:
            return False
        
        self.db.delete(project)
        self.db.commit()
        return True
    
    # Audit Run Management
    def create_audit_run(self, tenant_id: UUID, audit_data: AuditRunCreate, requested_by: UUID) -> AuditRun:
        """Create a new audit run"""
        audit_run = AuditRun(
            tenant_id=tenant_id,
            requested_by=requested_by,
            **audit_data.model_dump()
        )
        self.db.add(audit_run)
        self.db.commit()
        self.db.refresh(audit_run)
        return audit_run
    
    def get_audit_run(self, tenant_id: UUID, audit_run_id: UUID) -> Optional[AuditRun]:
        """Get an audit run by ID"""
        return self.db.query(AuditRun).filter(
            and_(AuditRun.id == audit_run_id, AuditRun.tenant_id == tenant_id)
        ).first()
    
    def get_audit_runs(self, tenant_id: UUID, project_id: Optional[UUID] = None,
                      state: Optional[str] = None, skip: int = 0, limit: int = 100) -> List[AuditRun]:
        """Get audit runs with optional filtering"""
        query = self.db.query(AuditRun).filter(AuditRun.tenant_id == tenant_id)
        
        if project_id:
            query = query.filter(AuditRun.project_id == project_id)
        if state:
            try:
                state_enum = AuditRunState(state)
                query = query.filter(AuditRun.state == state_enum)
            except ValueError:
                pass  # Invalid state, ignore filter
        
        return query.order_by(AuditRun.created_at.desc()).offset(skip).limit(limit).all()
    
    def update_audit_run(self, tenant_id: UUID, audit_run_id: UUID, 
                        audit_data: AuditRunUpdate) -> Optional[AuditRun]:
        """Update an audit run"""
        audit_run = self.get_audit_run(tenant_id, audit_run_id)
        if not audit_run:
            return None
        
        update_data = audit_data.model_dump(exclude_unset=True)
        for field, value in update_data.items():
            setattr(audit_run, field, value)
        
        self.db.commit()
        self.db.refresh(audit_run)
        return audit_run
    
    # Audit Execution
    async def start_audit(self, tenant_id: UUID, audit_run_id: UUID) -> bool:
        """Start an audit run"""
        audit_run = self.get_audit_run(tenant_id, audit_run_id)
        if not audit_run:
            return False
        
        if not audit_run.can_start():
            logger.warning(f"Audit run {audit_run_id} cannot be started in current state: {audit_run.state}")
            return False
        
        try:
            # Update state to queued
            audit_run.state = AuditRunState.QUEUED
            self.db.commit()
            
            # Start crawling
            project = audit_run.project
            if not project:
                raise ValueError("Project not found for audit run")
            
            crawl_config = {
                'max_pages': audit_run.max_pages,
                'crawl_delay': audit_run.crawl_delay,
                'respect_robots': audit_run.respect_robots_txt == 'yes',
                'max_depth': audit_run.crawl_depth,
                'include_external_links': audit_run.include_external_links == 'yes'
            }
            
            # Start crawler
            await self.crawler_service.start_crawl(
                str(audit_run_id),
                audit_run.seed_urls,
                crawl_config
            )
            
            # Update state to running
            audit_run.state = AuditRunState.RUNNING
            audit_run.started_at = datetime.utcnow()
            self.db.commit()
            
            # Start background processing
            asyncio.create_task(self._process_audit(audit_run_id))
            
            return True
            
        except Exception as e:
            logger.error(f"Failed to start audit {audit_run_id}: {e}")
            audit_run.state = AuditRunState.FAILED
            audit_run.error_message = str(e)
            self.db.commit()
            return False
    
    async def _process_audit(self, audit_run_id: UUID):
        """Process audit in background"""
        try:
            # Wait for crawling to complete
            await self._wait_for_crawling_completion(audit_run_id)
            
            # Analyze pages
            await self._analyze_pages(audit_run_id)
            
            # Generate findings
            await self._generate_findings(audit_run_id)
            
            # Update audit state
            audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
            if audit_run:
                audit_run.state = AuditRunState.COMPLETED
                audit_run.finished_at = datetime.utcnow()
                self.db.commit()
                
                logger.info(f"Audit {audit_run_id} completed successfully")
                
        except Exception as e:
            logger.error(f"Error processing audit {audit_run_id}: {e}")
            audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
            if audit_run:
                audit_run.state = AuditRunState.FAILED
                audit_run.error_message = str(e)
                self.db.commit()
    
    async def _wait_for_crawling_completion(self, audit_run_id: UUID):
        """Wait for crawling to complete"""
        max_wait_time = 3600  # 1 hour
        wait_interval = 30  # 30 seconds
        waited = 0
        
        while waited < max_wait_time:
            status = self.crawler_service.get_crawl_status(str(audit_run_id))
            
            if status["status"] == "not_found":
                # Crawling completed
                break
            
            if status["status"] == "running":
                # Update progress
                audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
                if audit_run:
                    audit_run.pages_crawled = status.get("crawled_urls", 0)
                    self.db.commit()
            
            await asyncio.sleep(wait_interval)
            waited += wait_interval
        
        if waited >= max_wait_time:
            raise TimeoutError("Crawling timed out")
    
    async def _analyze_pages(self, audit_run_id: UUID):
        """Analyze crawled pages"""
        audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
        if not audit_run:
            return
        
        pages = self.db.query(Page).filter(Page.audit_run_id == audit_run_id).all()
        
        for page in pages:
            try:
                # Update page status to analyzing
                page.status = PageStatus.ANALYZING
                self.db.commit()
                
                # Here you would run the analysis engine on the page data
                # For now, we'll just mark it as analyzed
                page.status = PageStatus.ANALYZED
                page.crawled_at = datetime.utcnow().isoformat()
                
                audit_run.pages_analyzed += 1
                self.db.commit()
                
            except Exception as e:
                logger.error(f"Error analyzing page {page.id}: {e}")
                page.status = PageStatus.FAILED
                page.error_message = str(e)
                self.db.commit()
    
    async def _generate_findings(self, audit_run_id: UUID):
        """Generate audit findings"""
        audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
        if not audit_run:
            return
        
        pages = self.db.query(Page).filter(Page.audit_run_id == audit_run_id).all()
        
        for page in pages:
            # Here you would run the analysis engine to generate findings
            # For now, we'll create a placeholder finding
            finding = AuditFinding(
                tenant_id=audit_run.tenant_id,
                audit_run_id=audit_run_id,
                page_id=page.id,
                check_code="PLACEHOLDER_CHECK",
                check_name="Placeholder Check",
                category=FindingCategory.TECHNICAL_SEO,
                severity=FindingSeverity.INFO,
                title="Analysis Complete",
                description="Page analysis has been completed.",
                recommendation="Review the analysis results.",
                impact="No impact - informational finding."
            )
            
            self.db.add(finding)
        
        self.db.commit()
        
        # Update finding counts
        self._update_finding_counts(audit_run_id)
    
    def _update_finding_counts(self, audit_run_id: UUID):
        """Update finding counts for audit run"""
        audit_run = self.db.query(AuditRun).filter(AuditRun.id == audit_run_id).first()
        if not audit_run:
            return
        
        findings = self.db.query(AuditFinding).filter(AuditFinding.audit_run_id == audit_run_id).all()
        
        audit_run.findings_count = len(findings)
        audit_run.critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        audit_run.high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        audit_run.medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        audit_run.low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        self.db.commit()
    
    # Finding Management
    def get_findings(self, tenant_id: UUID, audit_run_id: Optional[UUID] = None,
                    page_id: Optional[UUID] = None, severity: Optional[str] = None,
                    category: Optional[str] = None, skip: int = 0, limit: int = 100) -> List[AuditFinding]:
        """Get audit findings with optional filtering"""
        query = self.db.query(AuditFinding).filter(AuditFinding.tenant_id == tenant_id)
        
        if audit_run_id:
            query = query.filter(AuditFinding.audit_run_id == audit_run_id)
        if page_id:
            query = query.filter(AuditFinding.page_id == page_id)
        if severity:
            try:
                severity_enum = FindingSeverity(severity)
                query = query.filter(AuditFinding.severity == severity_enum)
            except ValueError:
                pass
        if category:
            try:
                category_enum = FindingCategory(category)
                query = query.filter(AuditFinding.category == category_enum)
            except ValueError:
                pass
        
        return query.order_by(AuditFinding.created_at.desc()).offset(skip).limit(limit).all()
    
    def update_finding(self, tenant_id: UUID, finding_id: UUID, 
                      status: Optional[str] = None, assigned_to: Optional[UUID] = None,
                      notes: Optional[str] = None) -> Optional[AuditFinding]:
        """Update audit finding"""
        finding = self.db.query(AuditFinding).filter(
            and_(AuditFinding.id == finding_id, AuditFinding.tenant_id == tenant_id)
        ).first()
        
        if not finding:
            return None
        
        if status is not None:
            finding.status = status
        if assigned_to is not None:
            finding.assigned_to = assigned_to
        if notes is not None:
            # Add notes to metadata
            if not finding.metadata:
                finding.metadata = {}
            finding.metadata['notes'] = notes
        
        if status == 'fixed':
            finding.resolved_at = datetime.utcnow().isoformat()
        
        self.db.commit()
        self.db.refresh(finding)
        return finding
    
    # Report Generation
    def generate_report(self, tenant_id: UUID, audit_run_id: UUID, 
                       format_type: str = 'html') -> Optional[str]:
        """Generate audit report"""
        audit_run = self.get_audit_run(tenant_id, audit_run_id)
        if not audit_run:
            return None
        
        findings = self.get_findings(tenant_id, audit_run_id=audit_run_id)
        pages = self.db.query(Page).filter(Page.audit_run_id == audit_run_id).all()
        
        try:
            return self.reporting_service.generate_report(audit_run, findings, pages, format_type)
        except Exception as e:
            logger.error(f"Error generating report for audit {audit_run_id}: {e}")
            return None
    
    def get_audit_summary(self, tenant_id: UUID, audit_run_id: UUID) -> Optional[Dict[str, Any]]:
        """Get audit summary statistics"""
        audit_run = self.get_audit_run(tenant_id, audit_run_id)
        if not audit_run:
            return None
        
        findings = self.get_findings(tenant_id, audit_run_id=audit_run_id)
        pages = self.db.query(Page).filter(Page.audit_run_id == audit_run_id).all()
        
        # Calculate summary
        total_findings = len(findings)
        critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        # Calculate overall score
        overall_score = 100.0
        for finding in findings:
            if finding.severity == FindingSeverity.CRITICAL:
                overall_score -= 15
            elif finding.severity == FindingSeverity.HIGH:
                overall_score -= 10
            elif finding.severity == FindingSeverity.MEDIUM:
                overall_score -= 5
            elif finding.severity == FindingSeverity.LOW:
                overall_score -= 2
        
        overall_score = max(0, overall_score)
        
        return {
            "audit_run_id": str(audit_run.id),
            "name": audit_run.name,
            "state": audit_run.state.value,
            "created_at": audit_run.created_at.isoformat(),
            "started_at": audit_run.started_at.isoformat() if audit_run.started_at else None,
            "finished_at": audit_run.finished_at.isoformat() if audit_run.finished_at else None,
            "duration": audit_run.duration,
            "pages_crawled": audit_run.pages_crawled,
            "pages_analyzed": audit_run.pages_analyzed,
            "total_findings": total_findings,
            "critical_findings": critical_findings,
            "high_findings": high_findings,
            "medium_findings": medium_findings,
            "low_findings": low_findings,
            "overall_score": round(overall_score, 1),
            "completion_percentage": audit_run.completion_percentage
        }
