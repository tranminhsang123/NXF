# Japanese Study Mobile (React Native)

Mobile app is scaffolded with Expo + TypeScript so the team can ship on both Google Play and App Store from one codebase.

## 1) Prerequisites

- Node.js 20+ (22 is fine)
- Android Studio for Android emulator
- Xcode (macOS only) for iOS simulator
- Expo Go app on your phone (optional)

## 2) First run

```bash
cd mobile
cp .env.example .env
npm install
npm run start
```

Set `EXPO_PUBLIC_API_URL` in `.env`:

- Android emulator: `http://10.0.2.2:8000/api`
- iOS simulator: `http://127.0.0.1:8000/api`
- Physical device: `http://YOUR_LAN_IP:8000/api`

Google login needs these variables in `mobile/.env`:

- `EXPO_PUBLIC_GOOGLE_ANDROID_CLIENT_ID`
- `EXPO_PUBLIC_GOOGLE_IOS_CLIENT_ID`
- `EXPO_PUBLIC_GOOGLE_WEB_CLIENT_ID`

Backend also needs `GOOGLE_CLIENT_ID` in root `.env` (same OAuth client used to mint ID token).

## 3) Project structure

```text
mobile/
  src/
    config/          # env + app config
    context/         # auth session provider
    navigation/      # auth stack + app stack
    screens/         # AuthScreen + tab screens
    providers/       # React Query provider
    lib/             # query client
    services/api/    # API request helpers
    services/auth/   # auth API + token storage
    services/minna/  # minna lesson APIs
    services/learning/
    services/social/
    services/admin/
    services/flashcards/
    types/           # auth/api/domain types
```

## 4) Current status

- Auth API integrated (`register`, `login`, `google login`, `me`, `logout`)
- Token is stored using `expo-secure-store`
- Session restore on app launch is enabled
- App is split into `Auth` and `Main` navigation stacks
- Main area now uses role-based tabs:
  - user: `Home`, `Learn`, `Social`, `Profile`
  - admin: + `Admin`
- Learn stack includes `Minna`, `Kanji`, `Vocabulary`, `Courses`, `Progress`, `Search`, `Flashcards`
- Social stack includes `chat groups` and `direct inbox`
- Admin stack includes dashboard stats, users lock/unlock, notifications, moderation actions
- React Query is enabled for data fetching and caching

## 5) QA and hardening next steps

1. Improve UI polish and per-section renderers to match web visuals exactly
2. Add pagination/infinite loading UI for all long lists
3. Add test coverage for mobile API edge cases and auth/session failures
4. Add crash reporting and analytics instrumentation

## 6) Build for stores with EAS

```bash
npm install -g eas-cli
eas login
eas init
eas build --platform android
eas build --platform ios
```

Then upload:

- Android AAB -> Google Play Console
- iOS IPA -> App Store Connect

## 7) Store release checklist

- App icon and splash image
- Privacy policy URL
- Terms of use URL (recommended)
- Screenshots for phone sizes
- App description and keywords
- Test account for reviewer (if login required)
