import { useState, useEffect, useCallback } from 'react';
import { dataService, DataServiceState } from '../services/dataService';

export function useData() {
  const [dataState, setDataState] = useState<DataServiceState>(dataService.getState());

  useEffect(() => {
    const unsubscribe = dataService.subscribe(setDataState);
    return unsubscribe;
  }, []);

  // CLIENT MANAGEMENT
  const getClients = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getClients(params);
  }, []);

  const getClient = useCallback(async (id: string) => {
    return dataService.getClient(id);
  }, []);

  const createClient = useCallback(async (clientData: any) => {
    return dataService.createClient(clientData);
  }, []);

  const updateClient = useCallback(async (id: string, clientData: any) => {
    return dataService.updateClient(id, clientData);
  }, []);

  // CAMPAIGN MANAGEMENT
  const getCampaigns = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getCampaigns(params);
  }, []);

  const createCampaign = useCallback(async (campaignData: any) => {
    return dataService.createCampaign(campaignData);
  }, []);

  const updateCampaign = useCallback(async (id: string, campaignData: any) => {
    return dataService.updateCampaign(id, campaignData);
  }, []);

  // PACKAGE MANAGEMENT
  const getPackages = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getPackages(params);
  }, []);

  const getPackage = useCallback(async (id: string) => {
    return dataService.getPackage(id);
  }, []);

  // PAGE MANAGEMENT
  const getPages = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getPages(params);
  }, []);

  const getPage = useCallback(async (slug: string) => {
    return dataService.getPage(slug);
  }, []);

  // MEDIA ASSET MANAGEMENT
  const getMediaAssets = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getMediaAssets(params);
  }, []);

  const getMediaAsset = useCallback(async (id: string) => {
    return dataService.getMediaAsset(id);
  }, []);

  // FAQ MANAGEMENT
  const getFaqs = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getFaqs(params);
  }, []);

  const getFaq = useCallback(async (id: string) => {
    return dataService.getFaq(id);
  }, []);

  // CASE STUDY MANAGEMENT
  const getCaseStudies = useCallback(async () => {
    return dataService.getCaseStudies();
  }, []);

  const getCaseStudy = useCallback(async (id: string) => {
    return dataService.getCaseStudy(id);
  }, []);

  // LEAD MANAGEMENT
  const getLeads = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getLeads(params);
  }, []);

  const submitLead = useCallback(async (leadData: any) => {
    return dataService.submitLead(leadData);
  }, []);

  const importLeads = useCallback(async (csvData: string, options?: {
    clientId?: string;
    sourceId?: string;
    overwriteExisting?: boolean;
  }) => {
    return dataService.importLeads(csvData, options);
  }, []);

  // SEO Tracking methods
  const getKeywords = useCallback(async (clientId?: string, status?: string, skip = 0, limit = 100) => {
    return dataService.getKeywords(clientId, status, skip, limit);
  }, []);

  const createKeyword = useCallback(async (keywordData: any) => {
    return dataService.createKeyword(keywordData);
  }, []);

  const updateKeyword = useCallback(async (keywordId: string, keywordData: any) => {
    return dataService.updateKeyword(keywordId, keywordData);
  }, []);

  const deleteKeyword = useCallback(async (keywordId: string) => {
    return dataService.deleteKeyword(keywordId);
  }, []);

  const getRankings = useCallback(async (keywordId?: string, clientId?: string, startDate?: string, endDate?: string, skip = 0, limit = 100) => {
    return dataService.getRankings(keywordId, clientId, startDate, endDate, skip, limit);
  }, []);

  const getReviews = useCallback(async (clientId?: string, status?: string, source?: string, skip = 0, limit = 100) => {
    return dataService.getReviews(clientId, status, source, skip, limit);
  }, []);

  const getCitations = useCallback(async (clientId?: string, status?: string, platformType?: string, skip = 0, limit = 100) => {
    return dataService.getCitations(clientId, status, platformType, skip, limit);
  }, []);

  const getKeywordPerformance = useCallback(async (clientId: string, startDate: string, endDate: string) => {
    return dataService.getKeywordPerformance(clientId, startDate, endDate);
  }, []);

  const getReviewSummary = useCallback(async (clientId: string) => {
    return dataService.getReviewSummary(clientId);
  }, []);

  const getCitationSummary = useCallback(async (clientId: string) => {
    return dataService.getCitationSummary(clientId);
  }, []);

  // Audit methods
  const getProjects = useCallback(async (clientId?: string, status?: string, skip = 0, limit = 100) => {
    return dataService.getProjects(clientId, status, skip, limit);
  }, []);

  const createProject = useCallback(async (projectData: any) => {
    return dataService.createProject(projectData);
  }, []);

  const updateProject = useCallback(async (projectId: string, projectData: any) => {
    return dataService.updateProject(projectId, projectData);
  }, []);

  const deleteProject = useCallback(async (projectId: string) => {
    return dataService.deleteProject(projectId);
  }, []);

  const getAuditRuns = useCallback(async (projectId?: string, state?: string, skip = 0, limit = 100) => {
    return dataService.getAuditRuns(projectId, state, skip, limit);
  }, []);

  const createAuditRun = useCallback(async (auditData: any) => {
    return dataService.createAuditRun(auditData);
  }, []);

  const startAudit = useCallback(async (auditRunId: string) => {
    return dataService.startAudit(auditRunId);
  }, []);

  const getAuditSummary = useCallback(async (auditRunId: string) => {
    return dataService.getAuditSummary(auditRunId);
  }, []);

  const getFindings = useCallback(async (auditRunId?: string, pageId?: string, severity?: string, category?: string, skip = 0, limit = 100) => {
    return dataService.getFindings(auditRunId, pageId, severity, category, skip, limit);
  }, []);

  const updateFinding = useCallback(async (findingId: string, status?: string, assignedTo?: string, notes?: string) => {
    return dataService.updateFinding(findingId, status, assignedTo, notes);
  }, []);

  const getAuditPages = useCallback(async (auditRunId?: string, skip = 0, limit = 100) => {
    return dataService.getAuditPages(auditRunId, skip, limit);
  }, []);

  const getAuditReport = useCallback(async (auditRunId: string, format: 'html' | 'csv' | 'json' = 'html') => {
    return dataService.getAuditReport(auditRunId, format);
  }, []);

  // USER MANAGEMENT
  const getUsers = useCallback(async (params?: Record<string, string | number | boolean>) => {
    return dataService.getUsers(params);
  }, []);

  const createUser = useCallback(async (userData: any) => {
    return dataService.createUser(userData);
  }, []);

  const updateUser = useCallback(async (id: string, userData: any) => {
    return dataService.updateUser(id, userData);
  }, []);

  // UTILITY FUNCTIONS
  const getLoadingState = useCallback((dataType: string) => {
    return dataService.getLoadingState(dataType);
  }, []);

  const getErrorState = useCallback((dataType: string) => {
    return dataService.getErrorState(dataType);
  }, []);

  const clearError = useCallback((dataType: string) => {
    dataService.clearError(dataType);
  }, []);

  const clearCache = useCallback((dataType: string) => {
    dataService.clearCache(dataType);
  }, []);

  const clearAllCache = useCallback(() => {
    dataService.clearAllCache();
  }, []);

  const refreshAllData = useCallback(async () => {
    return dataService.refreshAllData();
  }, []);

  return {
    // State
    ...dataState,
    
    // Client methods
    getClients,
    getClient,
    createClient,
    updateClient,
    
    // Campaign methods
    getCampaigns,
    createCampaign,
    updateCampaign,
    
    // Package methods
    getPackages,
    getPackage,
    
    // Page methods
    getPages,
    getPage,
    
    // Media asset methods
    getMediaAssets,
    getMediaAsset,
    
    // FAQ methods
    getFaqs,
    getFaq,
    
    // Case study methods
    getCaseStudies,
    getCaseStudy,
    
    // Lead methods
    getLeads,
    submitLead,
    importLeads,
    
    // SEO Tracking methods
    getKeywords,
    createKeyword,
    updateKeyword,
    deleteKeyword,
    getRankings,
    getReviews,
    getCitations,
    getKeywordPerformance,
    getReviewSummary,
    getCitationSummary,
    
    // Audit methods
    getProjects,
    createProject,
    updateProject,
    deleteProject,
    getAuditRuns,
    createAuditRun,
    startAudit,
    getAuditSummary,
    getFindings,
    updateFinding,
    getAuditPages,
    getAuditReport,
    
    // User methods
    getUsers,
    createUser,
    updateUser,
    
    // Utility methods
    getLoadingState,
    getErrorState,
    clearError,
    clearCache,
    clearAllCache,
    refreshAllData,
  };
}
