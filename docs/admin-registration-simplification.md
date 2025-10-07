# Admin Regisztráció Egyszerűsítési Terv

## Probléma
Jelenleg az admin regisztráció túlbonyolított - saját email rendszert próbál használni (Resend), ami problémákat okoz az email template-ekkel és a domain beállításokkal.

## Cél
Ugyanazt a működő email verifikációs rendszert használni, amit a céges regisztráció is használ.

## Hogyan működik a céges regisztráció most?

1. **Email + jelszó megadás** (`EmailPasswordStep`)
2. **Email validáció** (`EmailValidationStep`) - custom rendszer, NEM Supabase auth email verification
3. **Regisztráció befejezése** - `supabase.auth.signUp()` + automatikus HR role hozzárendelés a `handle_new_user` triggerből

## Megoldás az admin regisztrációhoz

### 1. Használjuk ugyanazt az email validációs flow-t
- Ugyanaz az `EmailPasswordStep` és `EmailValidationStep` komponens
- Ugyanaz az `email_verifications` tábla
- Ugyanaz a verifikációs email rendszer

### 2. Módosítsuk a `handle_new_user` triggert
```sql
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path TO 'public'
AS $function$
BEGIN
  INSERT INTO public.profiles (id, email, full_name)
  VALUES (
    new.id,
    new.email,
    COALESCE(new.raw_user_meta_data->>'full_name', '')
  );
  
  -- Ellenőrizzük, van-e pending admin approval request
  IF EXISTS (
    SELECT 1 FROM public.admin_approval_requests 
    WHERE user_id = new.id 
    AND approved = false
  ) THEN
    -- Ha van pending approval, NE adjunk role-t, várjuk a jóváhagyást
    RETURN new;
  END IF;
  
  -- Egyébként adjunk default HR role-t
  INSERT INTO public.user_roles (user_id, role)
  VALUES (new.id, 'hr');
  
  RETURN new;
END;
$function$;
```

### 3. SuperAdmin oldal egyszerűsítése

A SuperAdmin oldal legyen hasonló a céges regisztrációhoz:
- Email + jelszó megadás
- Email validáció (ugyanaz a custom rendszer)
- Regisztráció befejezése
- **ÚJ**: Pending approval állapot megjelenítése
- **ÚJ**: Admin approval email küldése (ezt a már működő `check-email-verification` function végzi)

### 4. Approval flow

1. User regisztrál SuperAdmin-on
2. Email validáció (custom rendszer)
3. `signUp()` sikeres → `handle_new_user` trigger látja a pending approval-t → NEM ad role-t
4. `check-email-verification` function küldi az approval emailt `zoltan.csepregi@cgpeu.com` címre
5. Approval link kattintás → `approve-admin-access` function
6. Admin role hozzáadása → user tud bejelentkezni

## Mit NE csináljunk

- ❌ Ne használjuk a Supabase saját email verification-jét
- ❌ Ne küldjünk emailt Resend-del a regisztrációkor (csak approval-nál)
- ❌ Ne komplikáljuk túl - használjuk a működő rendszert

## Előnyök

✅ Egyetlen működő email rendszer (nem keveredik Supabase + Resend)
✅ Konzisztens UX (ugyanaz mint a céges regisztráció)
✅ Kevesebb kód, egyszerűbb維護
✅ A működő céges regisztráció nem változik

## Fájlok, amiket módosítani kell

1. `supabase/migrations/` - új migration a `handle_new_user` trigger módosításához
2. `src/pages/SuperAdmin.tsx` - használja az EmailPasswordStep és EmailValidationStep komponenseket
3. `src/components/registration/EmailValidationStep.tsx` - esetleg új prop, hogy admin regisztráció esetén más üzenetet mutasson

## Fájlok, amiket NEM módosítunk

- ✅ `src/components/registration/RegistrationWizard.tsx` - marad
- ✅ `src/components/registration/EmailPasswordStep.tsx` - marad
- ✅ `src/components/registration/EmailValidationStep.tsx` - csak kis prop változás
- ✅ Céges regisztráció flow - SEMMIT nem módosítunk
