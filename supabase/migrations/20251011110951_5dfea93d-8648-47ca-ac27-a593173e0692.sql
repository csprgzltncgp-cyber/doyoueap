-- Add email notification tracking columns to draws table
ALTER TABLE public.draws
ADD COLUMN notification_email text,
ADD COLUMN notification_sent_at timestamp with time zone,
ADD COLUMN notification_status text CHECK (notification_status IN ('pending', 'sent', 'failed', 'not_applicable'));