-- Allow anyone to create approval requests during registration
CREATE POLICY "Anyone can create approval requests"
ON public.admin_approval_requests
FOR INSERT
WITH CHECK (true);

-- Allow users to view their own approval requests
CREATE POLICY "Users can view their own approval requests"
ON public.admin_approval_requests
FOR SELECT
USING (auth.uid() = user_id OR auth.uid() IS NULL);