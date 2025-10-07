-- Create storage bucket for magazine article images
INSERT INTO storage.buckets (id, name, public)
VALUES ('magazine-images', 'magazine-images', true);

-- Create RLS policies for magazine images bucket
CREATE POLICY "Admins can upload magazine images"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'magazine-images' 
  AND (auth.uid() IN (
    SELECT user_id FROM public.user_roles WHERE role = 'admin'
  ))
);

CREATE POLICY "Anyone can view magazine images"
ON storage.objects
FOR SELECT
TO public
USING (bucket_id = 'magazine-images');

CREATE POLICY "Admins can update magazine images"
ON storage.objects
FOR UPDATE
TO authenticated
USING (
  bucket_id = 'magazine-images'
  AND (auth.uid() IN (
    SELECT user_id FROM public.user_roles WHERE role = 'admin'
  ))
);

CREATE POLICY "Admins can delete magazine images"
ON storage.objects
FOR DELETE
TO authenticated
USING (
  bucket_id = 'magazine-images'
  AND (auth.uid() IN (
    SELECT user_id FROM public.user_roles WHERE role = 'admin'
  ))
);