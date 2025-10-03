"""
Lead model migrated from Symfony Lead entity
"""

from sqlalchemy import Column, String, Text, Boolean, DateTime, JSON, ForeignKey, Enum as SQLEnum
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID, ARRAY
import uuid
import enum

from app.models.base import TimestampMixin
from app.core.database import Base

class LeadStatus(str, enum.Enum):
    """Lead status enumeration"""
    NEW_LEAD = "new_lead"
    CONTACTED = "contacted"
    QUALIFIED = "qualified"
    PROPOSAL = "proposal"
    CLOSED_WON = "closed_won"
    CLOSED_LOST = "closed_lost"
    FOLLOW_UP = "follow_up"
    ON_HOLD = "on_hold"

class Lead(Base, TimestampMixin):
    """Lead model"""
    
    __tablename__ = "leads"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    
    # Core lead information
    full_name = Column(String(255), nullable=False, index=True)
    email = Column(String(255), nullable=False, index=True)
    phone = Column(String(50), nullable=True)
    
    # Business information
    firm = Column(String(255), nullable=True)
    website = Column(String(500), nullable=True)
    city = Column(String(100), nullable=True)
    state = Column(String(50), nullable=True)
    zip_code = Column(String(20), nullable=True)
    
    # Additional information
    message = Column(Text, nullable=True)
    practice_areas = Column(ARRAY(String), default=list)
    status = Column(SQLEnum(LeadStatus), default=LeadStatus.NEW_LEAD, nullable=False)
    
    # Tracking and metadata
    utm_json = Column(JSON, default=dict, nullable=True)
    interview_scheduled = Column(DateTime(timezone=True), nullable=True)
    follow_up_date = Column(DateTime(timezone=True), nullable=True)
    notes = Column(Text, nullable=True)
    is_test = Column(Boolean, default=False, nullable=False)
    
    # Foreign keys
    client_id = Column(PostgresUUID(as_uuid=True), ForeignKey('clients.id'), nullable=True)
    source_id = Column(PostgresUUID(as_uuid=True), ForeignKey('lead_sources.id'), nullable=True)
    
# Relationships - commented out for now to avoid circular imports
# client = relationship("Client", back_populates="leads")
# source = relationship("LeadSource", back_populates="leads")
    
    @property
    def status_value(self) -> str:
        """Get status as string value"""
        return self.status.value if self.status else "new_lead"
    
    @property
    def status_label(self) -> str:
        """Get human-readable status label"""
        labels = {
            LeadStatus.NEW_LEAD: "New Lead",
            LeadStatus.CONTACTED: "Contacted", 
            LeadStatus.QUALIFIED: "Qualified",
            LeadStatus.PROPOSAL: "Proposal",
            LeadStatus.CLOSED_WON: "Closed Won",
            LeadStatus.CLOSED_LOST: "Closed Lost",
            LeadStatus.FOLLOW_UP: "Follow Up",
            LeadStatus.ON_HOLD: "On Hold"
        }
        return labels.get(self.status, "Unknown")
