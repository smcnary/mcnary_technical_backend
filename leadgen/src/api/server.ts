import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import dotenv from 'dotenv';
import * as fs from 'fs';
import * as path from 'path';
import { CampaignSpec } from '../types/campaign';
import { Vertical } from '../types/canonical';
import { CampaignOrchestrator } from '../services/orchestrator';
import { GooglePlacesAdapter } from '../adapters/google-places';

// Load environment variables
dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

// Middleware
app.use(helmet());
app.use(cors());
app.use(express.json());

// Initialize orchestrator and register adapters
const orchestrator = new CampaignOrchestrator();

// Register Google Places adapter
const googlePlacesApiKey = process.env.GOOGLE_PLACES_API_KEY;
if (googlePlacesApiKey) {
  const googlePlacesAdapter = new GooglePlacesAdapter(googlePlacesApiKey);
  orchestrator.registerAdapter(googlePlacesAdapter);
  console.log('Google Places adapter registered');
} else {
  console.warn('GOOGLE_PLACES_API_KEY not found in environment variables');
}

// Validation middleware
const validateCampaignSpec = (req: express.Request, res: express.Response, next: express.NextFunction): void => {
  const { body } = req;
  
  if (!body.name || !body.vertical || !body.geo || !body.sources) {
    res.status(400).json({
      error: 'Missing required fields: name, vertical, geo, sources'
    });
    return;
  }

  if (!Object.values(Vertical).includes(body.vertical)) {
    res.status(400).json({
      error: `Invalid vertical. Must be one of: ${Object.values(Vertical).join(', ')}`
    });
    return;
  }

  if (!body.geo.country || !body.geo.radius_km) {
    res.status(400).json({
      error: 'Geo must include country and radius_km'
    });
    return;
  }

  next();
};

// Routes

// Health check
app.get('/health', (req, res) => {
  res.json({ status: 'healthy', timestamp: new Date().toISOString() });
});

// Create and run campaign
app.post('/v1/campaigns', validateCampaignSpec, async (req, res) => {
  try {
    const campaignSpec: CampaignSpec = req.body;
    
    console.log(`Starting campaign: ${campaignSpec.name}`);
    const run = await orchestrator.runCampaign(campaignSpec);
    
    res.json({
      success: true,
      run_id: run.id,
      status: run.status,
      message: `Campaign ${campaignSpec.name} ${run.status}`,
      stats: {
        total_leads: run.total_leads,
        successful_leads: run.successful_leads,
        failed_leads: run.failed_leads,
        cost_usd: run.cost_usd
      }
    });
  } catch (error) {
    console.error('Error running campaign:', error);
    res.status(500).json({
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error'
    });
  }
});

// Get campaign run status
app.get('/v1/campaigns/:runId/status', (req, res) => {
  const { runId } = req.params;
  const run = orchestrator.getRunStatus(runId);
  
  if (!run) {
    res.status(404).json({
      error: 'Run not found'
    });
    return;
  }
  
  res.json(run);
});

// Get all campaign runs with enhanced information
app.get('/v1/campaigns/runs', (req, res) => {
  const runs = orchestrator.getAllRuns();
  
  // Enhance runs with export file information
  const enhancedRuns = runs.map(run => {
    const exportFiles = orchestrator.getExportFilesForRun(run.id);
    return {
      ...run,
      export_files: exportFiles,
      duration_minutes: run.completed_at 
        ? Math.round((run.completed_at.getTime() - run.started_at.getTime()) / 60000)
        : null,
      success_rate: run.total_leads > 0 
        ? Math.round((run.successful_leads / run.total_leads) * 100)
        : 0
    };
  });
  
  res.json({
    runs: enhancedRuns,
    total: enhancedRuns.length,
    summary: {
      total_runs: enhancedRuns.length,
      completed_runs: enhancedRuns.filter(r => r.status === 'completed').length,
      failed_runs: enhancedRuns.filter(r => r.status === 'failed').length,
      total_leads: enhancedRuns.reduce((sum, r) => sum + r.successful_leads, 0),
      total_cost: enhancedRuns.reduce((sum, r) => sum + r.cost_usd, 0)
    }
  });
});

