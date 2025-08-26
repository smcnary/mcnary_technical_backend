import React, { useState } from 'react';
import LoadingModal, { AuthLoadingModal, RegistrationLoadingModal, RedirectLoadingModal } from './LoadingModal';

export default function LoadingModalDemo() {
  const [showBasic, setShowBasic] = useState(false);
  const [showAuth, setShowAuth] = useState(false);
  const [showRegistration, setShowRegistration] = useState(false);
  const [showRedirect, setShowRedirect] = useState(false);
  const [showProgress, setShowProgress] = useState(false);
  const [progress, setProgress] = useState(0);

  const simulateProgress = () => {
    setShowProgress(true);
    setProgress(0);
    
    const interval = setInterval(() => {
      setProgress(prev => {
        if (prev >= 100) {
          clearInterval(interval);
          setTimeout(() => setShowProgress(false), 1000);
          return 100;
        }
        return prev + 10;
      });
    }, 200);
  };

  return (
    <div className="min-h-screen bg-gray-50 py-12 px-4">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-8 text-center">
          Loading Modal Demo
        </h1>
        
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Basic Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Basic Loading</h3>
            <p className="text-gray-600 mb-4 text-sm">
              A customizable loading modal with title, message, and spinner.
            </p>
            <button
              onClick={() => setShowBasic(true)}
              className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Show Basic Modal
            </button>
          </div>

          {/* Auth Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Authentication</h3>
            <p className="text-gray-600 mb-4 text-sm">
              Specialized modal for login/signup processes.
            </p>
            <button
              onClick={() => setShowAuth(true)}
              className="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
            >
              Show Auth Modal
            </button>
          </div>

          {/* Registration Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Registration</h3>
            <p className="text-gray-600 mb-4 text-sm">
              Modal for account creation processes.
            </p>
            <button
              onClick={() => setShowRegistration(true)}
              className="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
            >
              Show Registration Modal
            </button>
          </div>

          {/* Redirect Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Redirect</h3>
            <p className="text-gray-600 mb-4 text-sm">
              Modal for navigation/redirect processes.
            </p>
            <button
              onClick={() => setShowRedirect(true)}
              className="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors"
            >
              Show Redirect Modal
            </button>
          </div>

          {/* Progress Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Progress Bar</h3>
            <p className="text-gray-600 mb-4 text-sm">
              Modal with progress bar for long-running tasks.
            </p>
            <button
              onClick={simulateProgress}
              className="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Show Progress Modal
            </button>
          </div>

          {/* Custom Loading Modal */}
          <div className="bg-white rounded-xl shadow-md p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Custom</h3>
            <p className="text-gray-600 mb-4 text-sm">
              Fully customizable modal with close button.
            </p>
            <button
              onClick={() => setShowBasic(true)}
              className="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors"
            >
              Show Custom Modal
            </button>
          </div>
        </div>

        {/* Auto-hide modals for demo */}
        {showAuth && setTimeout(() => setShowAuth(false), 3000)}
        {showRegistration && setTimeout(() => setShowRegistration(false), 4000)}
        {showRedirect && setTimeout(() => setShowRedirect(false), 2500)}
        {showBasic && setTimeout(() => setShowBasic(false), 3500)}

        {/* Loading Modals */}
        <LoadingModal
          isOpen={showBasic}
          title="Processing Request"
          message="Please wait while we handle your request. This may take a few moments."
          showSpinner={true}
        />

        <AuthLoadingModal isOpen={showAuth} action="authenticating" />

        <RegistrationLoadingModal isOpen={showRegistration} />

        <RedirectLoadingModal isOpen={showRedirect} destination="dashboard" />

        <LoadingModal
          isOpen={showProgress}
          title="Uploading Files"
          message="Please wait while we upload your files. Do not close this window."
          showSpinner={true}
          progress={progress}
          allowClose={true}
          onClose={() => setShowProgress(false)}
        />
      </div>
    </div>
  );
}
