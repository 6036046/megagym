# Gym Community Tools

A custom plugin for the MegaGym community theme.

## Functionaliteit

- Voegt twee custom post types toe: `Events` en `Reviews`.
- Registreert meta-boxen voor evenementdatum, locatie, inschrijf-link, reviewscore en externe productlink.
- Biedt shortcodes:
  - `[gct_events limit="3"]`
  - `[gct_reviews limit="3"]`
  - `[gct_cta"]`
- Biedt een instellingenpagina onder `Instellingen > Gym Community` voor de CTA-knop.

## Installatie

1. Upload de map `gym-community-tools` naar `wp-content/plugins/`.
2. Activeer de plugin vanuit het WordPress dashboard.
3. Maak een menu aan onder `Weergave > Menu's` en kies `Primary Menu`.
4. Voeg events en reviews toe via de nieuwe menu-items.

## Gebruik

- Gebruik `[gct_events]` op de homepage om evenementen te tonen.
- Gebruik `[gct_reviews]` om reviews te tonen.
- Gebruik `[gct_cta]` voor de call-to-action knop.
