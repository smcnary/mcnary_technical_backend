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

  const importLeadgenData = useCallback(async (leads: any[], clientId?: string, sourceId?: string) => {
    return dataService.importLeadgenData(leads, clientId, sourceId);
  }, []);

  const getLeadEvents = useCallback(async (leadId: string) => {
    return dataService.getLeadEvents(leadId);
  }, []);

  const getLeadStatistics = useCallback(async (leadId: string) => {
    return dataService.getLeadStatistics(leadId);
  }, []);

  const createLeadEvent = useCallback(async (leadId: string, eventData: {
    type: string;
    direction?: string;
    duration?: number;
    notes?: string;
    outcome?: string;
    next_action?: string;
  }) => {
    return dataService.createLeadEvent(leadId, eventData);
  }, []);

  // LEADGEN EXECUTION (Admin Only)
  const executeLeadgenCampaign = useCallback(async (config: any) => {
    return dataService.executeLeadgenCampaign(config);
  }, []);

  const getLeadgenVerticals = useCallback(async () => {
    return dataService.getLeadgenVerticals();
  }, []);

  const getLeadgenSources = useCallback(async () => {
    return dataService.getLeadgenSources();
  }, []);

  const getLeadgenCampaignStatus = useCallback(async (campaignId: string) => {
    return dataService.getLeadgenCampaignStatus(campaignId);
  }, []);

  const getLeadgenTemplate = useCallback(async () => {
    return dataService.getLeadgenTemplate();
  }, []);

  const submitLead = useCallback(async (leadData: any) => {
    return dataService.submitLead(leadData);
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
    importLeadgenData,
    getLeadEvents,
    getLeadStatistics,
    createLeadEvent,
    executeLeadgenCampaign,
    getLeadgenVerticals,
    getLeadgenSources,
    getLeadgenCampaignStatus,
    getLeadgenTemplate,
    
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
