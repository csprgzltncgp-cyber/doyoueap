-- Allow public read access to questionnaires for survey filling
CREATE POLICY "Anyone can view active questionnaires"
ON public.questionnaires
FOR SELECT
TO anon, authenticated
USING (is_active = true);