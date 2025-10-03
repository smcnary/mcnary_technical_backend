"""
Lead management endpoints
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_active_user, require_agency_admin
from app.schemas.lead import LeadCreate, LeadUpdate, LeadResponse
from app.models.lead import Lead
from app.models.user import User

leads_router = APIRouter()

@leads_router.get("/", response_model=List[LeadResponse])
async def get_leads(
    skip: int = 0,
    limit: int = 25,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get all leads"""
    # Multi-tenant filtering based on user role
    query = db.query(Lead)
    
    if current_user.is_system_admin():
        # System admin can see all leads
        pass
    elif current_user.is_agency_admin():
        # Agency admin can see leads from their agency's clients
        query = query.join(Lead.client).filter(Lead.client.has(agency_id=current_user.agency_id))
    elif current_user.client_id:
        # Client users can only see their own leads
        query = query.filter(Lead.client_id == current_user.client_id)
    else:
        raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    leads = query.offset(skip).limit(limit).all()
    return leads

@leads_router.get("/{lead_id}", response_model=LeadResponse)
async def get_lead(
    lead_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get a specific lead"""
    lead = db.query(Lead).filter(Lead.id == lead_id).first()
    if not lead:
        raise HTTPException(status_code=404, detail="Lead not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if lead.client and lead.client.agency_id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        elif current_user.client_id and lead.client_id != current_user.client_id:
            raise HTTPException(status_code=403, detail="Not enough permissions")
    
    return lead

@leads_router.post("/", response_model=LeadResponse)
async def create_lead(
    lead_data: LeadCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Create a new lead"""
    # Check permissions - only agency staff and system admins can create leads
    if not (current_user.is_system_admin() or current_user.is_agency_admin() or current_user.has_role("ROLE_AGENCY_STAFF")):
        raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    # Create lead
    lead = Lead(
        full_name=lead_data.full_name,
        email=lead_data.email,
        phone=lead_data.phone,
        firm=lead_data.firm,
        website=lead_data.website,
        city=lead_data.city,
        state=lead_data.state,
        zip_code=lead_data.zip_code,
        practice_areas=lead_data.practice_areas,
        status=lead_data.status,
        client_id=lead_data.client_id
    )
    
    db.add(lead)
    db.commit()
    db.refresh(lead)
    
    return lead

@leads_router.put("/{lead_id}", response_model=LeadResponse)
async def update_lead(
    lead_id: str,
    lead_data: LeadUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Update a lead"""
    lead = db.query(Lead).filter(Lead.id == lead_id).first()
    if not lead:
        raise HTTPException(status_code=404, detail="Lead not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if lead.client and lead.client.agency_id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        elif current_user.client_id and lead.client_id != current_user.client_id:
            raise HTTPException(status_code=403, detail="Not enough permissions")
    
    # Update fields
    update_data = lead_data.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(lead, field, value)
    
    db.commit()
    db.refresh(lead)
    
    return lead

@leads_router.delete("/{lead_id}")
async def delete_lead(
    lead_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_agency_admin)
):
    """Delete a lead (agency admin only)"""
    lead = db.query(Lead).filter(Lead.id == lead_id).first()
    if not lead:
        raise HTTPException(status_code=404, detail="Lead not found")
    
    db.delete(lead)
    db.commit()
    
    return {"message": "Lead deleted successfully"}