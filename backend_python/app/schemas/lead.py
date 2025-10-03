"""
Lead schemas for API requests and responses
"""

from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, EmailStr
from uuid import UUID

class LeadBase(BaseModel):
    """Base lead schema"""
    full_name: str
    email: Optional[EmailStr] = None
    phone: Optional[str] = None
    firm: Optional[str] = None
    website: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    zip_code: Optional[str] = None
    practice_areas: Optional[List[str]] = None
    status: str = "new_lead"

class LeadCreate(LeadBase):
    """Schema for creating a lead"""
    client_id: UUID

class LeadUpdate(BaseModel):
    """Schema for updating a lead"""
    full_name: Optional[str] = None
    email: Optional[EmailStr] = None
    phone: Optional[str] = None
    firm: Optional[str] = None
    website: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    zip_code: Optional[str] = None
    practice_areas: Optional[List[str]] = None
    status: Optional[str] = None

class LeadResponse(LeadBase):
    """Schema for lead response"""
    id: UUID
    client_id: UUID
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True
