"""
Current user endpoint
"""

from fastapi import APIRouter, Depends, HTTPException, status, Header
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.core.auth import get_current_user
from app.schemas.user import UserResponse

me_router = APIRouter()

@me_router.get("/me", response_model=UserResponse)
async def get_current_user_info(
    current_user = Depends(get_current_user)
):
    """Get current user information"""
    return {
        "id": str(current_user.id),
        "email": current_user.email,
        "displayName": current_user.get_name() or current_user.email,
        "name": current_user.get_name() or current_user.email,
        "firstName": current_user.first_name,
        "lastName": current_user.last_name,
        "roles": current_user.get_roles(),
        "clientId": str(current_user.client_id) if current_user.client_id else None,
        "tenantId": str(current_user.tenant_id) if current_user.tenant_id else None,
        "status": current_user.status,
        "lastLoginAt": current_user.last_login_at.isoformat() if current_user.last_login_at else None,
        "createdAt": current_user.created_at.isoformat() if current_user.created_at else None,
        "updatedAt": current_user.updated_at.isoformat() if current_user.updated_at else None,
    }
