import { test, expect } from '@playwright/test';

test.describe('Twilio Integration Tests', () => {
  const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';
  const TARGET_PHONE_NUMBER = '+17862133333';
  const TWILIO_PHONE_NUMBER = '+19187277458';

  test.beforeEach(async ({ request }) => {
    // Test Twilio connection before each test
    const connectionTest = await request.get(`${API_BASE_URL}/api/v1/twilio/test-connection`);
    
    if (!connectionTest.ok()) {
      test.skip(true, 'Twilio connection failed - skipping tests');
    }
  });

  test('should test Twilio connection', async ({ request }) => {
    const response = await request.get(`${API_BASE_URL}/api/v1/twilio/test-connection`);
    
    expect(response.ok()).toBeTruthy();
    
    const data = await response.json();
    expect(data.success).toBeTruthy();
    expect(data.data.accountSid).toBeDefined();
    expect(data.data.status).toBe('active');
  });

  test('should get phone information', async ({ request }) => {
    const response = await request.get(`${API_BASE_URL}/api/v1/twilio/phone-info`);
    
    expect(response.ok()).toBeTruthy();
    
    const data = await response.json();
    expect(data.success).toBeTruthy();
    expect(data.data.twilio_phone_number).toBe(TWILIO_PHONE_NUMBER);
    expect(data.data.target_phone_number).toBe(TARGET_PHONE_NUMBER);
    expect(data.data.target_phone_formatted).toBe('786-213-3333');
  });

  test('should make a call to target number 786-213-3333', async ({ request }) => {
    const response = await request.post(`${API_BASE_URL}/api/v1/twilio/call-target`, {
      data: {
        twiml: '<Response><Say>Hello! This is a test call from CounselRank Legal Services. Thank you for your interest in our services.</Say></Response>'
      }
    });
    
    expect(response.ok()).toBeTruthy();
    
    const data = await response.json();
    expect(data.success).toBeTruthy();
    expect(data.message).toContain('Call initiated successfully to 786-213-3333');
    expect(data.data.callSid).toBeDefined();
    expect(data.data.from).toBe(TWILIO_PHONE_NUMBER);
    expect(data.data.to).toBe(TARGET_PHONE_NUMBER);
    expect(data.data.direction).toBe('outbound-api');
    
    // Wait a moment for the call to be processed
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Verify call details can be retrieved
    const callDetailsResponse = await request.get(`${API_BASE_URL}/api/v1/twilio/call-details/${data.data.callSid}`);
    expect(callDetailsResponse.ok()).toBeTruthy();
    
    const callDetails = await callDetailsResponse.json();
    expect(callDetails.success).toBeTruthy();
    expect(callDetails.data.sid).toBe(data.data.callSid);
  });

  test('should send SMS to target number 786-213-3333', async ({ request }) => {
    const testMessage = 'Hello! This is a test SMS from CounselRank Legal Services. Thank you for your interest in our services.';
    
    const response = await request.post(`${API_BASE_URL}/api/v1/twilio/sms-target`, {
      data: {
        message: testMessage
      }
    });
    
    expect(response.ok()).toBeTruthy();
    
    const data = await response.json();
    expect(data.success).toBeTruthy();
    expect(data.message).toContain('SMS sent successfully to 786-213-3333');
    expect(data.data.messageSid).toBeDefined();
    expect(data.data.from).toBe(TWILIO_PHONE_NUMBER);
    expect(data.data.to).toBe(TARGET_PHONE_NUMBER);
    expect(data.data.body).toBe(testMessage);
    
    // Wait a moment for the SMS to be processed
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Verify message details can be retrieved
    const messageDetailsResponse = await request.get(`${API_BASE_URL}/api/v1/twilio/message-details/${data.data.messageSid}`);
    expect(messageDetailsResponse.ok()).toBeTruthy();
    
    const messageDetails = await messageDetailsResponse.json();
    expect(messageDetails.success).toBeTruthy();
    expect(messageDetails.data.sid).toBe(data.data.messageSid);
  });

  test('should validate SMS message length', async ({ request }) => {
    // Test with empty message
    const emptyResponse = await request.post(`${API_BASE_URL}/api/v1/twilio/sms-target`, {
      data: {
        message: ''
      }
    });
    
    expect(emptyResponse.status()).toBe(400);
    
    const emptyData = await emptyResponse.json();
    expect(emptyData.success).toBeFalsy();
    expect(emptyData.errors).toContain('Message is required');
    
    // Test with message too long (over 1600 characters)
    const longMessage = 'A'.repeat(1601);
    const longResponse = await request.post(`${API_BASE_URL}/api/v1/twilio/sms-target`, {
      data: {
        message: longMessage
      }
    });
    
    expect(longResponse.status()).toBe(400);
    
    const longData = await longResponse.json();
    expect(longData.success).toBeFalsy();
    expect(longData.errors).toContain('Message is too long');
  });

  test('should handle invalid call SID', async ({ request }) => {
    const response = await request.get(`${API_BASE_URL}/api/v1/twilio/call-details/invalid_sid`);
    
    expect(response.status()).toBe(404);
    
    const data = await response.json();
    expect(data.success).toBeFalsy();
    expect(data.message).toBe('Call details not found');
  });

  test('should handle invalid message SID', async ({ request }) => {
    const response = await request.get(`${API_BASE_URL}/api/v1/twilio/message-details/invalid_sid`);
    
    expect(response.status()).toBe(404);
    
    const data = await response.json();
    expect(data.success).toBeFalsy();
    expect(data.message).toBe('Message details not found');
  });

  test('should make call with custom TwiML URL', async ({ request }) => {
    const response = await request.post(`${API_BASE_URL}/api/v1/twilio/call-target`, {
      data: {
        twiml_url: 'https://demo.twilio.com/docs/voice.xml'
      }
    });
    
    expect(response.ok()).toBeTruthy();
    
    const data = await response.json();
    expect(data.success).toBeTruthy();
    expect(data.data.callSid).toBeDefined();
    expect(data.data.from).toBe(TWILIO_PHONE_NUMBER);
    expect(data.data.to).toBe(TARGET_PHONE_NUMBER);
  });

  test('should handle call to non-existent client', async ({ request }) => {
    const response = await request.post(`${API_BASE_URL}/api/v1/twilio/call-client/non-existent-id`, {
      data: {
        twiml: '<Response><Say>Hello!</Say></Response>'
      }
    });
    
    expect(response.status()).toBe(404);
    
    const data = await response.json();
    expect(data.success).toBeFalsy();
    expect(data.message).toBe('Client not found');
  });

  test('should handle SMS to non-existent client', async ({ request }) => {
    const response = await request.post(`${API_BASE_URL}/api/v1/twilio/sms-client/non-existent-id`, {
      data: {
        message: 'Test message'
      }
    });
    
    expect(response.status()).toBe(404);
    
    const data = await response.json();
    expect(data.success).toBeFalsy();
    expect(data.message).toBe('Client not found');
  });
});

test.describe('Twilio Integration - Phone Number Formatting', () => {
  const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:8000';

  test('should handle different phone number formats', async ({ request }) => {
    // Test various phone number formats that should all resolve to +17862133333
    const phoneFormats = [
      '786-213-3333',
      '(786) 213-3333',
      '786.213.3333',
      '7862133333',
      '17862133333',
      '+17862133333'
    ];

    for (const phoneFormat of phoneFormats) {
      const response = await request.post(`${API_BASE_URL}/api/v1/twilio/call-target`, {
        data: {
          twiml: `<Response><Say>Test call to ${phoneFormat}</Say></Response>`
        }
      });
      
      expect(response.ok()).toBeTruthy();
      
      const data = await response.json();
      expect(data.success).toBeTruthy();
      expect(data.data.to).toBe('+17862133333');
      
      // Wait between calls to avoid rate limiting
      await new Promise(resolve => setTimeout(resolve, 1000));
    }
  });
});
