import axios, { type AxiosRequestConfig } from 'axios';

import { env } from '../../config/env';
import { emitUnauthorized } from '../auth/sessionEvents';
import { clearAccessToken, getAccessToken } from '../auth/tokenStorage';

type RequestWithFallbackOptions<TData> = {
  method: 'get' | 'post' | 'put' | 'patch' | 'delete';
  url: string;
  data?: TData;
  token?: string;
  timeoutMs?: number;
  retryCount?: number;
  requireAuth?: boolean;
};

type RequestWithFallbackResult<TResponse> = {
  data: TResponse;
  baseUrl: string;
};

export async function requestWithFallback<TResponse, TData = unknown>({
  method,
  url,
  data,
  token,
  timeoutMs = env.apiTimeoutMs,
  retryCount = env.apiRetryCount,
  requireAuth = true,
}: RequestWithFallbackOptions<TData>): Promise<RequestWithFallbackResult<TResponse>> {
  let lastError: unknown = null;
  let unauthorizedHandled = false;
  const attemptsPerHost = Math.max(1, retryCount + 1);
  const resolvedToken = requireAuth ? token ?? (await getAccessToken()) : token;

  if (requireAuth && !resolvedToken) {
    emitUnauthorized('unauthorized');
    throw new Error('AUTH_TOKEN_MISSING');
  }

  for (const baseUrl of env.apiCandidateUrls) {
    for (let attempt = 0; attempt < attemptsPerHost; attempt += 1) {
      const config: AxiosRequestConfig<TData> = {
        method,
        url,
        baseURL: baseUrl,
        data,
        timeout: timeoutMs,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...(resolvedToken ? { Authorization: `Bearer ${resolvedToken}` } : {}),
        },
      };

      try {
        const response = await axios.request<TResponse>(config);
        return { data: response.data, baseUrl };
      } catch (error) {
        if (axios.isAxiosError(error) && error.response) {
          if (error.response.status === 401 && !unauthorizedHandled) {
            unauthorizedHandled = true;
            await clearAccessToken();
            emitUnauthorized('unauthorized');
          }

          throw error;
        }

        lastError = error;

        const hasMoreRetry = attempt < attemptsPerHost - 1;
        if (hasMoreRetry) {
          // Small incremental delay to avoid immediate retry storms.
          await wait(250 * (attempt + 1));
          continue;
        }
      }
    }
  }

  throw lastError ?? new Error('NO_API_ENDPOINT_AVAILABLE');
}

function wait(ms: number) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}
