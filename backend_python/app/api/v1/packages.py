"""
Packages endpoints
"""

from fastapi import APIRouter, Depends
from app.core.auth import get_current_user
from app.models.user import User

packages_router = APIRouter()

@packages_router.get("/packages")
async def get_packages(
    current_user: User = Depends(get_current_user)
):
    """Get service packages"""
    # Mock data for now - replace with actual database queries
    packages = [
        {
            "id": "1",
            "name": "Starter SEO Package",
            "description": "Basic SEO services for small businesses",
            "price": 1500,
            "billingCycle": "monthly",
            "features": [
                "Keyword research",
                "On-page optimization",
                "Monthly reporting",
                "Google Analytics setup"
            ],
            "isPopular": False,
            "sortOrder": 1,
            "clientId": current_user.client_id or "default",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "2",
            "name": "Professional SEO Package",
            "description": "Comprehensive SEO services for growing businesses",
            "price": 3000,
            "billingCycle": "monthly",
            "features": [
                "Advanced keyword research",
                "Technical SEO audit",
                "Content optimization",
                "Link building",
                "Monthly reporting",
                "Priority support"
            ],
            "isPopular": True,
            "sortOrder": 2,
            "clientId": current_user.client_id or "default",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "3",
            "name": "Enterprise SEO Package",
            "description": "Full-service SEO for large organizations",
            "price": 5000,
            "billingCycle": "monthly",
            "features": [
                "Comprehensive SEO strategy",
                "Advanced technical optimization",
                "Content marketing",
                "Link building campaign",
                "Competitor analysis",
                "Custom reporting",
                "Dedicated account manager",
                "24/7 support"
            ],
            "isPopular": False,
            "sortOrder": 3,
            "clientId": current_user.client_id or "default",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        }
    ]
    
    return {
        "data": packages,
        "pagination": {
            "page": 1,
            "per_page": 10,
            "total": len(packages),
            "pages": 1
        }
    }
