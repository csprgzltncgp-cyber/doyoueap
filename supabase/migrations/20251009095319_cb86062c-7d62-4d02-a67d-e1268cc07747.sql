-- Create a function to generate unsubscribe token automatically
CREATE OR REPLACE FUNCTION public.generate_unsubscribe_token()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  IF NEW.unsubscribe_token IS NULL THEN
    NEW.unsubscribe_token := encode(gen_random_bytes(32), 'base64');
  END IF;
  RETURN NEW;
END;
$$;

-- Create trigger to automatically generate token for new subscribers
DROP TRIGGER IF EXISTS set_unsubscribe_token ON public.newsletter_subscribers;
CREATE TRIGGER set_unsubscribe_token
  BEFORE INSERT ON public.newsletter_subscribers
  FOR EACH ROW
  EXECUTE FUNCTION public.generate_unsubscribe_token();

-- Make sure ALL existing subscribers have tokens (run again to be safe)
UPDATE public.newsletter_subscribers
SET unsubscribe_token = encode(gen_random_bytes(32), 'base64')
WHERE unsubscribe_token IS NULL OR unsubscribe_token = '';