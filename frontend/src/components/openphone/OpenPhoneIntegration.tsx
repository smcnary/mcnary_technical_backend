import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Switch } from '../ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '../ui/select';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Phone, MessageSquare, Settings, Plus, Sync, Trash2, CheckCircle, AlertCircle, Clock } from 'lucide-react';
import { openPhoneApiService, OpenPhoneIntegration, OpenPhoneNumber, OpenPhoneCallLog, OpenPhoneMessageLog } from '../../services/openPhoneApi';

interface OpenPhoneIntegrationProps {
  clientId: string;
  clientName: string;
}

export default function OpenPhoneIntegrationComponent({ clientId, clientName }: OpenPhoneIntegrationProps) {
  const [integrations, setIntegrations] = useState<OpenPhoneIntegration[]>([]);
  const [phoneNumbers, setPhoneNumbers] = useState<OpenPhoneNumber[]>([]);
  const [callLogs, setCallLogs] = useState<OpenPhoneCallLog[]>([]);
  const [messageLogs, setMessageLogs] = useState<OpenPhoneMessageLog[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [showCreateDialog, setShowCreateDialog] = useState(false);
  const [showCallLogs, setShowCallLogs] = useState(false);
  const [showMessageLogs, setShowMessageLogs] = useState(false);

  // Form state for creating integration
  const [formData, setFormData] = useState({
    phoneNumber: '',
    displayName: '',
    isDefault: false,
    autoLogCalls: true,
    autoLogMessages: true,
    syncContacts: false,
  });

  useEffect(() => {
    loadData();
  }, [clientId]);

  const loadData = async () => {
    setLoading(true);
    setError(null);
    
    try {
      const [integrationsData, phoneNumbersData] = await Promise.all([
        openPhoneApiService.getIntegrations(),
        openPhoneApiService.getPhoneNumbers(),
      ]);

      // Filter integrations for this client
      const clientIntegrations = integrationsData.filter(integration => integration.clientId === clientId);
      
      setIntegrations(clientIntegrations);
      setPhoneNumbers(phoneNumbersData);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const handleCreateIntegration = async () => {
    if (!formData.phoneNumber) {
      setError('Phone number is required');
      return;
    }

    setLoading(true);
    setError(null);

    try {
      await openPhoneApiService.createIntegration({
        clientId,
        phoneNumber: formData.phoneNumber,
        displayName: formData.displayName || undefined,
        isDefault: formData.isDefault,
        autoLogCalls: formData.autoLogCalls,
        autoLogMessages: formData.autoLogMessages,
        syncContacts: formData.syncContacts,
      });

      setShowCreateDialog(false);
      setFormData({
        phoneNumber: '',
        displayName: '',
        isDefault: false,
        autoLogCalls: true,
        autoLogMessages: true,
        syncContacts: false,
      });
      
      await loadData();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to create integration');
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteIntegration = async (id: string) => {
    if (!confirm('Are you sure you want to delete this integration?')) {
      return;
    }

    setLoading(true);
    setError(null);

    try {
      await openPhoneApiService.deleteIntegration(id);
      await loadData();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to delete integration');
    } finally {
      setLoading(false);
    }
  };

  const handleSyncIntegration = async (id: string) => {
    setLoading(true);
    setError(null);

    try {
      const result = await openPhoneApiService.syncIntegration(id);
      alert(`Sync completed! ${result.totalSynced} items synced (${result.callLogsSynced} calls, ${result.messageLogsSynced} messages)`);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to sync integration');
    } finally {
      setLoading(false);
    }
  };

  const loadCallLogs = async (integrationId?: string) => {
    try {
      const logs = await openPhoneApiService.getCallLogs({
        clientId,
        integrationId,
        limit: 50,
      });
      setCallLogs(logs);
      setShowCallLogs(true);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load call logs');
    }
  };

  const loadMessageLogs = async (integrationId?: string) => {
    try {
      const logs = await openPhoneApiService.getMessageLogs({
        clientId,
        integrationId,
        limit: 50,
      });
      setMessageLogs(logs);
      setShowMessageLogs(true);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load message logs');
    }
  };

  const formatDuration = (seconds?: number) => {
    if (!seconds) return 'N/A';
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  };

  const formatPhoneNumber = (number?: string) => {
    if (!number) return 'N/A';
    // Simple formatting - you might want to use a library like libphonenumber
    return number.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  };

  const getStatusBadge = (status: string, type: 'call' | 'message') => {
    const statusConfig = {
      call: {
        answered: { color: 'bg-green-100 text-green-800', icon: CheckCircle },
        missed: { color: 'bg-red-100 text-red-800', icon: AlertCircle },
        voicemail: { color: 'bg-yellow-100 text-yellow-800', icon: Clock },
        busy: { color: 'bg-orange-100 text-orange-800', icon: Clock },
        failed: { color: 'bg-red-100 text-red-800', icon: AlertCircle },
      },
      message: {
        sent: { color: 'bg-blue-100 text-blue-800', icon: CheckCircle },
        delivered: { color: 'bg-green-100 text-green-800', icon: CheckCircle },
        failed: { color: 'bg-red-100 text-red-800', icon: AlertCircle },
        pending: { color: 'bg-yellow-100 text-yellow-800', icon: Clock },
      },
    };

    const config = statusConfig[type][status as keyof typeof statusConfig[typeof type]] || 
                  { color: 'bg-gray-100 text-gray-800', icon: Clock };
    
    const IconComponent = config.icon;
    
    return (
      <Badge className={config.color}>
        <IconComponent className="h-3 w-3 mr-1" />
        {status}
      </Badge>
    );
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-lg font-semibold flex items-center gap-2">
            <Phone className="h-5 w-5" />
            OpenPhone Integration
          </h3>
          <p className="text-sm text-muted-foreground">
            Manage phone integrations for {clientName}
          </p>
        </div>
        <Dialog open={showCreateDialog} onOpenChange={setShowCreateDialog}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="h-4 w-4 mr-2" />
              Add Integration
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add OpenPhone Integration</DialogTitle>
              <DialogDescription>
                Connect a phone number to this client for call and message logging.
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <div>
                <Label htmlFor="phoneNumber">Phone Number</Label>
                <Select value={formData.phoneNumber} onValueChange={(value) => setFormData({ ...formData, phoneNumber: value })}>
                  <SelectTrigger>
                    <SelectValue placeholder="Select a phone number" />
                  </SelectTrigger>
                  <SelectContent>
                    {phoneNumbers.map((number) => (
                      <SelectItem key={number.id} value={number.phoneNumber}>
                        {number.displayName || number.phoneNumber}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Label htmlFor="displayName">Display Name (Optional)</Label>
                <Input
                  id="displayName"
                  value={formData.displayName}
                  onChange={(e) => setFormData({ ...formData, displayName: e.target.value })}
                  placeholder="e.g., Main Office Line"
                />
              </div>
              <div className="space-y-3">
                <div className="flex items-center justify-between">
                  <Label htmlFor="isDefault">Default Integration</Label>
                  <Switch
                    id="isDefault"
                    checked={formData.isDefault}
                    onCheckedChange={(checked) => setFormData({ ...formData, isDefault: checked })}
                  />
                </div>
                <div className="flex items-center justify-between">
                  <Label htmlFor="autoLogCalls">Auto-log Calls</Label>
                  <Switch
                    id="autoLogCalls"
                    checked={formData.autoLogCalls}
                    onCheckedChange={(checked) => setFormData({ ...formData, autoLogCalls: checked })}
                  />
                </div>
                <div className="flex items-center justify-between">
                  <Label htmlFor="autoLogMessages">Auto-log Messages</Label>
                  <Switch
                    id="autoLogMessages"
                    checked={formData.autoLogMessages}
                    onCheckedChange={(checked) => setFormData({ ...formData, autoLogMessages: checked })}
                  />
                </div>
                <div className="flex items-center justify-between">
                  <Label htmlFor="syncContacts">Sync Contacts</Label>
                  <Switch
                    id="syncContacts"
                    checked={formData.syncContacts}
                    onCheckedChange={(checked) => setFormData({ ...formData, syncContacts: checked })}
                  />
                </div>
              </div>
              <div className="flex justify-end gap-2">
                <Button variant="outline" onClick={() => setShowCreateDialog(false)}>
                  Cancel
                </Button>
                <Button onClick={handleCreateIntegration} disabled={loading}>
                  {loading ? 'Creating...' : 'Create Integration'}
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      {/* Error Display */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-center gap-2 text-red-800">
            <AlertCircle className="h-4 w-4" />
            {error}
          </div>
        </div>
      )}

      {/* Integrations List */}
      {integrations.length === 0 ? (
        <Card>
          <CardContent className="text-center py-8">
            <Phone className="h-12 w-12 mx-auto text-muted-foreground mb-4" />
            <h4 className="text-lg font-medium mb-2">No Phone Integrations</h4>
            <p className="text-muted-foreground mb-4">
              Connect a phone number to start logging calls and messages for this client.
            </p>
            <Button onClick={() => setShowCreateDialog(true)}>
              <Plus className="h-4 w-4 mr-2" />
              Add Integration
            </Button>
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-4">
          {integrations.map((integration) => (
            <Card key={integration.id}>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <div>
                    <CardTitle className="text-base flex items-center gap-2">
                      <Phone className="h-4 w-4" />
                      {integration.displayName || integration.phoneNumber}
                      {integration.isDefault && (
                        <Badge variant="default" className="text-xs">Default</Badge>
                      )}
                    </CardTitle>
                    <CardDescription>
                      {formatPhoneNumber(integration.phoneNumber)}
                    </CardDescription>
                  </div>
                  <div className="flex items-center gap-2">
                    <Badge variant={integration.status === 'active' ? 'default' : 'secondary'}>
                      {integration.status}
                    </Badge>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => handleSyncIntegration(integration.id)}
                      disabled={loading}
                    >
                      <Sync className="h-3 w-3 mr-1" />
                      Sync
                    </Button>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => handleDeleteIntegration(integration.id)}
                      disabled={loading}
                    >
                      <Trash2 className="h-3 w-3" />
                    </Button>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                  <div>
                    <Label className="text-xs text-muted-foreground">Auto-log Calls</Label>
                    <div className="flex items-center gap-1">
                      {integration.autoLogCalls ? (
                        <CheckCircle className="h-3 w-3 text-green-500" />
                      ) : (
                        <AlertCircle className="h-3 w-3 text-gray-400" />
                      )}
                      <span className="text-xs">{integration.autoLogCalls ? 'Enabled' : 'Disabled'}</span>
                    </div>
                  </div>
                  <div>
                    <Label className="text-xs text-muted-foreground">Auto-log Messages</Label>
                    <div className="flex items-center gap-1">
                      {integration.autoLogMessages ? (
                        <CheckCircle className="h-3 w-3 text-green-500" />
                      ) : (
                        <AlertCircle className="h-3 w-3 text-gray-400" />
                      )}
                      <span className="text-xs">{integration.autoLogMessages ? 'Enabled' : 'Disabled'}</span>
                    </div>
                  </div>
                  <div>
                    <Label className="text-xs text-muted-foreground">Sync Contacts</Label>
                    <div className="flex items-center gap-1">
                      {integration.syncContacts ? (
                        <CheckCircle className="h-3 w-3 text-green-500" />
                      ) : (
                        <AlertCircle className="h-3 w-3 text-gray-400" />
                      )}
                      <span className="text-xs">{integration.syncContacts ? 'Enabled' : 'Disabled'}</span>
                    </div>
                  </div>
                  <div>
                    <Label className="text-xs text-muted-foreground">Created</Label>
                    <div className="text-xs text-muted-foreground">
                      {new Date(integration.createdAt).toLocaleDateString()}
                    </div>
                  </div>
                </div>
                <div className="flex gap-2 mt-4">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => loadCallLogs(integration.id)}
                  >
                    <Phone className="h-3 w-3 mr-1" />
                    View Calls
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => loadMessageLogs(integration.id)}
                  >
                    <MessageSquare className="h-3 w-3 mr-1" />
                    View Messages
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      {/* Call Logs Dialog */}
      <Dialog open={showCallLogs} onOpenChange={setShowCallLogs}>
        <DialogContent className="max-w-4xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Call Logs</DialogTitle>
            <DialogDescription>
              Recent call activity for {clientName}
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            {callLogs.length === 0 ? (
              <div className="text-center py-8 text-muted-foreground">
                No call logs found
              </div>
            ) : (
              callLogs.map((log) => (
                <div key={log.id} className="border rounded-lg p-4">
                  <div className="flex items-center justify-between mb-2">
                    <div className="flex items-center gap-2">
                      <Phone className="h-4 w-4" />
                      <span className="font-medium">
                        {log.direction === 'inbound' ? 'Incoming' : 'Outgoing'} Call
                      </span>
                    </div>
                    {getStatusBadge(log.status, 'call')}
                  </div>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                      <Label className="text-xs text-muted-foreground">From</Label>
                      <div>{formatPhoneNumber(log.fromNumber)}</div>
                    </div>
                    <div>
                      <Label className="text-xs text-muted-foreground">To</Label>
                      <div>{formatPhoneNumber(log.toNumber)}</div>
                    </div>
                    <div>
                      <Label className="text-xs text-muted-foreground">Duration</Label>
                      <div>{formatDuration(log.duration)}</div>
                    </div>
                    <div>
                      <Label className="text-xs text-muted-foreground">Date</Label>
                      <div>{log.startedAt ? new Date(log.startedAt).toLocaleString() : 'N/A'}</div>
                    </div>
                  </div>
                  {log.transcript && (
                    <div className="mt-3">
                      <Label className="text-xs text-muted-foreground">Transcript</Label>
                      <div className="text-sm bg-gray-50 p-2 rounded mt-1 max-h-32 overflow-y-auto">
                        {log.transcript}
                      </div>
                    </div>
                  )}
                </div>
              ))
            )}
          </div>
        </DialogContent>
      </Dialog>

      {/* Message Logs Dialog */}
      <Dialog open={showMessageLogs} onOpenChange={setShowMessageLogs}>
        <DialogContent className="max-w-4xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Message Logs</DialogTitle>
            <DialogDescription>
              Recent message activity for {clientName}
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            {messageLogs.length === 0 ? (
              <div className="text-center py-8 text-muted-foreground">
                No message logs found
              </div>
            ) : (
              messageLogs.map((log) => (
                <div key={log.id} className="border rounded-lg p-4">
                  <div className="flex items-center justify-between mb-2">
                    <div className="flex items-center gap-2">
                      <MessageSquare className="h-4 w-4" />
                      <span className="font-medium">
                        {log.direction === 'inbound' ? 'Incoming' : 'Outgoing'} Message
                      </span>
                    </div>
                    {getStatusBadge(log.status, 'message')}
                  </div>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-3">
                    <div>
                      <Label className="text-xs text-muted-foreground">From</Label>
                      <div>{formatPhoneNumber(log.fromNumber)}</div>
                    </div>
                    <div>
                      <Label className="text-xs text-muted-foreground">To</Label>
                      <div>{formatPhoneNumber(log.toNumber)}</div>
                    </div>
                    <div>
                      <Label className="text-xs text-muted-foreground">Sent</Label>
                      <div>{new Date(log.sentAt).toLocaleString()}</div>
                    </div>
                  </div>
                  <div>
                    <Label className="text-xs text-muted-foreground">Message</Label>
                    <div className="text-sm bg-gray-50 p-2 rounded mt-1">
                      {log.content}
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
