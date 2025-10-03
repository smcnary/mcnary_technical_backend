"""
Main FastAPI application entry point
Migrated from Symfony backend
"""

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
import os
from dotenv import load_dotenv

from app.api.v1.auth import auth_router
from app.api.v1.leads import leads_router
from app.api.v1.me import me_router
# Commented out for now to get basic backend running
# from app.api.v1.users import users_router
# from app.api.v1.clients import clients_router
# from app.api.v1.agencies import agencies_router
# from app.api.v1.seo import router as seo_router
# from app.api.v1.audits import router as audit_router
from app.core.config import settings
from app.core.database import engine
from app.models import Base

# Load environment variables
load_dotenv()

# Create database tables (only if database is available)
try:
    Base.metadata.create_all(bind=engine)
except Exception as e:
    print(f"Warning: Could not create database tables: {e}")
    print("Database connection will be attempted when endpoints are accessed")

app = FastAPI(
    title="Technical Backend API",
    description="FastAPI backend migrated from Symfony PHP",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000", "http://127.0.0.1:3000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Include API routers
app.include_router(auth_router, prefix="/api/v1/auth", tags=["Authentication"])
app.include_router(leads_router, prefix="/api/v1/leads", tags=["Leads"])
app.include_router(me_router, prefix="/api/v1", tags=["User Info"])
# Commented out for now to get basic backend running
# app.include_router(users_router, prefix="/api/v1/users", tags=["Users"])
# app.include_router(clients_router, prefix="/api/v1/clients", tags=["Clients"])
# app.include_router(agencies_router, prefix="/api/v1/agencies", tags=["Agencies"])
# app.include_router(seo_router, prefix="/api/v1/seo", tags=["SEO Tracking"])
# app.include_router(audit_router, prefix="/api/v1/audits", tags=["Audit Service"])

@app.get("/")
async def root():
    """Health check endpoint"""
    return {"message": "Technical Backend API - Python FastAPI", "status": "healthy"}

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "service": "backend", "version": "1.0.0"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
