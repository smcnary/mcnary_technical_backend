#!/usr/bin/env node

/**
 * Test script to verify Sales Consultant and Admin access controls
 * 
 * This script tests the authentication and role-based access for:
 * 1. Admin user (should have full access)
 * 2. Sales Consultant user (should only have CRM access)
 */

const API_BASE_URL = 'http://localhost:8000/api/v1';

// Test users created via the backend command
const TEST_USERS = {
  admin: {
    email: 'admin@test.com',
    password: 'password123',
    role: 'ROLE_SYSTEM_ADMIN',
    expectedAccess: ['admin', 'crm']
  },
  salesConsultant: {
    email: 'sales@test.com', 
    password: 'password123',
    role: 'ROLE_SALES_CONSULTANT',
    expectedAccess: ['crm']
  }
};

async function testLogin(user) {
  console.log(`\n🔐 Testing login for ${user.email} (${user.role})`);
  
  try {
    const response = await fetch(`${API_BASE_URL}/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: user.email,
        password: user.password
      })
    });

    if (!response.ok) {
      throw new Error(`Login failed: ${response.status} ${response.statusText}`);
    }

    const data = await response.json();
    console.log(`✅ Login successful for ${user.email}`);
    console.log(`   Token: ${data.token.substring(0, 20)}...`);
    console.log(`   User roles: ${data.user.roles.join(', ')}`);
    
    return data.token;
  } catch (error) {
    console.error(`❌ Login failed for ${user.email}:`, error.message);
    return null;
  }
}

async function testAccess(token, user, path) {
  console.log(`\n🔍 Testing access to ${path} for ${user.email}`);
  
  try {
    const response = await fetch(`http://localhost:3002${path}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Cookie': `auth=${token}; role=${user.role}`
      }
    });

    const hasAccess = response.ok;
    const status = response.status;
    
    if (hasAccess) {
      console.log(`✅ Access granted to ${path} (${status})`);
    } else {
      console.log(`❌ Access denied to ${path} (${status})`);
    }
    
    return hasAccess;
  } catch (error) {
    console.error(`❌ Error testing access to ${path}:`, error.message);
    return false;
  }
}

async function runTests() {
  console.log('🚀 Starting access control tests...\n');
  
  for (const [userType, user] of Object.entries(TEST_USERS)) {
    console.log(`\n${'='.repeat(50)}`);
    console.log(`Testing ${userType.toUpperCase()} user`);
    console.log(`${'='.repeat(50)}`);
    
    // Test login
    const token = await testLogin(user);
    if (!token) {
      console.log(`❌ Skipping tests for ${user.email} due to login failure`);
      continue;
    }
    
    // Test different paths
    const testPaths = [
      '/admin',           // Main admin dashboard
      '/admin/crm',       // CRM dashboard
      '/admin/users',     // User management
      '/admin/settings',  // Admin settings
      '/client'           // Client dashboard
    ];
    
    console.log(`\n📋 Testing access to different paths:`);
    
    for (const path of testPaths) {
      const hasAccess = await testAccess(token, user, path);
      
      // Check if access matches expectations
      const shouldHaveAccess = user.expectedAccess.some(access => 
        path.includes(access) || (access === 'admin' && path.startsWith('/admin'))
      );
      
      if (hasAccess === shouldHaveAccess) {
        console.log(`   ✅ Access control working correctly`);
      } else {
        console.log(`   ⚠️  Access control mismatch - expected ${shouldHaveAccess}, got ${hasAccess}`);
      }
    }
  }
  
  console.log(`\n${'='.repeat(50)}`);
  console.log('🏁 Tests completed!');
  console.log(`${'='.repeat(50)}`);
  
  console.log('\n📝 Manual Testing Instructions:');
  console.log('1. Open http://localhost:3002/login');
  console.log('2. Test Admin user:');
  console.log('   - Email: admin@test.com');
  console.log('   - Password: password123');
  console.log('   - Should have access to all admin sections');
  console.log('3. Test Sales Consultant user:');
  console.log('   - Email: sales@test.com');
  console.log('   - Password: password123');
  console.log('   - Should only have access to CRM section');
  console.log('   - Should be redirected away from other admin sections');
}

// Run the tests
runTests().catch(console.error);
