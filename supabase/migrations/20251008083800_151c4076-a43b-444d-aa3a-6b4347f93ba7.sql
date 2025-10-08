-- Create newsletter-assets storage bucket for newsletter logos and featured images
INSERT INTO storage.buckets (id, name, public)
VALUES ('newsletter-assets', 'newsletter-assets', true);

-- Create RLS policies for newsletter-assets bucket
CREATE POLICY "Admins can upload newsletter assets"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'newsletter-assets' 
  AND (SELECT has_role(auth.uid(), 'admin'::app_role))
);

CREATE POLICY "Anyone can view newsletter assets"
ON storage.objects
FOR SELECT
TO public
USING (bucket_id = 'newsletter-assets');

CREATE POLICY "Admins can update newsletter assets"
ON storage.objects
FOR UPDATE
TO authenticated
USING (
  bucket_id = 'newsletter-assets' 
  AND (SELECT has_role(auth.uid(), 'admin'::app_role))
);

CREATE POLICY "Admins can delete newsletter assets"
ON storage.objects
FOR DELETE
TO authenticated
USING (
  bucket_id = 'newsletter-assets' 
  AND (SELECT has_role(auth.uid(), 'admin'::app_role))
);