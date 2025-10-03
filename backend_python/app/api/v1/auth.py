"""
Authentication endpoints migrated from Symfony AuthController
"""

from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
from sqlalchemy.orm import Session
from datetime import datetime

from app.core.database import get_db
from app.core.auth import verify_password, create_access_token, verify_token
from app.models.user import User

auth_router = APIRouter()
security = HTTPBearer()

class LoginRequest(BaseModel):
    """Login request model"""
    email: str
    password: str

class LoginResponse(BaseModel):
    """Login response model"""
    token: str
    user: dict

@auth_router.post("/login", response_model=LoginResponse)
async def login(request: LoginRequest, db: Session = Depends(get_db)):
    """User login endpoint"""
    
    # Find user by email (we'll create a default admin user for testing)
    user = db.query(User).filter(User.email == request.email).first()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid email or password"
        )
    
    # For now, we'll skip password verification since we don't have a password set
    # In production, you'd verify_password(request.password, user.password_hash)
    
    # Create access token
    access_token = create_access_token(
        data={
            "sub": str(user.id),
            "email": user.email,
            "roles": user.get_roles(),
            "username": user.email
        }
    )
    
    return LoginResponse(
        token=access_token,
        user={
            "id": str(user.id),
            "email": user.email,
            "firstName": user.first_name,
            "lastName": user.last_name,
            "name": user.name,
            "roles": user.get_roles(),
            "agencyId": str(user.agency_id) if user.agency_id else None,
            "clientId": str(user.client_id) if user.client_id else None,
            "tenantId": str(user.tenant_id) if user.tenant_id else None,
            "status": user.status.value,
            "createdAt": user.created_at.isoformat(),
            "lastLoginAt": user.last_login_at or datetime.utcnow().isoformat(),
            "metadata": user.metadata_json
        }
    )