// Get supported verticals
app.get('/v1/verticals', (req, res) => {
  res.json({
    verticals: Object.values(Vertical).map((v: Vertical) => ({
      value: v,
      label: v.replace('_', ' ').toUpperCase()
    }))
  });
});

// Get supported sources
app.get('/v1/sources', (req, res) => {
  res.json({
    sources: [
      {
        name: 'google_places',
        label: 'Google Places',
        supported_verticals: ['local_services', 'healthcare', 'real_estate'],
        description: 'Local business listings from Google Places API'
      }
    ]
  });
});

// Get leads from CSV files
app.get('/v1/leads', async (req, res) => {
  try {
    const { runId, campaign, limit = 50, offset = 0, search, vertical, minScore } = req.query;
    
    const exportsDir = path.join(process.cwd(), 'exports');
    if (!fs.existsSync(exportsDir)) {
      res.json({ leads: [], total: 0, message: 'No exports directory found' });
      return;
    }

    let csvFiles = fs.readdirSync(exportsDir).filter(file => file.endsWith('.csv'));
    
    // Filter by runId if provided
    if (runId) {
      csvFiles = csvFiles.filter(file => file.includes(runId as string));
    }
    
    // Filter by campaign name if provided
    if (campaign) {
      csvFiles = csvFiles.filter(file => file.toLowerCase().includes((campaign as string).toLowerCase()));
    }

    if (csvFiles.length === 0) {
      res.json({ leads: [], total: 0, message: 'No matching CSV files found' });
      return;
    }

    // Read and parse CSV files
    const allLeads: any[] = [];
    for (const file of csvFiles) {
      const filePath = path.join(exportsDir, file);
      const csvContent = fs.readFileSync(filePath, 'utf8');
      const leads = parseCSVToLeads(csvContent, file);
      allLeads.push(...leads);
    }

    // Apply filters
    let filteredLeads = allLeads;
    
    if (search) {
      const searchTerm = (search as string).toLowerCase();
      filteredLeads = filteredLeads.filter(lead => 
        lead.company_name?.toLowerCase().includes(searchTerm) ||
        lead.website?.toLowerCase().includes(searchTerm) ||
        lead.city?.toLowerCase().includes(searchTerm) ||
        lead.region?.toLowerCase().includes(searchTerm)
      );
    }
    
    if (vertical) {
      filteredLeads = filteredLeads.filter(lead => lead.vertical === vertical);
    }
    
    if (minScore) {
      const minScoreNum = parseFloat(minScore as string);
      filteredLeads = filteredLeads.filter(lead => (lead.lead_score || 0) >= minScoreNum);
    }

    // Sort by lead score (highest first)
    filteredLeads.sort((a, b) => (b.lead_score || 0) - (a.lead_score || 0));

    // Apply pagination
    const total = filteredLeads.length;
    const paginatedLeads = filteredLeads.slice(Number(offset), Number(offset) + Number(limit));

    res.json({
      leads: paginatedLeads,
      total,
      offset: Number(offset),
      limit: Number(limit),
      hasMore: Number(offset) + Number(limit) < total
    });
  } catch (error) {
    console.error('Error fetching leads:', error);
    res.status(500).json({
      error: error instanceof Error ? error.message : 'Unknown error'
    });
  }
});

// Get individual lead details
app.get('/v1/leads/:leadId', async (req, res) => {
  try {
    const { leadId } = req.params;
    
    const exportsDir = path.join(process.cwd(), 'exports');
    if (!fs.existsSync(exportsDir)) {
      res.status(404).json({ error: 'Lead not found' });
      return;
    }

    const csvFiles = fs.readdirSync(exportsDir).filter(file => file.endsWith('.csv'));
    
    for (const file of csvFiles) {
      const filePath = path.join(exportsDir, file);
      const csvContent = fs.readFileSync(filePath, 'utf8');
      const leads = parseCSVToLeads(csvContent, file);
      
      const lead = leads.find(l => l.lead_id === leadId);
      if (lead) {
        res.json(lead);
        return;
      }
    }
    
    res.status(404).json({ error: 'Lead not found' });
  } catch (error) {
    console.error('Error fetching lead:', error);
    res.status(500).json({
      error: error instanceof Error ? error.message : 'Unknown error'
    });
  }
});

