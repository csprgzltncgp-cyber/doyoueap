-- Create storage bucket for audit assets (logos)
INSERT INTO storage.buckets (id, name, public) 
VALUES ('audit-assets', 'audit-assets', true);

-- Create RLS policies for audit-assets bucket
CREATE POLICY "HR can upload audit assets"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'audit-assets' 
  AND has_role(auth.uid(), 'hr'::app_role)
);

CREATE POLICY "Everyone can view audit assets"
ON storage.objects
FOR SELECT
USING (bucket_id = 'audit-assets');