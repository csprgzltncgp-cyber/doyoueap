-- Add created_by column to gifts table to track who created each gift
ALTER TABLE public.gifts 
ADD COLUMN created_by uuid REFERENCES auth.users(id) ON DELETE SET NULL;

-- Update RLS policies for gifts table
-- Drop existing HR read policy
DROP POLICY IF EXISTS "gifts_hr_read" ON public.gifts;

-- Create new policies for HR users
CREATE POLICY "gifts_hr_read_all" 
ON public.gifts 
FOR SELECT 
USING (has_role(auth.uid(), 'hr'::app_role));

CREATE POLICY "gifts_hr_insert" 
ON public.gifts 
FOR INSERT 
WITH CHECK (has_role(auth.uid(), 'hr'::app_role) AND created_by = auth.uid());

CREATE POLICY "gifts_hr_delete_own" 
ON public.gifts 
FOR DELETE 
USING (
  has_role(auth.uid(), 'hr'::app_role) 
  AND created_by = auth.uid() 
  AND created_by IS NOT NULL
);