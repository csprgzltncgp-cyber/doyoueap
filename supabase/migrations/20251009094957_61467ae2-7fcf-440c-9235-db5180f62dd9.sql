-- Generate tokens for existing subscribers that don't have one
UPDATE public.newsletter_subscribers
SET unsubscribe_token = encode(gen_random_bytes(32), 'base64')
WHERE unsubscribe_token IS NULL;