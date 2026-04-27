# Pre-Launch Checklist

## 1. Server i environment

- Proveren `APP_ENV=production`
- Proveren `APP_DEBUG=false`
- Popunjen `APP_KEY`
- Popunjen `APP_URL`
- Podesen MySQL i testirana konekcija
- Podesen mail server
- HTTPS aktivan

## 2. Deploy komande

Pokrenuti:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 3. Osnovni panel flow

- `super_admin` login vodi na `/admin`
- `owner` login vodi na `/dashboard`
- owner ne moze na `/admin`
- super admin ne moze na `/dashboard`
- registracija pravi neaktivan owner nalog
- super admin moze da aktivira nalog
- super admin moze da odobri objavu sajta

## 4. Owner flow

- owner moze da kreira smestaj
- owner vidi samo svoje smestaje
- owner vidi samo svoje upite
- owner moze da unese SR i EN sadrzaj
- owner moze da otvori preview
- owner bez dozvole ne moze da objavi sajt
- owner sa dozvolom moze da objavi sajt

## 5. Public website flow

- objavljen smestaj je vidljiv na `/s/{slug}`
- draft smestaj nije javno vidljiv
- signed preview radi
- SR/EN switch radi na javnom sajtu
- fallback na srpski radi kada EN polja nisu popunjena
- hero slika, galerija i logo se prikazuju
- inquiry forma radi i cuva upit u bazi

## 6. Admin flow

- korisnici lista radi bez greske
- smestaji lista radi bez greske
- upiti lista radi bez greske
- teme lista radi bez greske
- platform settings radi bez greske
- admin dashboard widgeti rade

## 7. Sadrzaj i UX

- landing stranica izgleda ispravno na desktop i mobile
- tri teme imaju jasan vizuelni identitet
- demo preview za sve teme radi
- panel SR/EN switch je vidljiv i funkcionalan
- glavni tekstovi su provereni na SR i EN

## 8. Operativa

- backup baze je pripremljen
- backup `storage/app/public` je pripremljen
- queue worker je podignut ako se koristi `QUEUE_CONNECTION=database`
- error logging je provereno
- monitoring / uptime alat je dodat ako postoji

## 9. Posle launch-a

- kreirati prvi pravi owner nalog
- testirati jedan realan publish flow
- testirati jedan realan inquiry flow
- proveriti da li slike i media upload rade sa produkcionog domena
