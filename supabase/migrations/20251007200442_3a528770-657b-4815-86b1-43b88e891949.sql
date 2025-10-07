-- Remove overly restrictive authentication policy
-- The HR-specific policy already handles authentication properly
DROP POLICY IF EXISTS "require_authentication_for_viewing_responses" ON public.audit_responses;