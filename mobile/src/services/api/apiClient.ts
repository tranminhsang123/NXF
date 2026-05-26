import { requestWithFallback } from './requestWithFallback';

type RequestOptions = {
  requireAuth?: boolean;
  timeoutMs?: number;
  retryCount?: number;
  token?: string;
};

export function apiGet<TResponse>(url: string, options?: RequestOptions) {
  return requestWithFallback<TResponse>({
    method: 'get',
    url,
    ...options,
  });
}

export function apiPost<TResponse, TData = unknown>(url: string, data?: TData, options?: RequestOptions) {
  return requestWithFallback<TResponse, TData>({
    method: 'post',
    url,
    data,
    ...options,
  });
}

export function apiPut<TResponse, TData = unknown>(url: string, data?: TData, options?: RequestOptions) {
  return requestWithFallback<TResponse, TData>({
    method: 'put',
    url,
    data,
    ...options,
  });
}

export function apiPatch<TResponse, TData = unknown>(url: string, data?: TData, options?: RequestOptions) {
  return requestWithFallback<TResponse, TData>({
    method: 'patch',
    url,
    data,
    ...options,
  });
}

export function apiDelete<TResponse, TData = unknown>(url: string, data?: TData, options?: RequestOptions) {
  return requestWithFallback<TResponse, TData>({
    method: 'delete',
    url,
    data,
    ...options,
  });
}

