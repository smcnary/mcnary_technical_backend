'use client';

import React, { useState, useEffect } from 'react';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '../ui/dialog';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { 
  Phone, 
  PhoneOff, 
  User,
  Building,
  Clock,
  PhoneCall
} from 'lucide-react';
import { twilioApiService } from '../../services/twilioApi';

interface Lead {
  id: string;
  fullName: string;
  phone?: string;
  firm?: string;
  status: string;
}

interface CallModalProps {
  lead: Lead | null;
  isOpen: boolean;
  onClose: () => void;
  onHangup: () => void;
}

const getStatusLabel = (status: string) => {
  const statusMap: Record<string, string> = {
    'new_lead': 'New Lead',
    'contacted': 'Contacted',
    'interview_scheduled': 'Interview Scheduled',
    'interview_completed': 'Interview Completed',
    'application_received': 'Application Received',
    'audit_in_progress': 'Audit In Progress',
    'audit_complete': 'Audit Complete',
    'enrolled': 'Enrolled',
    'closed': 'Closed'
  };
  return statusMap[status] || status;
};

const getStatusBadgeVariant = (status: string) => {
  switch (status) {
    case 'new_lead':
      return 'default';
    case 'contacted':
      return 'secondary';
    case 'interview_scheduled':
      return 'outline';
    case 'interview_completed':
      return 'secondary';
    case 'application_received':
      return 'outline';
    case 'audit_in_progress':
      return 'secondary';
    case 'audit_complete':
      return 'outline';
    case 'enrolled':
      return 'default';
    case 'closed':
      return 'destructive';
    default:
      return 'secondary';
  }
};

