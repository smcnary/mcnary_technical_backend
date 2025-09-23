'use client';

import React, { useState, useEffect } from 'react';
import { useData } from '../../hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { 
  Phone, 
  Mail, 
  Calendar, 
  Clock,
  TrendingUp,
  Plus,
  Activity
} from 'lucide-react';

interface LeadStatisticsProps {
  leadId: string;
  leadName: string;
}

interface LeadEvent {
  id: string;
  type: string;
  type_label: string;
  direction?: string;
  direction_label?: string;
  duration?: number;
  notes?: string;
  outcome?: string;
  outcome_label?: string;
  next_action?: string;
  created_at: string;
}

interface Statistics {
  total_events: number;
  phone_calls: number;
  emails: number;
  meetings: number;
  applications: number;
  total_duration: number;
  last_contact?: string;
}

export default function LeadStatistics({ leadId, leadName }: LeadStatisticsProps) {
  const { getLeadStatistics, getLeadEvents, createLeadEvent } = useData();
  const [statistics, setStatistics] = useState<Statistics | null>(null);
  const [events, setEvents] = useState<LeadEvent[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [showAddEvent, setShowAddEvent] = useState(false);
  const [newEvent, setNewEvent] = useState({
    type: 'phone_call',
    direction: 'outbound',
    duration: '',
    notes: '',
    outcome: '',
    next_action: ''
  });

  useEffect(() => {
    loadData();
  }, [leadId]);

  const loadData = async () => {
    try {
      setIsLoading(true);
      const [stats, eventsData] = await Promise.all([
        getLeadStatistics(leadId),
        getLeadEvents(leadId)
      ]);
      setStatistics(stats);
      setEvents(eventsData);
    } catch (error) {
      console.error('Failed to load lead statistics:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleAddEvent = async () => {
    try {
      const eventData = {
        type: newEvent.type,
        direction: newEvent.direction,
        duration: newEvent.duration ? parseInt(newEvent.duration) : undefined,
        notes: newEvent.notes || undefined,
        outcome: newEvent.outcome || undefined,
        next_action: newEvent.next_action || undefined
      };

      await createLeadEvent(leadId, eventData);
      
      // Reset form
      setNewEvent({
        type: 'phone_call',
        direction: 'outbound',
        duration: '',
        notes: '',
        outcome: '',
        next_action: ''
      });
      setShowAddEvent(false);
      
      // Reload data
      await loadData();
    } catch (error) {
      console.error('Failed to create event:', error);
    }
  };

  const formatDuration = (seconds: number) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  if (isLoading) {
    return (
      <Card>
        <CardContent className="p-6">
          <div className="flex items-center justify-center">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span className="ml-2 text-gray-600 dark:text-gray-400">Loading statistics...</span>
          </div>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="space-y-4">
      {/* Statistics Overview */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Activity className="h-5 w-5" />
            Statistics for {leadName}
          </CardTitle>
          <CardDescription>
            Track dials, contacts, interviews, and applications
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full mx-auto mb-2">
                <Phone className="h-6 w-6 text-blue-600 dark:text-blue-400" />
              </div>
              <p className="text-2xl font-bold">{statistics?.phone_calls || 0}</p>
              <p className="text-sm text-gray-600 dark:text-gray-400">Phone Calls</p>
              {statistics?.total_duration && statistics.total_duration > 0 && (
                <p className="text-xs text-gray-500">
                  {formatDuration(statistics.total_duration)} total
                </p>
              )}
            </div>
            
            <div className="text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full mx-auto mb-2">
                <Mail className="h-6 w-6 text-green-600 dark:text-green-400" />
              </div>
              <p className="text-2xl font-bold">{statistics?.emails || 0}</p>
              <p className="text-sm text-gray-600 dark:text-gray-400">Emails</p>
            </div>
            
            <div className="text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full mx-auto mb-2">
                <Calendar className="h-6 w-6 text-purple-600 dark:text-purple-400" />
              </div>
              <p className="text-2xl font-bold">{statistics?.meetings || 0}</p>
              <p className="text-sm text-gray-600 dark:text-gray-400">Meetings</p>
            </div>
            
            <div className="text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-full mx-auto mb-2">
                <TrendingUp className="h-6 w-6 text-orange-600 dark:text-orange-400" />
              </div>
              <p className="text-2xl font-bold">{statistics?.applications || 0}</p>
              <p className="text-sm text-gray-600 dark:text-gray-400">Applications</p>
            </div>
          </div>
          
          {statistics?.last_contact && (
            <div className="mt-4 pt-4 border-t">
              <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <Clock className="h-4 w-4" />
                <span>Last contact: {formatDate(statistics.last_contact)}</span>
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Add Event Form */}
      {showAddEvent && (
        <Card>
          <CardHeader>
            <CardTitle>Add New Event</CardTitle>
            <CardDescription>Track a new interaction with this lead</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium mb-1">Event Type</label>
                <select
                  value={newEvent.type}
                  onChange={(e) => setNewEvent({ ...newEvent, type: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                >
                  <option value="phone_call">Phone Call</option>
                  <option value="email">Email</option>
                  <option value="meeting">Meeting</option>
                  <option value="note">Note</option>
                  <option value="application">Application</option>
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium mb-1">Direction</label>
                <select
                  value={newEvent.direction}
                  onChange={(e) => setNewEvent({ ...newEvent, direction: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                >
                  <option value="outbound">Outbound</option>
                  <option value="inbound">Inbound</option>
                </select>
              </div>
            </div>
            
            {newEvent.type === 'phone_call' && (
              <div>
                <label className="block text-sm font-medium mb-1">Duration (seconds)</label>
                <input
                  type="number"
                  value={newEvent.duration}
                  onChange={(e) => setNewEvent({ ...newEvent, duration: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                  placeholder="e.g., 300 for 5 minutes"
                />
              </div>
            )}
            
            <div>
              <label className="block text-sm font-medium mb-1">Notes</label>
              <textarea
                value={newEvent.notes}
                onChange={(e) => setNewEvent({ ...newEvent, notes: e.target.value })}
                className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                rows={3}
                placeholder="Add notes about this interaction..."
              />
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium mb-1">Outcome</label>
                <select
                  value={newEvent.outcome}
                  onChange={(e) => setNewEvent({ ...newEvent, outcome: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                >
                  <option value="">Select outcome</option>
                  <option value="positive">Positive</option>
                  <option value="neutral">Neutral</option>
                  <option value="negative">Negative</option>
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium mb-1">Next Action</label>
                <input
                  type="text"
                  value={newEvent.next_action}
                  onChange={(e) => setNewEvent({ ...newEvent, next_action: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800"
                  placeholder="e.g., Follow up in 1 week"
                />
              </div>
            </div>
            
            <div className="flex gap-2">
              <Button onClick={handleAddEvent}>
                <Plus className="h-4 w-4 mr-2" />
                Add Event
              </Button>
              <Button variant="outline" onClick={() => setShowAddEvent(false)}>
                Cancel
              </Button>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Recent Events */}
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <div>
            <CardTitle>Recent Events</CardTitle>
            <CardDescription>Latest interactions with this lead</CardDescription>
          </div>
          <Button variant="outline" onClick={() => setShowAddEvent(!showAddEvent)}>
            <Plus className="h-4 w-4 mr-2" />
            Add Event
          </Button>
        </CardHeader>
        <CardContent>
          {events.length === 0 ? (
            <div className="text-center py-8">
              <Activity className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-gray-500 dark:text-gray-400">No events recorded yet</p>
              <Button 
                className="mt-4" 
                onClick={() => setShowAddEvent(true)}
              >
                Add your first event
              </Button>
            </div>
          ) : (
            <div className="space-y-3">
              {events.map((event) => (
                <div 
                  key={event.id} 
                  className="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg"
                >
                  <div className="flex-shrink-0">
                    {event.type === 'phone_call' && <Phone className="h-5 w-5 text-blue-600" />}
                    {event.type === 'email' && <Mail className="h-5 w-5 text-green-600" />}
                    {event.type === 'meeting' && <Calendar className="h-5 w-5 text-purple-600" />}
                    {event.type === 'application' && <TrendingUp className="h-5 w-5 text-orange-600" />}
                    {event.type === 'note' && <Activity className="h-5 w-5 text-gray-600" />}
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="font-medium">{event.type_label}</span>
                      {event.direction_label && (
                        <Badge variant="outline" className="text-xs">
                          {event.direction_label}
                        </Badge>
                      )}
                      {event.outcome_label && (
                        <Badge 
                          variant={event.outcome === 'positive' ? 'default' : event.outcome === 'negative' ? 'destructive' : 'secondary'}
                          className="text-xs"
                        >
                          {event.outcome_label}
                        </Badge>
                      )}
                    </div>
                    
                    {event.notes && (
                      <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        {event.notes}
                      </p>
                    )}
                    
                    <div className="flex items-center gap-4 text-xs text-gray-500">
                      <span>{formatDate(event.created_at)}</span>
                      {event.duration && (
                        <span>{formatDuration(event.duration)}</span>
                      )}
                      {event.next_action && (
                        <span className="text-blue-600">Next: {event.next_action}</span>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
