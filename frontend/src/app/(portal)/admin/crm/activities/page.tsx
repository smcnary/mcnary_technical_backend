'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { 
  Search,
  Filter,
  Plus,
  Eye,
  Edit,
  Phone,
  Mail,
  Calendar as CalendarIcon,
  CheckCircle,
  Clock,
  AlertCircle,
  User,
  Building2,
  MapPin,
  Globe,
  RefreshCw,
  Users,
  Target,
  TrendingUp,
  DollarSign,
  Activity,
  FileText,
  PhoneCall,
  MessageSquare,
  Mail as MailIcon,
  Calendar as CalendarIcon2,
  CheckSquare,
  Square,
  Flag,
  Star
} from 'lucide-react';
import { format } from 'date-fns';

export default function ActivitiesManagement() {
  const { user, isAuthenticated, isAdmin } = useAuth();
  const {
    clients,
    leads,
    campaigns,
    getClients,
    getLeads,
    getCampaigns,
    getLoadingState,
    getErrorState,
    clearError,
  } = useData();

  const [searchTerm, setSearchTerm] = useState('');
  const [typeFilter, setTypeFilter] = useState('all');
  const [statusFilter, setStatusFilter] = useState('all');
  const [priorityFilter, setPriorityFilter] = useState('all');
  const [assignedToFilter, setAssignedToFilter] = useState('all');
  const [selectedActivity, setSelectedActivity] = useState<any>(null);
  const [isActivityDialogOpen, setIsActivityDialogOpen] = useState(false);
  const [isEditMode, setIsEditMode] = useState(false);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [dueDate, setDueDate] = useState<Date | undefined>(undefined);

  // Mock activities data - in a real app, this would come from an API
  const [activities, setActivities] = useState([
    {
      id: '1',
      type: 'call',
      title: 'Follow-up call with John Doe',
      description: 'Discuss proposal details and next steps',
      clientId: 'client-1',
      leadId: 'lead-1',
      assignedTo: 'user-1',
      priority: 'high',
      status: 'pending',
      dueDate: new Date('2024-01-15'),
      createdAt: new Date('2024-01-10'),
      completedAt: null,
    },
    {
      id: '2',
      type: 'email',
      title: 'Send proposal to ABC Law Firm',
      description: 'Email the detailed proposal and pricing',
      clientId: 'client-2',
      leadId: null,
      assignedTo: 'user-2',
      priority: 'medium',
      status: 'completed',
      dueDate: new Date('2024-01-12'),
      createdAt: new Date('2024-01-08'),
      completedAt: new Date('2024-01-12'),
    },
    {
      id: '3',
      type: 'meeting',
      title: 'Client onboarding meeting',
      description: 'Initial meeting to discuss campaign strategy',
      clientId: 'client-3',
      leadId: null,
      assignedTo: 'user-1',
      priority: 'high',
      status: 'pending',
      dueDate: new Date('2024-01-18'),
      createdAt: new Date('2024-01-14'),
      completedAt: null,
    },
    {
      id: '4',
      type: 'task',
      title: 'Update client website content',
      description: 'Review and update website content for better SEO',
      clientId: 'client-1',
      leadId: null,
      assignedTo: 'user-3',
      priority: 'low',
      status: 'in_progress',
      dueDate: new Date('2024-01-20'),
      createdAt: new Date('2024-01-12'),
      completedAt: null,
    },
  ]);

  // Load data on component mount
  useEffect(() => {
    if (isAuthenticated) {
      loadInitialData();
    }
  }, [isAuthenticated]);

  const loadInitialData = async () => {
    try {
      await Promise.all([
        getClients(),
        getLeads(),
        getCampaigns(),
      ]);
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  const handleRefresh = async () => {
    setIsRefreshing(true);
    try {
      await loadInitialData();
    } catch (error) {
      console.error('Failed to refresh data:', error);
    } finally {
      setIsRefreshing(false);
    }
  };

  // Filter activities based on search and filters
  const filteredActivities = activities.filter(activity => {
    const matchesSearch = !searchTerm || 
      activity.title?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      activity.description?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesType = typeFilter === 'all' || activity.type === typeFilter;
    const matchesStatus = statusFilter === 'all' || activity.status === statusFilter;
    const matchesPriority = priorityFilter === 'all' || activity.priority === priorityFilter;
    const matchesAssignedTo = assignedToFilter === 'all' || activity.assignedTo === assignedToFilter;
    
    return matchesSearch && matchesType && matchesStatus && matchesPriority && matchesAssignedTo;
  });

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'pending': return 'secondary';
      case 'in_progress': return 'default';
      case 'completed': return 'default';
      case 'cancelled': return 'destructive';
      default: return 'secondary';
    }
  };

  const getPriorityBadgeVariant = (priority: string) => {
    switch (priority) {
      case 'high': return 'destructive';
      case 'medium': return 'default';
      case 'low': return 'secondary';
      default: return 'secondary';
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'call': return <PhoneCall className="w-4 h-4" />;
      case 'email': return <MailIcon className="w-4 h-4" />;
      case 'meeting': return <Users className="w-4 h-4" />;
      case 'task': return <CheckSquare className="w-4 h-4" />;
      default: return <Activity className="w-4 h-4" />;
    }
  };

  const handleActivityAction = async (activityId: string, action: string, data?: any) => {
    try {
      // TODO: Implement activity actions API calls
      console.log(`Activity action: ${action} for activity ${activityId}`, data);
      
      if (action === 'update') {
        setActivities(prev => prev.map(activity => 
          activity.id === activityId ? { ...activity, ...data } : activity
        ));
      } else if (action === 'complete') {
        setActivities(prev => prev.map(activity => 
          activity.id === activityId ? { ...activity, status: 'completed', completedAt: new Date() } : activity
        ));
      }
      
      // Close dialog if editing
      if (action === 'update') {
        setIsActivityDialogOpen(false);
        setIsEditMode(false);
      }
    } catch (error) {
      console.error('Failed to perform activity action:', error);
    }
  };

  const handleViewActivity = (activity: any) => {
    setSelectedActivity(activity);
    setIsEditMode(false);
    setIsActivityDialogOpen(true);
  };

  const handleEditActivity = (activity: any) => {
    setSelectedActivity(activity);
    setIsEditMode(true);
    setIsActivityDialogOpen(true);
  };

  const handleStatusChange = async (activityId: string, newStatus: string) => {
    await handleActivityAction(activityId, 'update', { status: newStatus });
  };

  const handleCompleteActivity = async (activityId: string) => {
    await handleActivityAction(activityId, 'complete');
  };

  const getClientName = (clientId: string) => {
    const client = clients.find(c => c.id === clientId);
    return client?.name || 'Unknown Client';
  };

  const getLeadName = (leadId: string) => {
    const lead = leads.find(l => l.id === leadId);
    return lead?.fullName || 'Unknown Lead';
  };

  if (!isAuthenticated) {
    return <div>Please log in to access activities management.</div>;
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Activities Management" 
        description="Manage tasks, calls, meetings, and other activities"
        action={
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={handleRefresh} disabled={isRefreshing}>
              <RefreshCw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
              Refresh
            </Button>
            <Button size="sm">
              <Plus className="w-4 h-4 mr-2" />
              Add Activity
            </Button>
          </div>
        }
      />

      {/* Activity Overview Cards */}
      <div className="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Activities</CardTitle>
            <Activity className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activities.length}</div>
            <p className="text-xs text-muted-foreground">
              {activities.filter(a => a.status === 'pending').length} pending
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending</CardTitle>
            <Clock className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activities.filter(a => a.status === 'pending').length}</div>
            <p className="text-xs text-muted-foreground">
              {activities.filter(a => a.status === 'pending' && a.priority === 'high').length} high priority
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">In Progress</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activities.filter(a => a.status === 'in_progress').length}</div>
            <p className="text-xs text-muted-foreground">
              Active tasks
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Completed</CardTitle>
            <CheckCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activities.filter(a => a.status === 'completed').length}</div>
            <p className="text-xs text-muted-foreground">
              This month
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div className="relative">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search activities..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-8"
              />
            </div>
            
            <Select value={typeFilter} onValueChange={setTypeFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Types</SelectItem>
                <SelectItem value="call">Call</SelectItem>
                <SelectItem value="email">Email</SelectItem>
                <SelectItem value="meeting">Meeting</SelectItem>
                <SelectItem value="task">Task</SelectItem>
              </SelectContent>
            </Select>

            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
                <SelectItem value="in_progress">In Progress</SelectItem>
                <SelectItem value="completed">Completed</SelectItem>
                <SelectItem value="cancelled">Cancelled</SelectItem>
              </SelectContent>
            </Select>

            <Select value={priorityFilter} onValueChange={setPriorityFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Priority" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Priorities</SelectItem>
                <SelectItem value="high">High</SelectItem>
                <SelectItem value="medium">Medium</SelectItem>
                <SelectItem value="low">Low</SelectItem>
              </SelectContent>
            </Select>

            <Select value={assignedToFilter} onValueChange={setAssignedToFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Assigned To" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Users</SelectItem>
                <SelectItem value="user-1">John Doe</SelectItem>
                <SelectItem value="user-2">Jane Smith</SelectItem>
                <SelectItem value="user-3">Mike Johnson</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Activities Table */}
      <Card>
        <CardHeader>
          <CardTitle>Activities ({filteredActivities.length})</CardTitle>
          <CardDescription>Manage tasks, calls, meetings, and other activities</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Activity</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Client/Lead</TableHead>
                <TableHead>Priority</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Due Date</TableHead>
                <TableHead>Assigned To</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredActivities.map((activity) => (
                <TableRow key={activity.id}>
                  <TableCell>
                    <div className="space-y-1">
                      <div className="font-medium">{activity.title}</div>
                      <div className="text-sm text-muted-foreground">{activity.description}</div>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center space-x-2">
                      {getTypeIcon(activity.type)}
                      <span className="capitalize">{activity.type}</span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="space-y-1">
                      {activity.clientId && (
                        <div className="flex items-center text-sm">
                          <Building2 className="w-3 h-3 mr-1 text-muted-foreground" />
                          {getClientName(activity.clientId)}
                        </div>
                      )}
                      {activity.leadId && (
                        <div className="flex items-center text-sm">
                          <User className="w-3 h-3 mr-1 text-muted-foreground" />
                          {getLeadName(activity.leadId)}
                        </div>
                      )}
                    </div>
                  </TableCell>
                  <TableCell>
                    <Badge variant={getPriorityBadgeVariant(activity.priority)}>
                      {activity.priority}
                    </Badge>
                  </TableCell>
                  <TableCell>
                    <Select 
                      value={activity.status} 
                      onValueChange={(value) => handleStatusChange(activity.id, value)}
                    >
                      <SelectTrigger className="w-[120px]">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="in_progress">In Progress</SelectItem>
                        <SelectItem value="completed">Completed</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                      </SelectContent>
                    </Select>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center space-x-1">
                      <CalendarIcon className="w-3 h-3 text-muted-foreground" />
                      <span className="text-sm">
                        {format(activity.dueDate, 'MMM dd, yyyy')}
                      </span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center space-x-1">
                      <User className="w-3 h-3 text-muted-foreground" />
                      <span className="text-sm">
                        {activity.assignedTo === 'user-1' ? 'John Doe' : 
                         activity.assignedTo === 'user-2' ? 'Jane Smith' : 
                         activity.assignedTo === 'user-3' ? 'Mike Johnson' : 'Unknown'}
                      </span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center space-x-2">
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => handleViewActivity(activity)}
                      >
                        <Eye className="w-4 h-4" />
                      </Button>
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => handleEditActivity(activity)}
                      >
                        <Edit className="w-4 h-4" />
                      </Button>
                      {activity.status !== 'completed' && (
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleCompleteActivity(activity.id)}
                        >
                          <CheckCircle className="w-4 h-4" />
                        </Button>
                      )}
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Activity Detail Dialog */}
      <Dialog open={isActivityDialogOpen} onOpenChange={setIsActivityDialogOpen}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>
              {isEditMode ? 'Edit Activity' : 'Activity Details'}
            </DialogTitle>
            <DialogDescription>
              {isEditMode ? 'Update activity information' : 'View activity information'}
            </DialogDescription>
          </DialogHeader>
          {selectedActivity && (
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Title</Label>
                  <Input 
                    value={selectedActivity.title} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedActivity({...selectedActivity, title: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Type</Label>
                  <Select 
                    value={selectedActivity.type} 
                    disabled={!isEditMode}
                    onValueChange={isEditMode ? (value) => setSelectedActivity({...selectedActivity, type: value}) : undefined}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="call">Call</SelectItem>
                      <SelectItem value="email">Email</SelectItem>
                      <SelectItem value="meeting">Meeting</SelectItem>
                      <SelectItem value="task">Task</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label>Priority</Label>
                  <Select 
                    value={selectedActivity.priority} 
                    disabled={!isEditMode}
                    onValueChange={isEditMode ? (value) => setSelectedActivity({...selectedActivity, priority: value}) : undefined}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="high">High</SelectItem>
                      <SelectItem value="medium">Medium</SelectItem>
                      <SelectItem value="low">Low</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label>Status</Label>
                  <Select 
                    value={selectedActivity.status} 
                    disabled={!isEditMode}
                    onValueChange={isEditMode ? (value) => setSelectedActivity({...selectedActivity, status: value}) : undefined}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pending">Pending</SelectItem>
                      <SelectItem value="in_progress">In Progress</SelectItem>
                      <SelectItem value="completed">Completed</SelectItem>
                      <SelectItem value="cancelled">Cancelled</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div>
                <Label>Description</Label>
                <Textarea 
                  value={selectedActivity.description} 
                  readOnly={!isEditMode}
                  rows={4}
                  onChange={isEditMode ? (e) => setSelectedActivity({...selectedActivity, description: e.target.value}) : undefined}
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Due Date</Label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        className="w-full justify-start text-left font-normal"
                        disabled={!isEditMode}
                      >
                        <CalendarIcon2 className="mr-2 h-4 w-4" />
                        {selectedActivity.dueDate ? format(selectedActivity.dueDate, 'PPP') : 'Pick a date'}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0">
                      <Calendar
                        mode="single"
                        selected={selectedActivity.dueDate}
                        onSelect={isEditMode ? (date) => setSelectedActivity({...selectedActivity, dueDate: date}) : undefined}
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                </div>
                <div>
                  <Label>Assigned To</Label>
                  <Select 
                    value={selectedActivity.assignedTo} 
                    disabled={!isEditMode}
                    onValueChange={isEditMode ? (value) => setSelectedActivity({...selectedActivity, assignedTo: value}) : undefined}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="user-1">John Doe</SelectItem>
                      <SelectItem value="user-2">Jane Smith</SelectItem>
                      <SelectItem value="user-3">Mike Johnson</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {/* Actions */}
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setIsActivityDialogOpen(false)}>
                  Close
                </Button>
                {isEditMode ? (
                  <Button onClick={() => handleActivityAction(selectedActivity.id, 'update', selectedActivity)}>
                    Save Changes
                  </Button>
                ) : (
                  <Button onClick={() => setIsEditMode(true)}>
                    Edit Activity
                  </Button>
                )}
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
