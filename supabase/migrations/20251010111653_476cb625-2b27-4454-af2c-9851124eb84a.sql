-- =====================================================
-- GIFTS / LOTTERY SYSTEM - Complete Migration
-- =====================================================

-- 1) CREATE ENUM TYPES
-- =====================================================

CREATE TYPE audit_status AS ENUM ('draft', 'running', 'closed');
CREATE TYPE draw_mode AS ENUM ('auto', 'manual');
CREATE TYPE draw_status AS ENUM ('none', 'ready', 'done');

-- 2) CREATE NEW TABLES
-- =====================================================

-- Gifts catalog (admin managed, HR can read active items)
CREATE TABLE gifts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  description text,
  image_url text,
  value_eur numeric(10,2) NOT NULL CHECK (value_eur >= 1),
  is_default boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

-- Draw audit log (lottery execution records)
CREATE TABLE draws (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  audit_id uuid NOT NULL REFERENCES audits(id) ON DELETE CASCADE,
  company_name text NOT NULL,
  ts timestamptz NOT NULL DEFAULT now(),
  seed text NOT NULL,
  candidates_count int NOT NULL,
  winner_token text NOT NULL,
  report_url text,
  created_by uuid REFERENCES auth.users(id)
);

-- Optional email notifications for lottery participants
CREATE TABLE response_notifications (
  response_id uuid PRIMARY KEY REFERENCES audit_responses(id) ON DELETE CASCADE,
  email text NOT NULL,
  consent_ts timestamptz NOT NULL DEFAULT now()
);

-- 3) EXTEND EXISTING TABLES
-- =====================================================

-- Companies: add feature flag
ALTER TABLE companies
  ADD COLUMN enable_gifts boolean NOT NULL DEFAULT false;

-- Audits: add lottery-related columns
ALTER TABLE audits
  ADD COLUMN status audit_status NOT NULL DEFAULT 'draft',
  ADD COLUMN company_name text,
  ADD COLUMN gift_id uuid REFERENCES gifts(id),
  ADD COLUMN draw_mode draw_mode,
  ADD COLUMN draw_status draw_status NOT NULL DEFAULT 'none',
  ADD COLUMN closes_at timestamptz;

-- Backfill company_name from hr_user_id -> profiles
UPDATE audits a
SET company_name = p.company_name
FROM profiles p
WHERE a.hr_user_id = p.id
  AND a.company_name IS NULL;

-- Make company_name NOT NULL (after backfill)
ALTER TABLE audits ALTER COLUMN company_name SET NOT NULL;

-- Audit responses: add lottery participant fields
ALTER TABLE audit_responses
  ADD COLUMN participant_id_hash text,
  ADD COLUMN draw_token text,
  ADD COLUMN anonymized_hash text;

-- Unique constraint: one submission per participant per audit
CREATE UNIQUE INDEX audit_responses_unique_participant_per_audit
  ON audit_responses(audit_id, participant_id_hash)
  WHERE participant_id_hash IS NOT NULL;

-- 4) HELPER FUNCTIONS
-- =====================================================

-- Get user's company name (for RLS policies)
CREATE OR REPLACE FUNCTION get_user_company_name(_user_id uuid)
RETURNS text
LANGUAGE sql
STABLE
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT company_name
  FROM profiles
  WHERE id = _user_id
  LIMIT 1;
$$;

-- 5) ENABLE RLS ON NEW TABLES
-- =====================================================

ALTER TABLE gifts ENABLE ROW LEVEL SECURITY;
ALTER TABLE draws ENABLE ROW LEVEL SECURITY;
ALTER TABLE response_notifications ENABLE ROW LEVEL SECURITY;

-- 6) RLS POLICIES
-- =====================================================

-- GIFTS
-- Admin: full CRUD
CREATE POLICY gifts_admin_all ON gifts
  FOR ALL
  USING (has_role(auth.uid(), 'admin'))
  WITH CHECK (has_role(auth.uid(), 'admin'));

-- HR: read only active items
CREATE POLICY gifts_hr_read ON gifts
  FOR SELECT
  USING (
    has_role(auth.uid(), 'hr') 
    AND is_active = true
  );

-- DRAWS
-- Admin: full access
CREATE POLICY draws_admin_all ON draws
  FOR ALL
  USING (has_role(auth.uid(), 'admin'))
  WITH CHECK (has_role(auth.uid(), 'admin'));

-- HR: read only own company draws
CREATE POLICY draws_company_read ON draws
  FOR SELECT
  USING (
    has_role(auth.uid(), 'hr')
    AND company_name = get_user_company_name(auth.uid())
  );

-- RESPONSE_NOTIFICATIONS
-- Admin: can read all
CREATE POLICY rn_admin_read ON response_notifications
  FOR SELECT
  USING (has_role(auth.uid(), 'admin'));

-- No public access (backend writes via service role)

-- 7) TRIGGERS FOR UPDATED_AT
-- =====================================================

CREATE TRIGGER update_gifts_updated_at
  BEFORE UPDATE ON gifts
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();