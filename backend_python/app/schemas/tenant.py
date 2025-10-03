"""
Tenant schemas for API requests and responses
"""

from datetime import datetime
from pydantic import BaseModel
from uuid import UUID

class TenantBase(BaseModel):
    """Base tenant schema"""
    name: str
    slug: str
    status: str = "trial"
    timezone: str = "UTC"

class TenantCreate(TenantBase):
    """Schema for creating a tenant"""
    pass

class TenantUpdate(BaseModel):
    """Schema for updating a tenant"""
    name: Optional[str] = None
    slug: Optional[str] = None
    status: Optional[str] = None
    timezone: Optional[str] = None

class TenantResponse(TenantBase):
    """Schema for tenant response"""
    id: UUID
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True
