"""
Lighthouse Run model for performance testing
"""

from sqlalchemy import Column, String, ForeignKey, Enum as SQLEnum, Integer, Float, Boolean
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class LighthouseCategory(enum.Enum):
    """Lighthouse category types"""
    PERFORMANCE = "performance"
    ACCESSIBILITY = "accessibility"
    BEST_PRACTICES = "best_practices"
    SEO = "seo"
    PWA = "pwa"


class LighthouseRun(Base, TimestampMixin):
    """Lighthouse run entity for performance testing results"""
    
    __tablename__ = "lighthouse_runs"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    audit_run_id = Column(UUID(as_uuid=True), ForeignKey("audit_runs.id"), nullable=False)
    page_id = Column(UUID(as_uuid=True), ForeignKey("pages.id"), nullable=True)
    
    # Run identification
    url = Column(String(500), nullable=False)
    run_at = Column(String(50), nullable=False)
    
    # Overall scores (0-100)
    performance_score = Column(Integer, nullable=True)
    accessibility_score = Column(Integer, nullable=True)
    best_practices_score = Column(Integer, nullable=True)
    seo_score = Column(Integer, nullable=True)
    pwa_score = Column(Integer, nullable=True)
    
    # Performance metrics
    first_contentful_paint = Column(Float, nullable=True)  # FCP in seconds
    largest_contentful_paint = Column(Float, nullable=True)  # LCP in seconds
    first_input_delay = Column(Float, nullable=True)  # FID in milliseconds
    cumulative_layout_shift = Column(Float, nullable=True)  # CLS score
    speed_index = Column(Float, nullable=True)  # SI in seconds
    time_to_interactive = Column(Float, nullable=True)  # TTI in seconds
    total_blocking_time = Column(Float, nullable=True)  # TBT in milliseconds
    
    # Network and resource metrics
    total_requests = Column(Integer, nullable=True)
    total_size = Column(Integer, nullable=True)  # Total transfer size in bytes
    unused_css = Column(Integer, nullable=True)  # Unused CSS in bytes
    unused_js = Column(Integer, nullable=True)  # Unused JavaScript in bytes
    render_blocking_resources = Column(JSONB, nullable=True)
    
    # Opportunities and diagnostics
    opportunities = Column(JSONB, nullable=True)  # Performance opportunities
    diagnostics = Column(JSONB, nullable=True)  # Performance diagnostics
    audits = Column(JSONB, nullable=True)  # All audit results
    
    # Device and browser info
    device_type = Column(String(20), nullable=True)  # mobile, desktop
    browser = Column(String(50), nullable=True)
    viewport_width = Column(Integer, nullable=True)
    viewport_height = Column(Integer, nullable=True)
    
    # Additional metadata
    lighthouse_version = Column(String(20), nullable=True)
    config = Column(JSONB, nullable=True)  # Lighthouse configuration used
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    audit_run = relationship("AuditRun", back_populates="lighthouse_runs")
    page = relationship("Page", back_populates="lighthouse_runs")
    
    def __repr__(self):
        return f"<LighthouseRun(id={self.id}, url='{self.url}', performance={self.performance_score})>"
    
    @property
    def overall_score(self) -> float:
        """Calculate overall Lighthouse score"""
        scores = [
            self.performance_score,
            self.accessibility_score,
            self.best_practices_score,
            self.seo_score
        ]
        
        # Filter out None values
        valid_scores = [score for score in scores if score is not None]
        
        if not valid_scores:
            return 0.0
            
        return sum(valid_scores) / len(valid_scores)
    
    @property
    def performance_grade(self) -> str:
        """Get performance grade based on score"""
        if not self.performance_score:
            return "N/A"
        
        if self.performance_score >= 90:
            return "A"
        elif self.performance_score >= 80:
            return "B"
        elif self.performance_score >= 70:
            return "C"
        elif self.performance_score >= 50:
            return "D"
        else:
            return "F"
    
    @property
    def accessibility_grade(self) -> str:
        """Get accessibility grade based on score"""
        if not self.accessibility_score:
            return "N/A"
        
        if self.accessibility_score >= 90:
            return "A"
        elif self.accessibility_score >= 80:
            return "B"
        elif self.accessibility_score >= 70:
            return "C"
        elif self.accessibility_score >= 50:
            return "D"
        else:
            return "F"
    
    @property
    def seo_grade(self) -> str:
        """Get SEO grade based on score"""
        if not self.seo_score:
            return "N/A"
        
        if self.seo_score >= 90:
            return "A"
        elif self.seo_score >= 80:
            return "B"
        elif self.seo_score >= 70:
            return "C"
        elif self.seo_score >= 50:
            return "D"
        else:
            return "F"
    
    def get_core_web_vitals(self) -> dict:
        """Get Core Web Vitals data"""
        return {
            "lcp": self.largest_contentful_paint,
            "fid": self.first_input_delay,
            "cls": self.cumulative_layout_shift,
            "fcp": self.first_contentful_paint
        }
    
    def get_performance_opportunities(self) -> list:
        """Get performance opportunities with savings"""
        if not self.opportunities:
            return []
        
        opportunities = []
        for opp in self.opportunities:
            if opp.get("score") and opp["score"] < 0.9:  # Opportunities with room for improvement
                opportunities.append({
                    "id": opp.get("id"),
                    "title": opp.get("title"),
                    "description": opp.get("description"),
                    "score": opp.get("score"),
                    "displayValue": opp.get("displayValue"),
                    "savings": opp.get("details", {}).get("overallSavingsMs", 0)
                })
        
        # Sort by potential savings
        return sorted(opportunities, key=lambda x: x["savings"], reverse=True)
    
    def get_performance_diagnostics(self) -> list:
        """Get performance diagnostics"""
        if not self.diagnostics:
            return []
        
        diagnostics = []
        for diag in self.diagnostics:
            if diag.get("score") is not None:
                diagnostics.append({
                    "id": diag.get("id"),
                    "title": diag.get("title"),
                    "description": diag.get("description"),
                    "score": diag.get("score"),
                    "displayValue": diag.get("displayValue")
                })
        
        return diagnostics
    
    def get_audit_results(self, category: LighthouseCategory = None) -> list:
        """Get audit results for a specific category or all"""
        if not self.audits:
            return []
        
        if category:
            # Filter audits by category
            category_audits = [audit for audit in self.audits if audit.get("category") == category.value]
            return category_audits
        else:
            return self.audits
