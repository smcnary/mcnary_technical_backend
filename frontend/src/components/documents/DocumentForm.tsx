'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { 
  Save, 
  Send, 
  FileText, 
  Upload,
  X,
  Plus
} from 'lucide-react';
import { toast } from 'sonner';

interface DocumentFormProps {
  documentId?: string;
  clientId?: string;
  templateId?: string;
  onSave?: (document: any) => void;
  onCancel?: () => void;
}

interface Client {
  id: string;
  name: string;
}

interface DocumentTemplate {
  id: string;
  name: string;
  description?: string;
  type: string;
  content: string;
  variables: Record<string, any>;
  signatureFields: any[];
}

interface SignatureField {
  id: string;
  name: string;
  type: 'signature' | 'initial' | 'date' | 'text';
  required: boolean;
  signerEmail?: string;
  x?: number;
  y?: number;
  width?: number;
  height?: number;
}

export default function DocumentForm({ 
  documentId, 
  clientId, 
  templateId, 
  onSave, 
  onCancel 
}: DocumentFormProps) {
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [clients, setClients] = useState<Client[]>([]);
  const [templates, setTemplates] = useState<DocumentTemplate[]>([]);
  const [signatureFields, setSignatureFields] = useState<SignatureField[]>([]);
  
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    content: '',
    type: 'contract',
    clientId: clientId || '',
    templateId: templateId || '',
    requiresSignature: true,
    expiresAt: '',
    metadata: {},
    templateVariables: {},
  });

  useEffect(() => {
    fetchClients();
    fetchTemplates();
    
    if (documentId) {
      fetchDocument();
    }
  }, [documentId]);

  useEffect(() => {
    if (formData.templateId) {
      const template = templates.find(t => t.id === formData.templateId);
      if (template) {
        setFormData(prev => ({
          ...prev,
          content: template.content,
          type: template.type,
        }));
        
        // Set signature fields from template
        if (template.signatureFields) {
          setSignatureFields(template.signatureFields);
        }
      }
    }
  }, [formData.templateId, templates]);

  const fetchClients = async () => {
    try {
      const response = await fetch('/api/v1/clients', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });
      
      if (response.ok) {
        const data = await response.json();
        setClients(data.clients || []);
      }
    } catch (error) {
      console.error('Error fetching clients:', error);
    }
  };

  const fetchTemplates = async () => {
    try {
      const response = await fetch('/api/v1/document-templates', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });
      
      if (response.ok) {
        const data = await response.json();
        setTemplates(data.templates || []);
      }
    } catch (error) {
      console.error('Error fetching templates:', error);
    }
  };

  const fetchDocument = async () => {
    if (!documentId) return;
    
    try {
      setLoading(true);
      const response = await fetch(`/api/v1/documents/${documentId}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });
      
      if (response.ok) {
        const document = await response.json();
        setFormData({
          title: document.title || '',
          description: document.description || '',
          content: document.content || '',
          type: document.type || 'contract',
          clientId: document.client?.id || '',
          templateId: document.template?.id || '',
          requiresSignature: document.requiresSignature ?? true,
          expiresAt: document.expiresAt ? new Date(document.expiresAt).toISOString().split('T')[0] : '',
          metadata: document.metadata || {},
          templateVariables: document.metadata?.templateVariables || {},
        });
        
        if (document.signatureFields) {
          setSignatureFields(document.signatureFields);
        }
      }
    } catch (error) {
      console.error('Error fetching document:', error);
      toast.error('Failed to load document');
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (field: string, value: any) => {
    setFormData(prev => ({
      ...prev,
      [field]: value,
    }));
  };

  const addSignatureField = () => {
    const newField: SignatureField = {
      id: `field_${Date.now()}`,
      name: '',
      type: 'signature',
      required: true,
      signerEmail: '',
    };
    
    setSignatureFields(prev => [...prev, newField]);
  };

  const updateSignatureField = (index: number, field: string, value: any) => {
    setSignatureFields(prev => prev.map((item, i) => 
      i === index ? { ...item, [field]: value } : item
    ));
  };

  const removeSignatureField = (index: number) => {
    setSignatureFields(prev => prev.filter((_, i) => i !== index));
  };

  const handleSave = async (sendForSignature = false) => {
    if (!formData.title.trim()) {
      toast.error('Please enter a document title');
      return;
    }
    
    if (!formData.clientId) {
      toast.error('Please select a client');
      return;
    }

    try {
      setSaving(true);
      
      const payload = {
        ...formData,
        signatureFields: signatureFields.length > 0 ? signatureFields : undefined,
        expiresAt: formData.expiresAt || null,
      };

      const url = documentId 
        ? `/api/v1/documents/${documentId}`
        : '/api/v1/documents';
      
      const method = documentId ? 'PUT' : 'POST';

      const response = await fetch(url, {
        method,
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to save document');
      }

      const result = await response.json();
      toast.success(sendForSignature ? 'Document sent for signature!' : 'Document saved successfully!');
      
      onSave?.(result.document);
      
      // If sending for signature, do that too
      if (sendForSignature && result.document?.id) {
        await sendDocumentForSignature(result.document.id);
      }
      
    } catch (error) {
      console.error('Error saving document:', error);
      toast.error(error instanceof Error ? error.message : 'Failed to save document');
    } finally {
      setSaving(false);
    }
  };

  const sendDocumentForSignature = async (docId: string) => {
    try {
      const response = await fetch(`/api/v1/documents/${docId}/send-for-signature`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to send document for signature');
      }

      toast.success('Document sent for signature!');
    } catch (error) {
      console.error('Error sending document for signature:', error);
      toast.error(error instanceof Error ? error.message : 'Failed to send document for signature');
    }
  };

  const selectedTemplate = templates.find(t => t.id === formData.templateId);

  if (loading) {
    return (
      <Card>
        <CardContent className="p-6">
          <div className="animate-pulse space-y-4">
            <div className="h-4 bg-gray-200 rounded w-1/4"></div>
            <div className="h-10 bg-gray-200 rounded"></div>
            <div className="h-4 bg-gray-200 rounded w-1/3"></div>
            <div className="h-20 bg-gray-200 rounded"></div>
          </div>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText className="w-5 h-5" />
            {documentId ? 'Edit Document' : 'Create New Document'}
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-6">
          {/* Basic Information */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="title">Document Title *</Label>
              <Input
                id="title"
                value={formData.title}
                onChange={(e) => handleInputChange('title', e.target.value)}
                placeholder="Enter document title"
              />
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="type">Document Type</Label>
              <Select value={formData.type} onValueChange={(value) => handleInputChange('type', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Select type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="contract">Contract</SelectItem>
                  <SelectItem value="agreement">Agreement</SelectItem>
                  <SelectItem value="proposal">Proposal</SelectItem>
                  <SelectItem value="invoice">Invoice</SelectItem>
                  <SelectItem value="report">Report</SelectItem>
                  <SelectItem value="other">Other</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="description">Description</Label>
            <Textarea
              id="description"
              value={formData.description}
              onChange={(e) => handleInputChange('description', e.target.value)}
              placeholder="Enter document description"
              rows={3}
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="client">Client *</Label>
              <Select value={formData.clientId} onValueChange={(value) => handleInputChange('clientId', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Select client" />
                </SelectTrigger>
                <SelectContent>
                  {clients.map((client) => (
                    <SelectItem key={client.id} value={client.id}>
                      {client.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="template">Template (Optional)</Label>
              <Select value={formData.templateId} onValueChange={(value) => handleInputChange('templateId', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Select template" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="">No Template</SelectItem>
                  {templates.map((template) => (
                    <SelectItem key={template.id} value={template.id}>
                      {template.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>

          {/* Template Variables */}
          {selectedTemplate && selectedTemplate.variables && (
            <div className="space-y-4">
              <h4 className="font-medium">Template Variables</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {Object.keys(selectedTemplate.variables).map((variable) => (
                  <div key={variable} className="space-y-2">
                    <Label htmlFor={variable}>{variable}</Label>
                    <Input
                      id={variable}
                      value={formData.templateVariables[variable] || ''}
                      onChange={(e) => handleInputChange('templateVariables', {
                        ...formData.templateVariables,
                        [variable]: e.target.value,
                      })}
                      placeholder={`Enter ${variable}`}
                    />
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Document Content */}
          <div className="space-y-2">
            <Label htmlFor="content">Document Content</Label>
            <Textarea
              id="content"
              value={formData.content}
              onChange={(e) => handleInputChange('content', e.target.value)}
              placeholder="Enter document content or use a template"
              rows={10}
            />
          </div>

          {/* Signature Fields */}
          {formData.requiresSignature && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <h4 className="font-medium">Signature Fields</h4>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  onClick={addSignatureField}
                >
                  <Plus className="w-4 h-4 mr-1" />
                  Add Field
                </Button>
              </div>
              
              {signatureFields.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                  No signature fields defined. Click "Add Field" to add signature requirements.
                </p>
              ) : (
                <div className="space-y-3">
                  {signatureFields.map((field, index) => (
                    <div key={field.id} className="border rounded-lg p-4 space-y-3">
                      <div className="flex items-center justify-between">
                        <h5 className="font-medium">Signature Field {index + 1}</h5>
                        <Button
                          type="button"
                          variant="outline"
                          size="sm"
                          onClick={() => removeSignatureField(index)}
                        >
                          <X className="w-4 h-4" />
                        </Button>
                      </div>
                      
                      <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div className="space-y-2">
                          <Label>Field Name</Label>
                          <Input
                            value={field.name}
                            onChange={(e) => updateSignatureField(index, 'name', e.target.value)}
                            placeholder="e.g., Client Signature"
                          />
                        </div>
                        
                        <div className="space-y-2">
                          <Label>Field Type</Label>
                          <Select 
                            value={field.type} 
                            onValueChange={(value) => updateSignatureField(index, 'type', value)}
                          >
                            <SelectTrigger>
                              <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem value="signature">Signature</SelectItem>
                              <SelectItem value="initial">Initial</SelectItem>
                              <SelectItem value="date">Date</SelectItem>
                              <SelectItem value="text">Text</SelectItem>
                            </SelectContent>
                          </Select>
                        </div>
                        
                        <div className="space-y-2">
                          <Label>Signer Email</Label>
                          <Input
                            value={field.signerEmail || ''}
                            onChange={(e) => updateSignatureField(index, 'signerEmail', e.target.value)}
                            placeholder="signer@example.com"
                          />
                        </div>
                      </div>
                      
                      <div className="flex items-center space-x-2">
                        <Checkbox
                          id={`required-${index}`}
                          checked={field.required}
                          onCheckedChange={(checked) => updateSignatureField(index, 'required', checked)}
                        />
                        <Label htmlFor={`required-${index}`}>Required</Label>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          )}

          {/* Options */}
          <div className="space-y-4">
            <h4 className="font-medium">Options</h4>
            
            <div className="flex items-center space-x-2">
              <Checkbox
                id="requiresSignature"
                checked={formData.requiresSignature}
                onCheckedChange={(checked) => handleInputChange('requiresSignature', checked)}
              />
              <Label htmlFor="requiresSignature">Requires signature</Label>
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="expiresAt">Expiration Date (Optional)</Label>
              <Input
                id="expiresAt"
                type="date"
                value={formData.expiresAt}
                onChange={(e) => handleInputChange('expiresAt', e.target.value)}
              />
            </div>
          </div>

          {/* Actions */}
          <div className="flex justify-between items-center pt-4 border-t">
            <Button
              variant="outline"
              onClick={onCancel}
              disabled={saving}
            >
              Cancel
            </Button>
            
            <div className="flex gap-2">
              <Button
                variant="outline"
                onClick={() => handleSave(false)}
                disabled={saving}
              >
                {saving ? (
                  <>
                    <div className="w-4 h-4 mr-2 border-2 border-gray-600 border-t-transparent rounded-full animate-spin" />
                    Saving...
                  </>
                ) : (
                  <>
                    <Save className="w-4 h-4 mr-2" />
                    Save Draft
                  </>
                )}
              </Button>
              
              {formData.requiresSignature && (
                <Button
                  onClick={() => handleSave(true)}
                  disabled={saving}
                >
                  {saving ? (
                    <>
                      <div className="w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin" />
                      Sending...
                    </>
                  ) : (
                    <>
                      <Send className="w-4 h-4 mr-2" />
                      Save & Send for Signature
                    </>
                  )}
                </Button>
              )}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
