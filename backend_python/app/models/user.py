"""
User model - migrated from Symfony User entity
"""

from sqlalchemy import Column, String, DateTime, ForeignKey, Boolean, Text
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import uuid
import enum

from app.core.database import Base
from app.models.base import TimestampMixin, UUIDMixin

class UserStatus(str, enum.Enum):
    """User status enumeration"""
    INVITED = "invited"
    ACTIVE = "active"
    SUSPENDED = "suspended"
    DEACTIVATED = "deactivated"

class User(Base, TimestampMixin, UUIDMixin):
    __tablename__ = "users"
    
    # User identification
    email = Column(String(255), unique=True, nullable=False, index=True)
    password_hash = Column(String(255), nullable=True)
    first_name = Column(String(100), nullable=True)
    last_name = Column(String(100), nullable=True)
    
    # User status and role
    status = Column(String(50), default="invited", nullable=False)  # invited, active, suspended, deleted
    role = Column(String(50), nullable=False)  # ROLE_SYSTEM_ADMIN, ROLE_AGENCY_ADMIN, etc.
    
    # Login tracking
    last_login_at = Column(DateTime(timezone=True), nullable=True)
    
    # Multi-tenancy relationships
    organization_id = Column(UUID(as_uuid=True), ForeignKey("organizations.id"), nullable=False)
    organization = relationship("Organization", back_populates="users")
    
    agency_id = Column(UUID(as_uuid=True), ForeignKey("agencies.id"), nullable=True)
    agency = relationship("Agency", back_populates="users")
    
    tenant_id = Column(UUID(as_uuid=True), ForeignKey("tenants.id"), nullable=True)
    tenant = relationship("Tenant", back_populates="users")
    
    # Client access (for client users)
    client_id = Column(UUID(as_uuid=True), nullable=True)
    
    # Additional metadata
    metadata_json = Column('metadata', JSONB, nullable=True, default=dict)
    
    # Role constants (matching Symfony)
    ROLE_SYSTEM_ADMIN = 'ROLE_SYSTEM_ADMIN'
    ROLE_AGENCY_ADMIN = 'ROLE_AGENCY_ADMIN'
    ROLE_AGENCY_STAFF = 'ROLE_AGENCY_STAFF'
    ROLE_CLIENT_ADMIN = 'ROLE_CLIENT_ADMIN'
    ROLE_CLIENT_STAFF = 'ROLE_CLIENT_STAFF'
    ROLE_CLIENT_USER = 'ROLE_CLIENT_USER'
    ROLE_READ_ONLY = 'ROLE_READ_ONLY'
    
    def get_name(self) -> str:
        """Get full name or partial name"""
        if self.first_name and self.last_name:
            return f"{self.first_name} {self.last_name}"
        return self.first_name or self.last_name or ""
    
    def has_role(self, role: str) -> bool:
        """Check if user has specific role"""
        return self.role == role
    
    def is_system_admin(self) -> bool:
        """Check if user is system admin"""
        return self.has_role(self.ROLE_SYSTEM_ADMIN)
    
    def is_agency_admin(self) -> bool:
        """Check if user is agency admin"""
        return self.has_role(self.ROLE_AGENCY_ADMIN)
    
    def is_client_user(self) -> bool:
        """Check if user is client user"""
        return self.has_role(self.ROLE_CLIENT_USER)
    
    def is_read_only(self) -> bool:
        """Check if user is read-only"""
        return self.has_role(self.ROLE_READ_ONLY)
    
    def get_roles(self) -> list:
        """Get all roles for the user"""
        return ['ROLE_USER', self.role]
    
    def __repr__(self):
        return f"<User(id={self.id}, email='{self.email}', role='{self.role}')>"
