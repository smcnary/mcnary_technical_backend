"""
Organization schemas for API requests and responses
"""

from datetime import datetime
from typing import Optional, Dict, Any
from pydantic import BaseModel
from uuid import UUID

class OrganizationBase(BaseModel):
    """Base organization schema"""
    name: str
    domain: Optional[str] = None
    status: str = "active"

class OrganizationCreate(OrganizationBase):
    """Schema for creating an organization"""
    pass

class OrganizationUpdate(BaseModel):
    """Schema for updating an organization"""
    name: Optional[str] = None
    domain: Optional[str] = None
    status: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None

class OrganizationResponse(OrganizationBase):
    """Schema for organization response"""
    id: UUID
    metadata: Optional[Dict[str, Any]] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True
