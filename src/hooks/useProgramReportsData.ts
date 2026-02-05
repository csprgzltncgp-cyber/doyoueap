import { useState, useEffect, useCallback } from 'react';
import { supabase } from '@/integrations/supabase/client';

interface Country {
  id: number;
  name: string;
  code: string;
}

interface Riport {
  id: number;
  company_id: number;
  from: string;
  to: string;
  is_active: boolean;
}

interface CustomerSatisfactionValue {
  id: string;
  customer_satisfaction_id: string;
  country_id: string | number;
  value: string | number;
}

// Processed statistics from edge function
interface ProcessedStatistics {
  caseNumbers: {
    closed: number;
    interrupted: number;
    clientUnreachable: number;
    inProgress: number;
    total: number;
  };
  consultations: {
    total: number;
    onsite: number;
    ongoing: number;
  };
  activities: {
    workshop: number;
    crisis: number;
    orientation: number;
    healthDay: number;
    expertOutplacement: number;
    prizegame: number;
  };
  problemTypes: Record<string, number>;
  typeOfProblem: Record<string, number>;
  gender: Record<string, number>;
  age: Record<string, number>;
  employeeOrFamily: Record<string, number>;
  placeOfReceipt: Record<string, number>;
  language: Record<string, number>;
  source: Record<string, number>;
  crisis: Record<string, number>;
}

interface StatisticsPercentages {
  problemTypes: Record<string, number>;
  typeOfProblem: Record<string, number>;
  gender: Record<string, number>;
  age: Record<string, number>;
  employeeOrFamily: Record<string, number>;
  placeOfReceipt: Record<string, number>;
  language: Record<string, number>;
  source: Record<string, number>;
  crisis: Record<string, number>;
}

interface Highlights {
  mostFrequentProblem: { key: string; count: number; percentage: number } | null;
  dominantGender: { key: string; count: number; percentage: number } | null;
  dominantAgeGroup: { key: string; count: number; percentage: number } | null;
}

export interface RiportSummaryResponse {
  company: {
    id: number;
    name: string;
  };
  countries: Country[];
  current_country: Country | null;
  interval: {
    from: string;
    to: string;
  };
  quarter: number | null | string;
  month: number | null;
  year: string | number;
  period_type: 'month' | 'quarter';
  riports: Riport[];
  riport_values?: unknown[];
  customer_satisfaction_values: CustomerSatisfactionValue[];
  // Processed data from edge function
  processed_statistics: ProcessedStatistics | null;
  statistics_percentages: StatisticsPercentages | null;
  highlights: Highlights | null;
}

interface UseProgramReportsDataOptions {
  countryId?: number | null;
  quarter?: number | null;
  year?: number | null;
  periodType?: 'quarter' | 'month';
}

// Translation mappings for Hungarian labels (API now returns actual values, not IDs)
export const VALUE_TRANSLATIONS = {
  // Problem types - API returns: "Pszichológiai", "Jogi", "Pénzügyi", etc.
  problemTypes: {
    'Pszichológiai': 'Pszichológia',
    'Jogi': 'Jog',
    'Pénzügyi': 'Pénzügy',
    'Egészségügyi': 'Egészség',
    'Coaching': 'Coaching',
    // Keep direct mappings for already translated values
    'Pszichológia': 'Pszichológia',
    'Jog': 'Jog',
    'Pénzügy': 'Pénzügy',
    'Egészség': 'Egészség',
  },
  // Gender - API returns: "Male", "Female"
  gender: {
    'Male': 'Férfi',
    'Female': 'Nő',
    'Férfi': 'Férfi',
    'Nő': 'Nő',
  },
  // Age groups - API returns: "under 20", "between 20 and 29", etc.
  age: {
    'under 20': '20 alatt',
    'between 20 and 29': '20-29',
    'between 30 and 39': '30-39',
    'between 40 and 49': '40-49',
    'between 50 and 59': '50-59',
    'above 59': '60+',
    // Hungarian variants
    '18 alatt': '18 alatt',
    '18-25': '18-25',
    '26-35': '26-35',
    '36-45': '36-45',
    '46-55': '46-55',
    '56+': '56+',
  },
  // Employee or family member - API returns: "Employee", "Family Member"
  employeeOrFamily: {
    'Employee': 'Dolgozó',
    'Family Member': 'Családtag',
    'Dolgozó': 'Dolgozó',
    'Családtag': 'Családtag',
  },
  // Place of receipt / consultation mode - API returns actual text values
  placeOfReceipt: {
    'Phone': 'Telefon',
    'Video': 'Videó',
    'In-Person': 'Személyes',
    'Online': 'Online',
    'EAP Online/Mobile Application': 'EAP Online/Mobil',
    'Telefon': 'Telefon',
    'Személyes': 'Személyes',
    'Videó': 'Videó',
  },
  // Crisis - API returns: "Yes", "No"
  crisis: {
    'Yes': 'Igen',
    'No': 'Nem',
    'Igen': 'Igen',
    'Nem': 'Nem',
  },
  // Source - API returns actual text values
  source: {
    'Website': 'Weboldal',
    'Phone': 'Telefon',
    'Email': 'E-mail',
    'Personal': 'Személyes',
    'Poster/Flyer': 'Poster/Szórólap',
    'HR Recommendation': 'HR ajánlás',
    'Colleague': 'Kolléga',
    'Other': 'Egyéb',
    'Family member': 'Családtag',
    'I have used the program before': 'Korábbi felhasználó',
  },
};

// Helper function to translate a value code to Hungarian label
export function translateValue(category: keyof typeof VALUE_TRANSLATIONS, code: string): string {
  const translations = VALUE_TRANSLATIONS[category];
  return translations[code as keyof typeof translations] || code;
}

// Helper function to convert record with codes to record with labels
export function translateDistribution(
  category: keyof typeof VALUE_TRANSLATIONS,
  distribution: Record<string, number>
): Record<string, number> {
  const result: Record<string, number> = {};
  for (const [code, value] of Object.entries(distribution)) {
    const label = translateValue(category, code);
    result[label] = value;
  }
  return result;
}

export const useProgramReportsData = (options: UseProgramReportsDataOptions = {}) => {
  const [data, setData] = useState<RiportSummaryResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchData = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const { data: sessionData, error: sessionError } = await supabase.auth.getSession();
      
      if (sessionError || !sessionData.session) {
        throw new Error('Not authenticated');
      }

      // Always use current year (2025) if not explicitly specified
      const currentYear = new Date().getFullYear();
      
      const response = await supabase.functions.invoke('fetch-riport-data', {
        body: {
          country_id: options.countryId,
          quarter: options.quarter,
          year: options.year ?? currentYear,
          period_type: options.periodType || 'quarter',
          include_customer_satisfaction: true,
          include_customer_satisfaction_values: true,
        },
      });

      if (response.error) {
        throw new Error(response.error.message || 'Failed to fetch data');
      }

      if (!response.data.success) {
        throw new Error(response.data.error || 'Failed to fetch data');
      }

      setData(response.data.data);
    } catch (err) {
      console.error('Error fetching program reports data:', err);
      setError(err instanceof Error ? err.message : 'Unknown error');
    } finally {
      setLoading(false);
    }
  }, [options.countryId, options.quarter, options.year, options.periodType]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return { data, loading, error, refetch: fetchData };
};
