'use client';

import React, { useState } from 'react';
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
  Info
} from 'lucide-react';

interface GoogleSheetsImportModalProps {
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

export default function GoogleSheetsImportModal({ isOpen, onClose, onSuccess }: GoogleSheetsImportModalProps) {
  const { importLeadsFromGoogleSheets, clients, getLoadingState } = useData();
  const [spreadsheetUrl, setSpreadsheetUrl] = useState('');
  const [range, setRange] = useState('A:Z');
  const [clientId, setClientId] = useState('');
  const [overwriteExisting, setOverwriteExisting] = useState(false);
  const [isImporting, setIsImporting] = useState(false);
  const [importResult, setImportResult] = useState<any>(null);
  const [errors, setErrors] = useState<string[]>([]);

  const isLoading = getLoadingState('leads');

  const handleImport = async () => {
    if (!spreadsheetUrl) {
      setErrors(['Please provide a Google Sheets URL.']);
      return;
    }

    setIsImporting(true);
    setErrors([]);
    setImportResult(null);

    try {
      const result = await importLeadsFromGoogleSheets({
        spreadsheet_url: spreadsheetUrl,
        range: range || 'A:Z',
        client_id: clientId || undefined,
        overwrite_existing: overwriteExisting,
      });

      setImportResult(result);
      
      if (onSuccess) {
        onSuccess(result);
      }

      // Close modal after successful import
      setTimeout(() => {
        handleClose();
      }, 3000);

    } catch (error) {
      setErrors([error instanceof Error ? error.message : 'Import failed']);
    } finally {
      setIsImporting(false);
    }
  };

  const handleClose = () => {
    setSpreadsheetUrl('');
    setRange('A:Z');
    setClientId('');
    setOverwriteExisting(false);
    setImportResult(null);
    setErrors([]);
    onClose();
  };

  const validateGoogleSheetsUrl = (url: string): boolean => {
    const patterns = [
      /^https:\/\/docs\.google\.com\/spreadsheets\/d\/[a-zA-Z0-9-_]+/,
      /^https:\/\/docs\.google\.com\/spreadsheets\/d\/[a-zA-Z0-9-_]+\/edit/,
      /^https:\/\/docs\.google\.com\/spreadsheets\/d\/[a-zA-Z0-9-_]+\/edit#gid=\d+/
    ];
    
    return patterns.some(pattern => pattern.test(url));
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <Card className="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
          <div>
            <CardTitle className="text-xl font-semibold">Import Leads from Google Sheets</CardTitle>
            <CardDescription>
              Connect to Google Sheets and import leads automatically
            </CardDescription>
          </div>
          <Button variant="ghost" size="sm" onClick={handleClose}>
            <X className="h-4 w-4" />
          </Button>
        </CardHeader>

        <CardContent className="space-y-6">
          {/* Instructions */}
          <div className="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <Info className="h-5 w-5 text-blue-600 mt-0.5" />
            <div className="space-y-2 text-sm text-blue-800 dark:text-blue-200">
              <p className="font-semibold">Setup Instructions:</p>
              <ol className="list-decimal list-inside space-y-1">
                <li>Make sure your Google Sheet has headers in the first row</li>
                <li>Include columns like: Name, Email, Phone, Firm, Website, City, State, etc.</li>
                <li>Make sure the sheet is publicly accessible (Anyone with the link can view)</li>
                <li>Copy the Google Sheets URL and paste it below</li>
              </ol>
            </div>
          </div>

          {/* Google Sheets URL */}
          <div className="space-y-2">
            <Label htmlFor="spreadsheet-url">Google Sheets URL *</Label>
            <Input
              id="spreadsheet-url"
              type="url"
              placeholder="https://docs.google.com/spreadsheets/d/..."
              value={spreadsheetUrl}
              onChange={(e) => setSpreadsheetUrl(e.target.value)}
              className={!spreadsheetUrl || validateGoogleSheetsUrl(spreadsheetUrl) ? '' : 'border-red-300'}
            />
            {spreadsheetUrl && !validateGoogleSheetsUrl(spreadsheetUrl) && (
              <p className="text-sm text-red-600">Please enter a valid Google Sheets URL</p>
            )}
          </div>

          {/* Range */}
          <div className="space-y-2">
            <Label htmlFor="range">Data Range (Optional)</Label>
            <Input
              id="range"
              placeholder="A:Z (default: all columns)"
              value={range}
              onChange={(e) => setRange(e.target.value)}
            />
            <p className="text-sm text-gray-600 dark:text-gray-400">
              Specify the range to import (e.g., A1:Z100, Sheet1!A:Z)
            </p>
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

          {/* Options */}
          <div className="flex items-center space-x-2">
            <Checkbox
              id="overwrite"
              checked={overwriteExisting}
              onCheckedChange={(checked) => setOverwriteExisting(checked as boolean)}
            />
            <Label htmlFor="overwrite" className="text-sm">
              Overwrite existing leads (by email)
            </Label>
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

          {/* Import Progress */}
          {isImporting && (
            <div className="space-y-2">
              <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <Upload className="h-4 w-4 animate-spin" />
                Importing leads from Google Sheets...
              </div>
              <Progress value={undefined} className="w-full" />
            </div>
          )}

          {/* Import Result */}
          {importResult && (
            <Alert className="border-green-200 bg-green-50 dark:bg-green-950/20">
              <CheckCircle className="h-4 w-4 text-green-600" />
              <AlertDescription className="text-green-800 dark:text-green-200">
                <div className="space-y-1">
                  <p className="font-semibold">{importResult.message}</p>
                  <p>Imported: {importResult.imported} leads</p>
                  {importResult.updated > 0 && (
                    <p>Updated: {importResult.updated} leads</p>
                  )}
                  {importResult.skipped > 0 && (
                    <p>Skipped: {importResult.skipped} leads (already exist)</p>
                  )}
                  <p>Total processed: {importResult.total_processed} rows</p>
                  {importResult.errors && importResult.errors.length > 0 && (
                    <div className="mt-2">
                      <p className="font-semibold">Errors:</p>
                      <ul className="list-disc list-inside text-sm">
                        {importResult.errors.map((error: string, index: number) => (
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
            <Button variant="outline" onClick={handleClose} disabled={isImporting}>
              Cancel
            </Button>
            <Button 
              onClick={handleImport} 
              disabled={!spreadsheetUrl || isImporting || !validateGoogleSheetsUrl(spreadsheetUrl)}
              className="min-w-[140px]"
            >
              {isImporting ? (
                <>
                  <Upload className="h-4 w-4 mr-2 animate-spin" />
                  Importing...
                </>
              ) : (
                <>
                  <FileText className="h-4 w-4 mr-2" />
                  Import from Sheets
                </>
              )}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

