'use client';

import React, { useState, useRef } from 'react';
import { useData } from '../../hooks/useData';
import { Button } from '../ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Checkbox } from '../ui/checkbox';
import { Alert, AlertDescription } from '../ui/alert';
import { Progress } from '../ui/progress';
import { 
  Upload, 
  FileText, 
  CheckCircle, 
  AlertCircle, 
  X,
  Download,
  Info,
  Database
} from 'lucide-react';

interface LeadgenImportModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess?: (result: {
    message: string;
    imported: number;
    updated: number;
    skipped: number;
    total_processed: number;
    errors?: string[];
  }) => void;
}

export default function LeadgenImportModal({ isOpen, onClose, onSuccess }: LeadgenImportModalProps) {
  const { importLeadgenData, clients, getLoadingState } = useData();
  const [file, setFile] = useState<File | null>(null);
  const [jsonData, setJsonData] = useState<string>('');
  const [clientId, setClientId] = useState<string>('');
  const [isUploading, setIsUploading] = useState(false);
  const [uploadResult, setUploadResult] = useState<any>(null);
  const [errors, setErrors] = useState<string[]>([]);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const isLoading = getLoadingState('leads');

  const handleFileSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFile = event.target.files?.[0];
    if (selectedFile) {
      if (selectedFile.type !== 'application/json' && !selectedFile.name.endsWith('.json')) {
        setErrors(['Please select a valid JSON file.']);
        return;
      }
      
      setFile(selectedFile);
      setErrors([]);
      
      // Read file content
      const reader = new FileReader();
      reader.onload = (e) => {
        const content = e.target?.result as string;
        setJsonData(content);
      };
      reader.readAsText(selectedFile);
    }
  };

  const handleUpload = async () => {
    if (!jsonData) {
      setErrors(['Please select a JSON file first.']);
      return;
    }

    setIsUploading(true);
    setErrors([]);
    setUploadResult(null);

    try {
      // Parse JSON data
      const leadgenData = JSON.parse(jsonData);
      
      // Ensure it's an array
      const leads = Array.isArray(leadgenData) ? leadgenData : [leadgenData];

      const result = await importLeadgenData(leads, {
        clientId: clientId || undefined,
      });

      setUploadResult(result);
      
      if (onSuccess) {
        onSuccess(result);
      }

      // Close modal after successful upload
      setTimeout(() => {
        handleClose();
      }, 2000);

    } catch (error) {
      if (error instanceof SyntaxError) {
        setErrors(['Invalid JSON format. Please check your file.']);
      } else {
        setErrors([error instanceof Error ? error.message : 'Upload failed']);
      }
    } finally {
      setIsUploading(false);
    }
  };

  const handleClose = () => {
    setFile(null);
    setJsonData('');
    setClientId('');
    setUploadResult(null);
    setErrors([]);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
    onClose();
  };

  const downloadSample = () => {
    const sampleData = [
      {
        "lead_id": "sample-1",
        "legal_entity": {
          "name": "Sample Law Firm"
        },
        "vertical": "local_services",
        "website": "https://samplelaw.com",
        "emails": [
          {
            "value": "contact@samplelaw.com",
            "type": "generic"
          }
        ],
        "phones": [
          {
            "value": "(555) 123-4567",
            "type": "main"
          }
        ],
        "address": {
          "city": "New York",
          "region": "NY",
          "postal": "10001"
        },
        "reviews": {
          "rating": 4.5,
          "count": 25
        },
        "lead_score": 85,
        "tags": ["Personal Injury", "Criminal Defense"]
      }
    ];
    
    const blob = new Blob([JSON.stringify(sampleData, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'leadgen_sample.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <Card className="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
          <div>
            <CardTitle className="text-xl font-semibold">Import Leads from Leadgen Service</CardTitle>
            <CardDescription>
              Upload leadgen JSON data to import leads with statistics tracking
            </CardDescription>
          </div>
          <Button variant="ghost" size="sm" onClick={handleClose}>
            <X className="h-4 w-4" />
          </Button>
        </CardHeader>

        <CardContent className="space-y-6">
          {/* Sample Download */}
          <div className="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-950/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div className="flex items-center gap-2">
              <Info className="h-4 w-4 text-blue-600" />
              <span className="text-sm text-blue-800 dark:text-blue-200">
                Download sample leadgen JSON format
              </span>
            </div>
            <Button variant="outline" size="sm" onClick={downloadSample}>
              <Download className="h-4 w-4 mr-2" />
              Download Sample
            </Button>
          </div>

          {/* File Upload */}
          <div className="space-y-2">
            <Label htmlFor="json-file">Leadgen JSON File</Label>
            <div className="flex items-center gap-4">
              <Input
                ref={fileInputRef}
                id="json-file"
                type="file"
                accept=".json"
                onChange={handleFileSelect}
                className="flex-1"
              />
              {file && (
                <div className="flex items-center gap-2 text-sm text-green-600">
                  <Database className="h-4 w-4" />
                  {file.name}
                </div>
              )}
            </div>
          </div>

          {/* Client Selection */}
          <div className="space-y-2">
            <Label htmlFor="client">Assign to Client (Optional)</Label>
            <select
              id="client"
              value={clientId}
              onChange={(e) => setClientId(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            >
              <option value="">No client assignment</option>
              {clients.map((client) => (
                <option key={client.id} value={client.id}>
                  {client.name}
                </option>
              ))}
            </select>
          </div>

          {/* Errors */}
          {errors.length > 0 && (
            <Alert variant="destructive">
              <AlertCircle className="h-4 w-4" />
              <AlertDescription>
                <ul className="list-disc list-inside space-y-1">
                  {errors.map((error, index) => (
                    <li key={index}>{error}</li>
                  ))}
                </ul>
              </AlertDescription>
            </Alert>
          )}

          {/* Upload Progress */}
          {isUploading && (
            <div className="space-y-2">
              <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <Upload className="h-4 w-4 animate-spin" />
                Uploading and processing leadgen data...
              </div>
              <Progress value={undefined} className="w-full" />
            </div>
          )}

          {/* Upload Result */}
          {uploadResult && (
            <Alert className="border-green-200 bg-green-50 dark:bg-green-950/20">
              <CheckCircle className="h-4 w-4 text-green-600" />
              <AlertDescription className="text-green-800 dark:text-green-200">
                <div className="space-y-1">
                  <p className="font-semibold">{uploadResult.message}</p>
                  <p>Imported: {uploadResult.imported} leads</p>
                  {uploadResult.updated > 0 && (
                    <p>Updated: {uploadResult.updated} leads</p>
                  )}
                  {uploadResult.skipped > 0 && (
                    <p>Skipped: {uploadResult.skipped} leads (already exist)</p>
                  )}
                  <p>Total processed: {uploadResult.total_processed}</p>
                  {uploadResult.errors && uploadResult.errors.length > 0 && (
                    <div className="mt-2">
                      <p className="font-semibold">Errors:</p>
                      <ul className="list-disc list-inside text-sm">
                        {uploadResult.errors.map((error: string, index: number) => (
                          <li key={index}>{error}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                </div>
              </AlertDescription>
            </Alert>
          )}

          {/* Actions */}
          <div className="flex justify-end gap-3 pt-4 border-t">
            <Button variant="outline" onClick={handleClose} disabled={isUploading}>
              Cancel
            </Button>
            <Button 
              onClick={handleUpload} 
              disabled={!jsonData || isUploading}
              className="min-w-[120px]"
            >
              {isUploading ? (
                <>
                  <Upload className="h-4 w-4 mr-2 animate-spin" />
                  Uploading...
                </>
              ) : (
                <>
                  <Database className="h-4 w-4 mr-2" />
                  Import Leads
                </>
              )}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
