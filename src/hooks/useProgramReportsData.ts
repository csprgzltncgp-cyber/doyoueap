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

// Translation mappings for Hungarian labels (based on Laravel case_input_values)
export const VALUE_TRANSLATIONS = {
  // Problem types (case_input_id = 7)
  problemTypes: {
    '1': 'Pszichológia',
    '2': 'Jog',
    '3': 'Pénzügy',
    '4': 'Egészség',
    '5': 'Coaching',
  },
  // Gender (case_input_id = 10)
  gender: {
    '9': 'Férfi',
    '10': 'Nő',
  },
  // Age groups (case_input_id = 11)
  age: {
    '11': '18 alatt',
    '12': '18-25',
    '13': '26-35',
    '14': '36-45',
    '15': '46-55',
    '16': '56+',
  },
  // Employee or family member (case_input_id = 9)
  employeeOrFamily: {
    '7': 'Dolgozó',
    '8': 'Családtag',
  },
  // Place of receipt / consultation mode (case_input_id = 6)
  placeOfReceipt: {
    '3': 'Telefon',
    '4': 'Online',
    '5': 'Személyes',
    '6': 'Videó',
  },
  // Crisis (case_input_id = 3)
  crisis: {
    '1': 'Igen',
    '2': 'Nem',
  },
  // Source (case_input_id = 12)
  source: {
    '18': 'Weboldal',
    '19': 'Telefon',
    '20': 'E-mail',
    '21': 'Személyes',
    '22': 'Poster/Szórólap',
    '23': 'HR ajánlás',
    '24': 'Kolléga',
    '25': 'Egyéb',
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

      const response = await supabase.functions.invoke('fetch-riport-data', {
        body: {
          country_id: options.countryId,
          quarter: options.quarter,
          year: options.year,
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
