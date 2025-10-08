-- Add copyright text and color fields to newsletter_templates table
ALTER TABLE newsletter_templates 
ADD COLUMN IF NOT EXISTS footer_copyright_text text DEFAULT '© 2025 Az EAP világ vezető szakfolyóirata. Minden jog fenntartva.',
ADD COLUMN IF NOT EXISTS footer_copyright_color text DEFAULT '#ffffff';