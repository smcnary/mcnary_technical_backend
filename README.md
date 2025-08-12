# McNary Technical Backend

A Symfony-based backend API with a separate React frontend, providing a professional legal services platform with lead capture, case studies, and FAQ management.

## 🚀 Features

- **Lead Capture Form**: Professional legal inquiry submission
- **Case Studies Display**: Showcase successful legal cases with filtering
- **FAQ Management**: Searchable frequently asked questions
- **Responsive Design**: Mobile-first, modern UI/UX
- **Real-time API Integration**: Direct connection to Symfony backend
- **TypeScript**: Full type safety and IntelliSense

## 🛠️ Technology Stack

### Backend
- **Symfony 6** with PHP 8+
- **API Platform** for REST/GraphQL APIs
- **Doctrine ORM** with database migrations
- **JWT Authentication**
- **Multi-tenancy support**

### Frontend
- **React 18** with TypeScript
- **Modern CSS** with responsive design
- **Fetch API** for backend communication
- **Component-based architecture**
- **Mobile-responsive design**

## 📋 Prerequisites

### Backend
- PHP 8.1+
- Composer
- MySQL/PostgreSQL database
- Symfony CLI (optional)

### Frontend
- Node.js 16+ and npm
- Running Symfony backend
- Backend accessible at `http://localhost:8000`

## 🚀 Quick Start

### Backend Setup
```bash
cd backend

# Install dependencies
composer install

# Set up environment
cp .env.example .env
# Edit .env with your database credentials

# Run migrations
php bin/console doctrine:migrations:migrate

# Start server
symfony server:start
# Or: php -S localhost:8000 -t public/
```

### Frontend Setup
```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

The backend will run at `http://localhost:8000` and frontend at `http://localhost:3000`

## 🔗 Project Structure

```
mcnary_technical_backend/
├── backend/               # Symfony backend application
│   ├── src/              # PHP source code
│   │   ├── Entity/       # Doctrine entities
│   │   ├── Controller/   # API controllers
│   │   ├── Repository/   # Data repositories
│   │   └── ...
│   ├── config/           # Symfony configuration
│   ├── migrations/       # Database migrations
│   ├── public/           # Web root directory
│   ├── composer.json     # Backend dependencies
│   └── README.md         # Backend documentation
├── frontend/             # React frontend application
│   ├── src/
│   │   ├── components/   # React components
│   │   ├── services/     # API services
│   │   └── App.tsx       # Main app component
│   ├── package.json      # Frontend dependencies
│   ├── vite.config.ts    # Build configuration
│   └── README.md         # Frontend documentation
├── README.md             # Main project documentation
└── .gitignore           # Git ignore rules
```

### API Endpoints
- `GET /api` - API discovery
- `POST /api/leads` - Lead submission
- `GET /api/case_studies` - Case studies listing
- `GET /api/faqs` - FAQ listing

## 📱 Components

### LeadForm
- Professional legal inquiry form
- Practice area selection
- Budget and timeline options
- Consent management
- Form validation

### CaseStudies
- Grid layout for case studies
- Practice area filtering
- Active/inactive status filtering
- Responsive card design
- Metrics display

### Faqs
- Accordion-style FAQ display
- Search functionality
- Expandable questions
- Status indicators

## 🎨 Styling

- **Modern Design**: Clean, professional legal services aesthetic
- **Responsive Layout**: Works on all device sizes
- **Interactive Elements**: Hover effects and smooth transitions
- **Color Scheme**: Professional blues and grays
- **Typography**: Readable, accessible fonts

## 🔧 Configuration

### API Base URL
The frontend connects to the backend at `http://localhost:8000` by default. To change this:

1. Edit `src/services/api.ts`
2. Update `API_BASE_URL` constant
3. Restart the development server

### Environment Variables
Create a `.env` file in the root directory:
```env
REACT_APP_API_BASE_URL=http://localhost:8000
```

## 📱 Responsive Design

- **Desktop**: Full-featured layout with side-by-side elements
- **Tablet**: Optimized for medium screens
- **Mobile**: Single-column layout with touch-friendly controls

## 🧪 Testing

```bash
# Run tests
npm test

# Run tests with coverage
npm test -- --coverage

# Run tests in watch mode
npm test -- --watch
```

## 🚀 Deployment

### Build for Production
```bash
npm run build
```

### Deploy to Static Hosting
The `build` folder contains optimized static files ready for deployment to:
- Netlify
- Vercel
- AWS S3
- GitHub Pages
- Any static hosting service

### Environment Configuration
For production, update the API base URL to point to your production backend.

## 🔒 Security Features

- **Input Validation**: Client-side form validation
- **CORS Handling**: Proper cross-origin request handling
- **Error Handling**: Graceful error display and recovery
- **Data Sanitization**: Safe data transmission to backend

## 📊 Performance

- **Lazy Loading**: Components load on demand
- **Optimized Images**: Responsive image handling
- **Minified CSS/JS**: Production builds are optimized
- **Efficient Rendering**: React optimization techniques

## 🐛 Troubleshooting

### Common Issues

1. **Backend Connection Failed**
   - Ensure Symfony backend is running
   - Check backend URL in `api.ts`
   - Verify CORS configuration

2. **Build Errors**
   - Clear `node_modules` and reinstall
   - Check TypeScript compilation
   - Verify React version compatibility

3. **Styling Issues**
   - Check CSS imports
   - Verify responsive breakpoints
   - Clear browser cache

### Debug Mode
Enable React DevTools in your browser for component debugging.

## 📚 API Documentation

### Lead Submission
```typescript
const leadData = {
  name: "John Doe",
  email: "john@example.com",
  phone: "+1234567890",
  practiceAreas: ["Personal Injury"],
  consent: true
};

await apiService.submitLead(leadData);
```

### Fetching Data
```typescript
// Get case studies
const caseStudies = await apiService.getCaseStudies();

// Get FAQs
const faqs = await apiService.getFaqs();
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is proprietary software for McNary Legal Services.

## 🆘 Support

For technical support or questions:
- Check the backend documentation
- Review API responses in browser dev tools
- Verify backend connectivity
- Check console for error messages

---

**Built with ❤️ for McNary Legal Services**
