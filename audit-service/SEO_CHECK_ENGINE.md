# SEO Check Engine Implementation

## Overview

The SEO Check Engine is a comprehensive system that analyzes crawled web pages against SEO best practices and generates actionable findings with weighted scoring. It's designed to be modular, extensible, and production-ready.

## 🏗️ **Architecture**

### Core Components

1. **CheckInterface** (`src/Service/Check/CheckInterface.php`)
   - Defines the contract for individual SEO checks
   - Methods: `getCode()`, `getCategory()`, `getSeverity()`, `run()`, `isApplicable()`

2. **AbstractCheck** (`src/Service/Check/AbstractCheck.php`)
   - Base implementation with common helper methods
   - HTML parsing utilities, evidence collection, finding creation

3. **SeoAnalyzer** (`src/Service/SeoAnalyzer.php`)
   - Orchestrates the analysis process
   - Manages check registration and execution
   - Handles database persistence of findings

4. **SeoScorer** (`src/Service/SeoScorer.php`)
   - Calculates weighted scores based on findings
   - Implements category-based scoring system
   - Generates quick wins and top issues

5. **FindingResult** (`src/ValueObject/FindingResult.php`)
   - Value object representing a single SEO finding
   - Contains severity, impact score, evidence, recommendations

6. **Scorecard** (`src/ValueObject/Scorecard.php`)
   - Comprehensive scoring results
   - Category scores, metrics, quick wins, top issues

## 📊 **Scoring System**

### Category Weights (Total: 100 points)
- **Technical SEO**: 40 points
- **On-Page SEO**: 35 points  
- **Local SEO**: 25 points

### Severity Weights
- **Critical**: 10 points deduction
- **High**: 7 points deduction
- **Medium**: 4 points deduction
- **Low**: 1 point deduction

### Scoring Formula
```
Category Score = max(0, Category Weight - Total Deductions)
Overall Score = Σ(Category Score × Category Weight) / Total Weight
```

## 🔍 **Implemented Checks**

### Technical SEO Checks

#### 1. HTTP Status Code Check (`technical.http_status_code`)
- **Severity**: Critical
- **Impact**: 10.0
- **Effort**: Medium
- **Description**: Detects non-200 status codes that impact SEO
- **Recommendation**: Fix redirects, server errors, or client errors

#### 2. HTTPS Check (`technical.https`)
- **Severity**: Critical  
- **Impact**: 8.0
- **Effort**: Medium
- **Description**: Ensures pages are served over HTTPS
- **Recommendation**: Implement SSL/TLS certificate and redirect HTTP to HTTPS

#### 3. Mobile-Friendly Check (`technical.mobile_friendly`)
- **Severity**: High
- **Impact**: 6.0
- **Effort**: Medium
- **Description**: Checks for mobile-friendly viewport configuration
- **Recommendation**: Add responsive viewport meta tag

#### 4. Robots Directives Check (`technical.robots_directives`)
- **Severity**: High
- **Impact**: 8.0
- **Effort**: Small
- **Description**: Detects robots directives blocking indexing
- **Recommendation**: Remove or modify noindex/nofollow directives

### On-Page SEO Checks

#### 1. Title Tag Check (`onpage.title_tag`)
- **Severity**: High
- **Impact**: 10.0
- **Effort**: Small
- **Description**: Validates title tag presence and length (30-60 chars)
- **Recommendation**: Add unique, descriptive title with target keywords

#### 2. Meta Description Check (`onpage.meta_description`)
- **Severity**: Medium
- **Impact**: 6.0
- **Effort**: Small
- **Description**: Validates meta description presence and length (120-160 chars)
- **Recommendation**: Add compelling meta description for better CTR

#### 3. H1 Tag Check (`onpage.h1_tag`)
- **Severity**: High
- **Impact**: 6.0
- **Effort**: Small
- **Description**: Ensures single, descriptive H1 tag per page
- **Recommendation**: Add single H1 tag with target keywords

#### 4. Image Alt Text Check (`onpage.image_alt_text`)
- **Severity**: Medium
- **Impact**: 5.0
- **Effort**: Medium
- **Description**: Checks for missing alt text on images
- **Recommendation**: Add descriptive alt text to all images

### Local SEO Checks

#### 1. Local Business Schema Check (`local.business_schema`)
- **Severity**: High
- **Impact**: 8.0
- **Effort**: Medium
- **Description**: Detects missing LocalBusiness schema markup
- **Recommendation**: Add LocalBusiness schema with business details

## 🚀 **Usage**

### Command Line Interface
```bash
# Analyze an audit run synchronously
php bin/console audit:analyze <run-id> --sync

# Queue analysis job asynchronously
php bin/console audit:analyze <run-id>
```

### Programmatic Usage
```php
// Run analysis on audit run
$findings = $seoAnalyzer->analyzeAuditRun($runId);

// Calculate scores
$scorecard = $seoScorer->score($runId);

// Get category-specific score
$technicalScore = $seoScorer->scoreCategory($runId, 'technical');
```

