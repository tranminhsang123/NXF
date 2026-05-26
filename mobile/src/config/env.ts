import Constants from 'expo-constants';
import { NativeModules, Platform } from 'react-native';

const androidEmulatorApiUrl = 'http://10.0.2.2:8000/api';
const localhostApiUrl = 'http://127.0.0.1:8000/api';

const normalizeUrl = (url: string) => url.trim().replace(/\/+$/, '');

const parseHostFromUrl = (value?: string | null) => {
  if (!value) {
    return null;
  }

  try {
    const parsed = new URL(value);
    return parsed.hostname || null;
  } catch {
    return null;
  }
};

const parseHostFromHostPort = (value?: string | null) => {
  if (!value) {
    return null;
  }

  const [host] = value.split(':');
  return host || null;
};

const extractDevServerHost = () => {
  const scriptUrl = NativeModules.SourceCode?.scriptURL as string | undefined;
  const expoHostUri =
    Constants.expoConfig?.hostUri ??
    (Constants as { manifest2?: { extra?: { expoClient?: { hostUri?: string } } } }).manifest2?.extra?.expoClient
      ?.hostUri;
  const expoDebuggerHost = (Constants as { manifest?: { debuggerHost?: string } }).manifest?.debuggerHost;

  return (
    parseHostFromHostPort(expoHostUri) ||
    parseHostFromHostPort(expoDebuggerHost) ||
    parseHostFromUrl(scriptUrl) ||
    null
  );
};

const getLanApiUrl = () => {
  const host = extractDevServerHost();
  return host ? `http://${host}:8000/api` : null;
};

const configuredApiUrl = process.env.EXPO_PUBLIC_API_URL?.trim() ?? '';
const lanApiUrl = getLanApiUrl();

const platformDefaultApiUrl = Platform.OS === 'android' ? androidEmulatorApiUrl : localhostApiUrl;
const primaryApiUrl = configuredApiUrl || lanApiUrl || platformDefaultApiUrl;

const apiCandidateUrls = [configuredApiUrl, lanApiUrl, platformDefaultApiUrl]
  .filter((url): url is string => Boolean(url))
  .map(normalizeUrl)
  .filter((url, index, arr) => arr.indexOf(url) === index);

const parsePositiveNumber = (value: string | undefined, fallback: number) => {
  const parsed = Number(value);
  return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
};

export const env = {
  apiUrl: normalizeUrl(primaryApiUrl),
  apiCandidateUrls,
  detectedLanApiUrl: lanApiUrl ? normalizeUrl(lanApiUrl) : null,
  apiTimeoutMs: parsePositiveNumber(process.env.EXPO_PUBLIC_API_TIMEOUT_MS, 10000),
  apiRetryCount: parsePositiveNumber(process.env.EXPO_PUBLIC_API_RETRY_COUNT, 1),
  googleAndroidClientId: process.env.EXPO_PUBLIC_GOOGLE_ANDROID_CLIENT_ID?.trim() ?? '',
  googleIosClientId: process.env.EXPO_PUBLIC_GOOGLE_IOS_CLIENT_ID?.trim() ?? '',
  googleWebClientId: process.env.EXPO_PUBLIC_GOOGLE_WEB_CLIENT_ID?.trim() ?? '',
};
