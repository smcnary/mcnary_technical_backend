import { cookies } from 'next/headers'

export async function getAuthToken(): Promise<string | undefined> {
  const cookieStore = await cookies();
  return cookieStore.get('auth')?.value
}

export async function getUserRole(): Promise<string | undefined> {
  const cookieStore = await cookies();
  return cookieStore.get('role')?.value
}
