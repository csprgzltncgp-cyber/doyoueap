-- Add target_responses column to audits table
ALTER TABLE public.audits 
ADD COLUMN target_responses INTEGER;

COMMENT ON COLUMN public.audits.target_responses IS 'Optional target number of responses for completion percentage calculation';

-- Add email_count column to track number of emails sent (for tokenes mode)
ALTER TABLE public.audits 
ADD COLUMN email_count INTEGER;

COMMENT ON COLUMN public.audits.email_count IS 'Number of emails sent for tokenes mode surveys';