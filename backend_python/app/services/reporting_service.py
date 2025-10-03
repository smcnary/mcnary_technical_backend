"""
Reporting service for SEO audit results
"""

import json
import csv
from typing import List, Dict, Any, Optional
from datetime import datetime
from pathlib import Path
import logging
from io import StringIO
import base64

from app.models.audit import AuditRun, AuditFinding, Page, FindingSeverity, FindingCategory
from app.services.analysis_engine import FindingResult

logger = logging.getLogger(__name__)


class ReportGenerator:
    """Base class for report generators"""
    
    def generate(self, audit_run: AuditRun, findings: List[AuditFinding], pages: List[Page]) -> str:
        """Generate report content"""
        raise NotImplementedError


class HTMLReportGenerator(ReportGenerator):
    """HTML report generator"""
    
    def generate(self, audit_run: AuditRun, findings: List[AuditFinding], pages: List[Page]) -> str:
        """Generate HTML report"""
        
        # Calculate summary statistics
        summary = self._calculate_summary(findings, pages)
        
        html = f"""
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Audit Report - {audit_run.name}</title>
    <style>
        {self._get_css_styles()}
    </style>
</head>
<body>
    <div class="container">
        <header class="report-header">
            <h1>SEO Audit Report</h1>
            <div class="audit-info">
                <h2>{audit_run.name}</h2>
                <p><strong>Project:</strong> {audit_run.project.name if audit_run.project else 'Unknown'}</p>
                <p><strong>Website:</strong> {audit_run.project.website_url if audit_run.project else 'Unknown'}</p>
                <p><strong>Audit Date:</strong> {audit_run.created_at.strftime('%B %d, %Y at %I:%M %p')}</p>
                <p><strong>Pages Crawled:</strong> {audit_run.pages_crawled}</p>
                <p><strong>Pages Analyzed:</strong> {audit_run.pages_analyzed}</p>
            </div>
        </header>

        <section class="summary-section">
            <h2>Executive Summary</h2>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Overall Score</h3>
                    <div class="score {self._get_score_class(summary['overall_score'])}">
                        {summary['overall_score']:.0f}
                    </div>
                </div>
                <div class="summary-card">
                    <h3>Total Findings</h3>
                    <div class="finding-count">
                        {summary['total_findings']}
                    </div>
                </div>
                <div class="summary-card">
                    <h3>Critical Issues</h3>
                    <div class="critical-count">
                        {summary['critical_findings']}
                    </div>
                </div>
                <div class="summary-card">
                    <h3>High Priority</h3>
                    <div class="high-count">
                        {summary['high_findings']}
                    </div>
                </div>
            </div>
        </section>

        <section class="findings-section">
            <h2>Detailed Findings</h2>
            {self._generate_findings_html(findings)}
        </section>

        <section class="pages-section">
            <h2>Pages Analyzed</h2>
            {self._generate_pages_html(pages)}
        </section>

        <footer class="report-footer">
            <p>Report generated on {datetime.now().strftime('%B %d, %Y at %I:%M %p')}</p>
            <p>SEO Audit Tool v1.0</p>
        </footer>
    </div>
</body>
</html>
        """
        
        return html
    
    def _calculate_summary(self, findings: List[AuditFinding], pages: List[Page]) -> Dict[str, Any]:
        """Calculate summary statistics"""
        total_findings = len(findings)
        critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        # Calculate overall score (starting from 100 and subtracting penalties)
        overall_score = 100.0
        for finding in findings:
            if finding.severity == FindingSeverity.CRITICAL:
                overall_score -= 15
            elif finding.severity == FindingSeverity.HIGH:
                overall_score -= 10
            elif finding.severity == FindingSeverity.MEDIUM:
                overall_score -= 5
            elif finding.severity == FindingSeverity.LOW:
                overall_score -= 2
        
        overall_score = max(0, overall_score)
        
        return {
            'total_findings': total_findings,
            'critical_findings': critical_findings,
            'high_findings': high_findings,
            'medium_findings': medium_findings,
            'low_findings': low_findings,
            'overall_score': overall_score,
            'total_pages': len(pages)
        }
    
    def _get_score_class(self, score: float) -> str:
        """Get CSS class for score display"""
        if score >= 90:
            return "excellent"
        elif score >= 80:
            return "good"
        elif score >= 70:
            return "fair"
        elif score >= 60:
            return "poor"
        else:
            return "critical"
    
    def _generate_findings_html(self, findings: List[AuditFinding]) -> str:
        """Generate HTML for findings section"""
        if not findings:
            return "<p>No issues found during the audit.</p>"
        
        # Group findings by severity
        severity_groups = {
            FindingSeverity.CRITICAL: [],
            FindingSeverity.HIGH: [],
            FindingSeverity.MEDIUM: [],
            FindingSeverity.LOW: []
        }
        
        for finding in findings:
            severity_groups[finding.severity].append(finding)
        
        html = ""
        for severity in [FindingSeverity.CRITICAL, FindingSeverity.HIGH, FindingSeverity.MEDIUM, FindingSeverity.LOW]:
            if severity_groups[severity]:
                html += f"""
                <div class="severity-group">
                    <h3 class="severity-{severity.value}">{severity.value.title()} Issues ({len(severity_groups[severity])})</h3>
                """
                
                for finding in severity_groups[severity]:
                    html += f"""
                    <div class="finding-card">
                        <div class="finding-header">
                            <h4>{finding.title}</h4>
                            <span class="severity-badge {severity.value}">{severity.value}</span>
                        </div>
                        <div class="finding-content">
                            <p><strong>Description:</strong> {finding.description}</p>
                            <p><strong>Recommendation:</strong> {finding.recommendation}</p>
                            <p><strong>Impact:</strong> {finding.impact}</p>
                            {f'<p><strong>Element:</strong> {finding.element}</p>' if finding.element else ''}
                            {f'<p><strong>URL:</strong> <a href="{finding.url}" target="_blank">{finding.url}</a></p>' if finding.url else ''}
                        </div>
                    </div>
                    """
                
                html += "</div>"
        
        return html
    
    def _generate_pages_html(self, pages: List[Page]) -> str:
        """Generate HTML for pages section"""
        if not pages:
            return "<p>No pages were analyzed.</p>"
        
        html = """
        <div class="pages-table">
            <table>
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Findings</th>
                        <th>Word Count</th>
                        <th>Load Time</th>
                    </tr>
                </thead>
                <tbody>
        """
        
        for page in pages:
            html += f"""
                    <tr>
                        <td><a href="{page.url}" target="_blank">{page.url}</a></td>
                        <td>{page.title or 'No title'}</td>
                        <td><span class="status-badge {page.status.value}">{page.status.value}</span></td>
                        <td>{page.total_findings}</td>
                        <td>{page.word_count or 0}</td>
                        <td>{page.response_time or 0}ms</td>
                    </tr>
            """
        
        html += """
                </tbody>
            </table>
        </div>
        """
        
        return html
    
    def _get_css_styles(self) -> str:
        """Get CSS styles for the report"""
        return """
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .report-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .audit-info {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        
        .audit-info h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .audit-info p {
            margin: 0.5rem 0;
        }
        
        .summary-section {
            padding: 2rem;
            background: #f8f9fa;
        }
        
        .summary-section h2 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .summary-card h3 {
            margin-bottom: 1rem;
            color: #555;
        }
        
        .score {
            font-size: 3rem;
            font-weight: bold;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        .score.excellent { background: #27ae60; color: white; }
        .score.good { background: #3498db; color: white; }
        .score.fair { background: #f39c12; color: white; }
        .score.poor { background: #e74c3c; color: white; }
        .score.critical { background: #8e44ad; color: white; }
        
        .finding-count, .critical-count, .high-count {
            font-size: 2rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .findings-section, .pages-section {
            padding: 2rem;
        }
        
        .findings-section h2, .pages-section h2 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .severity-group {
            margin-bottom: 2rem;
        }
        
        .severity-critical, .severity-high, .severity-medium, .severity-low {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .severity-critical { background: #ffebee; border-left: 4px solid #f44336; }
        .severity-high { background: #fff3e0; border-left: 4px solid #ff9800; }
        .severity-medium { background: #f3e5f5; border-left: 4px solid #9c27b0; }
        .severity-low { background: #e8f5e8; border-left: 4px solid #4caf50; }
        
        .finding-card {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .finding-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .finding-header h4 {
            color: #2c3e50;
        }
        
        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .severity-badge.critical { background: #f44336; color: white; }
        .severity-badge.high { background: #ff9800; color: white; }
        .severity-badge.medium { background: #9c27b0; color: white; }
        .severity-badge.low { background: #4caf50; color: white; }
        
        .pages-table {
            overflow-x: auto;
        }
        
        .pages-table table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .pages-table th,
        .pages-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .pages-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .pages-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-badge.crawled { background: #4caf50; color: white; }
        .status-badge.analyzed { background: #2196f3; color: white; }
        .status-badge.failed { background: #f44336; color: white; }
        .status-badge.pending { background: #ff9800; color: white; }
        
        .report-footer {
            background: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .report-footer p {
            margin: 0.5rem 0;
        }
        """