export default function CallModal({ lead, isOpen, onClose, onHangup }: CallModalProps) {
  const [callDuration, setCallDuration] = useState(0);
  const [isConnected, setIsConnected] = useState(false);
  const [callStartTime, setCallStartTime] = useState<Date | null>(null);
  const [isInitiating, setIsInitiating] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [twilioFromNumber, setTwilioFromNumber] = useState<string | null>(null);

  useEffect(() => {
    let interval: NodeJS.Timeout;
    
    if (isConnected && callStartTime) {
      interval = setInterval(() => {
        const now = new Date();
        const duration = Math.floor((now.getTime() - callStartTime.getTime()) / 1000);
        setCallDuration(duration);
      }, 1000);
    }

    return () => {
      if (interval) {
        clearInterval(interval);
      }
    };
  }, [isConnected, callStartTime]);

  // Load Twilio integrations to get a phone number to call from
  useEffect(() => {
    const loadTwilioNumber = async () => {
      try {
        const integrations = await twilioApiService.getIntegrations();
        // Find the first active or default integration
        const activeIntegration = integrations.find(i => i.isDefault || i.status === 'active');
        if (activeIntegration) {
          setTwilioFromNumber(activeIntegration.phoneNumber);
        }
      } catch (err) {
        console.error('Failed to load Twilio integrations:', err);
      }
    };

    if (isOpen) {
      loadTwilioNumber();
    }
  }, [isOpen]);

  useEffect(() => {
    if (isOpen && lead?.phone && twilioFromNumber && !isConnected && !isInitiating) {
      // Automatically initiate call when modal opens
      initiateCall();
    }
    
    if (!isOpen) {
      // Reset call state when modal closes
      setIsConnected(false);
      setCallDuration(0);
      setCallStartTime(null);
      setIsInitiating(false);
      setError(null);
    }
  }, [isOpen, twilioFromNumber]);

  const handleHangup = () => {
    setIsConnected(false);
    setCallDuration(0);
    setCallStartTime(null);
    onHangup();
  };

  const formatDuration = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  const initiateCall = async () => {
    if (!lead?.phone || !twilioFromNumber) {
      setError('Missing required information to place call');
      return;
    }
    
    setIsInitiating(true);
    setError(null);
    
    try {
      console.log(`Initiating Twilio call to ${lead.fullName} at ${lead.phone} from ${twilioFromNumber}`);
      
      // Make the actual Twilio call
      await twilioApiService.makeCall({
        fromNumber: twilioFromNumber,
        toNumber: lead.phone,
      });
      
      // Mark as connected after successful API call
      setIsConnected(true);
      setCallStartTime(new Date());
      setIsInitiating(false);
    } catch (error) {
      console.error('Failed to initiate call:', error);
      setError(error instanceof Error ? error.message : 'Failed to initiate call');
      setIsInitiating(false);
    }
  };

  if (!lead) return null;

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3">
            <PhoneCall className="h-6 w-6 text-green-600" />
            Calling {lead.fullName}
            <Badge variant={getStatusBadgeVariant(lead.status)} className="ml-auto">
              {getStatusLabel(lead.status)}
            </Badge>
          </DialogTitle>
        </DialogHeader>

        <div className="py-6">
          {/* Contact Info */}
          <Card className="mb-4">
            <CardHeader className="pb-3">
              <CardTitle className="text-lg flex items-center gap-2">
                <User className="h-5 w-5" />
                Contact Information
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              {lead.firm && (
                <div className="flex items-center gap-3">
                  <Building className="h-4 w-4 text-gray-500" />
                  <span className="text-sm">{lead.firm}</span>
                </div>
              )}
              <div className="flex items-center gap-3">
                <Phone className="h-4 w-4 text-gray-500" />
                <span className="text-sm font-mono">{lead.phone}</span>
              </div>
            </CardContent>
          </Card>

          {/* Error Message */}
          {error && (
            <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <p className="text-sm text-red-600">{error}</p>
            </div>
          )}

          {/* Twilio Number Info */}
          {twilioFromNumber && (
            <div className="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
              <p className="text-sm text-blue-600">
                Calling from Twilio: {twilioFromNumber}
              </p>
            </div>
          )}

          {/* Call Status */}
          <div className="text-center py-6">
            {error ? (
              <div className="space-y-4">
                <div className="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                  <PhoneOff className="h-8 w-8 text-red-600" />
                </div>
                <div>
                  <h3 className="text-lg font-medium text-red-600">Call Failed</h3>
                  <p className="text-sm text-gray-600">Unable to connect</p>
                </div>
              </div>
            ) : !isConnected ? (
              <div className="space-y-4">
                <div className="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
                  <Phone className="h-8 w-8 text-blue-600 animate-pulse" />
                </div>
                <div>
                  <h3 className="text-lg font-medium">Connecting...</h3>
                  <p className="text-sm text-gray-600">Calling {lead.fullName}</p>
                </div>
              </div>
            ) : (
              <div className="space-y-4">
                <div className="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                  <Phone className="h-8 w-8 text-green-600" />
                </div>
                <div>
                  <h3 className="text-lg font-medium text-green-600">Connected</h3>
                  <p className="text-sm text-gray-600">Call duration: {formatDuration(callDuration)}</p>
                </div>
              </div>
            )}
          </div>

          {/* Call Controls */}
          <div className="flex justify-center gap-4">
            {error ? (
              <Button 
                onClick={() => {
                  setError(null);
                  initiateCall();
                }}
                className="bg-blue-600 hover:bg-blue-700"
              >
                <Phone className="h-4 w-4 mr-2" />
                Retry Call
              </Button>
            ) : !isConnected ? (
              <Button 
                className="bg-green-600 hover:bg-green-700"
                disabled
              >
                <Phone className="h-4 w-4 mr-2" />
                {isInitiating ? 'Initiating Call...' : 'Connecting...'}
              </Button>
            ) : (
              <Button 
                onClick={handleHangup}
                variant="destructive"
                className="bg-red-600 hover:bg-red-700"
              >
                <PhoneOff className="h-4 w-4 mr-2" />
                Hang Up
              </Button>
            )}
          </div>

          {/* Call Notes */}
          <Card className="mt-6">
            <CardHeader>
              <CardTitle className="text-sm">Call Notes</CardTitle>
            </CardHeader>
            <CardContent>
              <textarea 
                className="w-full h-20 p-2 border rounded-md text-sm resize-none"
                placeholder="Add notes about this call..."
              />
            </CardContent>
          </Card>
        </div>
      </DialogContent>
    </Dialog>
  );
}
