-- Create a security definer function to check if a profile belongs to the same company as the current user
CREATE OR REPLACE FUNCTION public.same_company_as_user(_profile_id uuid, _user_id uuid)
RETURNS boolean
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1
    FROM public.profiles p1
    INNER JOIN public.profiles p2 ON p1.company_name = p2.company_name
    WHERE p1.id = _user_id
      AND p2.id = _profile_id
      AND p1.company_name IS NOT NULL
      AND p2.company_name IS NOT NULL
  )
$$;

-- Drop the existing overly permissive policy
DROP POLICY IF EXISTS "HR can view all profiles" ON public.profiles;

-- Create a new restricted policy that only allows HR to view profiles from their own company
CREATE POLICY "HR can view profiles from their company"
ON public.profiles
FOR SELECT
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND same_company_as_user(id, auth.uid())
);