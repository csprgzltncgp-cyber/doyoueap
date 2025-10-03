-- Drop existing policy
DROP POLICY IF EXISTS "HR can view responses for their audits" ON public.audit_responses;

-- Create security definer function to check if user owns the audit
CREATE OR REPLACE FUNCTION public.user_owns_audit(_user_id uuid, _audit_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.audits
    WHERE id = _audit_id
      AND hr_user_id = _user_id
  )
$$;

-- Create new policy using the security definer function
CREATE POLICY "HR can view responses for their audits"
ON public.audit_responses
FOR SELECT
USING (public.user_owns_audit(auth.uid(), audit_id));