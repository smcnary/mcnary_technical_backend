"""
Client schemas for API requests and responses
"""

from datetime import datetime
from typing import Optional, Dict, Any
from pydantic import BaseModel
from uuid import UUID

class ClientBase(BaseModel):
    """Base client schema"""
    name: str
    slug: Optional[str] = None
    description: Optional[str] = None
    website_url: Optional[str] = None
    phone: Optional[str] = None
    email: Optional[str] = None
    address: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    postal_code: Optional[str] = None
    country: Optional[str] = None
    industry: str = "law"
    status: str = "active"

class ClientCreate(ClientBase):
    """Schema for creating a client"""
    agency_id: UUID

class ClientUpdate(BaseModel):
    """Schema for updating a client"""
    name: Optional[str] = None
    slug: Optional[str] = None
    description: Optional[str] = None
    website_url: Optional[str] = None
    phone: Optional[str] = None
    email: Optional[str] = None
    address: Optional[str] = None
    city: Optional[str] = None
    state: Optional[str] = None
    postal_code: Optional[str] = None
    country: Optional[str] = None
    industry: Optional[str] = None
    status: Optional[str] = None
    google_business_profile: Optional[Dict[str, Any]] = None
    google_search_console: Optional[Dict[str, Any]] = None
    google_analytics: Optional[Dict[str, Any]] = None
    metadata: Optional[Dict[str, Any]] = None

class ClientResponse(ClientBase):
    """Schema for client response"""
    id: UUID
    agency_id: UUID
    google_business_profile: Optional[Dict[str, Any]] = None
    google_search_console: Optional[Dict[str, Any]] = None
    google_analytics: Optional[Dict[str, Any]] = None
    metadata: Optional[Dict[str, Any]] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True
