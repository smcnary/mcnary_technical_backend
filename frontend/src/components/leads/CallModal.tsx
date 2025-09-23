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

  useEffect(() => {
    if (isOpen && lead?.phone) {
      // Simulate call initiation
      setTimeout(() => {
        setIsConnected(true);
        setCallStartTime(new Date());
      }, 2000);
    }
    
    if (!isOpen) {
      // Reset call state when modal closes
      setIsConnected(false);
      setCallDuration(0);
      setCallStartTime(null);
    }
  }, [isOpen, lead?.phone]);

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
    if (!lead?.phone) return;
    
    try {
      // Here you would integrate with OpenPhone API
      // For now, we'll simulate the call initiation
      console.log(`Initiating call to ${lead.fullName} at ${lead.phone} via OpenPhone API`);
      
      // Simulate API call to OpenPhone
      // const response = await fetch('/api/openphone/call', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({ 
      //     phoneNumber: lead.phone,
      //     leadId: lead.id 
      //   })
      // });
      
    } catch (error) {
      console.error('Failed to initiate call:', error);
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

          {/* Call Status */}
          <div className="text-center py-6">
            {!isConnected ? (
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
            {!isConnected ? (
              <Button 
                onClick={initiateCall}
                className="bg-green-600 hover:bg-green-700"
                disabled
              >
                <Phone className="h-4 w-4 mr-2" />
                Initiating Call...
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
