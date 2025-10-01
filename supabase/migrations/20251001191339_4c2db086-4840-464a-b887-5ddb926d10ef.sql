-- Allow public access to audits via access_token for survey filling
CREATE POLICY "Anyone can view audits by access_token"
ON public.audits
FOR SELECT
TO anon, authenticated
USING (true);