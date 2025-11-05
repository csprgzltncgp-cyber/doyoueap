-- Add INSERT policy for api_logs table to enable audit trail logging
-- This allows the service role (edge functions) to insert API logs

CREATE POLICY "Service role can insert API logs"
ON public.api_logs
FOR INSERT
TO service_role
WITH CHECK (true);

-- Also allow authenticated users to insert logs for their own API keys
CREATE POLICY "Users can insert API logs for their keys"
ON public.api_logs
FOR INSERT
TO authenticated
WITH CHECK (
  EXISTS (
    SELECT 1 FROM public.api_keys
    WHERE api_keys.id = api_logs.api_key_id
    AND api_keys.user_id = auth.uid()
  )
);