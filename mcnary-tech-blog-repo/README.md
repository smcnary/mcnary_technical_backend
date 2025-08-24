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
mcnary-tech-blog-repo/
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
   cd mcnary-tech-blog-repo
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
   DATABASE_URL=postgresql://user:password@localhost:5439/blog
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

## 📝 Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run start` - Start production server
- `npm run lint` - Run ESLint
- `npm run type-check` - Run TypeScript type checking
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check code formatting

## 🐳 Docker Development

### Using Docker Compose

1. **Start all services**
   ```bash
   docker-compose up -d
   ```

2. **View logs**
   ```bash
   docker-compose logs -f
   ```

3. **Stop services**
   ```bash
   docker-compose down
   ```

### Manual Docker Build

1. **Build the image**
   ```bash
   docker build -t mcnary-tech-blog .
   ```

2. **Run the container**
   ```bash
   docker run -p 3000:3000 mcnary-tech-blog
   ```

## 🔧 Configuration

### Next.js Configuration
Custom Next.js configuration in `next.config.js` with:
- Image optimization
- API routes
- Environment variables
- Performance optimizations

### Tailwind CSS
Extended Tailwind configuration with:
- Custom color palette
- Typography plugin
- Forms plugin
- Custom animations

### TypeScript
Strict TypeScript configuration with:
- Path aliases
- Strict type checking
- Modern ES features

## 📱 Responsive Design

Built with a mobile-first approach:
- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px
- **Desktop**: 1024px+

## 🚀 Deployment

### Vercel (Recommended)
1. Push your code to GitHub
2. Connect your repository to Vercel
3. Deploy automatically on every push

### Docker Production
1. Build production image
   ```bash
   docker build -t mcnary-tech-blog:prod .
   ```

2. Run with environment variables
   ```bash
   docker run -p 3000:3000 \
     -e NODE_ENV=production \
     -e API_URL=https://api.yourdomain.com \
     mcnary-tech-blog:prod
   ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check the inline code comments and TypeScript types
- **Issues**: Report bugs and feature requests via GitHub Issues
- **Discussions**: Join the conversation in GitHub Discussions

## 🔮 Roadmap

- [ ] Enhanced admin interface
- [ ] Advanced search capabilities
- [ ] Content analytics dashboard
- [ ] Multi-language support
- [ ] Advanced caching strategies
- [ ] Performance monitoring
- [ ] A/B testing framework

---

Built with ❤️ using modern web technologies for the McNary Tech platform
