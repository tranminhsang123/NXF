import { useCallback, useMemo, useState } from 'react';

import { getApiErrorMessage } from '../services/api/getApiErrorMessage';

export type ApiStatusType = 'idle' | 'loading' | 'success' | 'error';

export function useApiStatus(initialMessage: string) {
  const [statusMessage, setStatusMessage] = useState(initialMessage);
  const [statusType, setStatusType] = useState<ApiStatusType>('idle');

  const setIdleMessage = useCallback((message: string) => {
    setStatusType('idle');
    setStatusMessage(message);
  }, []);

  const setLoadingMessage = useCallback((message: string) => {
    setStatusType('loading');
    setStatusMessage(message);
  }, []);

  const setSuccessMessage = useCallback((message: string) => {
    setStatusType('success');
    setStatusMessage(message);
  }, []);

  const setErrorFromApi = useCallback((error: unknown, fallbackMessage: string) => {
    setStatusType('error');
    setStatusMessage(getApiErrorMessage(error, fallbackMessage));
  }, []);

  return useMemo(
    () => ({
      statusMessage,
      statusType,
      isLoadingStatus: statusType === 'loading',
      setStatusMessage,
      setIdleMessage,
      setLoadingMessage,
      setSuccessMessage,
      setErrorFromApi,
    }),
    [setErrorFromApi, setIdleMessage, setLoadingMessage, setSuccessMessage, statusMessage, statusType],
  );
}

