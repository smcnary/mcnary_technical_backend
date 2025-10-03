"""
FAQs endpoints
"""

from fastapi import APIRouter, Depends
from app.core.auth import get_current_user
from app.models.user import User

faqs_router = APIRouter()

@faqs_router.get("/faqs")
async def get_faqs(
    current_user: User = Depends(get_current_user)
):
    """Get frequently asked questions"""
    # Mock data for now - replace with actual database queries
    faqs = [
        {
            "id": "1",
            "question": "How long does it take to see SEO results?",
            "answer": "SEO is a long-term strategy. You can typically expect to see initial improvements within 3-6 months, with significant results after 6-12 months of consistent optimization.",
            "category": "general",
            "sortOrder": 1,
            "isActive": True,
            "sort": 1,
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "2",
            "question": "What's included in your SEO packages?",
            "answer": "Our SEO packages include keyword research, on-page optimization, technical SEO audits, content optimization, link building, and monthly reporting. Higher-tier packages include additional services like content marketing and dedicated account management.",
            "category": "services",
            "sortOrder": 2,
            "isActive": True,
            "sort": 2,
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "3",
            "question": "Do you work with local businesses?",
            "answer": "Yes! We specialize in local SEO for businesses of all sizes. Our local SEO services include Google Business Profile optimization, local citation building, and location-based keyword targeting.",
            "category": "local",
            "sortOrder": 3,
            "isActive": True,
            "sort": 3,
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        {
            "id": "4",
            "question": "How do you measure SEO success?",
            "answer": "We track key metrics including organic traffic growth, keyword rankings, conversion rates, and lead generation. We provide detailed monthly reports showing your progress and ROI.",
            "category": "reporting",
            "sortOrder": 4,
            "isActive": True,
            "sort": 4,
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        }
    ]
    
    return {
        "data": faqs,
        "pagination": {
            "page": 1,
            "per_page": 10,
            "total": len(faqs),
            "pages": 1
        }
    }
