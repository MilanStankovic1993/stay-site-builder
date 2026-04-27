# StaySite Builder

StaySite Builder je Laravel + Filament MVP platforma za izradu brzih i modernih sajtova za privatni smestaj. Vlasnik unosi podatke o svom objektu kroz jednostavan panel, bira temu i objavljuje javni sajt bez generisanja posebnog projekta po korisniku.

## Arhitektura proizvoda

- `Landing stranica` na `/` sa opisom proizvoda, demo temama i CTA akcijama
- `Owner panel` na `/dashboard` za registraciju, prijavu i website builder flow
- `Super admin panel` na `/admin` za upravljanje korisnicima, smestajima, temama i platformom
- `Javni storefront` na `/s/{slug}` za objavljene sajtove
- `Preview ruta` na signed linku za proveru sajta pre objave

## Sta postoji u MVP-u

- owner registracija i login preko Filament panela
- super admin upravljanje svim korisnicima
- owner upravljanje sopstvenim smestajima i upitima
- build / publish / unpublish flow za sajt
- Spatie Media Library kolekcije: `hero`, `gallery`, `logo`
- Spatie Settings za globalna podesavanja platforme
- inquiry forma koja cuva upite u bazi
- demo seed podaci i placeholder slike

## Glavni modeli

- `User`
- `Accommodation`
- `Amenity`
- `AccommodationInquiry`
- `ThemePreset`
- `PlatformSettings`

## Demo nalozi

- super admin: `admin@example.com` / `password`
- demo owner: `owner@example.com` / `password`

## Lokalno pokretanje

1. Instaliraj PHP 8.3+, Composer, Node.js i MySQL.
2. Kreiraj bazu `staysite_builder`.
3. Kopiraj `.env.example` u `.env` i po potrebi prilagodi kredencijale.
4. Pokreni:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Produkcioni deploy checklist

1. Kopiraj `.env.production.example` u `.env` na serveru i popuni:
- `APP_KEY`
- `APP_URL`
- MySQL kredencijale
- SMTP podatke

2. Na serveru pokreni:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

3. Obavezno proveri:
- da li je `public/storage` dostupan
- da li je `queue` pokrenut ako koristis `QUEUE_CONNECTION=database`
- da li mail radi
- da li je HTTPS aktivan
- da li se `APP_DEBUG=false`

4. Pre live pustanja proveri:
- owner registraciju
- aktivaciju korisnika od strane super admina
- odobravanje objave sajta
- demo temu i jedan pravi public sajt
- SR/EN prebacivanje na landing strani, panelima i storefront-u

## Preporuke za produkciju

- koristi poseban MySQL korisnicki nalog za aplikaciju
- ukljuci redovne backup-e za bazu i `storage/app/public`
- koristi process manager za queue worker
- dodaj error monitoring i uptime monitoring
- ako ocekujes veci broj slika, planiraj prelazak na S3 / R2 storage

## Glavne rute

- landing: `/`
- owner panel login: `/dashboard/login`
- owner registracija: `/dashboard/register`
- super admin login: `/admin/login`
- demo javni sajt: `/s/villa-lavanda-tara`

## Napomene

- Za brzu lokalnu proveru mozes koristiti SQLite, ali ciljna MVP postavka ostaje MySQL.
- Javno se prikazuju samo `published` smestaji.
- Preview link omogucava proveru sajta pre javne objave.
- U ovoj fazi nema booking engine-a, online naplate ni sinhronizacije sa Booking/Airbnb.

## Sledeci logicni koraci

- owner dashboard sa jasnim onboarding koracima i statistikama
- dodatne Blade teme
- email notifikacije za upite
- SaaS paketi i billing
- custom domeni
- kalendar raspolozivosti i sezonske cene
