"""
Authentication endpoints
"""

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from datetime import timedelta

from app.core.database import get_db
from app.core.auth import authenticate_user, create_access_token, get_current_user, update_last_login
from app.schemas.user import UserLogin, Token, UserResponse
from app.models.user import User
from app.core.config import settings

auth_router = APIRouter()

@auth_router.post("/login")
async def login(user_credentials: UserLogin, db: Session = Depends(get_db)):
    """Login endpoint"""
    user = authenticate_user(db, user_credentials.email, user_credentials.password)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect email or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    # Update last login
    update_last_login(db, user)
    
    access_token_expires = timedelta(minutes=settings.access_token_expire_minutes)
    access_token = create_access_token(
        data={"sub": user.email}, expires_delta=access_token_expires
    )
    
    # Return the format expected by the frontend
    return {
        "token": access_token,
        "user": {
            "id": str(user.id),
            "email": user.email,
            "displayName": user.get_name() or user.email,
            "name": user.get_name() or user.email,
            "firstName": user.first_name,
            "lastName": user.last_name,
            "roles": user.get_roles(),
            "clientId": str(user.client_id) if user.client_id else None,
            "tenantId": str(user.tenant_id) if user.tenant_id else None,
            "status": user.status,
            "lastLoginAt": user.last_login_at.isoformat() if user.last_login_at else None,
            "createdAt": user.created_at.isoformat() if user.created_at else None,
            "updatedAt": user.updated_at.isoformat() if user.updated_at else None,
        }
    }

@auth_router.get("/me", response_model=UserResponse)
async def get_current_user_info(current_user: User = Depends(get_current_user)):
    """Get current user information"""
    return current_user

@auth_router.post("/logout")
async def logout(current_user: User = Depends(get_current_user)):
    """Logout endpoint - invalidate token on server side if needed"""
    # In a stateless JWT system, logout is typically handled client-side
    # by removing the token. This endpoint can be used for server-side
    # token blacklisting if implemented in the future.
    return {"message": "Logged out successfully"}