class CSVReportGenerator(ReportGenerator):
    """CSV report generator"""
    
    def generate(self, audit_run: AuditRun, findings: List[AuditFinding], pages: List[Page]) -> str:
        """Generate CSV report"""
        output = StringIO()
        writer = csv.writer(output)
        
        # Write header
        writer.writerow([
            'Audit Name',
            'Project',
            'Website URL',
            'Audit Date',
            'Pages Crawled',
            'Pages Analyzed',
            'Total Findings',
            'Critical Findings',
            'High Findings',
            'Medium Findings',
            'Low Findings'
        ])
        
        # Write summary row
        summary = self._calculate_summary(findings, pages)
        writer.writerow([
            audit_run.name,
            audit_run.project.name if audit_run.project else 'Unknown',
            audit_run.project.website_url if audit_run.project else 'Unknown',
            audit_run.created_at.strftime('%Y-%m-%d %H:%M:%S'),
            audit_run.pages_crawled,
            audit_run.pages_analyzed,
            summary['total_findings'],
            summary['critical_findings'],
            summary['high_findings'],
            summary['medium_findings'],
            summary['low_findings']
        ])
        
        # Add empty row
        writer.writerow([])
        
        # Write findings header
        writer.writerow([
            'Finding ID',
            'Check Code',
            'Title',
            'Severity',
            'Category',
            'Description',
            'Recommendation',
            'Impact',
            'Element',
            'URL',
            'Status'
        ])
        
        # Write findings
        for finding in findings:
            writer.writerow([
                finding.id,
                finding.check_code,
                finding.title,
                finding.severity.value,
                finding.category.value,
                finding.description,
                finding.recommendation,
                finding.impact,
                finding.element,
                finding.url,
                finding.status
            ])
        
        # Add empty row
        writer.writerow([])
        
        # Write pages header
        writer.writerow([
            'Page ID',
            'URL',
            'Title',
            'Status',
            'Status Code',
            'Response Time',
            'Word Count',
            'Total Findings',
            'Critical Findings',
            'High Findings'
        ])
        
        # Write pages
        for page in pages:
            writer.writerow([
                page.id,
                page.url,
                page.title or '',
                page.status.value,
                page.status_code or 0,
                page.response_time or 0,
                page.word_count or 0,
                page.total_findings,
                page.critical_findings,
                page.high_findings
            ])
        
        return output.getvalue()
    
    def _calculate_summary(self, findings: List[AuditFinding], pages: List[Page]) -> Dict[str, Any]:
        """Calculate summary statistics"""
        total_findings = len(findings)
        critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        return {
            'total_findings': total_findings,
            'critical_findings': critical_findings,
            'high_findings': high_findings,
            'medium_findings': medium_findings,
            'low_findings': low_findings,
            'total_pages': len(pages)
        }


