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

// Translation mappings for Hungarian labels
// API returns case_input_value IDs - these are stable and language-independent
export const VALUE_TRANSLATIONS = {
  // Problem types (case_input_id = 7) - Permission IDs
  // Mapped from Laravel permissions table
  problemTypes: {
    // Permission IDs for main problem types
    '1': 'Pszichológia',
    '2': 'Jog',
    '3': 'Pénzügy',
    '4': 'Egészség',
    '5': 'Coaching',
    '6': 'Munkajog',
    '7': 'Adójog',
  },
  // Gender (case_input_id = 10) - case_input_value IDs
  gender: {
    '9': 'Férfi',      // Male
    '10': 'Nő',        // Female
    '208': 'Egyéb',    // Other/Unknown
  },
  // Age groups (case_input_id = 11) - case_input_value IDs
  age: {
    '11': '18 alatt',
    '12': '18-25',
    '13': '26-35',
    '14': '36-45',
    '15': '46-55',
    '16': '56+',
    // Alternative age group IDs
    '269': '20 alatt',
    '270': '20-29',
    '271': '30-39',
    '272': '40-49',
    '273': '50-59',
    '274': '60+',
  },
  // Employee or family member (case_input_id = 9) - case_input_value IDs
  employeeOrFamily: {
    '7': 'Dolgozó',    // Employee
    '8': 'Családtag',  // Family Member
  },
  // Place of receipt / consultation mode (case_input_id = 6) - case_input_value IDs
  placeOfReceipt: {
    '1': 'Személyes',   // In-Person
    '2': 'Telefon',     // Phone
    '3': 'Videó',       // Video
    '4': 'Online',      // Online
    '5': 'Chat',        // Chat
    '6': 'E-mail',      // Email
    '163': 'EAP Online/Mobil', // EAP Online/Mobile Application
  },
  // Crisis (case_input_id = 3) - case_input_value IDs
  crisis: {
    '17': 'Igen',      // Yes
    '18': 'Nem',       // No
  },
  // Source (case_input_id = 12) - case_input_value IDs
  source: {
    '19': 'Weboldal',
    '20': 'Telefon',
    '21': 'E-mail',
    '22': 'Személyes',
    '23': 'Poster/Szórólap',
    '24': 'HR ajánlás',
    '25': 'Kolléga',
    '26': 'Családtag',
    '27': 'Korábbi felhasználó',
    '28': 'Egyéb',
    // Alternative source IDs
    '159': 'Weboldal',
    '160': 'Telefon',
    '161': 'E-mail',
    '162': 'HR ajánlás',
    '164': 'Kolléga',
    '165': 'Egyéb',
  },
  // Language (case_input_id = 32) - uses language_skill IDs
  language: {
    '1': 'Magyar',
    '2': 'Angol',
    '3': 'Német',
    '4': 'Cseh',
    '5': 'Szlovák',
    '6': 'Lengyel',
    '7': 'Román',
    '8': 'Francia',
    '9': 'Spanyol',
    '10': 'Olasz',
    '11': 'Orosz',
    '12': 'Ukrán',
    '13': 'Szerb',
    '14': 'Horvát',
    '15': 'Szlovén',
    '16': 'Bolgár',
    '17': 'Görög',
    '18': 'Török',
    '19': 'Portugál',
    '20': 'Holland',
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
