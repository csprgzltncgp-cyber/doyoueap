-- Add DELETE policy for newsletter_campaigns
CREATE POLICY "Admins can delete campaigns"
ON newsletter_campaigns
FOR DELETE
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role));

-- Add UPDATE policy for newsletter_campaigns (for future use)
CREATE POLICY "Admins can update campaigns"
ON newsletter_campaigns
FOR UPDATE
TO authenticated
USING (has_role(auth.uid(), 'admin'::app_role));