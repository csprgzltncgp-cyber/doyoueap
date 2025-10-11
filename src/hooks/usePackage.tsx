import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { useAuth } from './useAuth';

export type PackageType = 'starter' | 'professional' | 'enterprise' | null;

export const usePackage = () => {
  const { user } = useAuth();
  const [packageType, setPackageType] = useState<PackageType>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPackage = async () => {
      if (!user) {
        setPackageType(null);
        setLoading(false);
        return;
      }

      try {
        const { data, error } = await supabase
          .from('profiles')
          .select('selected_package')
          .eq('id', user.id)
          .single();

        if (error) {
          console.error('Error fetching package:', error);
          setPackageType(null);
        } else {
          // Map "pro" to "professional" for backwards compatibility
          const rawPackage = data?.selected_package;
          const mappedPackage = rawPackage === 'pro' ? 'professional' : rawPackage;
          setPackageType(mappedPackage as PackageType || null);
        }
      } catch (error) {
        console.error('Error fetching package:', error);
        setPackageType(null);
      } finally {
        setLoading(false);
      }
    };

    fetchPackage();
  }, [user]);

  return { packageType, loading };
};
