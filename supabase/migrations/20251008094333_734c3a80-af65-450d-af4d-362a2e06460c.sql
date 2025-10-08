-- Add sender email and name fields to newsletter_templates
ALTER TABLE public.newsletter_templates
ADD COLUMN IF NOT EXISTS sender_email text NOT NULL DEFAULT 'noreply@doyoueap.com',
ADD COLUMN IF NOT EXISTS sender_name text NOT NULL DEFAULT 'DoYouEAP';

COMMENT ON COLUMN public.newsletter_templates.sender_email IS 'A feladó email címe';
COMMENT ON COLUMN public.newsletter_templates.sender_name IS 'A feladó neve';