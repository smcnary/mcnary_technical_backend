'use client';

import React, { useState, useRef, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { 
  CheckCircle, 
  X, 
  RotateCcw, 
  Download,
  AlertCircle
} from 'lucide-react';
import { format } from 'date-fns';

interface DocumentSignatureProps {
  documentId: string;
  documentTitle: string;
  onSigned?: () => void;
  onCancel?: () => void;
}

interface SignatureData {
  signatureData?: string;
  signatureImage?: string;
  comments?: string;
}

export default function DocumentSignature({ 
  documentId, 
  documentTitle, 
  onSigned, 
  onCancel 
}: DocumentSignatureProps) {
  const [isDrawing, setIsDrawing] = useState(false);
  const [signatureData, setSignatureData] = useState<string>('');
  const [comments, setComments] = useState('');
  const [isSigning, setIsSigning] = useState(false);
  const [error, setError] = useState<string | null>(null);
  
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const signatureRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Set canvas size
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * window.devicePixelRatio;
    canvas.height = rect.height * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

    // Set drawing styles
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
  }, []);

  const startDrawing = (e: React.MouseEvent<HTMLCanvasElement>) => {
    setIsDrawing(true);
    const canvas = canvasRef.current;
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.beginPath();
    ctx.moveTo(x, y);
  };

  const draw = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (!isDrawing) return;

    const canvas = canvasRef.current;
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.lineTo(x, y);
    ctx.stroke();
  };

  const stopDrawing = () => {
    setIsDrawing(false);
    saveSignature();
  };

  const saveSignature = () => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    // Create a copy of the signature for display
    const signatureCanvas = signatureRef.current;
    if (signatureCanvas) {
      const signatureCtx = signatureCanvas.getContext('2d');
      if (signatureCtx) {
        signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        signatureCtx.drawImage(canvas, 0, 0);
      }
    }

    // Save signature data
    const dataURL = canvas.toDataURL('image/png');
    setSignatureData(dataURL);
  };

  const clearSignature = () => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    setSignatureData('');
  };

  const handleSign = async () => {
    if (!signatureData.trim()) {
      setError('Please provide a signature');
      return;
    }

    setIsSigning(true);
    setError(null);

    try {
      const response = await fetch(`/api/v1/documents/${documentId}/sign`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          signature_data: signatureData,
          signature_image: signatureData,
          comments: comments.trim() || null,
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to sign document');
      }

      const result = await response.json();
      console.log('Document signed successfully:', result);
      
      onSigned?.();
    } catch (error) {
      console.error('Error signing document:', error);
      setError(error instanceof Error ? error.message : 'Failed to sign document');
    } finally {
      setIsSigning(false);
    }
  };

  const handleCancel = () => {
    clearSignature();
    setComments('');
    setError(null);
    onCancel?.();
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <CheckCircle className="w-5 h-5 text-blue-600" />
            Sign Document
          </CardTitle>
          <p className="text-muted-foreground">
            Please review and sign the document: <strong>{documentTitle}</strong>
          </p>
        </CardHeader>
        <CardContent className="space-y-6">
          {error && (
            <div className="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-md">
              <AlertCircle className="w-4 h-4 text-red-600" />
              <span className="text-red-800">{error}</span>
            </div>
          )}

          {/* Document Preview */}
          <div className="border rounded-lg p-4 bg-gray-50">
            <h4 className="font-medium mb-2">Document Preview</h4>
            <div className="text-sm text-muted-foreground">
              Document ID: {documentId}
            </div>
            <div className="text-sm text-muted-foreground">
              Signing Date: {format(new Date(), 'MMMM d, yyyy')}
            </div>
          </div>

          {/* Signature Canvas */}
          <div className="space-y-4">
            <Label htmlFor="signature">Your Signature</Label>
            <div className="border-2 border-dashed border-gray-300 rounded-lg p-4">
              <canvas
                ref={canvasRef}
                className="w-full h-32 border border-gray-200 rounded cursor-crosshair"
                onMouseDown={startDrawing}
                onMouseMove={draw}
                onMouseUp={stopDrawing}
                onMouseLeave={stopDrawing}
                style={{ touchAction: 'none' }}
              />
              <div className="flex justify-between items-center mt-2">
                <p className="text-sm text-muted-foreground">
                  Draw your signature above
                </p>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={clearSignature}
                  disabled={!signatureData}
                >
                  <RotateCcw className="w-4 h-4 mr-1" />
                  Clear
                </Button>
              </div>
            </div>
          </div>

          {/* Signature Preview */}
          {signatureData && (
            <div className="space-y-2">
              <Label>Signature Preview</Label>
              <div className="border rounded-lg p-4 bg-white">
                <canvas
                  ref={signatureRef}
                  className="w-full h-16 border border-gray-200 rounded"
                  style={{ maxWidth: '300px' }}
                />
              </div>
            </div>
          )}

          {/* Comments */}
          <div className="space-y-2">
            <Label htmlFor="comments">Comments (Optional)</Label>
            <Textarea
              id="comments"
              placeholder="Add any comments about this signature..."
              value={comments}
              onChange={(e) => setComments(e.target.value)}
              rows={3}
            />
          </div>

          {/* Legal Notice */}
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 className="font-medium text-blue-900 mb-2">Legal Notice</h4>
            <p className="text-sm text-blue-800">
              By signing this document, you acknowledge that you have read, understood, 
              and agree to be bound by the terms and conditions contained herein. 
              Your electronic signature has the same legal effect as a handwritten signature.
            </p>
          </div>

          {/* Actions */}
          <div className="flex justify-between items-center pt-4 border-t">
            <Button
              variant="outline"
              onClick={handleCancel}
              disabled={isSigning}
            >
              <X className="w-4 h-4 mr-2" />
              Cancel
            </Button>
            
            <div className="flex gap-2">
              <Button
                variant="outline"
                onClick={() => {
                  const link = document.createElement('a');
                  link.download = `document-${documentId}.pdf`;
                  link.href = `/api/v1/documents/${documentId}/download`;
                  link.click();
                }}
              >
                <Download className="w-4 h-4 mr-2" />
                Download
              </Button>
              
              <Button
                onClick={handleSign}
                disabled={!signatureData.trim() || isSigning}
              >
                {isSigning ? (
                  <>
                    <div className="w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    Signing...
                  </>
                ) : (
                  <>
                    <CheckCircle className="w-4 h-4 mr-2" />
                    Sign Document
                  </>
                )}
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
