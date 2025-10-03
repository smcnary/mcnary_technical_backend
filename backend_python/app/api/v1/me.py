"""
Current user endpoint
"""

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.core.auth import verify_token
from app.models.user import User

me_router = APIRouter()

def get_current_user(token: str = None, db: Session = None):
    """Get current user from token"""
    if not token or not db:
        raise HTTPException(
            STATUS_CODE=status.HTTP_401_UNAUTHORIZED,
            detail="Authentication required"
        )
    
    payload = verify_token(token)
    user_id = payload.get("sub")
    
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="User not found"
        )
    
    return user

@me_router.get("/me")
async def get_current_user_info(
    authorization: str = Depends(lambda: None), 
    db: Session = Depends(get_db)
):
    """Get current user information"""
    
    # This is a simplified version - in production you'd extract the token from headers
    raise HTTPException(
        status_code=status.HTTP_501_NOT_IMPLEMENTED,
        detail="This endpoint needs proper token extraction from headers"
    )
