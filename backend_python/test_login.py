"""
Test login endpoint directly
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

def test_login():
    """Test login endpoint"""
    response = client.post(
        "/api/v1/auth/login",
        json={"email": "smcnary@live.com", "password": "TulsaSEO122"}
    )
    print(f"Status: {response.status_code}")
    print(f"Response: {response.text}")
    return response

if __name__ == "__main__":
    test_login()
