-- Create a function to safely increment article view count
-- This allows anyone to increment the view count without UPDATE permissions
CREATE OR REPLACE FUNCTION public.increment_article_view_count(_article_id uuid)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  UPDATE public.magazine_articles
  SET view_count = view_count + 1
  WHERE id = _article_id;
END;
$$;