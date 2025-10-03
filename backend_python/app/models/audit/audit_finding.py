"""
Audit Finding model for SEO audit results
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum, Integer, Float, Boolean
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class FindingSeverity(enum.Enum):
    """Finding severity levels"""
    CRITICAL = "critical"
    HIGH = "high"
    MEDIUM = "medium"
    LOW = "low"
    INFO = "info"


class FindingCategory(enum.Enum):
    """Finding categories"""
    TECHNICAL_SEO = "technical_seo"
    ON_PAGE_SEO = "on_page_seo"
    CONTENT = "content"
    PERFORMANCE = "performance"
    MOBILE = "mobile"
    SECURITY = "security"
    ACCESSIBILITY = "accessibility"
    UX = "ux"
    BACKLINKS = "backlinks"
    LOCAL_SEO = "local_seo"
    SOCIAL_MEDIA = "social_media"
    ANALYTICS = "analytics"


class AuditFinding(Base, TimestampMixin):
    """Audit finding entity for SEO audit results"""
    
    __tablename__ = "audit_findings"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    audit_run_id = Column(UUID(as_uuid=True), ForeignKey("audit_runs.id"), nullable=False)
    page_id = Column(UUID(as_uuid=True), ForeignKey("pages.id"), nullable=True)
    
    # Finding identification
    check_code = Column(String(100), nullable=False, index=True)  # Unique check identifier
    check_name = Column(String(255), nullable=False)
    category = Column(SQLEnum(FindingCategory), nullable=False)
    severity = Column(SQLEnum(FindingSeverity), nullable=False)
    
    # Finding details
    title = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    recommendation = Column(Text, nullable=True)
    impact = Column(String(255), nullable=True)  # High-level impact description
    
    # Technical details
    element = Column(String(255), nullable=True)  # HTML element, CSS selector, etc.
    attribute = Column(String(255), nullable=True)  # HTML attribute name
    value = Column(Text, nullable=True)  # Current value
    expected_value = Column(Text, nullable=True)  # Expected value
    
    # Location information
    url = Column(String(500), nullable=True)
    line_number = Column(Integer, nullable=True)
    column_number = Column(Integer, nullable=True)
    xpath = Column(String(500), nullable=True)
    css_selector = Column(String(500), nullable=True)
    
    # Metrics and scores
    score_impact = Column(Float, nullable=True)  # Impact on overall score (-100 to 100)
    priority_score = Column(Integer, nullable=True)  # Calculated priority score
    difficulty = Column(String(20), nullable=True)  # easy, medium, hard
    
    # Status and tracking
    status = Column(String(20), nullable=False, default="open")  # open, in_progress, fixed, ignored
    assigned_to = Column(UUID(as_uuid=True), ForeignKey("users.id"), nullable=True)
    due_date = Column(String(50), nullable=True)
    resolved_at = Column(String(50), nullable=True)
    
    # Additional data
    screenshots = Column(JSONB, nullable=True)  # Screenshot URLs
    code_snippets = Column(JSONB, nullable=True)  # Code examples
    references = Column(JSONB, nullable=True)  # External references, documentation
    tags = Column(JSONB, nullable=True)
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    audit_run = relationship("AuditRun", back_populates="findings")
    page = relationship("Page", back_populates="findings")
    assigned_user = relationship("User")
    
    def __repr__(self):
        return f"<AuditFinding(id={self.id}, check='{self.check_code}', severity={self.severity.value})>"
    
    @property
    def is_resolved(self) -> bool:
        """Check if finding is resolved"""
        return self.status in ["fixed", "resolved"]
    
    @property
    def is_ignored(self) -> bool:
        """Check if finding is ignored"""
        return self.status == "ignored"
    
    @property
    def is_open(self) -> bool:
        """Check if finding is open"""
        return self.status == "open"
    
    @property
    def severity_weight(self) -> int:
        """Get severity weight for scoring"""
        weights = {
            FindingSeverity.CRITICAL: 10,
            FindingSeverity.HIGH: 7,
            FindingSeverity.MEDIUM: 4,
            FindingSeverity.LOW: 2,
            FindingSeverity.INFO: 1
        }
        return weights.get(self.severity, 1)
    
    def calculate_priority_score(self) -> int:
        """Calculate priority score based on severity and impact"""
        base_score = self.severity_weight * 10
        
        # Adjust based on score impact
        if self.score_impact:
            if self.score_impact > 0:
                base_score += int(self.score_impact)
            else:
                base_score += abs(int(self.score_impact))
        
        # Adjust based on difficulty
        difficulty_multiplier = {
            "easy": 1.0,
            "medium": 1.2,
            "hard": 1.5
        }
        
        multiplier = difficulty_multiplier.get(self.difficulty, 1.0)
        return int(base_score * multiplier)
