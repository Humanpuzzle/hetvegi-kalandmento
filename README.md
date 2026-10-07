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
│   ├── class-plugin.php             # Fő plugin osztály
│   ├── class-program-provider.php   # Adatforrás kezelés
│   ├── class-program-status-calculator.php  # Státusz számítás
│   ├── class-rest-controller.php    # REST API végpontok
│   └── class-shortcode.php          # Shortcode renderelés
├── enums/
│   └── ProgramStatus.php            # Program státuszok enum
├── assets/
│   ├── css/
│   │   └── frontend.css             # Frontend stílusok
│   └── js/
│       └── frontend.js              # Frontend logika
├── data/
│   └── programs.json                # Program adatok
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

### Használt Technológiák

- PHP 8.1+ (enum, strict types, readonly properties)
- WordPress REST API
- Natív JavaScript (fetch API)
- WordPress enqueue rendszer CSS/JS-hez

### Fejlesztői Döntések

1. **Natív JavaScript**: Nincs build lépés, egyszerű telepítés, kis méret.
2. **JSON adatforrás**: Egyszerű, verzionálható, később cserélhető.
3. **PHP 8.1 enum**: Típusbiztos státusz kezelés.
4. **Nincs Composer**: Minimális függőségek, egyszerű deployment.