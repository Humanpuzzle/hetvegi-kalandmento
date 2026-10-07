# Hétvégi Kalandmentő

WordPress bővítmény hétvégi programok megjelenítéséhez és szűréséhez.

## Telepítés

1. Másold a `hetvegi-kalandmento` mappát a `wp-content/plugins/` könyvtárba.
2. Aktiváld a bővítményt a WordPress admin felületén.
3. Használd a `[hetvegi_kalandmento]` shortcode-ot bármely bejegyzésben vagy oldalon.

## Fejlesztés

### Struktúra

```
hetvegi-kalandmento/
├── hetvegi-kalandmento.php          # Plugin bootstrap
├── includes/
│   ├── Plugin.php                   # Fő plugin osztály
│   ├── ProgramProvider.php          # Adatforrás kezelés
│   ├── ProgramStatusCalculator.php  # Státusz számítás
│   ├── RestController.php           # REST API végpontok
│   └── Shortcode.php                # Shortcode renderelés
├── enums/
│   └── Enums/ProgramStatus.php      # Program státuszok enum
├── assets/
│   ├── css/
│   │   └── frontend.css             # Frontend stílusok
│   └── js/
│       └── frontend.js              # Frontend logika
├── data/
│   └── programs.json                # Program adatok
├── tests/
│   └── run_tests.php                # Teszt futtató (ProgramStatusCalculator)
└── README.md
```

### Architektúra

- **ProgramProvider**: JSON fájlból betölti a programokat (később cserélhető DB/re API-ra)
- **ProgramStatusCalculator**: Üzleti logika a státusz kiszámításához (WP-től független)
- **RestController**: REST API végpont `/wp-json/hetvegi-kalandmento/v1/programs`
- **Shortcode**: `[hetvegi_kalandmento]` - HTML konténer + asset betöltés
- **Frontend**: Natív JavaScript, fetch API-val lekéri az adatokat, rendereli

### Program Státuszok

| Enum érték | Megjelenítés (status_label) | Magyarázat |
|------------|----------------------------|------------|
| `available` | Elérhető | Több mint 20% szabad hely |
| `limited` | Már csak néhány hely | ≤20% szabad hely |
| `full` | Betelt | Nincs szabad hely |
| `cancelled` | Lemondva | Program lemondva |
| `not_bookable` | Nem foglalható | Múltbeli, hibás adat, stb. |

### REST API

**Endpoint:** `GET /wp-json/hetvegi-kalandmento/v1/programs`

**Válasz formátum:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Pilisi családi panorámatúra",
      "location": "Dobogókő",
      "start_at": "2026-10-10T09:00:00+02:00",
      "capacity": 20,
      "booked": 11,
      "remaining": 9,
      "difficulty": "könnyű",
      "price_huf": 4900,
      "cancelled": false,
      "status": "available",
      "status_label": "Elérhető",
      "bookable": true
    }
  ]
}
```

### Üzleti szabályok (előbbirend alapján)

1. **cancelled = true** → `cancelled` (Lemondva)
2. **Érvénytelen foglalási adatok** (capacity ≤ 0, booked < 0, booked > capacity) → `not_bookable` (Nem foglalható)
3. **Múltbeli esemény** (start_at ≤ reference_time) → `not_bookable` (Nem foglalható)
4. **booked ≥ capacity** → `full` (Betelt)
5. **remaining × 100 ≤ capacity × 20** (pontosan 20% vagy kevesebb) → `limited` (Már csak néhány hely)
6. **Egyéb esetben** → `available` (Elérhető)

### Használt Technológiák

- PHP 8.3+
- WordPress REST API
- Natív JavaScript (fetch API, createElement/textContent)
- WordPress enqueue rendszer CSS/JS-hez

### Fejlesztői Döntések

1. **Natív JavaScript**: Nincs build lépés, egyszerű telepítés, kis méret.
2. **JSON adatforrás**: Egyszerű, verzionálható, később cserélhető.
3. **PHP 8.1 enum**: Típusbiztos státusz kezelés.
4. **Nincs Composer**: Minimális függőségek, egyszerű deployment.
5. **Státusz számítás PHP-ban**: Determinisztikus, tesztelhető, nem függ a frontendtől.
6. **Nincs foglalási funkció**: Csak megjelenítés és CTA gombok.

### Hozzáférhetőség

- Szemantikus HTML: `<article>`, `<time datetime="">`, `<label>` + `<select>`
- `aria-live` régiók: betöltés (polite), hiba (assertive), üres eredmény (polite)
- Látható fókuszállapotok (`:focus-visible`)
- Státusz nem csak színnel, hanem CSS pseudo-element jelzőponttal és szöveges címkével jelenik meg.
- Letiltott gombok vizuálisankülönböztetve (`cursor: not-allowed`, opacity, szürke szín)
- Mobilbarát tap targetek (min 44×44px)

### Ismert Korlátok

- Nincs szerveroldali gyorsítótár (minden kérés újra lekéri a JSON-t)
- Nincs foglalási/megerősítési funkció – csak CTA gombok
- Nincs admin felület a programok kezeléséhez (csak JSON szerkesztés)
- A fixture-ben a reference_time rögzített, ezért a státuszszámítás determinisztikus és reprodukálható.

### Továbbfejlesztési Lehetőségek (Több Iddő Esetén)

- WordPress transients alapú gyorsítótár a REST endpointra
- A ProgramProvider jelenleg a JSON fixture kezeléséért felel, így az adatforrás kezelése nincs a REST Controllerbe helyezve.
- Admin felület a programok CRUD-jához
- Továbbfejlesztett szűrés (dátum, helyszín, státusz)
- Részletesebb programkártyák és programoldalak.
- URL-alapú szűrőállapot és megosztható szűrési eredmények.
- Foglalási funkció (AJAX POST végpont, e-mail megerősítés)
- Többnyelvűség (i18n) támogatás
- Automatikus képek/képernyőképek a programokhoz

### AI Segítség

Ez a bővítmény AI támogatással készült (GitHub Copilot / opencode). Az AI-t használtam:
- Kód generálásához (PHP osztályok, JS logika, CSS)
- Teszteléshez (teszt esetek generálása)
- Dokumentáció írásához (README, kód kommentek)
- Probléma megoldáshoz (hook regisztráció, útvonalspecifikus hibák)

Fejlesztési idő: kb. 3 óra (tervezés, implementáció, tesztelés, dokumentáció)

## Licenc

GPL-2.0-or-later