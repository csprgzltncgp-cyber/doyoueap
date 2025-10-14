-- Create storage policies for communication posters in audit-assets bucket

-- Allow admins to insert poster files
CREATE POLICY "Admins can upload poster files"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (
  bucket_id = 'audit-assets' 
  AND (storage.foldername(name))[1] = 'posters'
  AND (
    SELECT has_role(auth.uid(), 'admin'::app_role)
  )
);

-- Allow admins to update poster files
CREATE POLICY "Admins can update poster files"
ON storage.objects
FOR UPDATE
TO authenticated
USING (
  bucket_id = 'audit-assets' 
  AND (storage.foldername(name))[1] = 'posters'
  AND (
    SELECT has_role(auth.uid(), 'admin'::app_role)
  )
);

-- Allow admins to delete poster files
CREATE POLICY "Admins can delete poster files"
ON storage.objects
FOR DELETE
TO authenticated
USING (
  bucket_id = 'audit-assets' 
  AND (storage.foldername(name))[1] = 'posters'
  AND (
    SELECT has_role(auth.uid(), 'admin'::app_role)
  )
);

-- Allow HR users to view poster files
CREATE POLICY "HR can view poster files"
ON storage.objects
FOR SELECT
TO authenticated
USING (
  bucket_id = 'audit-assets' 
  AND (storage.foldername(name))[1] = 'posters'
  AND (
    SELECT has_role(auth.uid(), 'hr'::app_role)
  )
);