"""
Application configuration
"""

from pydantic_settings import BaseSettings
from typing import Optional

class Settings(BaseSettings):
    """Application settings"""
    
    # Database settings
    database_url: str = "postgresql://admin:admin@localhost:5432/technical_db"
    
    # JWT settings
    secret_key: str = "your-secret-key-change-in-production"
    algorithm: str = "HS256"
    access_token_expire_minutes: int = 1440  # 24 hours
    
    # API settings
    api_v1_str: str = "/api/v1"
    project_name: str = "Technical Backend API"
    
    # Environment
    environment: str = "development"
    debug: bool = True
    
    class Config:
        env_file = ".env"

settings = Settings()
