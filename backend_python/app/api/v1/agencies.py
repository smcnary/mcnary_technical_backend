"""
Agency management endpoints
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_active_user, require_system_admin
from app.schemas.agency import AgencyCreate, AgencyUpdate, AgencyResponse
from app.models.agency import Agency
from app.models.user import User

agencies_router = APIRouter()

@agencies_router.get("/", response_model=List[AgencyResponse])
async def get_agencies(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get all agencies"""
    # Multi-tenant filtering based on user role
    query = db.query(Agency)
    
    if current_user.is_system_admin():
        # System admin can see all agencies
        pass
    elif current_user.is_agency_admin():
        # Agency admin can only see their own agency
        query = query.filter(Agency.id == current_user.agency_id)
    else:
        raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    agencies = query.offset(skip).limit(limit).all()
    return agencies

@agencies_router.get("/{agency_id}", response_model=AgencyResponse)
async def get_agency(
    agency_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get a specific agency"""
    agency = db.query(Agency).filter(Agency.id == agency_id).first()
    if not agency:
        raise HTTPException(status_code=404, detail="Agency not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if agency.id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        else:
            raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    return agency

@agencies_router.post("/", response_model=AgencyResponse)
async def create_agency(
    agency_data: AgencyCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_system_admin)
):
    """Create a new agency (system admin only)"""
    # Create agency
    agency = Agency(
        name=agency_data.name,
        domain=agency_data.domain,
        description=agency_data.description,
        website_url=agency_data.website_url,
        phone=agency_data.phone,
        email=agency_data.email,
        address=agency_data.address,
        city=agency_data.city,
        state=agency_data.state,
        postal_code=agency_data.postal_code,
        country=agency_data.country,
        status=agency_data.status
    )
    
    db.add(agency)
    db.commit()
    db.refresh(agency)
    
    return agency

@agencies_router.put("/{agency_id}", response_model=AgencyResponse)
async def update_agency(
    agency_id: str,
    agency_data: AgencyUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Update an agency"""
    agency = db.query(Agency).filter(Agency.id == agency_id).first()
    if not agency:
        raise HTTPException(status_code=404, detail="Agency not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if agency.id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        else:
            raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    # Update fields
    update_data = agency_data.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(agency, field, value)
    
    db.commit()
    db.refresh(agency)
    
    return agency

@agencies_router.delete("/{agency_id}")
async def delete_agency(
    agency_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_system_admin)
):
    """Delete an agency (system admin only)"""
    agency = db.query(Agency).filter(Agency.id == agency_id).first()
    if not agency:
        raise HTTPException(status_code=404, detail="Agency not found")
    
    db.delete(agency)
    db.commit()
    
    return {"message": "Agency deleted successfully"}
