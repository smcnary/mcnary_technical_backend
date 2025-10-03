"""
Test script for Phase 1-2 implementation
"""

import requests
import json

# Configuration
BASE_URL = "http://localhost:8000"
ADMIN_EMAIL = "smcnary@live.com"
ADMIN_PASSWORD = "TulsaSEO122"

def test_health_check():
    """Test health check endpoint"""
    print("Testing health check...")
    response = requests.get(f"{BASE_URL}/health")
    print(f"Status: {response.status_code}")
    print(f"Response: {response.json()}")
    return response.status_code == 200

def test_login():
    """Test login endpoint"""
    print("\nTesting login...")
    login_data = {
        "email": ADMIN_EMAIL,
        "password": ADMIN_PASSWORD
    }
    response = requests.post(f"{BASE_URL}/api/v1/auth/login", json=login_data)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        token_data = response.json()
        print(f"Token received: {token_data['access_token'][:50]}...")
        return token_data['access_token']
    else:
        print(f"Error: {response.text}")
        return None

def test_get_current_user(token):
    """Test get current user endpoint"""
    print("\nTesting get current user...")
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(f"{BASE_URL}/api/v1/auth/me", headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        user_data = response.json()
        print(f"User: {user_data['email']} ({user_data['role']})")
        return user_data
    else:
        print(f"Error: {response.text}")
        return None

def test_get_users(token):
    """Test get users endpoint"""
    print("\nTesting get users...")
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(f"{BASE_URL}/api/v1/users/", headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        users = response.json()
        print(f"Found {len(users)} users")
        return users
    else:
        print(f"Error: {response.text}")
        return None

def test_get_agencies(token):
    """Test get agencies endpoint"""
    print("\nTesting get agencies...")
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(f"{BASE_URL}/api/v1/agencies/", headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        agencies = response.json()
        print(f"Found {len(agencies)} agencies")
        return agencies
    else:
        print(f"Error: {response.text}")
        return None

def test_get_clients(token):
    """Test get clients endpoint"""
    print("\nTesting get clients...")
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(f"{BASE_URL}/api/v1/clients/", headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        clients = response.json()
        print(f"Found {len(clients)} clients")
        return clients
    else:
        print(f"Error: {response.text}")
        return None

def test_get_leads(token):
    """Test get leads endpoint"""
    print("\nTesting get leads...")
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(f"{BASE_URL}/api/v1/leads/", headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        leads = response.json()
        print(f"Found {len(leads)} leads")
        return leads
    else:
        print(f"Error: {response.text}")
        return None

def test_create_lead(token, user_data):
    """Test create lead endpoint"""
    print("\nTesting create lead...")
    headers = {"Authorization": f"Bearer {token}"}
    
    # Get agency ID from user data
    agency_id = user_data.get('agency_id')
    if not agency_id:
        print("No agency ID found in user data")
        return None
    
    lead_data = {
        "full_name": "Test Lead",
        "email": "test@example.com",
        "phone": "555-1234",
        "firm": "Test Firm",
        "website": "https://testfirm.com",
        "city": "Tulsa",
        "state": "OK",
        "zip_code": "74135",
        "practice_areas": ["Personal Injury"],
        "status": "new_lead",
        "client_id": "00000000-0000-0000-0000-000000000000"  # Placeholder UUID
    }
    
    response = requests.post(f"{BASE_URL}/api/v1/leads/", json=lead_data, headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        lead = response.json()
        print(f"Lead created: {lead['full_name']} ({lead['email']})")
        return lead
    else:
        print(f"Error: {response.text}")
        return None

def main():
    """Run all tests"""
    print("=== Phase 1-2 Implementation Test ===")
    
    # Test health check
    if not test_health_check():
        print("Health check failed. Make sure the server is running.")
        return
    
    # Test login
    token = test_login()
    if not token:
        print("Login failed. Make sure the database is set up correctly.")
        return
    
    # Test get current user
    user_data = test_get_current_user(token)
    if not user_data:
        print("Get current user failed.")
        return
    
    # Test get users
    test_get_users(token)
    
    # Test get agencies
    test_get_agencies(token)
    
    # Test get clients
    test_get_clients(token)
    
    # Test get leads
    test_get_leads(token)
    
    # Test create lead (this might fail if no clients exist)
    test_create_lead(token, user_data)
    
    print("\n=== Test Complete ===")

if __name__ == "__main__":
    main()
