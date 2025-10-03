"""
Background worker for audit processing
"""

import asyncio
import logging
from typing import Dict, Any, Optional
from uuid import UUID
from datetime import datetime
from sqlalchemy.orm import Session

from app.core.database import get_db_session
from app.services.audit_service import AuditService
from app.services.crawler_service import CrawlerService
from app.services.analysis_engine import SEOAnalysisEngine
from app.models.audit import AuditRun, AuditRunState, Page, PageStatus, AuditFinding
from app.models.audit import FindingSeverity, FindingCategory

logger = logging.getLogger(__name__)


class AuditWorker:
    """Background worker for processing SEO audits"""
    
    def __init__(self):
        self.crawler_service = CrawlerService()
        self.analysis_engine = SEOAnalysisEngine()
        self.active_tasks: Dict[str, asyncio.Task] = {}
    
    async def process_audit(self, audit_run_id: UUID) -> bool:
        """Process a complete audit run"""
        try:
            logger.info(f"Starting audit processing for {audit_run_id}")
            
            # Get database session
            db = next(get_db_session())
            audit_service = AuditService(db)
            
            # Get audit run
            audit_run = audit_service.get_audit_run(audit_run_id, audit_run_id)  # Using same ID for tenant and audit
            if not audit_run:
                logger.error(f"Audit run {audit_run_id} not found")
                return False
            
            # Update state to running
            audit_run.state = AuditRunState.RUNNING
            audit_run.started_at = datetime.utcnow()
            db.commit()
            
            try:
                # Phase 1: Crawling
                await self._crawl_phase(audit_run, audit_service)
                
                # Phase 2: Analysis
                await self._analysis_phase(audit_run, audit_service)
                
                # Phase 3: Report Generation
                await self._report_phase(audit_run, audit_service)
                
                # Mark as completed
                audit_run.state = AuditRunState.COMPLETED
                audit_run.finished_at = datetime.utcnow()
                db.commit()
                
                logger.info(f"Audit {audit_run_id} completed successfully")
                return True
                
            except Exception as e:
                logger.error(f"Error processing audit {audit_run_id}: {e}")
                audit_run.state = AuditRunState.FAILED
                audit_run.error_message = str(e)
                db.commit()
                return False
            
            finally:
                db.close()
                
        except Exception as e:
            logger.error(f"Critical error in audit worker: {e}")
            return False
    
    async def _crawl_phase(self, audit_run: AuditRun, audit_service: AuditService):
        """Handle the crawling phase"""
        logger.info(f"Starting crawl phase for audit {audit_run.id}")
        
        # Configure crawler
        crawl_config = {
            'max_pages': audit_run.max_pages,
            'crawl_delay': audit_run.crawl_delay,
            'respect_robots': audit_run.respect_robots_txt == 'yes',
            'max_depth': audit_run.crawl_depth,
            'include_external_links': audit_run.include_external_links == 'yes',
            'include_images': audit_run.include_images == 'yes',
            'include_js': audit_run.include_js == 'yes',
            'include_css': audit_run.include_css == 'yes'
        }
        
        # Start crawling
        await self.crawler_service.start_crawl(
            str(audit_run.id),
            audit_run.seed_urls,
            crawl_config
        )
        
        # Wait for crawling to complete
        await self._wait_for_crawling_completion(audit_run.id, audit_service)
        
        logger.info(f"Crawl phase completed for audit {audit_run.id}")
    
    async def _wait_for_crawling_completion(self, audit_run_id: UUID, audit_service: AuditService):
        """Wait for crawling to complete and update progress"""
        max_wait_time = 3600  # 1 hour
        wait_interval = 30  # 30 seconds
        waited = 0
        
        while waited < max_wait_time:
            status = self.crawler_service.get_crawl_status(str(audit_run_id))
            
            if status["status"] == "not_found":
                # Crawling completed
                break
            
            if status["status"] == "running":
                # Update progress in database
                audit_run = audit_service.get_audit_run(audit_run_id, audit_run_id)
                if audit_run:
                    audit_run.pages_crawled = status.get("crawled_urls", 0)
                    audit_service.db.commit()
            
            await asyncio.sleep(wait_interval)
            waited += wait_interval
        
        if waited >= max_wait_time:
            raise TimeoutError("Crawling timed out")
    
    async def _analysis_phase(self, audit_run: AuditRun, audit_service: AuditService):
        """Handle the analysis phase"""
        logger.info(f"Starting analysis phase for audit {audit_run.id}")
        
        # Get all pages for this audit
        pages = audit_service.db.query(Page).filter(Page.audit_run_id == audit_run.id).all()
        
        total_pages = len(pages)
        analyzed_pages = 0
        
        for page in pages:
            try:
                # Update page status to analyzing
                page.status = PageStatus.ANALYZING
                audit_service.db.commit()
                
                # Analyze the page
                await self._analyze_single_page(page, audit_service)
                
                # Mark as analyzed
                page.status = PageStatus.ANALYZED
                page.crawled_at = datetime.utcnow().isoformat()
                
                analyzed_pages += 1
                audit_run.pages_analyzed = analyzed_pages
                audit_service.db.commit()
                
                logger.info(f"Analyzed page {page.url} ({analyzed_pages}/{total_pages})")
                
            except Exception as e:
                logger.error(f"Error analyzing page {page.id}: {e}")
                page.status = PageStatus.FAILED
                page.error_message = str(e)
                audit_service.db.commit()
        
        logger.info(f"Analysis phase completed for audit {audit_run.id}")
    
    async def _analyze_single_page(self, page: Page, audit_service: AuditService):
        """Analyze a single page and generate findings"""
        # Create a mock CrawlResult from the page data
        from app.services.crawler_service import CrawlResult
        
        crawl_result = CrawlResult(
            url=page.url,
            status_code=page.status_code or 200,
            response_time=page.response_time or 0,
            content_length=page.content_length or 0,
            content_type=page.content_type or "text/html",
            title=page.title,
            meta_description=page.meta_description,
            meta_keywords=page.meta_keywords,
            h1_tags=page.h1_tags or [],
            h2_tags=page.h2_tags or [],
            h3_tags=page.h3_tags or [],
            images=page.images or [],
            links=page.links or [],
            scripts=page.scripts or [],
            stylesheets=page.stylesheets or [],
            word_count=page.word_count or 0,
            reading_time=page.reading_time or 0,
            canonical_url=page.canonical_url,
            redirect_chain=page.redirect_chain or []
        )
        
        # Run SEO analysis
        findings = self.analysis_engine.analyze_page(crawl_result)
        
        # Save findings to database
        for finding in findings:
            audit_finding = AuditFinding(
                tenant_id=page.tenant_id,
                audit_run_id=page.audit_run_id,
                page_id=page.id,
                check_code=finding.check_code,
                check_name=finding.check_name,
                category=finding.category,
                severity=finding.severity,
                title=finding.title,
                description=finding.description,
                recommendation=finding.recommendation,
                impact=finding.impact,
                element=finding.element,
                attribute=finding.attribute,
                value=finding.value,
                expected_value=finding.expected_value,
                score_impact=finding.score_impact,
                difficulty=finding.difficulty,
                url=page.url,
                status="open"
            )
            
            # Calculate priority score
            audit_finding.priority_score = audit_finding.calculate_priority_score()
            
            audit_service.db.add(audit_finding)
        
        audit_service.db.commit()
        
        # Update finding counts
        self._update_page_finding_counts(page, audit_service)
    
    def _update_page_finding_counts(self, page: Page, audit_service: AuditService):
        """Update finding counts for a page"""
        findings = audit_service.db.query(AuditFinding).filter(
            AuditFinding.page_id == page.id
        ).all()
        
        # Update page finding counts (these would be calculated properties)
        # For now, we'll store them in metadata
        if not page.metadata:
            page.metadata = {}
        
        page.metadata.update({
            'total_findings': len(findings),
            'critical_findings': len([f for f in findings if f.severity == FindingSeverity.CRITICAL]),
            'high_findings': len([f for f in findings if f.severity == FindingSeverity.HIGH]),
            'medium_findings': len([f for f in findings if f.severity == FindingSeverity.MEDIUM]),
            'low_findings': len([f for f in findings if f.severity == FindingSeverity.LOW])
        })
        
        audit_service.db.commit()
    
    async def _report_phase(self, audit_run: AuditRun, audit_service: AuditService):
        """Handle the report generation phase"""
        logger.info(f"Starting report phase for audit {audit_run.id}")
        
        # Update final finding counts
        self._update_audit_finding_counts(audit_run, audit_service)
        
        # Generate overall score
        self._calculate_audit_score(audit_run, audit_service)
        
        logger.info(f"Report phase completed for audit {audit_run.id}")
    
    def _update_audit_finding_counts(self, audit_run: AuditRun, audit_service: AuditService):
        """Update finding counts for audit run"""
        findings = audit_service.db.query(AuditFinding).filter(
            AuditFinding.audit_run_id == audit_run.id
        ).all()
        
        audit_run.findings_count = len(findings)
        audit_run.critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        audit_run.high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        audit_run.medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        audit_run.low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        audit_service.db.commit()
    
    def _calculate_audit_score(self, audit_run: AuditRun, audit_service: AuditService):
        """Calculate overall audit score"""
        findings = audit_service.db.query(AuditFinding).filter(
            AuditFinding.audit_run_id == audit_run.id
        ).all()
        
        # Calculate score based on findings
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
        
        # Store score in metadata
        if not audit_run.metadata:
            audit_run.metadata = {}
        
        audit_run.metadata['overall_score'] = max(0, overall_score)
        audit_run.metadata['score_calculated_at'] = datetime.utcnow().isoformat()
        
        audit_service.db.commit()
    
    async def cancel_audit(self, audit_run_id: UUID) -> bool:
        """Cancel a running audit"""
        try:
            # Stop crawler if running
            await self.crawler_service.stop_crawl(str(audit_run_id))
            
            # Update audit state
            db = next(get_db_session())
            audit_service = AuditService(db)
            
            audit_run = audit_service.get_audit_run(audit_run_id, audit_run_id)
            if audit_run and audit_run.can_cancel():
                audit_run.state = AuditRunState.CANCELED
                audit_run.finished_at = datetime.utcnow()
                db.commit()
                
                logger.info(f"Audit {audit_run_id} canceled")
                return True
            
            return False
            
        except Exception as e:
            logger.error(f"Error canceling audit {audit_run_id}: {e}")
            return False
        finally:
            db.close()
    
    async def retry_audit(self, audit_run_id: UUID) -> bool:
        """Retry a failed audit"""
        try:
            db = next(get_db_session())
            audit_service = AuditService(db)
            
            audit_run = audit_service.get_audit_run(audit_run_id, audit_run_id)
            if not audit_run:
                return False
            
            if not audit_run.can_retry():
                logger.warning(f"Audit {audit_run_id} cannot be retried")
                return False
            
            # Increment retry count
            audit_run.retry_count += 1
            audit_run.error_message = None
            db.commit()
            
            # Start processing again
            await self.process_audit(audit_run_id)
            
            return True
            
        except Exception as e:
            logger.error(f"Error retrying audit {audit_run_id}: {e}")
            return False
        finally:
            db.close()


# Global worker instance
audit_worker = AuditWorker()


async def start_audit_task(audit_run_id: UUID) -> asyncio.Task:
    """Start an audit processing task"""
    task = asyncio.create_task(audit_worker.process_audit(audit_run_id))
    audit_worker.active_tasks[str(audit_run_id)] = task
    return task


async def cancel_audit_task(audit_run_id: UUID) -> bool:
    """Cancel an audit processing task"""
    task = audit_worker.active_tasks.get(str(audit_run_id))
    if task:
        task.cancel()
        del audit_worker.active_tasks[str(audit_run_id)]
        return True
    return False
