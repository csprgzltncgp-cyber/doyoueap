-- Enable RLS policies for gift images in audit-assets bucket

-- Allow everyone to view gift images (public access)
CREATE POLICY "Anyone can view gift images"
ON storage.objects
FOR SELECT
USING (bucket_id = 'audit-assets' AND (storage.foldername(name))[1] = 'gifts');

-- Allow authenticated users to upload gift images
CREATE POLICY "Authenticated users can upload gift images"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (bucket_id = 'audit-assets' AND (storage.foldername(name))[1] = 'gifts');

-- Allow authenticated users to update gift images
CREATE POLICY "Authenticated users can update gift images"
ON storage.objects
FOR UPDATE
TO authenticated
USING (bucket_id = 'audit-assets' AND (storage.foldername(name))[1] = 'gifts');

-- Allow authenticated users to delete gift images
CREATE POLICY "Authenticated users can delete gift images"
ON storage.objects
FOR DELETE
TO authenticated
USING (bucket_id = 'audit-assets' AND (storage.foldername(name))[1] = 'gifts');