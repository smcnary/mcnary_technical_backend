"""
User schemas for API requests and responses
"""

from datetime import datetime
from typing import Optional
from pydantic import BaseModel, EmailStr
from uuid import UUID

class UserBase(BaseModel):
    """Base user schema"""
    email: EmailStr
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    status: str = "invited"
    role: str

class UserCreate(UserBase):
    """Schema for creating a user"""
    password: str
    organization_id: UUID
    agency_id: Optional[UUID] = None
    tenant_id: Optional[UUID] = None
    client_id: Optional[UUID] = None

class UserUpdate(BaseModel):
    """Schema for updating a user"""
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    status: Optional[str] = None
    role: Optional[str] = None
    agency_id: Optional[UUID] = None
    tenant_id: Optional[UUID] = None
    client_id: Optional[UUID] = None

class UserResponse(UserBase):
    """Schema for user response"""
    id: UUID
    organization_id: UUID
    agency_id: Optional[UUID] = None
    tenant_id: Optional[UUID] = None
    client_id: Optional[UUID] = None
    last_login_at: Optional[datetime] = None
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True

class UserLogin(BaseModel):
    """Schema for user login"""
    email: EmailStr
    password: str

class Token(BaseModel):
    """Schema for JWT token response"""
    access_token: str
    token_type: str = "bearer"

class TokenData(BaseModel):
    """Schema for token data"""
    email: Optional[str] = None
