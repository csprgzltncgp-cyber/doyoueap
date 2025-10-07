-- Add pending_verification column to admin_approval_requests
ALTER TABLE public.admin_approval_requests 
ADD COLUMN IF NOT EXISTS pending_verification BOOLEAN DEFAULT FALSE;

-- Update approval_token to allow NULL for pending verifications
ALTER TABLE public.admin_approval_requests 
ALTER COLUMN approval_token DROP NOT NULL;

-- Create trigger function to send approval request after email verification
CREATE OR REPLACE FUNCTION public.send_admin_approval_after_verification()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
  approval_record RECORD;
BEGIN
  -- Check if this is an email confirmation event
  IF NEW.email_confirmed_at IS NOT NULL AND OLD.email_confirmed_at IS NULL THEN
    -- Find pending approval request
    SELECT * INTO approval_record
    FROM public.admin_approval_requests
    WHERE user_id = NEW.id 
    AND pending_verification = true
    LIMIT 1;
    
    IF FOUND THEN
      -- Generate approval token
      UPDATE public.admin_approval_requests
      SET approval_token = encode(gen_random_bytes(24), 'base64'),
          pending_verification = false
      WHERE id = approval_record.id;
      
      -- Note: The actual email will be sent by the edge function
      -- triggered by the application after detecting the verified email
    END IF;
  END IF;
  
  RETURN NEW;
END;
$$;

-- Create trigger on auth.users for email verification
DROP TRIGGER IF EXISTS on_email_verified ON auth.users;
CREATE TRIGGER on_email_verified
  AFTER UPDATE ON auth.users
  FOR EACH ROW
  EXECUTE FUNCTION public.send_admin_approval_after_verification();
