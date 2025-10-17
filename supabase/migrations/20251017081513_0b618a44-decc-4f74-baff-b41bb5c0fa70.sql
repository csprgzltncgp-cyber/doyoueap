-- Fix email_verifications RLS policy to allow unauthenticated users to check verification status

-- Drop existing restrictive SELECT policy
DROP POLICY IF EXISTS "Users can check verification status for their own email" ON public.email_verifications;

-- Create new permissive SELECT policy for verification checking during registration
CREATE POLICY "Anyone can check verification status" 
ON public.email_verifications 
FOR SELECT 
USING (true);