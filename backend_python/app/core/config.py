"""
Application configuration
"""

from pydantic_settings import BaseSettings
from typing import Optional, List

class Settings(BaseSettings):
    """Application settings"""
    
    # Database settings
    database_url: str = "postgresql://smcnary@localhost:5432/tulsa_seo"
    
    # JWT settings
    secret_key: str = "your-secret-key-change-in-production"
    algorithm: str = "HS256"
    access_token_expire_minutes: int = 1440  # 24 hours
    
    # API settings
    api_v1_str: str = "/api/v1"
    project_name: str = "Technical Backend API"
    
    # CORS settings
    cors_origins: List[str] = ["http://localhost:3000", "http://127.0.0.1:3000"]
    
    # Redis settings (for Celery)
    redis_url: str = "redis://localhost:6379/0"
    
    # Environment
    environment: str = "development"
    debug: bool = True
    
    # Multi-tenancy settings
    enable_multi_tenancy: bool = True
    
    # Default admin user
    default_admin_email: str = "smcnary@live.com"
    default_admin_password: str = "TulsaSEO122"
    
    class Config:
        env_file = ".env"

settings = Settings()
