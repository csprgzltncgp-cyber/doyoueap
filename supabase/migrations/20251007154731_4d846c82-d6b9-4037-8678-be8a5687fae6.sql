-- Create export_history table
CREATE TABLE public.export_history (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
  audit_id UUID NOT NULL REFERENCES public.audits(id) ON DELETE CASCADE,
  audit_name TEXT NOT NULL,
  file_name TEXT NOT NULL,
  file_type TEXT NOT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Enable RLS
ALTER TABLE public.export_history ENABLE ROW LEVEL SECURITY;

-- Users can view their own export history
CREATE POLICY "Users can view their own export history"
ON public.export_history
FOR SELECT
USING (auth.uid() = user_id);

-- Users can insert their own export history
CREATE POLICY "Users can insert their own export history"
ON public.export_history
FOR INSERT
WITH CHECK (auth.uid() = user_id);

-- Users can delete their own export history
CREATE POLICY "Users can delete their own export history"
ON public.export_history
FOR DELETE
USING (auth.uid() = user_id);

-- Create index for faster queries
CREATE INDEX idx_export_history_user_id ON public.export_history(user_id);
CREATE INDEX idx_export_history_created_at ON public.export_history(created_at DESC);