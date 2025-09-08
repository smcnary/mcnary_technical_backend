'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table';
import { 
  CreditCard,
  Download,
  Eye,
  Calendar,
  DollarSign,
  FileText,
  AlertCircle,
  CheckCircle,
  Clock,
  Loader2,
  Plus
} from 'lucide-react';

interface Invoice {
  id: string;
  number: string;
  amount: number;
  status: 'paid' | 'pending' | 'overdue' | 'draft';
  dueDate: string;
  issueDate: string;
  description: string;
}

interface Subscription {
  id: string;
  name: string;
  price: number;
  billingCycle: 'monthly' | 'yearly';
  status: 'active' | 'cancelled' | 'past_due';
  nextBillingDate: string;
  features: string[];
}

export default function ClientBillingPage() {
  const { user, isClientAdmin } = useAuth();
  const { 
    packages, 
    getPackages, 
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [subscriptions, setSubscriptions] = useState<Subscription[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadingState = getLoadingState('packages');
  const errorState = getErrorState('packages');

  useEffect(() => {
    loadBillingData();
  }, []);

  const loadBillingData = async () => {
    setIsLoading(true);
    try {
      clearError('packages');
      
      // Load packages (which represent subscription plans)
      await getPackages();
      
      // Mock data for invoices and subscriptions
      // In a real implementation, these would come from your billing API
      setInvoices([
        {
          id: '1',
          number: 'INV-2024-001',
          amount: 2500.00,
          status: 'paid',
          dueDate: '2024-01-15',
          issueDate: '2024-01-01',
          description: 'Monthly SEO Services - January 2024'
        },
        {
          id: '2',
          number: 'INV-2024-002',
          amount: 2500.00,
          status: 'pending',
          dueDate: '2024-02-15',
          issueDate: '2024-02-01',
          description: 'Monthly SEO Services - February 2024'
        },
        {
          id: '3',
          number: 'INV-2024-003',
          amount: 5000.00,
          status: 'overdue',
          dueDate: '2024-01-30',
          issueDate: '2024-01-15',
          description: 'Additional Services - Website Audit'
        }
      ]);

      setSubscriptions([
        {
          id: '1',
          name: 'Professional SEO Package',
          price: 2500.00,
          billingCycle: 'monthly',
          status: 'active',
          nextBillingDate: '2024-03-01',
          features: ['Monthly SEO Reports', 'Keyword Research', 'Content Optimization', 'Local SEO Management']
        }
      ]);
    } catch (err) {
      console.error('Failed to load billing data:', err);
    } finally {
      setIsLoading(false);
    }
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'paid':
      case 'active':
        return 'default';
      case 'pending':
        return 'secondary';
      case 'overdue':
        return 'destructive';
      case 'cancelled':
        return 'outline';
      default:
        return 'secondary';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'paid':
      case 'active':
        return <CheckCircle className="h-4 w-4 text-green-500" />;
      case 'pending':
        return <Clock className="h-4 w-4 text-yellow-500" />;
      case 'overdue':
        return <AlertCircle className="h-4 w-4 text-red-500" />;
      default:
        return <Clock className="h-4 w-4 text-gray-500" />;
    }
  };

  const totalPaid = invoices.filter(inv => inv.status === 'paid').reduce((sum, inv) => sum + inv.amount, 0);
  const totalPending = invoices.filter(inv => inv.status === 'pending').reduce((sum, inv) => sum + inv.amount, 0);
  const totalOverdue = invoices.filter(inv => inv.status === 'overdue').reduce((sum, inv) => sum + inv.amount, 0);

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Billing" 
        subtitle="Manage your invoices, payments, and subscription plans" 
      />

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Paid</CardTitle>
            <CheckCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{formatCurrency(totalPaid)}</div>
            <p className="text-xs text-muted-foreground">
              All time payments
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending</CardTitle>
            <Clock className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{formatCurrency(totalPending)}</div>
            <p className="text-xs text-muted-foreground">
              Awaiting payment
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Overdue</CardTitle>
            <AlertCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{formatCurrency(totalOverdue)}</div>
            <p className="text-xs text-muted-foreground">
              Past due amount
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Plans</CardTitle>
            <CreditCard className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{subscriptions.filter(s => s.status === 'active').length}</div>
            <p className="text-xs text-muted-foreground">
              Current subscriptions
            </p>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Current Subscriptions */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="h-5 w-5" />
              Current Subscriptions
            </CardTitle>
            <CardDescription>
              Your active subscription plans and billing information
            </CardDescription>
          </CardHeader>
          <CardContent>
            {isLoading ? (
              <div className="flex items-center justify-center py-8">
                <Loader2 className="h-6 w-6 animate-spin mr-2" />
                Loading subscriptions...
              </div>
            ) : subscriptions.length === 0 ? (
              <div className="text-center py-8 text-muted-foreground">
                <CreditCard className="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p>No active subscriptions</p>
                <Button className="mt-4">
                  <Plus className="h-4 w-4 mr-2" />
                  Browse Plans
                </Button>
              </div>
            ) : (
              <div className="space-y-4">
                {subscriptions.map((subscription) => (
                  <div key={subscription.id} className="p-4 border rounded-lg">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <h3 className="font-medium">{subscription.name}</h3>
                        <p className="text-sm text-muted-foreground mt-1">
                          {formatCurrency(subscription.price)} / {subscription.billingCycle}
                        </p>
                        <div className="flex items-center gap-2 mt-2">
                          {getStatusIcon(subscription.status)}
                          <Badge variant={getStatusBadgeVariant(subscription.status)}>
                            {subscription.status.replace('_', ' ')}
                          </Badge>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="text-sm text-muted-foreground">Next billing</p>
                        <p className="text-sm font-medium">{formatDate(subscription.nextBillingDate)}</p>
                      </div>
                    </div>
                    
                    <div className="mt-4">
                      <h4 className="text-sm font-medium mb-2">Features included:</h4>
                      <div className="flex flex-wrap gap-1">
                        {subscription.features.map((feature, index) => (
                          <Badge key={index} variant="outline" className="text-xs">
                            {feature}
                          </Badge>
                        ))}
                      </div>
                    </div>

                    <div className="flex items-center gap-2 mt-4">
                      <Button variant="outline" size="sm">
                        <Eye className="h-4 w-4 mr-2" />
                        View Details
                      </Button>
                      {isClientAdmin && (
                        <Button variant="outline" size="sm">
                          Manage
                        </Button>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Payment Methods */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="h-5 w-5" />
              Payment Methods
            </CardTitle>
            <CardDescription>
              Manage your payment methods and billing preferences
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="p-4 border rounded-lg">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                      <CreditCard className="h-4 w-4 text-blue-600" />
                    </div>
                    <div>
                      <p className="font-medium">•••• •••• •••• 4242</p>
                      <p className="text-sm text-muted-foreground">Expires 12/25</p>
                    </div>
                  </div>
                  <Badge variant="default">Primary</Badge>
                </div>
              </div>

              <div className="p-4 border rounded-lg">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                      <CreditCard className="h-4 w-4 text-gray-600" />
                    </div>
                    <div>
                      <p className="font-medium">•••• •••• •••• 1234</p>
                      <p className="text-sm text-muted-foreground">Expires 08/26</p>
                    </div>
                  </div>
                  <Badge variant="outline">Backup</Badge>
                </div>
              </div>

              <Button variant="outline" className="w-full">
                <Plus className="h-4 w-4 mr-2" />
                Add Payment Method
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Invoices */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText className="h-5 w-5" />
            Invoice History
          </CardTitle>
          <CardDescription>
            View and download your invoices and payment history
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Invoice #</TableHead>
                  <TableHead>Description</TableHead>
                  <TableHead>Amount</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Issue Date</TableHead>
                  <TableHead>Due Date</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {invoices.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-8 text-muted-foreground">
                      No invoices found
                    </TableCell>
                  </TableRow>
                ) : (
                  invoices.map((invoice) => (
                    <TableRow key={invoice.id}>
                      <TableCell className="font-medium">{invoice.number}</TableCell>
                      <TableCell>{invoice.description}</TableCell>
                      <TableCell>{formatCurrency(invoice.amount)}</TableCell>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          {getStatusIcon(invoice.status)}
                          <Badge variant={getStatusBadgeVariant(invoice.status)}>
                            {invoice.status}
                          </Badge>
                        </div>
                      </TableCell>
                      <TableCell>{formatDate(invoice.issueDate)}</TableCell>
                      <TableCell>{formatDate(invoice.dueDate)}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex items-center justify-end gap-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4 mr-2" />
                            View
                          </Button>
                          <Button variant="outline" size="sm">
                            <Download className="h-4 w-4 mr-2" />
                            Download
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}