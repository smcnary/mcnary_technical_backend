"""
Credential model for audit service integrations
"""

from sqlalchemy import Column, String, Text, ForeignKey, Enum as SQLEnum, Boolean, Integer
from sqlalchemy.dialects.postgresql import UUID, JSONB
from sqlalchemy.orm import relationship
import uuid
import enum

from app.models.base import Base, TimestampMixin


class CredentialType(enum.Enum):
    """Credential type"""
    GOOGLE_ANALYTICS = "google_analytics"
    GOOGLE_SEARCH_CONSOLE = "google_search_console"
    GOOGLE_PAGESPEED_INSIGHTS = "google_pagespeed_insights"
    GOOGLE_BUSINESS_PROFILE = "google_business_profile"
    FACEBOOK = "facebook"
    TWITTER = "twitter"
    LINKEDIN = "linkedin"
    INSTAGRAM = "instagram"
    YELP = "yelp"
    TRIPADVISOR = "tripadvisor"
    CUSTOM_API = "custom_api"


class CredentialStatus(enum.Enum):
    """Credential status"""
    ACTIVE = "active"
    INACTIVE = "inactive"
    EXPIRED = "expired"
    INVALID = "invalid"
    PENDING_VERIFICATION = "pending_verification"


class Credential(Base, TimestampMixin):
    """Credential entity for external service integrations"""
    
    __tablename__ = "credentials"
    
    id = Column(UUID(as_uuid=True), primary_key=True, default=uuid.uuid4)
    tenant_id = Column(UUID(as_uuid=True), nullable=False, index=True)
    project_id = Column(UUID(as_uuid=True), ForeignKey("projects.id"), nullable=False)
    
    # Credential details
    name = Column(String(255), nullable=False)
    credential_type = Column(SQLEnum(CredentialType), nullable=False)
    status = Column(SQLEnum(CredentialStatus), nullable=False, default=CredentialStatus.ACTIVE)
    
    # Authentication data (encrypted)
    client_id = Column(String(255), nullable=True)
    client_secret = Column(String(255), nullable=True)
    access_token = Column(Text, nullable=True)  # Encrypted
    refresh_token = Column(Text, nullable=True)  # Encrypted
    api_key = Column(String(255), nullable=True)  # Encrypted
    
    # Service-specific data
    account_id = Column(String(255), nullable=True)
    property_id = Column(String(255), nullable=True)
    view_id = Column(String(255), nullable=True)
    profile_id = Column(String(255), nullable=True)
    
    # Token expiration
    expires_at = Column(String(50), nullable=True)
    token_type = Column(String(50), nullable=True)
    scope = Column(Text, nullable=True)
    
    # Configuration
    settings = Column(JSONB, nullable=True)
    webhook_url = Column(String(500), nullable=True)
    rate_limit = Column(Integer, nullable=True, default=1000)  # Requests per hour
    
    # Verification and testing
    last_verified_at = Column(String(50), nullable=True)
    last_test_at = Column(String(50), nullable=True)
    test_result = Column(JSONB, nullable=True)
    error_message = Column(Text, nullable=True)
    
    # Additional metadata
    notes = Column(Text, nullable=True)
    metadata_json = Column(JSONB, nullable=True)
    
    # Relationships
    project = relationship("Project", back_populates="credentials")
    
    def __repr__(self):
        return f"<Credential(id={self.id}, name='{self.name}', type={self.credential_type.value})>"
    
    @property
    def is_expired(self) -> bool:
        """Check if credential is expired"""
        if not self.expires_at:
            return False
        
        try:
            from datetime import datetime
            expiry = datetime.fromisoformat(self.expires_at.replace('Z', '+00:00'))
            return datetime.now(expiry.tzinfo) > expiry
        except (ValueError, AttributeError):
            return False
    
    @property
    def needs_refresh(self) -> bool:
        """Check if credential needs token refresh"""
        return (self.credential_type in [
            CredentialType.GOOGLE_ANALYTICS,
            CredentialType.GOOGLE_SEARCH_CONSOLE,
            CredentialType.GOOGLE_PAGESPEED_INSIGHTS,
            CredentialType.GOOGLE_BUSINESS_PROFILE
        ] and self.is_expired and self.refresh_token)
    
    def get_auth_headers(self) -> dict:
        """Get authentication headers for API requests"""
        if self.token_type and self.access_token:
            return {"Authorization": f"{self.token_type} {self.access_token}"}
        elif self.api_key:
            return {"X-API-Key": self.api_key}
        return {}
    
    def get_auth_params(self) -> dict:
        """Get authentication parameters for API requests"""
        params = {}
        
        if self.client_id:
            params["client_id"] = self.client_id
        if self.account_id:
            params["account_id"] = self.account_id
        if self.property_id:
            params["property_id"] = self.property_id
        if self.view_id:
            params["view_id"] = self.view_id
        if self.profile_id:
            params["profile_id"] = self.profile_id
            
        return params
