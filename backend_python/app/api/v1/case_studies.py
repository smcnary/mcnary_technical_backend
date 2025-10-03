"""
Case studies endpoints
"""

from fastapi import APIRouter, Depends
from app.core.auth import get_current_user
from app.models.user import User

case_studies_router = APIRouter()

@case_studies_router.get("/case_studies")
async def get_case_studies(
    current_user: User = Depends(get_current_user)
):
    """Get case studies"""
    # Mock data for now - replace with actual database queries
    case_studies = [
        {
            "id": "1",
            "title": "Law Firm SEO Success Story",
            "slug": "law-firm-seo-success",
            "summary": "How we helped a local law firm increase their organic traffic by 300%",
            "metricsJson": {
                "traffic_increase": "300%",
                "leads_generated": "150",
                "ranking_improvements": "85%"
            },
            "heroImage": "/images/case-study-1.jpg",
            "practiceArea": "Legal",
            "isActive": True,
            "sort": 1,
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "2",
            "title": "Medical Practice Local SEO",
            "slug": "medical-practice-local-seo",
            "summary": "Local SEO strategy that brought in 200+ new patients",
            "metricsJson": {
                "new_patients": "200+",
                "local_rankings": "Top 3",
                "phone_calls": "500% increase"
            },
            "heroImage": "/images/case-study-2.jpg",
            "practiceArea": "Medical",
            "isActive": True,
            "sort": 2,
            "createdAt": "2024-02-01T00:00:00Z",
            "updatedAt": "2024-02-01T00:00:00Z"
        }
    ]
    
    return {
        "data": case_studies,
        "pagination": {
            "page": 1,
            "per_page": 10,
            "total": len(case_studies),
            "pages": 1
        }
    }
