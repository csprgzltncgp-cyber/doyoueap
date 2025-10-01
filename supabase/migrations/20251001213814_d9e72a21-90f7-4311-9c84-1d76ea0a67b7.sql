-- Add new columns to audits table for the 8-step audit creation process

-- Step 1: Access mode (tokenes / public link / qr code)
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS access_mode text DEFAULT 'public_link' CHECK (access_mode IN ('tokenes', 'public_link', 'qr_code'));

-- Step 2: Communication support
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS email_template jsonb DEFAULT '{}'::jsonb,
ADD COLUMN IF NOT EXISTS communication_text text;

-- Step 3: UI customization (branding)
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS custom_colors jsonb DEFAULT '{}'::jsonb;

-- Step 4: Timing and recurrence
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS start_date timestamp with time zone,
ADD COLUMN IF NOT EXISTS recurrence_config jsonb DEFAULT '{}'::jsonb;

-- Step 7: Languages
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS available_languages text[] DEFAULT ARRAY['HU'];

-- Step 8: Program naming
ALTER TABLE public.audits 
ADD COLUMN IF NOT EXISTS program_name text DEFAULT 'DoYouEAP' CHECK (char_length(program_name) <= 60);