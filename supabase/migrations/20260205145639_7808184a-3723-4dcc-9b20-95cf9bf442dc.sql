-- Create table to cache value type mappings from Laravel API
CREATE TABLE public.value_type_mappings_cache (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  company_id integer NOT NULL,
  language_id integer NOT NULL DEFAULT 2,
  mappings jsonb NOT NULL DEFAULT '{}',
  fetched_at timestamp with time zone NOT NULL DEFAULT now(),
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  updated_at timestamp with time zone NOT NULL DEFAULT now(),
  UNIQUE(company_id, language_id)
);

-- Enable RLS
ALTER TABLE public.value_type_mappings_cache ENABLE ROW LEVEL SECURITY;

-- Allow read access for authenticated users (cache is shared data)
CREATE POLICY "Allow read access for authenticated users"
ON public.value_type_mappings_cache
FOR SELECT
TO authenticated
USING (true);

-- Allow service role to manage cache
CREATE POLICY "Allow service role full access"
ON public.value_type_mappings_cache
FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

-- Create trigger to update updated_at
CREATE TRIGGER update_value_type_mappings_cache_updated_at
BEFORE UPDATE ON public.value_type_mappings_cache
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

-- Add comment
COMMENT ON TABLE public.value_type_mappings_cache IS 'Cache for Laravel API value type mappings to avoid rate limiting';