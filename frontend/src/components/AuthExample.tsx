import { useState } from "react";
import ClientLoginModal from "./ClientLoginModal";
import ClientRegisterModal from "./ClientRegisterModal";
import { clearAuthData, getAuthToken, type LoginResponse } from "../lib/api";

/**
 * Example component showing how to use the client authentication modals
 */
export default function AuthExample() {
  const [showLogin, setShowLogin] = useState(false);
  const [showRegister, setShowRegister] = useState(false);
  const [user, setUser] = useState<LoginResponse["user"] | null>(null);

  // Check if user is already logged in on component mount
  useState(() => {
    const userData = localStorage.getItem("userData");
    if (userData) {
      try {
        setUser(JSON.parse(userData));
      } catch {
        // Invalid stored data, clear it
        clearAuthData();
      }
    }
  });

  const handleLoginSuccess = (response: LoginResponse) => {
    setUser(response.user);
    console.log("Login successful:", response);
    // Redirect to dashboard or update app state
  };

  const handleLogout = () => {
    clearAuthData();
    setUser(null);
    // Redirect to home page or update app state
  };

  return (
    <div className="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full space-y-8">
        {user ? (
          // Logged in state
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-2xl font-bold text-center text-gray-900 mb-4">
              Welcome back!
            </h2>
            <div className="space-y-2 text-sm text-gray-600">
              <p><strong>Name:</strong> {user.name || "N/A"}</p>
              <p><strong>Email:</strong> {user.email}</p>
              <p><strong>Role:</strong> {user.role}</p>
              <p><strong>Status:</strong> {user.status}</p>
            </div>
            <button
              onClick={handleLogout}
              className="mt-4 w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors"
            >
              Logout
            </button>
          </div>
        ) : (
          // Not logged in state
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-2xl font-bold text-center text-gray-900 mb-6">
              Client Portal
            </h2>
            <div className="space-y-4">
              <button
                onClick={() => setShowLogin(true)}
                className="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
              >
                Login
              </button>
              <button
                onClick={() => setShowRegister(true)}
                className="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
              >
                Register New Client
              </button>
            </div>
            <div className="mt-4 text-center text-sm text-gray-600">
              <p>Token stored: {getAuthToken() ? "Yes" : "No"}</p>
            </div>
          </div>
        )}

        {/* Login Modal */}
        <ClientLoginModal
          open={showLogin}
          onClose={() => setShowLogin(false)}
          onSuccess={handleLoginSuccess}
        />

        {/* Register Modal */}
        {showRegister && (
          <div className="fixed inset-0 z-50">
            <ClientRegisterModal />
          </div>
        )}
      </div>
    </div>
  );
}
