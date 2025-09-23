// Technology Stack Detection Service
// Uses Wappalyzer API to detect website technologies

export interface Technology {
  name: string;
  confidence: number;
  version?: string;
  categories: string[];
  website?: string;
  description?: string;
}

export interface TechStackResult {
  url: string;
  technologies: Technology[];
  lastAnalyzed?: string;
  error?: string;
}

export interface TechStackApiResponse {
  url: string;
  technologies: Array<{
    name: string;
    confidence: number;
    version?: string;
    categories: string[];
    website?: string;
    description?: string;
  }>;
}

class TechStackService {
  private apiKey: string | null = null;
  private baseUrl = 'https://api.wappalyzer.com/v2/lookup';

  constructor() {
    // Get API key from environment variables
    this.apiKey = process.env.NEXT_PUBLIC_WAPPALYZER_API_KEY || null;
  }

  /**
   * Set the Wappalyzer API key
   */
  setApiKey(apiKey: string): void {
    this.apiKey = apiKey;
  }

  /**
   * Check if the service is properly configured
   */
  isConfigured(): boolean {
    return !!this.apiKey;
  }

  /**
   * Analyze a website's technology stack
   */
  async analyzeWebsite(url: string): Promise<TechStackResult> {
    if (!this.isConfigured()) {
      return {
        url,
        technologies: [],
        error: 'Wappalyzer API key not configured'
      };
    }

    try {
      // Ensure URL has protocol
      const normalizedUrl = this.normalizeUrl(url);
      
      const response = await fetch(`${this.baseUrl}/`, {
        method: 'GET',
        headers: {
          'x-api-key': this.apiKey!,
          'Content-Type': 'application/json',
        },
        // Add URL as query parameter
        // Note: Wappalyzer API expects the URL in the query string
      });

      // For Wappalyzer API, we need to use the URL as a query parameter
      const apiUrl = `${this.baseUrl}/?urls=${encodeURIComponent(normalizedUrl)}`;
      
      const finalResponse = await fetch(apiUrl, {
        method: 'GET',
        headers: {
          'x-api-key': this.apiKey!,
          'Content-Type': 'application/json',
        },
      });

      if (!finalResponse.ok) {
        throw new Error(`Wappalyzer API error: ${finalResponse.status} ${finalResponse.statusText}`);
      }

      const data = await finalResponse.json();
      
      // Handle the response format from Wappalyzer API
      if (data && data.length > 0) {
        const result = data[0];
        return {
          url: normalizedUrl,
          technologies: result.technologies || [],
          lastAnalyzed: new Date().toISOString()
        };
      }

      return {
        url: normalizedUrl,
        technologies: [],
        lastAnalyzed: new Date().toISOString()
      };

    } catch (error) {
      console.error('Error analyzing website technology stack:', error);
      return {
        url,
        technologies: [],
        error: error instanceof Error ? error.message : 'Unknown error occurred'
      };
    }
  }

  /**
   * Normalize URL to ensure it has a protocol
   */
  private normalizeUrl(url: string): string {
    if (!url) return '';
    
    // Remove any whitespace
    url = url.trim();
    
    // Add protocol if missing
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
      url = 'https://' + url;
    }
    
    return url;
  }

  /**
   * Get technology categories for better organization
   */
  getTechnologyCategories(technologies: Technology[]): Record<string, Technology[]> {
    const categories: Record<string, Technology[]> = {};
    
    technologies.forEach(tech => {
      tech.categories.forEach(category => {
        if (!categories[category]) {
          categories[category] = [];
        }
        categories[category].push(tech);
      });
    });
    
    return categories;
  }

  /**
   * Get a summary of the technology stack
   */
  getTechStackSummary(technologies: Technology[]): {
    total: number;
    categories: string[];
    topTechnologies: Technology[];
  } {
    const categories = new Set<string>();
    technologies.forEach(tech => {
      tech.categories.forEach(cat => categories.add(cat));
    });

    // Sort by confidence and get top 5
    const topTechnologies = technologies
      .sort((a, b) => b.confidence - a.confidence)
      .slice(0, 5);

    return {
      total: technologies.length,
      categories: Array.from(categories),
      topTechnologies
    };
  }

  /**
   * Check if a specific technology is detected
   */
  hasTechnology(technologies: Technology[], techName: string): boolean {
    return technologies.some(tech => 
      tech.name.toLowerCase().includes(techName.toLowerCase())
    );
  }

  /**
   * Get technologies by category
   */
  getTechnologiesByCategory(technologies: Technology[], category: string): Technology[] {
    return technologies.filter(tech => 
      tech.categories.some(cat => 
        cat.toLowerCase().includes(category.toLowerCase())
      )
    );
  }
}

// Export singleton instance
export const techStackService = new TechStackService();
export default techStackService;
