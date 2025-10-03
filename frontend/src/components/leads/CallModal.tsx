'use client';

import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Button } from '../ui/button';
import { Card, CardContent } from '../ui/card';
import { Badge } from '../ui/badge';
import { 
  Phone, 
  PhoneOff, 
  Mic, 
  MicOff, 
  Volume2, 
  VolumeX,
  Clock,
  User,
  Building
} from 'lucide-react';

interface Lead {
  id: string;
  fullName: string;
  email: string;
  phone?: string;
  firm?: string;
  website?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  message?: string;
  practiceAreas?: string[];
  status: string;
  createdAt?: string;
  updatedAt?: string;
}

interface CallModalProps {
  lead: Lead | null;
  isOpen: boolean;
  onClose: () => void;
  onHangup: () => void;
}

export default function CallModal({ lead, isOpen, onClose, onHangup }: CallModalProps) {
  const [isConnected, setIsConnected] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const [isSpeakerOn, setIsSpeakerOn] = useState(false);
  const [callDuration, setCallDuration] = useState(0);
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

  const handleCall = () => {
    setIsConnected(true);
    setCallStartTime(new Date());
    setCallDuration(0);
  };

  const handleHangup = () => {
    setIsConnected(false);
    setCallStartTime(null);
    setCallDuration(0);
    setIsMuted(false);
    setIsSpeakerOn(false);
    onHangup();
  };

  const toggleMute = () => {
    setIsMuted(!isMuted);
  };

  const toggleSpeaker = () => {
    setIsSpeakerOn(!isSpeakerOn);
  };

  const formatDuration = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  if (!lead) return null;

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <Phone className="h-5 w-5" />
            {isConnected ? 'Call in Progress' : 'Make Call'}
          </DialogTitle>
        </DialogHeader>

        <div className="space-y-6">
          {/* Lead Information */}
          <Card>
            <CardContent className="pt-6">
              <div className="text-center space-y-2">
                <div className="flex items-center justify-center gap-2">
                  <User className="h-5 w-5 text-gray-500" />
                  <h3 className="text-lg font-semibold">{lead.fullName}</h3>
                </div>
                {lead.firm && (
                  <div className="flex items-center justify-center gap-2">
                    <Building className="h-4 w-4 text-gray-500" />
                    <span className="text-sm text-gray-600 dark:text-gray-400">{lead.firm}</span>
                  </div>
                )}
                {lead.phone && (
                  <div className="flex items-center justify-center gap-2">
                    <Phone className="h-4 w-4 text-gray-500" />
                    <span className="text-sm font-mono">{lead.phone}</span>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Call Status */}
          {isConnected && (
            <Card>
              <CardContent className="pt-6">
                <div className="text-center space-y-2">
                  <div className="flex items-center justify-center gap-2">
                    <Clock className="h-4 w-4 text-gray-500" />
                    <span className="text-sm text-gray-600 dark:text-gray-400">Call Duration</span>
                  </div>
                  <div className="text-2xl font-mono font-bold text-green-600">
                    {formatDuration(callDuration)}
                  </div>
                  <Badge variant="default" className="bg-green-100 text-green-800">
                    Connected
                  </Badge>
                </div>
              </CardContent>
            </Card>
          )}

          {/* Call Controls */}
          <div className="flex justify-center space-x-4">
            {!isConnected ? (
              <Button 
                onClick={handleCall}
                size="lg"
                className="bg-green-600 hover:bg-green-700 text-white rounded-full w-16 h-16"
              >
                <Phone className="h-6 w-6" />
              </Button>
            ) : (
              <>
                <Button
                  onClick={toggleMute}
                  variant={isMuted ? "destructive" : "outline"}
                  size="lg"
                  className="rounded-full w-12 h-12"
                >
                  {isMuted ? <MicOff className="h-5 w-5" /> : <Mic className="h-5 w-5" />}
                </Button>
                
                <Button
                  onClick={handleHangup}
                  size="lg"
                  className="bg-red-600 hover:bg-red-700 text-white rounded-full w-16 h-16"
                >
                  <PhoneOff className="h-6 w-6" />
                </Button>
                
                <Button
                  onClick={toggleSpeaker}
                  variant={isSpeakerOn ? "default" : "outline"}
                  size="lg"
                  className="rounded-full w-12 h-12"
                >
                  {isSpeakerOn ? <Volume2 className="h-5 w-5" /> : <VolumeX className="h-5 w-5" />}
                </Button>
              </>
            )}
          </div>

          {/* Call Notes */}
          {isConnected && (
            <Card>
              <CardContent className="pt-6">
                <h4 className="text-sm font-semibold mb-2">Call Notes</h4>
                <textarea
                  className="w-full h-20 p-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm resize-none"
                  placeholder="Add notes about the call..."
                />
              </CardContent>
            </Card>
          )}

          {/* Action Buttons */}
          <div className="flex justify-end space-x-2">
            <Button variant="outline" onClick={onClose}>
              Close
            </Button>
            {isConnected && (
              <Button onClick={handleHangup} variant="destructive">
                End Call
              </Button>
            )}
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
