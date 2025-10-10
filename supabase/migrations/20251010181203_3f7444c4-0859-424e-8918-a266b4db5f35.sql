-- Add 'completed' value to draw_status enum if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_enum 
        WHERE enumtypid = 'draw_status'::regtype 
        AND enumlabel = 'completed'
    ) THEN
        ALTER TYPE draw_status ADD VALUE 'completed';
    END IF;
END $$;