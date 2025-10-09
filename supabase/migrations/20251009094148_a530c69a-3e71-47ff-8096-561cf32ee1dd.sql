-- Add unsubscribe token to newsletter_subscribers
ALTER TABLE public.newsletter_subscribers
ADD COLUMN IF NOT EXISTS unsubscribe_token TEXT UNIQUE DEFAULT encode(gen_random_bytes(32), 'base64');

-- Create index for faster lookups
CREATE INDEX IF NOT EXISTS idx_newsletter_subscribers_unsubscribe_token 
ON public.newsletter_subscribers(unsubscribe_token);