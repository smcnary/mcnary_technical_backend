"""
User model migrated from Symfony User entity
"""

from sqlalchemy import Column, String, Boolean, Text, UUID, ForeignKey, Enum as SQLEnum
from sqlalchemy.orm import relationship
from sqlalchemy.dialects.postgresql import UUID as PostgresUUID
import uuid
import enum

from app.models.base import TimestampMixin
from app.core.database import Base

class UserRole(str, enum.Enum):
    """User role enumeration"""
    SYSTEM_ADMIN = "ROLE_SYSTEM_ADMIN"
    AGENCY_ADMIN = "ROLE_AGENCY_ADMIN"
    AGENCY_STAFF = "ROLE_AGENCY_STAFF"
    CLIENT_ADMIN = "ROLE_CLIENT_ADMIN"
    CLIENT_STAFF = "ROLE_CLIENT_STAFF"
    CLIENT_USER = "ROLE_CLIENT_USER"
    SALES_CONSULTANT = "ROLE_SALES_CONSULTANT"
    READ_ONLY = "ROLE_READ_ONLY"
    USER = "ROLE_USER"

class UserStatus(str, enum.Enum):
    """User status enumeration"""
    INVITED = "invited"
    ACTIVE = "active"
    SUSPENDED = "suspended"
    DEACTIVATED = "deactivated"

class User(Base, TimestampMixin):
    """User model"""
    
    __tablename__ = "users"
    
    id = Column(PostgresUUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    email = Column(String(255), unique=True, index=True, nullable=False)
    password_hash = Column(String(255), nullable=True)
    first_name = Column(String(255), nullable=True)
    last_name = Column(String(255), nullable=True)
    status = Column(SQLEnum(UserStatus), default=UserStatus.INVITED, nullable=False)
    last_login_at = Column(String(255), nullable=True)
    
    # Foreign keys
    organization_id = Column(PostgresUUID(as_uuid=True), ForeignKey('organizations.id'), nullable=False)
    agency_id = Column(PostgresUUID(as_uuid=True), ForeignKey('agencies.id'), nullable=True)
    tenant_id = Column(PostgresUUID(as_uuid=True), ForeignKey('tenants.id'), nullable=True)
    client_id = Column(PostgresUUID(as_uuid=True), nullable=True)
    
    # Metadata
    metadata_json = Column(Text, nullable=True)
    
# Relationships - commented out for now to avoid circular imports
# organization = relationship("Organization", back_populates="users")
# agency = relationship("Agency", back_populates="users")
# tenant = relationship("Tenant", back_populates="users")
    
    @property
    def full_name(self) -> str:
        """Get user's full name"""
        if self.first_name and self.last_name:
            return f"{self.first_name} {self.last_name}"
        return self.email
    
    @property
    def name(self) -> str:
        """Alias for full_name"""
        return self.full_name
    
    def get_roles(self) -> list[str]:
        """Get user roles - simplified implementation"""
        # In the actual implementation, you might have a separate roles table
        # For now, we'll assume system admin role
        return ["ROLE_USER", "ROLE_SYSTEM_ADMIN"]
    
    def has_role(self, role: str) -> bool:
        """Check if user has a specific role"""
        return role in self.get_roles()
