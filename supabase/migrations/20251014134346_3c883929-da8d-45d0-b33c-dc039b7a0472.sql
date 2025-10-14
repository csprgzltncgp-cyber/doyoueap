-- Create communication_posters table for QR code poster management
CREATE TABLE public.communication_posters (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  has_gift BOOLEAN NOT NULL DEFAULT false,
  poster_images TEXT[] NOT NULL DEFAULT '{}',
  source_file_url TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now()),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now()),
  created_by UUID REFERENCES auth.users(id)
);

-- Enable RLS
ALTER TABLE public.communication_posters ENABLE ROW LEVEL SECURITY;

-- Admins can manage all posters
CREATE POLICY "Admins can manage communication posters"
ON public.communication_posters
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- HR can view posters
CREATE POLICY "HR can view communication posters"
ON public.communication_posters
FOR SELECT
USING (has_role(auth.uid(), 'hr'::app_role));

-- Update trigger
CREATE TRIGGER update_communication_posters_updated_at
BEFORE UPDATE ON public.communication_posters
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();