-- Add sent and failed count columns to newsletter_campaigns table
ALTER TABLE newsletter_campaigns 
ADD COLUMN sent_count INTEGER DEFAULT 0,
ADD COLUMN failed_count INTEGER DEFAULT 0;