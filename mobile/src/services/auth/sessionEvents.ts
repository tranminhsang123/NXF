type UnauthorizedReason = 'token_expired' | 'invalid_token' | 'unauthorized';

type UnauthorizedListener = (reason: UnauthorizedReason) => void;

const listeners = new Set<UnauthorizedListener>();

export function onUnauthorized(listener: UnauthorizedListener) {
  listeners.add(listener);
  return () => {
    listeners.delete(listener);
  };
}

export function emitUnauthorized(reason: UnauthorizedReason) {
  listeners.forEach((listener) => {
    try {
      listener(reason);
    } catch {
      // Do not break other listeners because of one failing callback.
    }
  });
}

