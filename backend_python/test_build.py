#!/usr/bin/env python3
"""
Build verification script for Python backend
"""

import sys
import os

def test_imports():
    """Test that all modules can be imported"""
    print("Testing imports...")
    
    try:
        from app.main import app
        print("✅ Main app imports successfully")
        
        from app.core.config import settings
        print("✅ Configuration imports successfully")
        
        from app.core.database import get_db
        print("✅ Database module imports successfully")
        
        from app.core.auth import create_access_token, verify_token
        print("✅ Auth module imports successfully")
        
        from app.models.user import User
        from app.models.lead import Lead
        from app.models.organization import Organization
        print("✅ Models import successfully")
        
        from app.api.v1.auth import auth_router
        from app.api.v1.leads import leads_router
        from app.api.v1.me import me_router
        print("✅ API routers import successfully")
        
        return True
        
    except Exception as e:
        print(f"❌ Import failed: {e}")
        return False

def test_fastapi_config():
    """Test FastAPI configuration"""
    print("\nTesting FastAPI configuration...")
    
    try:
        from app.main import app
        from fastapi.testclient import TestClient
        
        client = TestClient(app)
        
        # Test health endpoint
        response = client.get('/health')
        if response.status_code == 200:
            print("✅ Health endpoint works")
            print(f"   Response: {response.json()}")
        else:
            print(f"❌ Health endpoint failed: {response.status_code}")
            return False
            
        # Test root endpoint
        response = client.get('/')
        if response.status_code == 200:
            print("✅ Root endpoint works")
        else:
            print(f"❌ Root endpoint failed: {response.status_code}")
            return False
            
        return True
        
    except Exception as e:
        print(f"❌ FastAPI test failed: {e}")
        return False

def test_dependencies():
    """Test that all required dependencies are available"""
    print("\nTesting dependencies...")
    
    required_modules = [
        ('fastapi', 'fastapi'),
        ('uvicorn', 'uvicorn'),
        ('sqlalchemy', 'sqlalchemy'),
        ('psycopg2', 'psycopg2'),
        ('pydantic', 'pydantic'),
        ('python-jose', 'jose'),
        ('passlib', 'passlib'),
        ('python-dotenv', 'dotenv')
    ]
    
    missing_modules = []
    
    for package_name, module_name in required_modules:
        try:
            __import__(module_name)
            print(f"✅ {package_name}")
        except ImportError:
            print(f"❌ {package_name} - MISSING")
            missing_modules.append(package_name)
    
    if missing_modules:
        print(f"\n❌ Missing dependencies: {missing_modules}")
        return False
    
    print("✅ All dependencies available")
    return True

def main():
    """Main test function"""
    print("Python Backend Build Verification")
    print("=" * 40)
    
    tests = [
        test_dependencies,
        test_imports,
        test_fastapi_config
    ]
    
    all_passed = True
    
    for test in tests:
        if not test():
            all_passed = False
    
    print("\n" + "=" * 40)
    if all_passed:
        print("✅ All tests passed! Python backend is building correctly.")
        return 0
    else:
        print("❌ Some tests failed. Check the output above.")
        return 1

if __name__ == "__main__":
    sys.exit(main())
