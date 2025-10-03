"""
User management endpoints
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_active_user, require_system_admin
from app.schemas.user import UserCreate, UserUpdate, UserResponse
from app.models.user import User
from app.core.auth import get_password_hash

users_router = APIRouter()

@users_router.get("/", response_model=List[UserResponse])
async def get_users(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_system_admin)
):
    """Get all users (system admin only)"""
    users = db.query(User).offset(skip).limit(limit).all()
    return users

@users_router.get("/{user_id}", response_model=UserResponse)
async def get_user(
    user_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get a specific user"""
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    
    # Check permissions - users can only see their own profile unless they're system admin
    if not current_user.is_system_admin() and user.id != current_user.id:
        raise HTTPException(status_code=403, detail="Not enough permissions")
    
    return user

@users_router.post("/", response_model=UserResponse)
async def create_user(
    user_data: UserCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_system_admin)
):
    """Create a new user (system admin only)"""
    # Check if user already exists
    existing_user = db.query(User).filter(User.email == user_data.email).first()
    if existing_user:
        raise HTTPException(status_code=400, detail="Email already registered")
    
    # Hash password
    hashed_password = get_password_hash(user_data.password)
    
    # Create user
    user = User(
        email=user_data.email,
        password_hash=hashed_password,
        first_name=user_data.first_name,
        last_name=user_data.last_name,
        status=user_data.status,
        role=user_data.role,
        organization_id=user_data.organization_id,
        agency_id=user_data.agency_id,
        tenant_id=user_data.tenant_id,
        client_id=user_data.client_id
    )
    
    db.add(user)
    db.commit()
    db.refresh(user)
    
    return user

@users_router.put("/{user_id}", response_model=UserResponse)
async def update_user(
    user_id: str,
    user_data: UserUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Update a user"""
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    
    # Check permissions - users can only update their own profile unless they're system admin
    if not current_user.is_system_admin() and user.id != current_user.id:
        raise HTTPException(status_code=403, detail="Not enough permissions")
    
    # Update fields
    update_data = user_data.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(user, field, value)
    
    db.commit()
    db.refresh(user)
    
    return user

@users_router.delete("/{user_id}")
async def delete_user(
    user_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_system_admin)
):
    """Delete a user (system admin only)"""
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    
    db.delete(user)
    db.commit()
    
    return {"message": "User deleted successfully"}
