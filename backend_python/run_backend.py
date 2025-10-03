#!/usr/bin/env python3
"""
Startup script for the Python FastAPI backend
"""

import os
import sys
import subprocess

def setup_environment():
    """Set up environment variables"""
    env_vars = {
        "DATABASE_URL": "postgresql://admin:admin@localhost:5432/technical_db",
        "SECRET_KEY": "technical-backend-secret-key-change-in-production",
        "ALGORITHM": "HS256",
        "ENVIRONMENT": "development",
        "DEBUG": "true"
    }
    
    for key, value in env_vars.items():
        os.environ[key] = value

def create_admin_user():
    """Create admin user before starting server"""
    try:
        print("Creating admin user...")
        os.system("python create_admin_user.py")
        print("Admin user created successfully")
    except Exception as e:
        print(f"Warning: Could not create admin user: {e}")

def main():
    """Main startup function"""
    print("Starting Python FastAPI Backend...")
    
    # Set up environment
    setup_environment()
    
    # Create admin user
    create_admin_user()
    
    # Start the server
    print("Starting FastAPI server on http://localhost:8000")
    os.system("uvicorn app.main:app --host 0.0.0.0 --port 8000 --reload")

if __name__ == "__main__":
    main()
