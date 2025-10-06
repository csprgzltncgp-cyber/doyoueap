-- Add a RESTRICTIVE baseline policy that explicitly requires authentication for reading responses
-- This is belt-and-suspenders security - the existing policies already prevent anonymous reads,
-- but this makes the security requirement explicit and may satisfy security scanners
CREATE POLICY "require_authentication_for_viewing_responses"
ON public.audit_responses
AS RESTRICTIVE
FOR SELECT
USING (auth.uid() IS NOT NULL);

-- Add a comment explaining the security model
COMMENT ON POLICY "require_authentication_for_viewing_responses" ON public.audit_responses IS 
'RESTRICTIVE policy ensuring only authenticated users can attempt to view responses. The existing PERMISSIVE policies (admin and HR audit owner checks) further restrict which responses authenticated users can actually see.';