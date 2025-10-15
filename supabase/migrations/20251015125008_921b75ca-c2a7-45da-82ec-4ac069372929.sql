-- Javítjuk az első kérdés szövegét a használta ág 5. lépésében
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,4,questions,2,question}',
  '"Mikor érnéd el szívesen?"'
)
WHERE is_active = true;