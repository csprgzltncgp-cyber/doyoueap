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
  problemDetails: Record<string, number>;
  gender: Record<string, number>;
  age: Record<string, number>;
  employeeOrFamily: Record<string, number>;
  placeOfReceipt: Record<string, number>;
  language: Record<string, number>;
  source: Record<string, number>;
  crisis: Record<string, number>;
  genderByProblemType: Record<string, Record<string, number>>;
  ageByProblemType: Record<string, Record<string, number>>;
}

interface StatisticsPercentages {
  problemTypes: Record<string, number>;
  typeOfProblem: Record<string, number>;
  problemDetails: Record<string, number>;
  gender: Record<string, number>;
  age: Record<string, number>;
  employeeOrFamily: Record<string, number>;
  placeOfReceipt: Record<string, number>;
  language: Record<string, number>;
  source: Record<string, number>;
  crisis: Record<string, number>;
  genderByProblemType: Record<string, Record<string, number>>;
  ageByProblemType: Record<string, Record<string, number>>;
}

interface Highlights {
  mostFrequentProblem: { key: string; count: number; percentage: number } | null;
  dominantGender: { key: string; count: number; percentage: number } | null;
  dominantAgeGroup: { key: string; count: number; percentage: number } | null;
}

// Value type mappings from API (type -> value -> label)
export type ValueTypeMapping = {
  [type: string]: {
    [value: string]: string;
  };
};

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
  // Value type mappings from API
  value_type_mappings?: ValueTypeMapping;
}

interface UseProgramReportsDataOptions {
  countryId?: number | null;
  quarter?: number | null;
  year?: number | null;
  periodType?: 'quarter' | 'month';
}

// Riport value type constants matching Laravel
export const RIPORT_VALUE_TYPE_IDS = {
  CRISIS: '3',
  PLACE_OF_RECEIPT: '6',
  PROBLEM_TYPE: '7',
  EMPLOYEE_OR_FAMILY: '9',
  GENDER: '10',
  AGE: '11',
  SOURCE: '12',
  LANGUAGE: '32',
};

// Helper function to get label from API mappings
export function getValueLabel(
  mappings: ValueTypeMapping | undefined,
  typeId: string,
  valueId: string
): string {
  if (!mappings || !mappings[typeId]) {
    return valueId; // Return raw value if no mapping
  }
  return mappings[typeId][valueId] || valueId;
}

// Helper function to translate a distribution using API mappings
export function translateDistributionFromApi(
  mappings: ValueTypeMapping | undefined,
  typeId: string,
  distribution: Record<string, number>
): Record<string, number> {
  const result: Record<string, number> = {};
  for (const [valueId, count] of Object.entries(distribution)) {
    const label = getValueLabel(mappings, typeId, valueId);
    result[label] = count;
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
