

interface LoadingModalProps {
  isOpen: boolean;
  title?: string;
  message?: string;
  showSpinner?: boolean;
  progress?: number; // 0-100 for progress bar
  onClose?: () => void;
  allowClose?: boolean;
}

export default function LoadingModal({
  isOpen,
  title = "Loading...",
  message = "Please wait while we process your request.",
  showSpinner = true,
  progress,
  onClose,
  allowClose = false
}: LoadingModalProps) {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[70] animate-fade-in" role="dialog" aria-modal="true" aria-labelledby="loading-title">
      {/* Overlay */}
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300" />
      
      {/* Modal */}
      <div className="absolute inset-0 flex items-center justify-center px-4">
        <div className="w-full max-w-md rounded-2xl bg-white shadow-2xl ring-1 ring-black/10 animate-slide-up">
          <div className="px-8 py-8 text-center">
            {/* Close button (optional) */}
            {allowClose && onClose && (
              <button
                onClick={onClose}
                className="absolute top-4 right-4 inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 transition-all duration-200"
                aria-label="Close"
              >
                <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
              </button>
            )}

            {/* Spinner */}
            {showSpinner && (
              <div className="mx-auto mb-6 h-16 w-16">
                <div className="relative h-full w-full">
                  {/* Outer ring */}
                  <div className="absolute inset-0 rounded-full border-4 border-gray-200"></div>
                  {/* Spinning ring */}
                  <div className="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-600 animate-spin"></div>
                  {/* Inner circle */}
                  <div className="absolute inset-2 rounded-full bg-gray-50"></div>
                </div>
              </div>
            )}

            {/* Title */}
            <h2 id="loading-title" className="text-xl font-bold text-gray-900 mb-3">
              {title}
            </h2>

            {/* Message */}
            <p className="text-gray-600 mb-6 leading-relaxed">
              {message}
            </p>

            {/* Progress bar (optional) */}
            {progress !== undefined && (
              <div className="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div 
                  className="bg-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
                  style={{ width: `${progress}%` }}
                ></div>
              </div>
            )}

            {/* Progress percentage (optional) */}
            {progress !== undefined && (
              <p className="text-sm text-gray-500 font-medium">
                {progress}% Complete
              </p>
            )}

            {/* Additional loading indicators */}
            <div className="flex justify-center space-x-2 mt-6">
              <div className="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></div>
              <div className="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style={{ animationDelay: '150ms' }}></div>
              <div className="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style={{ animationDelay: '300ms' }}></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// Specialized loading modals for common use cases
export function AuthLoadingModal({ isOpen, action = "logging in" }: { isOpen: boolean; action?: string }) {
  return (
    <LoadingModal
      isOpen={isOpen}
      title="Authenticating..."
      message={`Please wait while we ${action}. This may take a few moments.`}
      showSpinner={true}
    />
  );
}

export function RegistrationLoadingModal({ isOpen }: { isOpen: boolean }) {
  return (
    <LoadingModal
      isOpen={isOpen}
      title="Creating Your Account..."
      message="We're setting up your organization, client profile, and admin account. This process takes a moment to ensure everything is configured correctly."
      showSpinner={true}
    />
  );
}

export function RedirectLoadingModal({ isOpen, destination = "dashboard" }: { isOpen: boolean; destination?: string }) {
  return (
    <LoadingModal
      isOpen={isOpen}
      title="Redirecting..."
      message={`Taking you to your ${destination}. Please wait...`}
      showSpinner={true}
    />
  );
}
