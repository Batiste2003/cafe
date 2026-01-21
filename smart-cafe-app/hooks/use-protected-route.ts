import { useEffect } from 'react';
import { useRouter, useSegments } from 'expo-router';
import { useAuth } from '@/contexts/AuthContext';

export function useProtectedRoute() {
  const { isAuthenticated, loading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (loading) return;

    const inAuthGroup = segments[0] === '(tabs)';
    const inProductGroup = segments[0] === 'product';

    if (!isAuthenticated && inAuthGroup) {
      router.replace('/auth/login');
    } else if (isAuthenticated && !inAuthGroup && !inProductGroup) {
      router.replace('/(tabs)');
    }
  }, [isAuthenticated, segments, loading, router]);
}
