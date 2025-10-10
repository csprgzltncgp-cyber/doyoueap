-- Fix trigger functions to have immutable search_path

CREATE OR REPLACE FUNCTION public.update_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION public.generate_unsubscribe_token()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  IF NEW.unsubscribe_token IS NULL THEN
    NEW.unsubscribe_token := encode(gen_random_bytes(32), 'base64');
  END IF;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION public.send_admin_approval_after_verification()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
DECLARE
  approval_record RECORD;
BEGIN
  IF NEW.email_confirmed_at IS NOT NULL AND OLD.email_confirmed_at IS NULL THEN
    SELECT * INTO approval_record
    FROM public.admin_approval_requests
    WHERE user_id = NEW.id 
    AND pending_verification = true
    LIMIT 1;
    
    IF FOUND THEN
      UPDATE public.admin_approval_requests
      SET approval_token = encode(gen_random_bytes(24), 'base64'),
          pending_verification = false
      WHERE id = approval_record.id;
    END IF;
  END IF;
  
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
BEGIN
  INSERT INTO public.profiles (id, email, full_name)
  VALUES (
    new.id,
    new.email,
    COALESCE(new.raw_user_meta_data->>'full_name', '')
  );
  
  INSERT INTO public.user_roles (user_id, role)
  VALUES (new.id, 'hr');
  
  RETURN new;
END;
$$;

CREATE OR REPLACE FUNCTION public.verify_email_with_token(_token text)
RETURNS jsonb
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $$
DECLARE
  verification_record RECORD;
  result jsonb;
BEGIN
  SELECT * INTO verification_record
  FROM public.email_verifications
  WHERE token = _token
  LIMIT 1;
  
  IF verification_record.token IS NULL THEN
    RETURN jsonb_build_object(
      'success', false,
      'error', 'Invalid verification token'
    );
  END IF;
  
  IF verification_record.verified = true THEN
    RETURN jsonb_build_object(
      'success', true,
      'message', 'Email already verified',
      'email', verification_record.email
    );
  END IF;
  
  IF verification_record.expires_at < now() THEN
    RETURN jsonb_build_object(
      'success', false,
      'error', 'Verification token has expired'
    );
  END IF;
  
  UPDATE public.email_verifications
  SET verified = true
  WHERE token = _token;
  
  RETURN jsonb_build_object(
    'success', true,
    'message', 'Email verified successfully',
    'email', verification_record.email
  );
END;
$$;