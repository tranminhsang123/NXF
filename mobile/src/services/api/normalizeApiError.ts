import axios from 'axios';

import type { ApiError } from '../../types/api';

export function normalizeApiError(error: unknown): ApiError {
  if (axios.isAxiosError(error)) {
    if (!error.response) {
      if (error.code === 'ECONNABORTED') {
        return { message: 'Ket noi backend qua lau. Vui long thu lai.' };
      }

      return { message: 'Khong the ket noi backend. Kiem tra mang va thu lai.' };
    }

    const message = (error.response?.data as { message?: string } | undefined)?.message ?? 'Yeu cau that bai.';
    const fieldErrors = (error.response?.data as { errors?: Record<string, string[]> } | undefined)?.errors;

    return {
      message,
      fieldErrors,
      statusCode: error.response?.status,
    };
  }

  if (error instanceof Error) {
    if (error.message === 'AUTH_TOKEN_MISSING') {
      return { message: 'Ban can dang nhap lai de tiep tuc.' };
    }

    return { message: error.message };
  }

  return { message: 'Da xay ra loi khong xac dinh.' };
}