class JSONReportGenerator(ReportGenerator):
    """JSON report generator"""
    
    def generate(self, audit_run: AuditRun, findings: List[AuditFinding], pages: List[Page]) -> str:
        """Generate JSON report"""
        summary = self._calculate_summary(findings, pages)
        
        report_data = {
            "audit_info": {
                "id": str(audit_run.id),
                "name": audit_run.name,
                "description": audit_run.description,
                "project": {
                    "id": str(audit_run.project.id) if audit_run.project else None,
                    "name": audit_run.project.name if audit_run.project else None,
                    "website_url": audit_run.project.website_url if audit_run.project else None
                },
                "created_at": audit_run.created_at.isoformat(),
                "started_at": audit_run.started_at.isoformat() if audit_run.started_at else None,
                "finished_at": audit_run.finished_at.isoformat() if audit_run.finished_at else None,
                "state": audit_run.state.value,
                "pages_crawled": audit_run.pages_crawled,
                "pages_analyzed": audit_run.pages_analyzed
            },
            "summary": summary,
            "findings": [
                {
                    "id": str(finding.id),
                    "check_code": finding.check_code,
                    "check_name": finding.check_name,
                    "category": finding.category.value,
                    "severity": finding.severity.value,
                    "title": finding.title,
                    "description": finding.description,
                    "recommendation": finding.recommendation,
                    "impact": finding.impact,
                    "element": finding.element,
                    "attribute": finding.attribute,
                    "value": finding.value,
                    "expected_value": finding.expected_value,
                    "url": finding.url,
                    "status": finding.status,
                    "priority_score": finding.priority_score,
                    "created_at": finding.created_at.isoformat()
                }
                for finding in findings
            ],
            "pages": [
                {
                    "id": str(page.id),
                    "url": page.url,
                    "canonical_url": page.canonical_url,
                    "title": page.title,
                    "status": page.status.value,
                    "status_code": page.status_code,
                    "response_time": page.response_time,
                    "content_length": page.content_length,
                    "word_count": page.word_count,
                    "reading_time": page.reading_time,
                    "total_findings": page.total_findings,
                    "critical_findings": page.critical_findings,
                    "high_findings": page.high_findings,
                    "created_at": page.created_at.isoformat()
                }
                for page in pages
            ],
            "generated_at": datetime.now().isoformat()
        }
        
        return json.dumps(report_data, indent=2)
    
    def _calculate_summary(self, findings: List[AuditFinding], pages: List[Page]) -> Dict[str, Any]:
        """Calculate summary statistics"""
        total_findings = len(findings)
        critical_findings = len([f for f in findings if f.severity == FindingSeverity.CRITICAL])
        high_findings = len([f for f in findings if f.severity == FindingSeverity.HIGH])
        medium_findings = len([f for f in findings if f.severity == FindingSeverity.MEDIUM])
        low_findings = len([f for f in findings if f.severity == FindingSeverity.LOW])
        
        # Calculate overall score
        overall_score = 100.0
        for finding in findings:
            if finding.severity == FindingSeverity.CRITICAL:
                overall_score -= 15
            elif finding.severity == FindingSeverity.HIGH:
                overall_score -= 10
            elif finding.severity == FindingSeverity.MEDIUM:
                overall_score -= 5
            elif finding.severity == FindingSeverity.LOW:
                overall_score -= 2
        
        overall_score = max(0, overall_score)
        
        # Calculate category breakdown
        category_breakdown = {}
        for finding in findings:
            category = finding.category.value
            if category not in category_breakdown:
                category_breakdown[category] = {
                    "total": 0,
                    "critical": 0,
                    "high": 0,
                    "medium": 0,
                    "low": 0
                }
            
            category_breakdown[category]["total"] += 1
            category_breakdown[category][finding.severity.value] += 1
        
        return {
            "total_findings": total_findings,
            "critical_findings": critical_findings,
            "high_findings": high_findings,
            "medium_findings": medium_findings,
            "low_findings": low_findings,
            "overall_score": round(overall_score, 1),
            "total_pages": len(pages),
            "category_breakdown": category_breakdown
        }