// Helper function to parse CSV to lead objects
function parseCSVToLeads(csvContent: string, filename: string): any[] {
  const lines = csvContent.trim().split('\n');
  if (lines.length < 2) return [];
  
  const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
  const leads: any[] = [];
  
  for (let i = 1; i < lines.length; i++) {
    const values = parseCSVLine(lines[i]);
    if (values.length !== headers.length) continue;
    
    const lead: any = {};
    headers.forEach((header, index) => {
      lead[header] = values[index];
    });
    
    // Add metadata
    lead.source_file = filename;
    lead.run_id = filename.match(/run_([^_]+)/)?.[1] || 'unknown';
    
    // Convert numeric fields
    if (lead.lead_score) lead.lead_score = parseFloat(lead.lead_score);
    if (lead.rating) lead.rating = parseFloat(lead.rating);
    if (lead.review_count) lead.review_count = parseInt(lead.review_count);
    if (lead.latitude) lead.latitude = parseFloat(lead.latitude);
    if (lead.longitude) lead.longitude = parseFloat(lead.longitude);
    if (lead.founded_year) lead.founded_year = parseInt(lead.founded_year);
    
    leads.push(lead);
  }
  
  return leads;
}

// Helper function to parse CSV line handling quoted values
function parseCSVLine(line: string): string[] {
  const result: string[] = [];
  let current = '';
  let inQuotes = false;
  
  for (let i = 0; i < line.length; i++) {
    const char = line[i];
    
    if (char === '"') {
      if (inQuotes && line[i + 1] === '"') {
        current += '"';
        i++; // Skip next quote
      } else {
        inQuotes = !inQuotes;
      }
    } else if (char === ',' && !inQuotes) {
      result.push(current.trim());
      current = '';
    } else {
      current += char;
    }
  }
  
  result.push(current.trim());
  return result;
}

