import { apiService, User, LoginResponse } from './api';

export interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  email: string;
  name: string;
  password: string;
  confirmPassword: string;
}

class AuthService {
  private state: AuthState = {
    user: null,
    token: null,
    isAuthenticated: false,
    isLoading: false,
    error: null,
  };

  private listeners: ((state: AuthState) => void)[] = [];

  constructor() {
    this.initializeAuth();
  }

  // Initialize authentication state
  private async initializeAuth(): Promise<void> {
    const token = apiService.getAuthToken();
    if (token) {
      // Set the token in state but don't validate it immediately
      // This prevents the circular dependency issue
      this.state.token = token;
      this.state.isAuthenticated = true;
      this.state.isLoading = false;
      this.notifyListeners();
      
      // Optionally validate the token in the background
      this.validateTokenInBackground(token);
    }
  }

  // Validate token in background without blocking initialization
  private async validateTokenInBackground(token: string): Promise<void> {
    try {
      const user = await apiService.getCurrentUser();
      this.state.user = user;
      this.state.error = null;
      this.notifyListeners();
    } catch (error) {
      console.warn('Token validation failed, clearing auth state:', error);
      this.logout();
    }
  }

  // Subscribe to auth state changes
  subscribe(listener: (state: AuthState) => void): () => void {
    this.listeners.push(listener);
    // Return unsubscribe function
    return () => {
      const index = this.listeners.indexOf(listener);
      if (index > -1) {
        this.listeners.splice(index, 1);
      }
    };
  }

  // Notify all listeners of state changes
  private notifyListeners(): void {
    this.listeners.forEach(listener => listener({ ...this.state }));
  }

  // Get current auth state
  getState(): AuthState {
    return { ...this.state };
  }

  // Login user
  async login(credentials: LoginCredentials): Promise<LoginResponse> {
    try {
      this.state.isLoading = true;
      this.state.error = null;
      this.notifyListeners();

      const response = await apiService.login(credentials.email, credentials.password);
      
      this.state.user = response.user;
      this.state.token = response.token;
      this.state.isAuthenticated = true;
      this.state.error = null;

      this.notifyListeners();
      return response;
    } catch (error) {
      this.state.error = error instanceof Error ? error.message : 'Login failed';
      this.notifyListeners();
      throw error;
    } finally {
      this.state.isLoading = false;
      this.notifyListeners();
    }
  }

  // Logout user
  async logout(): Promise<void> {
    try {
      if (this.state.token) {
        await apiService.logout();
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Clear state regardless of API call success
      this.state.user = null;
      this.state.token = null;
      this.state.isAuthenticated = false;
      this.state.error = null;
      this.notifyListeners();
    }
  }

  // Refresh authentication
  async refreshAuth(): Promise<boolean> {
    const token = apiService.getAuthToken();
    if (!token) return false;

    try {
      const user = await apiService.getCurrentUser();
      this.state.user = user;
      this.state.token = token;
      this.state.isAuthenticated = true;
      this.state.error = null;
      this.notifyListeners();
      return true;
    } catch (error) {
      console.error('Failed to refresh authentication:', error);
      this.logout();
      return false;
    }
  }

  // Check if user has specific role
  hasRole(role: string): boolean {
    return this.state.user?.roles.includes(role) || false;
  }

  // Check if user has any of the specified roles
  hasAnyRole(roles: string[]): boolean {
    return this.state.user?.roles.some(role => roles.includes(role)) || false;
  }

  // Check if user is admin
  isAdmin(): boolean {
    return this.hasAnyRole(['ROLE_SYSTEM_ADMIN', 'ROLE_AGENCY_ADMIN', 'ROLE_AGENCY_STAFF', 'ROLE_CLIENT_STAFF']);
  }

  // Check if user is client admin
  isClientAdmin(): boolean {
    return this.hasRole('ROLE_CLIENT_ADMIN');
  }

  // Check if user is client staff
  isClientStaff(): boolean {
    return this.hasRole('ROLE_CLIENT_STAFF');
  }

  // Check if user is sales consultant
  isSalesConsultant(): boolean {
    return this.hasRole('ROLE_SALES_CONSULTANT');
  }

  // Get user's client ID
  getClientId(): string | null {
    return this.state.user?.clientId || null;
  }

  // Get user's tenant ID
  getTenantId(): string | null {
    return this.state.user?.tenantId || null;
  }

  // Clear error
  clearError(): void {
    this.state.error = null;
    this.notifyListeners();
  }

  // Set loading state
  setLoading(isLoading: boolean): void {
    this.state.isLoading = isLoading;
    this.notifyListeners();
  }
}

// Export singleton instance
export const authService = new AuthService();
export default authService;
