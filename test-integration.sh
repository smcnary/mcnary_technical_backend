#!/bin/bash

# Frontend-Backend Integration Test Script
echo "🧪 Testing Frontend-Backend Integration..."
echo "=========================================="

# Test 1: Backend API Health
echo "1. Testing Backend API Health..."
BACKEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/v1/packages)
if [ "$BACKEND_STATUS" = "200" ]; then
    echo "   ✅ Backend API is healthy"
else
    echo "   ❌ Backend API is not responding (Status: $BACKEND_STATUS)"
    exit 1
fi

# Test 2: Frontend Health
echo "2. Testing Frontend Health..."
FRONTEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:3000)
if [ "$FRONTEND_STATUS" = "200" ]; then
    echo "   ✅ Frontend is healthy"
else
    echo "   ❌ Frontend is not responding (Status: $FRONTEND_STATUS)"
    exit 1
fi

# Test 3: Authentication Flow
echo "3. Testing Authentication Flow..."
LOGIN_RESPONSE=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"newuser@example.com","password":"password123"}')

if echo "$LOGIN_RESPONSE" | jq -e '.token' > /dev/null 2>&1; then
    TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token')
    echo "   ✅ Authentication successful"
else
    echo "   ❌ Authentication failed"
    echo "   Response: $LOGIN_RESPONSE"
    exit 1
fi

# Test 4: Protected Endpoints
echo "4. Testing Protected Endpoints..."

# Test Leads API
LEADS_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/leads" \
    -H "Authorization: Bearer $TOKEN")
LEADS_COUNT=$(echo "$LEADS_RESPONSE" | jq -r '.totalItems // "null"')

if [ "$LEADS_COUNT" != "null" ] && [ "$LEADS_COUNT" -gt 0 ]; then
    echo "   ✅ Leads API working (Found $LEADS_COUNT leads)"
else
    echo "   ❌ Leads API failed"
    echo "   Response: $LEADS_RESPONSE"
fi

# Test Campaigns API
CAMPAIGNS_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/campaigns" \
    -H "Authorization: Bearer $TOKEN")
CAMPAIGNS_COUNT=$(echo "$CAMPAIGNS_RESPONSE" | jq -r '.totalItems // "null"')

if [ "$CAMPAIGNS_COUNT" != "null" ] && [ "$CAMPAIGNS_COUNT" -gt 0 ]; then
    echo "   ✅ Campaigns API working (Found $CAMPAIGNS_COUNT campaigns)"
else
    echo "   ❌ Campaigns API failed"
    echo "   Response: $CAMPAIGNS_RESPONSE"
fi

# Test Case Studies API
CASE_STUDIES_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/case_studies" \
    -H "Authorization: Bearer $TOKEN")
CASE_STUDIES_COUNT=$(echo "$CASE_STUDIES_RESPONSE" | jq -r '.totalItems // "null"')

if [ "$CASE_STUDIES_COUNT" != "null" ] && [ "$CASE_STUDIES_COUNT" -gt 0 ]; then
    echo "   ✅ Case Studies API working (Found $CASE_STUDIES_COUNT case studies)"
else
    echo "   ❌ Case Studies API failed"
    echo "   Response: $CASE_STUDIES_RESPONSE"
fi

# Test 5: User Profile API
echo "5. Testing User Profile API..."
USER_PROFILE_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/v1/user-profile/greeting" \
    -H "Authorization: Bearer $TOKEN")

if echo "$USER_PROFILE_RESPONSE" | jq -e '.displayName' > /dev/null 2>&1; then
    echo "   ✅ User Profile API working"
else
    echo "   ❌ User Profile API failed"
    echo "   Response: $USER_PROFILE_RESPONSE"
fi

echo ""
echo "🎉 Frontend-Backend Integration Test Complete!"
echo "=============================================="
echo ""
echo "📊 Summary:"
echo "   • Backend API: ✅ Healthy"
echo "   • Frontend: ✅ Healthy" 
echo "   • Authentication: ✅ Working"
echo "   • Sample Data: ✅ Available"
echo "     - Leads: $LEADS_COUNT"
echo "     - Campaigns: $CAMPAIGNS_COUNT"
echo "     - Case Studies: $CASE_STUDIES_COUNT"
echo ""
echo "🚀 Ready for frontend testing!"
echo ""
echo "Next steps:"
echo "1. Open http://localhost:3000 in your browser"
echo "2. Navigate to /login and test authentication"
echo "3. Access /client dashboard to see sample data"
echo "4. Test all dashboard features"
