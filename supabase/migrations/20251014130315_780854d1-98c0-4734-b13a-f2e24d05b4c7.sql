-- Create communication_templates table for admin-managed templates
CREATE TABLE public.communication_templates (
  id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  template_type TEXT NOT NULL CHECK (template_type IN ('email', 'public_link', 'qr_code')),
  has_gift BOOLEAN NOT NULL DEFAULT false,
  subject TEXT,
  content TEXT NOT NULL,
  created_by UUID REFERENCES auth.users(id),
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now()),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT timezone('utc'::text, now())
);

-- Enable RLS
ALTER TABLE public.communication_templates ENABLE ROW LEVEL SECURITY;

-- Admins can manage templates
CREATE POLICY "Admins can manage communication templates"
ON public.communication_templates
FOR ALL
USING (has_role(auth.uid(), 'admin'::app_role))
WITH CHECK (has_role(auth.uid(), 'admin'::app_role));

-- HR can view templates
CREATE POLICY "HR can view communication templates"
ON public.communication_templates
FOR SELECT
USING (has_role(auth.uid(), 'hr'::app_role));

-- Trigger for updated_at
CREATE TRIGGER update_communication_templates_updated_at
BEFORE UPDATE ON public.communication_templates
FOR EACH ROW
EXECUTE FUNCTION public.update_updated_at_column();

-- Insert default templates
INSERT INTO public.communication_templates (template_type, has_gift, subject, content) VALUES
('email', false, 'Kérjük, töltsd ki {programName} kérdőívünket!', 'Kedves Kollégánk!

Szeretnénk megkérni, hogy töltsd ki {programName} kérdőívünket. A kitöltés mindössze 3-5 percet vesz igénybe, és nagy segítséget jelent számunkra.

Kérjük, kattints az alábbi linkre a kitöltéshez:
{link}

Köszönjük a segítséged!

Üdvözlettel,
HR Csapat'),

('email', true, 'Töltsd ki {programName} kérdőívünket és nyerj!', 'Kedves Kollégánk!

Szeretnénk megkérni, hogy töltsd ki {programName} kérdőívünket. A kitöltés mindössze 3-5 percet vesz igénybe.

🎁 Minden kitöltő között értékes ajándékot sorsolunk ki!

Kérjük, kattints az alábbi linkre a kitöltéshez:
{link}

Köszönjük a segítséged és sok szerencsét!

Üdvözlettel,
HR Csapat'),

('public_link', false, NULL, 'Kérjük, töltsd ki {programName} kérdőívünket!

A kitöltés anonim és mindössze 3-5 percet vesz igénybe.

Link: {link}

Köszönjük a közreműködésed!'),

('public_link', true, NULL, 'Töltsd ki {programName} kérdőívünket és nyerj értékes ajándékot!

🎁 Minden kitöltő között sorsolunk!

A kitöltés anonim és mindössze 3-5 percet vesz igénybe.

Link: {link}

Sok szerencsét!'),

('qr_code', false, NULL, 'Szkenneld be a QR kódot és töltsd ki {programName} kérdőívünket!

⏱️ Mindössze 3-5 perc
🔒 Teljesen anonim
📊 Segíts nekünk jobbá válni!'),

('qr_code', true, NULL, 'Szkenneld be a QR kódot, töltsd ki {programName} kérdőívünket és nyerj!

🎁 Értékes nyeremények
⏱️ Mindössze 3-5 perc
🔒 Teljesen anonim

Sok szerencsét!');