// Serve leads browser
app.get('/leads', (req, res) => {
  res.send(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadGen - Leads Browser</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc; 
            color: #334155;
            line-height: 1.6;
        }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .header h1 { color: #1e293b; font-size: 2.5rem; margin-bottom: 10px; }
        .header p { color: #64748b; font-size: 1.1rem; }
        .nav { margin-bottom: 20px; }
        .nav a { 
            color: #3b82f6; 
            text-decoration: none; 
            margin-right: 20px; 
            font-weight: 500;
        }
        .nav a:hover { text-decoration: underline; }
        .filters { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .filter-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 500; 
            color: #374151;
        }
        .filter-group input, .filter-group select { 
            width: 100%; 
            padding: 8px 12px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            font-size: 14px;
        }
        .filter-group input:focus, .filter-group select:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .leads-section { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .leads-section h2 { color: #1e293b; margin-bottom: 20px; }
        .leads-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 20px; 
            margin-top: 20px;
        }
        .lead-card { 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 20px; 
            transition: all 0.2s;
            cursor: pointer;
        }
        .lead-card:hover { 
            border-color: #3b82f6; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .lead-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: start; 
            margin-bottom: 15px;
        }
        .lead-name { 
            font-size: 1.2rem; 
            font-weight: 600; 
            color: #1e293b; 
            margin-bottom: 5px;
        }
        .lead-score { 
            background: #3b82f6; 
            color: white; 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 0.8rem; 
            font-weight: 600;
        }
        .lead-info { 
            display: grid; 
            gap: 8px; 
            font-size: 0.9rem;
        }
        .lead-info div { 
            display: flex; 
            align-items: center; 
            color: #64748b;
        }
        .lead-info .icon { 
            margin-right: 8px; 
            width: 16px; 
            text-align: center;
        }
        .lead-tags { 
            margin-top: 15px; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 5px;
        }
        .tag { 
            background: #f1f5f9; 
            color: #475569; 
            padding: 2px 8px; 
            border-radius: 12px; 
            font-size: 0.8rem;
        }
        .loading { text-align: center; padding: 40px; color: #64748b; }
        .error { 
            background: #fee2e2; 
            color: #dc2626; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0;
        }
        .pagination { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px; 
            margin-top: 30px;
        }
        .pagination button { 
            background: #3b82f6; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 6px; 
            cursor: pointer;
        }
        .pagination button:disabled { 
            background: #9ca3af; 
            cursor: not-allowed;
        }
        .pagination span { 
            color: #64748b; 
            font-size: 0.9rem;
        }
        .modal { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
            z-index: 1000;
        }
        .modal-content { 
            background: white; 
            margin: 5% auto; 
            padding: 30px; 
            border-radius: 12px; 
            width: 90%; 
            max-width: 800px; 
            max-height: 80vh; 
            overflow-y: auto;
        }
        .modal-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
        }
        .modal-header h3 { color: #1e293b; }
        .close { 
            background: none; 
            border: none; 
            font-size: 24px; 
            cursor: pointer; 
            color: #64748b;
        }
        .detail-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px;
        }
        .detail-section h4 { 
            color: #374151; 
            margin-bottom: 10px; 
            font-size: 1rem;
        }
        .detail-section p { 
            color: #64748b; 
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 LeadGen - Leads Browser</h1>
            <p>Browse and search through all your generated leads</p>
            <div class="nav">
                <a href="/dashboard">📊 Campaign Dashboard</a>
                <a href="/leads">🔍 Leads Browser</a>
            </div>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="search" placeholder="Company name, website, city...">
            </div>
            <div class="filter-group">
                <label>Campaign</label>
                <select id="campaign">
                    <option value="">All Campaigns</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Vertical</label>
                <select id="vertical">
                    <option value="">All Verticals</option>
                    <option value="local_services">Local Services</option>
                    <option value="b2b_saas">B2B SaaS</option>
                    <option value="ecommerce">E-commerce</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="real_estate">Real Estate</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Min Score</label>
                <input type="number" id="minScore" placeholder="0" min="0" max="100">
            </div>
        </div>

        <div class="leads-section">
            <h2>📋 Leads</h2>
            <div id="leads-content">
                <div class="loading">Loading leads...</div>
            </div>
            <div class="pagination" id="pagination" style="display: none;">
                <button id="prevBtn" onclick="changePage(-1)">Previous</button>
                <span id="pageInfo"></span>
                <button id="nextBtn" onclick="changePage(1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Lead Detail Modal -->
    <div id="leadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Lead Details</h3>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        let currentPage = 0;
        let currentFilters = {};
        let totalLeads = 0;
        const pageSize = 20;

        async function loadLeads() {
            try {
                const params = new URLSearchParams({
                    limit: pageSize,
                    offset: currentPage * pageSize,
                    ...currentFilters
                });

                const response = await fetch(\`/v1/leads?\${params}\`);
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Failed to load leads');
                }

                totalLeads = data.total;
                displayLeads(data.leads);
                updatePagination();
            } catch (error) {
                console.error('Error loading leads:', error);
                document.getElementById('leads-content').innerHTML = \`
                    <div class="error">
                        Error loading leads: \${error.message}
                    </div>
                \`;
            }
        }

        function displayLeads(leads) {
            const leadsContent = document.getElementById('leads-content');
            
            if (leads.length === 0) {
                leadsContent.innerHTML = '<div class="loading">No leads found</div>';
                return;
            }

            const leadsGrid = \`
                <div class="leads-grid">
                    \${leads.map(lead => \`
                        <div class="lead-card" onclick="showLeadDetail('\${lead.lead_id}')">
                            <div class="lead-header">
                                <div>
                                    <div class="lead-name">\${lead.company_name || 'Unknown Company'}</div>
                                    <div style="color: #64748b; font-size: 0.9rem;">\${lead.vertical || 'Unknown Vertical'}</div>
                                </div>
                                <div class="lead-score">\${lead.lead_score || 0}</div>
                            </div>
                            <div class="lead-info">
                                \${lead.website ? \`<div><span class="icon">🌐</span>\${lead.website}</div>\` : ''}
                                \${lead.primary_phone ? \`<div><span class="icon">📞</span>\${lead.primary_phone}</div>\` : ''}
                                \${lead.primary_email ? \`<div><span class="icon">✉️</span>\${lead.primary_email}</div>\` : ''}
                                \${lead.city && lead.region ? \`<div><span class="icon">📍</span>\${lead.city}, \${lead.region}</div>\` : ''}
                                \${lead.rating ? \`<div><span class="icon">⭐</span>\${lead.rating} (\${lead.review_count || 0} reviews)</div>\` : ''}
                            </div>
                            \${lead.tags ? \`
                                <div class="lead-tags">
                                    \${lead.tags.split(';').filter(tag => tag.trim()).map(tag => \`
                                        <span class="tag">\${tag.trim()}</span>
                                    \`).join('')}
                                </div>
                            \` : ''}
                        </div>
                    \`).join('')}
                </div>
            \`;
            
            leadsContent.innerHTML = leadsGrid;
        }

        async function showLeadDetail(leadId) {
            try {
                const response = await fetch(\`/v1/leads/\${leadId}\`);
                const lead = await response.json();
                
                if (!response.ok) {
                    throw new Error(lead.error || 'Lead not found');
                }

                document.getElementById('modalTitle').textContent = lead.company_name || 'Lead Details';
                
                const modalContent = \`
                    <div class="detail-grid">
                        <div class="detail-section">
                            <h4>Company Information</h4>
                            <p><strong>Name:</strong> \${lead.company_name || 'N/A'}</p>
                            <p><strong>Vertical:</strong> \${lead.vertical || 'N/A'}</p>
                            <p><strong>Website:</strong> \${lead.website ? \`<a href="\${lead.website}" target="_blank">\${lead.website}</a>\` : 'N/A'}</p>
                            <p><strong>Lead Score:</strong> \${lead.lead_score || 0}</p>
                            <p><strong>Founded:</strong> \${lead.founded_year || 'N/A'}</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Contact Information</h4>
                            <p><strong>Phone:</strong> \${lead.primary_phone || 'N/A'}</p>
                            <p><strong>Email:</strong> \${lead.primary_email || 'N/A'}</p>
                            <p><strong>Address:</strong> \${lead.address_line1 || ''} \${lead.address_line2 || ''}</p>
                            <p><strong>City:</strong> \${lead.city || 'N/A'}</p>
                            <p><strong>Region:</strong> \${lead.region || 'N/A'}</p>
                            <p><strong>Postal Code:</strong> \${lead.postal_code || 'N/A'}</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Business Details</h4>
                            <p><strong>Rating:</strong> \${lead.rating || 'N/A'}</p>
                            <p><strong>Review Count:</strong> \${lead.review_count || 'N/A'}</p>
                            <p><strong>Employee Range:</strong> \${lead.employee_range || 'N/A'}</p>
                            <p><strong>Revenue Range:</strong> \${lead.revenue_range || 'N/A'}</p>
                            <p><strong>Tech Signals:</strong> \${lead.tech_signals || 'N/A'}</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Campaign Information</h4>
                            <p><strong>Source File:</strong> \${lead.source_file || 'N/A'}</p>
                            <p><strong>Run ID:</strong> \${lead.run_id || 'N/A'}</p>
                            <p><strong>Created:</strong> \${lead.created_at ? new Date(lead.created_at).toLocaleString() : 'N/A'}</p>
                            <p><strong>Updated:</strong> \${lead.updated_at ? new Date(lead.updated_at).toLocaleString() : 'N/A'}</p>
                        </div>
                    </div>
                    
                    \${lead.score_explanations ? \`
                        <div class="detail-section" style="margin-top: 20px;">
                            <h4>Score Explanations</h4>
                            <p>\${lead.score_explanations}</p>
                        </div>
                    \` : ''}
                \`;
                
                document.getElementById('modalContent').innerHTML = modalContent;
                document.getElementById('leadModal').style.display = 'block';
            } catch (error) {
                console.error('Error loading lead details:', error);
                alert('Error loading lead details: ' + error.message);
            }
        }

        function closeModal() {
            document.getElementById('leadModal').style.display = 'none';
        }

        function updatePagination() {
            const pagination = document.getElementById('pagination');
            const pageInfo = document.getElementById('pageInfo');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            if (totalLeads <= pageSize) {
                pagination.style.display = 'none';
                return;
            }
            
            pagination.style.display = 'flex';
            pageInfo.textContent = \`Page \${currentPage + 1} of \${Math.ceil(totalLeads / pageSize)}\`;
            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = (currentPage + 1) * pageSize >= totalLeads;
        }

        function changePage(direction) {
            const newPage = currentPage + direction;
            const maxPage = Math.ceil(totalLeads / pageSize) - 1;
            
            if (newPage >= 0 && newPage <= maxPage) {
                currentPage = newPage;
                loadLeads();
            }
        }

        function applyFilters() {
            currentFilters = {
                search: document.getElementById('search').value,
                campaign: document.getElementById('campaign').value,
                vertical: document.getElementById('vertical').value,
                minScore: document.getElementById('minScore').value
            };
            
            // Remove empty filters
            Object.keys(currentFilters).forEach(key => {
                if (!currentFilters[key]) {
                    delete currentFilters[key];
                }
            });
            
            currentPage = 0;
            loadLeads();
        }

        // Event listeners
        document.getElementById('search').addEventListener('input', debounce(applyFilters, 500));
        document.getElementById('campaign').addEventListener('change', applyFilters);
        document.getElementById('vertical').addEventListener('change', applyFilters);
        document.getElementById('minScore').addEventListener('input', debounce(applyFilters, 500));

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('leadModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Load campaigns for filter
        async function loadCampaigns() {
            try {
                const response = await fetch('/v1/campaigns/runs');
                const data = await response.json();
                
                if (response.ok) {
                    const campaigns = [...new Set(data.runs.map(run => run.campaign_id))];
                    const campaignSelect = document.getElementById('campaign');
                    
                    campaigns.forEach(campaign => {
                        const option = document.createElement('option');
                        option.value = campaign;
                        option.textContent = campaign;
                        campaignSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading campaigns:', error);
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadCampaigns();
            loadLeads();
        });
    </script>
</body>
</html>
  `);
});

// Serve campaign dashboard
app.get('/dashboard', (req, res) => {
  res.send(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeadGen Campaign Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc; 
            color: #334155;
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .header h1 { color: #1e293b; font-size: 2.5rem; margin-bottom: 10px; }
        .header p { color: #64748b; font-size: 1.1rem; }
        .summary-cards { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .card { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        }
        .card h3 { color: #1e293b; margin-bottom: 10px; font-size: 1.1rem; }
        .card .number { font-size: 2rem; font-weight: bold; color: #3b82f6; }
        .card .label { color: #64748b; font-size: 0.9rem; }
        .runs-section { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .runs-section h2 { color: #1e293b; margin-bottom: 20px; }
        .runs-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        .runs-table th, .runs-table td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0;
        }
        .runs-table th { 
            background: #f8fafc; 
            font-weight: 600; 
            color: #475569;
        }
        .status { 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 600;
        }
        .status.completed { background: #dcfce7; color: #166534; }
        .status.failed { background: #fee2e2; color: #dc2626; }
        .status.running { background: #dbeafe; color: #2563eb; }
        .status.pending { background: #fef3c7; color: #d97706; }
        .loading { text-align: center; padding: 40px; color: #64748b; }
        .error { 
            background: #fee2e2; 
            color: #dc2626; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0;
        }
        .refresh-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .refresh-btn:hover { background: #2563eb; }
        .export-files { font-size: 0.8rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 LeadGen Campaign Dashboard</h1>
            <p>Monitor your lead generation campaigns and track performance metrics</p>
            <div class="nav">
                <a href="/dashboard">📊 Campaign Dashboard</a>
                <a href="/leads">🔍 Leads Browser</a>
            </div>
        </div>

        <button class="refresh-btn" onclick="loadDashboard()">🔄 Refresh Data</button>

        <div id="summary" class="summary-cards">
            <div class="card">
                <h3>Total Campaigns</h3>
                <div class="number" id="total-runs">-</div>
                <div class="label">All time runs</div>
            </div>
            <div class="card">
                <h3>Successful Runs</h3>
                <div class="number" id="completed-runs">-</div>
                <div class="label">Completed successfully</div>
            </div>
            <div class="card">
                <h3>Total Leads</h3>
                <div class="number" id="total-leads">-</div>
                <div class="label">Leads generated</div>
            </div>
            <div class="card">
                <h3>Total Cost</h3>
                <div class="number" id="total-cost">-</div>
                <div class="label">USD spent</div>
            </div>
        </div>

        <div class="runs-section">
            <h2>📊 Campaign Runs</h2>
            <div id="runs-content">
                <div class="loading">Loading campaign data...</div>
            </div>
        </div>
    </div>

    <script>
        async function loadDashboard() {
            try {
                const response = await fetch('/v1/campaigns/runs');
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Failed to load data');
                }

                // Update summary cards
                document.getElementById('total-runs').textContent = data.summary.total_runs;
                document.getElementById('completed-runs').textContent = data.summary.completed_runs;
                document.getElementById('total-leads').textContent = data.summary.total_leads.toLocaleString();
                document.getElementById('total-cost').textContent = '$' + data.summary.total_cost.toFixed(2);

                // Update runs table
                const runsContent = document.getElementById('runs-content');
                if (data.runs.length === 0) {
                    runsContent.innerHTML = '<div class="loading">No campaign runs found</div>';
                    return;
                }

                const table = \`
                    <table class="runs-table">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Duration</th>
                                <th>Leads</th>
                                <th>Success Rate</th>
                                <th>Cost</th>
                                <th>Exports</th>
                            </tr>
                        </thead>
                        <tbody>
                            \${data.runs.map(run => \`
                                <tr>
                                    <td><strong>\${run.campaign_id}</strong></td>
                                    <td><span class="status \${run.status}">\${run.status}</span></td>
                                    <td>\${new Date(run.started_at).toLocaleString()}</td>
                                    <td>\${run.duration_minutes ? run.duration_minutes + ' min' : '-'}</td>
                                    <td>\${run.successful_leads.toLocaleString()}</td>
                                    <td>\${run.success_rate}%</td>
                                    <td>\$\${run.cost_usd.toFixed(2)}</td>
                                    <td class="export-files">
                                        \${run.export_files && run.export_files.length > 0 
                                            ? run.export_files.map(file => \`<div>\${file}</div>\`).join('')
                                            : '-'
                                        }
                                    </td>
                                </tr>
                            \`).join('')}
                        </tbody>
                    </table>
                \`;
                
                runsContent.innerHTML = table;
            } catch (error) {
                console.error('Error loading dashboard:', error);
                document.getElementById('runs-content').innerHTML = \`
                    <div class="error">
                        Error loading campaign data: \${error.message}
                    </div>
                \`;
            }
        }

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', loadDashboard);
        
        // Auto-refresh every 30 seconds
        setInterval(loadDashboard, 30000);
    </script>
</body>
</html>
  `);
});

// Example campaign endpoint
app.get('/v1/campaigns/example', (req, res) => {
  const exampleCampaign: CampaignSpec = {
    name: 'Denver Plumbers Q4',
    vertical: Vertical.LOCAL_SERVICES,
    geo: {
      city: 'Denver',
      region: 'CO',
      country: 'US',
      radius_km: 50
    },
    filters: {
      min_rating: 4.0,
      review_count_min: 10,
      keywords: ['plumber', 'plumbing'],
      types: ['plumber']
    },
    sources: ['google_places'],
    enrichment: [],
    budget: {
      max_cost_usd: 100
    },
    schedule: {
      enabled: false
    },
    destinations: []
  };
  
  res.json({
    example: exampleCampaign,
    description: 'Example campaign configuration for testing'
  });
});

// Error handling middleware
app.use((error: Error, req: express.Request, res: express.Response, next: express.NextFunction) => {
  console.error('Unhandled error:', error);
  res.status(500).json({
    success: false,
    error: 'Internal server error'
  });
});

// 404 handler
app.use('*', (req, res) => {
  res.status(404).json({
    error: 'Endpoint not found'
  });
});

// Start server
app.listen(port, () => {
  console.log(`LeadGen API server running on port ${port}`);
  console.log(`Health check: http://localhost:${port}/health`);
  console.log(`API documentation: http://localhost:${port}/v1/campaigns/example`);
});

export default app;
