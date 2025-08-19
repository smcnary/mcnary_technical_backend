import { cookies } from 'next/headers'

export function getAuthToken(): string | undefined {
  return cookies().get('auth')?.value
}

export function getUserRole(): string | undefined {
  return cookies().get('role')?.value
}
