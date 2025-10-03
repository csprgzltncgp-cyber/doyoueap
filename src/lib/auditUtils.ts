/**
 * MEGJEGYZÉS: A kódbázisban az "audit" szó "felmérés"-t (EAP Pulse survey) jelent.
 * A felhasználói felületen mindenhol "felmérés" vagy "EAP Pulse felmérés" néven jelenik meg.
 * Az adatbázis táblák és technikai elemek megőrizték az eredeti "audit" elnevezést.
 */

interface AuditForDisplay {
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config?: {
    frequency?: string;
  };
  is_active: boolean;
  expires_at: string | null;
}

export const formatAuditName = (audit: AuditForDisplay): string => {
  // Formázza a dátumot
  const startDate = new Date(audit.start_date).toLocaleDateString('hu-HU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });

  // Program neve
  const programName = audit.program_name || 'EAP';

  // Hozzáférési mód formázása
  const accessModeMap: Record<string, string> = {
    'tokenes': 'Tokenes link',
    'public_link': 'Nyilvános link',
    'qr_code': 'QR kód',
  };
  const accessMode = accessModeMap[audit.access_mode] || audit.access_mode;

  // Ismétlés időköze
  let recurrence = '';
  if (audit.recurrence_config?.frequency) {
    const frequencyMap: Record<string, string> = {
      'monthly': 'Havonta',
      'quarterly': 'Negyedévente',
      'biannually': 'Félévente',
      'annually': 'Évente',
    };
    recurrence = frequencyMap[audit.recurrence_config.frequency] || audit.recurrence_config.frequency;
  }

  // Státusz meghatározása
  let status = 'Futó';
  if (!audit.is_active) {
    status = 'Zárt';
  } else if (audit.expires_at && new Date(audit.expires_at) < new Date()) {
    status = 'Lejárt';
  }

  // Összerakjuk a nevet
  const parts = [startDate, programName, accessMode];
  if (recurrence) {
    parts.push(recurrence);
  }
  parts.push(status);

  return parts.join(' - ');
};

// Standard felmérés interface for all HR pages
// NOTE: "Audit" in code = "Felmérés/Survey" in UI
export interface StandardAudit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
  access_token?: string;
}

// Standard query string for fetching felmérések
export const AUDIT_SELECT_FIELDS = 'id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at';
