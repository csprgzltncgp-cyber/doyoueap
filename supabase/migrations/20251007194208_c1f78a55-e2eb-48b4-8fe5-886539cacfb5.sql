-- Add view_count column to magazine_articles table
ALTER TABLE public.magazine_articles 
ADD COLUMN view_count INTEGER DEFAULT 0 NOT NULL;

-- Add index for better query performance on view_count
CREATE INDEX idx_magazine_articles_view_count ON public.magazine_articles(view_count DESC);