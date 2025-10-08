-- Add copyright text fields back to newsletter_templates table
ALTER TABLE public.newsletter_templates 
ADD COLUMN footer_copyright_text text DEFAULT '© 2025 Az EAP-világ vezető szakfolyóirata. Minden jog fenntartva.',
ADD COLUMN footer_copyright_color text DEFAULT '#ffffff';