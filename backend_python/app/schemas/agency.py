"""
Agency schemas for API requests and responses
"""

from datetime import datetime
from typing import Optional, Dict, Any
from pydantic import BaseModel
from uuid import UUID

class AgencyBase(BaseModel):
    """Base agency schema"""
    name: str
    domain: Optional[str] = None
    description: Optional[str] = None
    website_url: Optional[str] = None
    phone: Optional[str] = None
    email: Optional[str] = None
    address: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    postal_code: Optional[str] = None
    country: Optional[str] = None
    status: str = "active"

class AgencyCreate(AgencyBase):
    """Schema for creating an agency"""
    pass

class AgencyUpdate(BaseModel):
    """Schema for updating an agency"""
    name: Optional[str] = None
    domain: Optional[str] = None
    description: Optional[str] = None
    website_url: Optional[str] = None
    phone: Optional[str] = None
    email: Optional[str] = None
    address: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    postal_code: Optional[str] = None
    country: Optional[str] = None
    status: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None

class AgencyResponse(AgencyBase):
    """Schema for agency response"""
    id: UUID
    metadata: Optional[Dict[str, Any]] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True