class ReportingService:
    """Main reporting service"""
    
    def __init__(self):
        self.generators = {
            'html': HTMLReportGenerator(),
            'csv': CSVReportGenerator(),
            'json': JSONReportGenerator()
        }
    
    def generate_report(self, 
                       audit_run: AuditRun, 
                       findings: List[AuditFinding], 
                       pages: List[Page],
                       format_type: str = 'html') -> str:
        """Generate report in specified format"""
        
        if format_type not in self.generators:
            raise ValueError(f"Unsupported report format: {format_type}")
        
        generator = self.generators[format_type]
        return generator.generate(audit_run, findings, pages)
    
    def get_available_formats(self) -> List[str]:
        """Get list of available report formats"""
        return list(self.generators.keys())
    
    def generate_multi_format_report(self, 
                                   audit_run: AuditRun, 
                                   findings: List[AuditFinding], 
                                   pages: List[Page]) -> Dict[str, str]:
        """Generate reports in all available formats"""
        reports = {}
        
        for format_type, generator in self.generators.items():
            try:
                reports[format_type] = generator.generate(audit_run, findings, pages)
            except Exception as e:
                logger.error(f"Error generating {format_type} report: {e}")
                reports[format_type] = f"Error generating {format_type} report: {str(e)}"
        
        return reports
