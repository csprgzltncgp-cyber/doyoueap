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
  country_id: string;
  value: string;
}

interface RiportSummaryResponse {
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
  month: number;
  year: string;
  period_type: 'month' | 'quarter';
  riports: Riport[];
  riport_values: string;
  customer_satisfaction_values: CustomerSatisfactionValue[];
}

interface UseProgramReportsDataOptions {
  countryId?: number | null;
  quarter?: number | null;
  year?: number | null;
  periodType?: 'quarter' | 'month';
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
