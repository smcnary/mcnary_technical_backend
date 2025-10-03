"""
Lead management endpoints migrated from Symfony LeadsController
"""

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List, Optional
from pydantic import BaseModel
from datetime import datetime
import uuid as uuid_lib

from app.core.database import get_db
from app.models.lead import Lead, LeadStatus

leads_router = APIRouter()

class LeadCreate(BaseModel):
    """Lead creation model"""
    full_name: str
    email: str
    phone: Optional[str] = None
    firm: Optional[str] = None
    website: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    zip_code: Optional[str] = None
    message: Optional[str] = None
    practice_areas: Optional[List[str]] = None
    status: Optional[str] = "new_lead"

class LeadResponse(BaseModel):
    """Lead response model"""
    id: str
    full_name: str
    email: str
    phone: Optional[str]
    firm: Optional[str]
    website: Optional[str]
    city: Optional[str]
    state: Optional[str]
    zip_code: Optional[str]
    message: Optional[str]
    practice_areas: List[str]
    status: str
    status_label: str
    created_at: datetime
    updated_at: datetime
    
    @classmethod
    def from_orm_lead(cls, lead: Lead):
        """Convert SQLAlchemy Lead to response model"""
        return cls(
            id=str(lead.id),
            full_name=lead.full_name,
            email=lead.email,
            phone=lead.phone,
            firm=lead.firm,
            website=lead.website,
            city=lead.city,
            state=lead.state,
            zip_code=lead.zip_code,
            message=lead.message,
            practice_areas=lead.practice_areas or [],
            status=lead.status_value,
            status_label=lead.status_label,
            created_at=lead.created_at,
            updated_at=lead.updated_at
        )

@leads_router.post("/", response_model=LeadResponse)
async def create_lead(lead_data: LeadCreate, db: Session = Depends(get_db)):
    """Create a new lead - migrated from Symfony LeadsController::createLead"""
    
    try:
        # Create the lead
        lead = Lead(
            full_name=lead_data.full_name,
            email=lead_data.email,
            phone=lead_data.phone,
            firm=lead_data.firm,
            website=lead_data.website,
            city=lead_data.city,
            state=lead_data.state,
            zip_code=lead_data.zip_code,
            message=lead_data.message,
            practice_areas=lead_data.practice_areas or [],
            status=LeadStatus.NEW_LEAD
        )
        
        db.add(lead)
        db.commit()
        db.refresh(lead)
        
        return LeadResponse.from_orm_lead(lead)
        
    except Exception as e:
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create lead: {str(e)}"
        )

@leads_router.get("/", response_model=List[LeadResponse])
async def get_leads(
    skip: int = 0,
    limit: int = 100,
    per_page: int = 25,
    page: int = 1,
    db: Session = Depends(get_db)
):
    """Get paginated list of leads"""
    
    offset = (page - 1) * per_page if per_page > 0 else skip
    limit_val = per_page if per_page > 0 else limit
    
    leads = db.query(Lead).offset(offset).limit(limit_val).all()
    return [LeadResponse.from_orm_lead(lead) for lead in leads]

@leads_router.get("/{lead_id}", response_model=LeadResponse)
async def get_lead(lead_id: str, db: Session = Depends(get_db)):
    """Get a specific lead by ID"""
    
    try:
        lead_uuid = uuid_lib.UUID(lead_id)
    except ValueError:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Invalid lead ID format"
        )
    
    lead = db.query(Lead).filter(Lead.id == lead_uuid).first()
    if not lead:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Lead not found"
        )
    
    return LeadResponse.from_orm_lead(lead)