### Message Queue Integration
```php
// Queue analysis job
$message = new AnalyzeAuditRunMessage($auditRunId);
$messageBus->dispatch($message);
```

## 📋 **Analysis Workflow**

1. **Page Analysis** - Run all applicable checks on each crawled page
2. **Finding Generation** - Create FindingResult objects for issues found
3. **Database Persistence** - Store findings as Finding entities
4. **Score Calculation** - Calculate category and overall scores
5. **Quick Wins Identification** - Find high-impact, low-effort issues
6. **Report Generation** - Compile results into actionable insights

## 🔧 **Extending the Engine**

### Adding New Checks

1. **Create Check Class**
```php
class CustomCheck extends AbstractCheck
{
    public function getCode(): string { return 'custom.check_code'; }
    public function getCategory(): string { return 'technical'; }
    public function getSeverity(): string { return 'high'; }
    public function getTitle(): string { return 'Custom Check Title'; }
    public function getDescription(): string { return 'Check description'; }
    public function getRecommendation(): ?string { return 'Fix recommendation'; }
    public function getEffort(): string { return 'medium'; }
    public function getImpactScore(): float { return 7.0; }
    
    public function isApplicable(Page $page): bool
    {
        return $page->isHtml();
    }
    
    public function run(Page $page): ?FindingResult
    {
        // Check logic here
        if ($issueFound) {
            return $this->createFinding($page, $evidence);
        }
        return $this->createPassFinding($page);
    }
}
```

2. **Register Check**
```php
// In SeoAnalyzer::registerChecks()
$this->checks[] = new CustomCheck();
```

### Adding New Categories

1. **Update Category Weights**
```php
// In SeoScorer
private const CATEGORY_WEIGHTS = [
    'technical' => 40,
    'onpage' => 35,
    'local' => 20,
    'performance' => 5, // New category
];
```

2. **Create Category-Specific Checks**
```php
namespace App\Service\Check\Performance;

class PageSpeedCheck extends AbstractCheck
{
    public function getCategory(): string { return 'performance'; }
    // ... implementation
}
```

## 📊 **Output Examples**

### Scorecard Output
```json
{
  "run_id": "uuid",
  "overall_score": 78.5,
  "category_scores": {
    "technical": 85.0,
    "onpage": 72.0,
    "local": 75.0
  },
  "total_findings": 12,
  "critical_findings": 2,
  "high_findings": 4,
  "medium_findings": 4,
  "low_findings": 2,
  "quick_wins": [
    {
      "title": "Add Missing Title Tags",
      "impact_score": 10.0,
      "effort": "small",
      "recommendation": "Add descriptive title tags..."
    }
  ]
}
```

### Finding Output
```json
{
  "check_code": "onpage.title_tag",
  "category": "onpage",
  "severity": "high",
  "title": "Title Tag Issues",
  "description": "Page has title tag issues that impact SEO performance",
  "recommendation": "Add a unique, descriptive title tag...",
  "evidence": {
    "url": "https://example.com/page",
    "title_length": 15,
    "issues": ["too_short"]
  },
  "impact_score": 10.0,
  "effort": "small"
}
```

## 🧪 **Testing**

### Unit Tests
- `SeoAnalyzerTest.php` - Tests analyzer functionality
- Individual check tests for each SEO check
- Scoring system validation tests

### Test Coverage
- ✅ Check execution and finding generation
- ✅ Scoring calculations and category weights
- ✅ Error handling and edge cases
- ✅ Message queue integration
- ✅ Database persistence

## 🔄 **Integration Points**

### With Crawler Service
- Analyzes pages after crawling completes
- Uses crawled page data and metadata
- Stores findings linked to specific pages

### With Database
- Persists findings as `Finding` entities
- Updates audit run totals and scores
- Maintains relationships between pages and findings

### With Message Queue
- Processes analysis jobs asynchronously
- Handles job failures gracefully
- Updates audit run status

## 🚧 **Future Enhancements**

1. **Performance Checks** - Core Web Vitals, PageSpeed integration
2. **Content Analysis** - Keyword density, readability scores
3. **Link Analysis** - Internal/external link structure
4. **Schema Validation** - Structured data validation
5. **Competitor Analysis** - Compare against competitor sites
6. **Historical Tracking** - Track improvements over time
7. **Custom Rules** - User-defined SEO check rules
8. **AI-Powered Insights** - Machine learning recommendations

## 🐛 **Known Limitations**

- HTML content parsing relies on basic regex (could use DOM parser)
- Some checks need actual HTML content (currently using placeholders)
- No JavaScript rendering support for SPA analysis
- Limited to same-domain analysis
- No real-time analysis updates

## 📚 **Dependencies**

- `doctrine/orm` - Database persistence
- `symfony/messenger` - Async job processing
- `psr/log` - Logging interface
- `phpunit/phpunit` - Testing framework

The SEO Check Engine provides a solid foundation for comprehensive SEO analysis with room for extensive customization and expansion!
