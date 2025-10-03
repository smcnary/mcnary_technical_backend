import { useState, useEffect, useCallback } from 'react';
import { authService, AuthState, LoginCredentials, RegisterData } from '../services/auth';

export function useAuth() {
  const [authState, setAuthState] = useState<AuthState>(authService.getState());

  useEffect(() => {
    const unsubscribe = authService.subscribe(setAuthState);
    return unsubscribe;
  }, []);

  const login = useCallback(async (credentials: LoginCredentials) => {
    return authService.login(credentials);
  }, []);

  const logout = useCallback(async () => {
    return authService.logout();
  }, []);

  const refreshAuth = useCallback(async () => {
    return authService.refreshAuth();
  }, []);

  const clearError = useCallback(() => {
    authService.clearError();
  }, []);

  const hasRole = useCallback((role: string) => {
    return authService.hasRole(role);
  }, []);

  const hasAnyRole = useCallback((roles: string[]) => {
    return authService.hasAnyRole(roles);
  }, []);

  const isAdmin = useCallback(() => {
    return authService.isAdmin();
  }, []);

  const isClientAdmin = useCallback(() => {
    return authService.isClientAdmin();
  }, []);

  const isClientStaff = useCallback(() => {
    return authService.isClientStaff();
  }, []);

  const isSalesConsultant = useCallback(() => {
    return authService.hasRole('ROLE_SALES_CONSULTANT') || authService.hasRole('ROLE_AGENCY_STAFF');
  }, []);

  const getClientId = useCallback(() => {
    return authService.getClientId();
  }, []);

  const getTenantId = useCallback(() => {
    return authService.getTenantId();
  }, []);

  return {
    ...authState,
    login,
    logout,
    refreshAuth,
    clearError,
    hasRole,
    hasAnyRole,
    isAdmin,
    isClientAdmin,
    isClientStaff,
    isSalesConsultant,
    getClientId,
    getTenantId,
  };
}
