"""
Campaign endpoints
"""

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List, Optional
from app.core.database import get_db
from app.core.auth import get_current_user
from app.models.user import User

campaigns_router = APIRouter()

@campaigns_router.get("/campaigns")
async def get_campaigns(
    per_page: int = 10,
    page: int = 1,
    sort: str = "-createdAt",
    current_user: User = Depends(get_current_user)
):
    """Get campaigns for the current user"""
    # Mock data for now - replace with actual database queries
    campaigns = [
        {
            "id": "1",
            "name": "Local SEO Campaign",
            "description": "Local search optimization for law firms",
            "type": "local_seo",
            "status": "active",
            "clientId": current_user.client_id or "default",
            "startDate": "2024-01-01",
            "endDate": "2024-12-31",
            "budget": 5000,
            "goals": ["Increase local visibility", "Generate more leads"],
            "metrics": ["rankings", "traffic", "leads"],
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "2", 
            "name": "Content Marketing Campaign",
            "description": "Content creation and optimization",
            "type": "content",
            "status": "active",
            "clientId": current_user.client_id or "default",
            "startDate": "2024-02-01",
            "endDate": "2024-12-31",
            "budget": 3000,
            "goals": ["Increase organic traffic", "Build authority"],
            "metrics": ["traffic", "engagement", "conversions"],
            "createdAt": "2024-02-01T00:00:00Z",
            "updatedAt": "2024-02-01T00:00:00Z"
        }
    ]
    
    return {
        "data": campaigns,
        "pagination": {
            "page": page,
            "per_page": per_page,
            "total": len(campaigns),
            "pages": 1
        }
    }
