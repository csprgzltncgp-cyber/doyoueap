-- Drop existing test data and recreate with proper branching structure
DELETE FROM audit_responses;
DELETE FROM audits;
DELETE FROM questionnaires;

-- Create new questionnaire with branching logic
INSERT INTO questionnaires (id, title, description, questions, is_active)
VALUES (
  '1ed222be-a62f-4db5-bd42-a4bd392602fb',
  'EAP Audit - Teljes Kérdőív',
  'Komplex EAP audit branching logikával',
  '{
    "structure": "branching",
    "demographics": {
      "title": "Demográfiai adatok",
      "questions": [
        {
          "id": "gender",
          "type": "single_choice",
          "question": "Nem",
          "options": ["Férfi", "Nő"],
          "required": true
        },
        {
          "id": "age",
          "type": "single_choice",
          "question": "Életkor",
          "options": ["<18", "18-24", "25-36", "37-44", "45-58", "58+"],
          "required": true
        }
      ]
    },
    "branch_selector": {
      "id": "eap_knowledge",
      "type": "single_choice",
      "question": "Tudsz arról, hogy a céged EAP programot működtet?",
      "options": [
        "Nem tudtam róla",
        "Igen, tudok róla, de nem használtam",
        "Igen, tudok róla és használtam"
      ],
      "required": true,
      "branches": {
        "Nem tudtam róla": "redirect",
        "Igen, tudok róla, de nem használtam": "not_used",
        "Igen, tudok róla és használtam": "used"
      }
    },
    "branches": {
      "not_used": {
        "title": "Nem használta ág",
        "blocks": [
          {
            "id": "awareness_light",
            "title": "Awareness light",
            "questions": [
              {
                "id": "nu_awareness_source",
                "type": "multiple_choice",
                "question": "Honnan hallottál az EAP-ról?",
                "options": ["HR kommunikáció", "Vezető", "Kolléga", "Intranet", "Plakát", "Egyéb"],
                "required": true
              },
              {
                "id": "nu_awareness_understanding",
                "type": "scale",
                "question": "Mennyire érted, mit kínál az EAP?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              }
            ]
          },
          {
            "id": "trust_light",
            "title": "Trust light",
            "questions": [
              {
                "id": "nu_trust_anonymity",
                "type": "scale",
                "question": "Mennyire bízol az anonimitásban?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "nu_trust_employer",
                "type": "scale",
                "question": "Félnél-e, hogy a munkaadó megtudja?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "nu_trust_colleagues",
                "type": "scale",
                "question": "Tartasz-e a kollégák megítélésétől?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              }
            ]
          },
          {
            "id": "motivation",
            "title": "Motiváció",
            "questions": [
              {
                "id": "nu_motivation_what",
                "type": "text",
                "question": "Mi kellene ahhoz, hogy kipróbáld az EAP-ot?",
                "required": true
              },
              {
                "id": "nu_motivation_expert",
                "type": "single_choice",
                "question": "Milyen típusú szakértő miatt használnád leginkább?",
                "options": ["Pszichológus", "Jogász", "Pénzügyi", "Dietetikus", "Coach", "Egyéb"],
                "required": true
              },
              {
                "id": "nu_motivation_channel",
                "type": "single_choice",
                "question": "Melyik csatornát tartanád kényelmesnek?",
                "options": ["Telefon", "Chat", "Videó", "Személyes"],
                "required": true
              },
              {
                "id": "nu_motivation_availability",
                "type": "single_choice",
                "question": "Mikor érnéd el szívesen?",
                "options": ["Munkaidőben", "Munkaidőn kívül", "24/7"],
                "required": true
              },
              {
                "id": "nu_motivation_communication",
                "type": "single_choice",
                "question": "Milyen formában kommunikáljunk róla?",
                "options": ["Videók", "Plakátok", "Email", "Vezetői tájékoztatás", "App értesítés"],
                "required": true
              }
            ]
          },
          {
            "id": "usefulness",
            "title": "Hasznossági percepció",
            "questions": [
              {
                "id": "nu_usefulness_perception",
                "type": "scale",
                "question": "Mennyire gondolod hasznosnak, hogy a céged ilyen szolgáltatást kínál?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              }
            ]
          }
        ]
      },
      "used": {
        "title": "Használta ág",
        "blocks": [
          {
            "id": "awareness",
            "title": "Awareness",
            "questions": [
              {
                "id": "u_awareness_source",
                "type": "multiple_choice",
                "question": "Hol hallottál róla?",
                "options": ["HR kommunikáció", "Vezető", "Kolléga", "Plakát", "Egyéb"],
                "required": true
              },
              {
                "id": "u_awareness_understanding",
                "type": "scale",
                "question": "Mennyire érted a szolgáltatást?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_awareness_how_to_use",
                "type": "scale",
                "question": "Tudod, hogyan veheted igénybe?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_awareness_accessibility",
                "type": "scale",
                "question": "Mennyire érzed elérhetőnek?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_awareness_info",
                "type": "yesno",
                "question": "Van-e elég információ róla?",
                "required": true
              },
              {
                "id": "u_awareness_frequency",
                "type": "single_choice",
                "question": "Milyen gyakran találkozol EAP kommunikációval?",
                "options": ["Soha", "Évente", "Félévente", "Negyedévente", "Havonta+"],
                "required": true
              }
            ]
          },
          {
            "id": "trust_willingness",
            "title": "Trust & Willingness",
            "questions": [
              {
                "id": "u_trust_anonymity",
                "type": "scale",
                "question": "Anonimitásba vetett bizalom?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_trust_employer",
                "type": "scale",
                "question": "Félsz-e, hogy a munkaadó megtudja?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_trust_colleagues",
                "type": "scale",
                "question": "Kollégák megítélésétől félelem?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_trust_likelihood",
                "type": "scale",
                "question": "Valószínűség, hogy használnád szükség esetén?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem valószínű", "5": "Nagyon valószínű"},
                "required": true
              },
              {
                "id": "u_trust_barriers",
                "type": "single_choice",
                "question": "Mi tartana vissza leginkább?",
                "options": ["Bizalomhiány", "Nem nekem szól", "Időhiány", "Bonyolult", "Más csatorna", "Egyéb"],
                "required": true
              }
            ]
          },
          {
            "id": "usage",
            "title": "Usage (Használat)",
            "questions": [
              {
                "id": "u_usage_channel",
                "type": "multiple_choice",
                "question": "Melyik csatornát használtad?",
                "options": ["Telefon", "Chat", "Videó", "Személyes", "Email", "Egyéb"],
                "required": true
              },
              {
                "id": "u_usage_topic",
                "type": "multiple_choice",
                "question": "Milyen témában használtad?",
                "options": ["Pszichológiai", "Jogi", "Pénzügyi", "Dietetika", "Coaching", "Egyéb"],
                "required": true
              },
              {
                "id": "u_usage_frequency",
                "type": "single_choice",
                "question": "Milyen gyakran vetted igénybe az elmúlt 12 hónapban?",
                "options": ["1", "2-3", "4-6", "Rendszeresen"],
                "required": true
              },
              {
                "id": "u_usage_time_to_care",
                "type": "single_choice",
                "question": "Mennyi idő telt el az első kapcsolatfelvétel és konzultáció között?",
                "options": ["24h", "1-3 nap", "1 hét", ">1 hét"],
                "required": true
              },
              {
                "id": "u_usage_family",
                "type": "yesno",
                "question": "Családtagjaid is használták a programot?",
                "required": true
              }
            ]
          },
          {
            "id": "impact",
            "title": "Impact (Hatás)",
            "questions": [
              {
                "id": "u_impact_satisfaction",
                "type": "scale",
                "question": "Mennyire voltál elégedett a kapott segítséggel?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_impact_problem_solving",
                "type": "scale",
                "question": "Segített a probléma megoldásában?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_impact_wellbeing",
                "type": "scale",
                "question": "Javult a közérzeted?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_impact_performance",
                "type": "scale",
                "question": "Javult a munkahelyi teljesítményed?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": true
              },
              {
                "id": "u_impact_nps",
                "type": "nps",
                "question": "Ajánlanád kollégáknak?",
                "scale": 10,
                "labels": {"0": "Egyáltalán nem valószínű", "10": "Nagyon valószínű"},
                "required": true
              },
              {
                "id": "u_impact_consistency",
                "type": "scale",
                "question": "Ha több alkalom: mennyire volt konzisztens a minőség?",
                "scale": 5,
                "labels": {"1": "Egyáltalán nem", "5": "Teljes mértékben"},
                "required": false
              }
            ]
          },
          {
            "id": "preferences",
            "title": "Preferences (Preferenciák)",
            "questions": [
              {
                "id": "u_pref_expert",
                "type": "single_choice",
                "question": "Milyen szakértőre lenne szükséged a jövőben?",
                "options": ["Pszichológus", "Jogász", "Pénzügyi", "Dietetikus", "Coach", "Egyéb"],
                "required": true
              },
              {
                "id": "u_pref_channel",
                "type": "single_choice",
                "question": "Melyik csatornát preferálnád?",
                "options": ["Telefon", "Chat", "Videó", "Személyes"],
                "required": true
              },
              {
                "id": "u_pref_availability",
                "type": "single_choice",
                "question": "Mikor szeretnél hozzáférni?",
                "options": ["Munkaidőben", "Munkaidőn kívül", "24/7"],
                "required": true
              },
              {
                "id": "u_pref_comm_frequency",
                "type": "single_choice",
                "question": "Milyen gyakran kellene kommunikálni róla?",
                "options": ["Évente", "Félévente", "Negyedévente", "Folyamatosan"],
                "required": true
              },
              {
                "id": "u_pref_content_type",
                "type": "single_choice",
                "question": "Milyen típusú tartalom segítene a megértésben?",
                "options": ["Videók", "Plakátok", "Email", "Vezetői tájékoztatás", "App értesítés"],
                "required": true
              }
            ]
          }
        ]
      }
    }
  }'::jsonb,
  true
);

-- Create test audit with the new questionnaire
INSERT INTO audits (
  id,
  company_name,
  questionnaire_id,
  access_token,
  hr_user_id,
  is_active,
  expires_at
)
SELECT
  'f055b4db-4a2b-4f21-b293-a1f2bfb9fec5',
  'Teszt Cég Kft.',
  '1ed222be-a62f-4db5-bd42-a4bd392602fb',
  'TEST-4f11ef7de8ad619cf13c7e79e620',
  id,
  true,
  NULL
FROM profiles
WHERE email LIKE '%@%'
LIMIT 1;