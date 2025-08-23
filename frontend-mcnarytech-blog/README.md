# McNary Tech Blog

A high-performance, SEO-optimized blog platform built with Next.js 14, React 18, and TypeScript, designed to integrate seamlessly with the existing McNary backend infrastructure.

## 🚀 Features

- **SEO-First Design**: Built with search engine optimization as a primary concern
- **Performance Optimized**: Lighthouse scores >90 across all metrics
- **Multi-Tenant Support**: Built on existing backend tenant isolation
- **Modern Tech Stack**: Next.js 14, React 18, TypeScript, Tailwind CSS
- **Static Generation**: Pre-rendered content for optimal performance
- **Responsive Design**: Mobile-first approach with excellent UX
- **Content Management**: Admin interface for managing posts, categories, and tags
- **Search & Discovery**: Full-text search with filtering and sorting
- **RSS Feeds & Sitemaps**: Automatic generation for content discovery

## 🛠️ Tech Stack

- **Frontend**: Next.js 14, React 18, TypeScript
- **Styling**: Tailwind CSS with custom design system
- **Backend Integration**: Symfony 7.3 + API Platform
- **Database**: PostgreSQL 16
- **Caching**: Redis + Next.js caching strategies
- **Deployment**: Docker, Docker Compose
- **SEO**: Next.js metadata, structured data, sitemaps

## 📁 Project Structure

```
frontend-mcnarytech-blog/
├── src/
│   ├── app/                    # Next.js App Router
│   │   ├── (blog)/            # Blog route group
│   │   ├── (admin)/           # Admin route group
│   │   ├── api/                # API routes
│   │   ├── globals.css         # Global styles
│   │   ├── layout.tsx          # Root layout
│   │   └── page.tsx            # Home page
│   ├── components/
│   │   ├── ui/                 # Reusable UI components
│   │   ├── blog/               # Blog-specific components
│   │   ├── layout/             # Layout components
│   │   └── admin/              # Admin components
│   ├── lib/
│   │   ├── api.ts              # API client
│   │   ├── types.ts            # TypeScript types
│   │   └── utils.ts            # Utility functions
│   └── hooks/                  # Custom React hooks
├── public/                     # Static assets
├── next.config.js              # Next.js configuration
├── tailwind.config.js          # Tailwind CSS configuration
├── tsconfig.json               # TypeScript configuration
└── package.json                # Dependencies
```

## 🚀 Getting Started

### Prerequisites

- Node.js 18+ 
- npm or yarn
- Access to McNary backend API
- PostgreSQL database
- Redis instance

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd frontend-mcnarytech-blog
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp env.example .env.local
   ```
   
   Update `.env.local` with your configuration:
   ```bash
   NODE_ENV=development
   API_URL=http://localhost:8000
   DATABASE_URL=postgresql://user:password@localhost:5432/blog
   REDIS_URL=redis://localhost:6379
   
   # SEO
   NEXT_PUBLIC_SITE_URL=http://localhost:3000
   NEXT_PUBLIC_SITE_NAME="McNary Tech Blog"
   NEXT_PUBLIC_SITE_DESCRIPTION="Technology insights and development tips"
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

5. **Open your browser**
   Navigate to [http://localhost:3000](http://localhost:3000)

## 🏗️ Development

### Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run start` - Start production server
- `npm run lint` - Run ESLint
- `npm run type-check` - Run TypeScript type checking

### Code Quality

The project uses several tools to maintain code quality:

- **ESLint**: Code linting with Next.js and TypeScript rules
- **Prettier**: Code formatting
- **TypeScript**: Static type checking
- **Tailwind CSS**: Utility-first CSS framework

### Component Development

Components are organized by functionality:

- **UI Components** (`src/components/ui/`): Reusable, generic components
- **Blog Components** (`src/components/blog/`): Blog-specific functionality
- **Layout Components** (`src/components/layout/`): Page structure components
- **Admin Components** (`src/components/admin/`): Content management interface

## 🔧 Configuration

### Next.js Configuration

The `next.config.js` file includes:

- Security headers (CSP, X-Frame-Options, etc.)
- Image optimization settings
- API route rewrites for sitemap and RSS
- Performance optimizations

### Tailwind CSS Configuration

Custom design system with:

- Primary color palette
- Typography scale
- Component variants
- Responsive breakpoints

### TypeScript Configuration

Strict TypeScript configuration with:

- Path mapping (`@/*` alias)
- Next.js types
- Strict mode enabled

## 📱 Responsive Design

The blog is built with a mobile-first approach:

- Responsive grid layouts
- Mobile-optimized navigation
- Touch-friendly interactions
- Optimized images for all screen sizes

## 🔍 SEO Features

### Meta Tags

- Dynamic title and description generation
- Open Graph and Twitter Card support
- Canonical URLs
- Robots meta tags

### Structured Data

- Blog post schema markup
- Organization schema
- Author schema
- Breadcrumb navigation

### Performance

- Static generation for optimal SEO
- Image optimization with Next.js Image
- Core Web Vitals optimization
- Lighthouse score targets >90

## 🚀 Deployment

### Docker Deployment

1. **Build the image**
   ```bash
   docker build -t mcnary-tech-blog .
   ```

2. **Run with Docker Compose**
   ```bash
   docker-compose up -d
   ```

### Environment Variables

Required environment variables for production:

```bash
NODE_ENV=production
API_URL=https://api.mcnarytech.com
DATABASE_URL=postgresql://user:password@db:5432/blog
REDIS_URL=redis://redis:6379
NEXT_PUBLIC_SITE_URL=https://mcnarytech.com
```

## 📊 Performance Monitoring

### Core Web Vitals

- **First Contentful Paint**: <1.5s
- **Largest Contentful Paint**: <2.5s
- **Cumulative Layout Shift**: <0.1
- **Time to Interactive**: <3.5s

### Monitoring Tools

- Next.js built-in performance monitoring
- Lighthouse CI integration
- Real User Monitoring (RUM)
- Performance budgets

## 🔒 Security

### Security Headers

- Content Security Policy (CSP)
- X-Frame-Options
- X-Content-Type-Options
- Referrer Policy

### Input Validation

- Zod schema validation
- XSS protection
- CSRF protection
- SQL injection prevention

## 🧪 Testing

### Testing Strategy

- **Unit Tests**: Component and utility testing
- **Integration Tests**: API route testing
- **E2E Tests**: User journey testing
- **Performance Tests**: Lighthouse score validation

### Running Tests

```bash
npm run test          # Run all tests
npm run test:unit     # Run unit tests only
npm run test:e2e      # Run E2E tests only
```

## 📚 API Integration

### Backend API

The blog integrates with the existing McNary backend:

- **Posts**: CRUD operations for blog posts
- **Categories**: Content categorization
- **Tags**: Content tagging system
- **Users**: Author management
- **SEO Metadata**: Search engine optimization

### API Client

The `src/lib/api.ts` file provides:

- Cached API requests
- Error handling
- Type-safe responses
- Tenant-aware requests

## 🤝 Contributing

### Development Workflow

1. Create a feature branch
2. Make your changes
3. Add tests if applicable
4. Run linting and type checking
5. Submit a pull request

### Code Standards

- Follow TypeScript best practices
- Use Tailwind CSS utility classes
- Maintain component reusability
- Write comprehensive tests
- Document complex logic

## 📄 License

This project is proprietary software owned by McNary Tech.

## 🆘 Support

For support and questions:

- **Development Team**: dev@mcnarytech.com
- **Documentation**: [Internal Wiki]
- **Issue Tracking**: [Internal Jira]

---

Built with ❤️ by the McNary Tech Team
