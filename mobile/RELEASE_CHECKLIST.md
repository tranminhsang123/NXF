# Mobile Release Checklist

## Build & Runtime

- [ ] `npm run start -- --clear` runs cleanly
- [ ] `npx tsc --noEmit` passes
- [ ] Login / register / restore session works
- [ ] User tabs (`Home`, `Learn`, `Social`, `Profile`) load API data
- [ ] Admin tab appears only for admin role and endpoints respond

## API Smoke

- [ ] `php artisan test --filter=ApiParitySmokeTest` passes
- [ ] `/api/learning/*` endpoints accessible with user token
- [ ] `/api/social/*` endpoints accessible with user token
- [ ] `/api/admin/*` endpoints forbidden for normal user and allowed for admin

## Performance & Reliability

- [ ] Lists use pagination where available (`users`, `kanji`, `progress`)
- [ ] React Query cache is enabled for data fetching
- [ ] Error messages are normalized via `normalizeApiError`
- [ ] No blocking loading states over 10s on normal network

## Store Readiness

- [ ] App icon + splash are production assets
- [ ] Privacy policy URL and support contact are ready
- [ ] Screenshots captured for phone form factors
- [ ] Internal test account credentials prepared
- [ ] Android AAB and iOS IPA builds complete on EAS
