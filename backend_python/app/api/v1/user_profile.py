"""
User profile endpoints
"""

from fastapi import APIRouter, Depends
from app.core.auth import get_current_user
from app.models.user import User

user_profile_router = APIRouter()

@user_profile_router.get("/user-profile/greeting")
async def get_user_profile_greeting(
    current_user: User = Depends(get_current_user)
):
    """Get user profile greeting"""
    return {
        "message": f"Hello, {current_user.get_name() or current_user.email}!",
        "user": {
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
    }
