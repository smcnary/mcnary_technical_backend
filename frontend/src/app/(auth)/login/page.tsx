"use client"

import { useState } from 'react'
import Link from 'next/link'

export default function LoginPage() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    // TODO: Call backend auth and set httpOnly cookies via API route or backend redirect
    alert('Implement login with backend')
  }

  return (
    <div>
      <h1 className="text-xl font-semibold mb-4">Sign in</h1>
      <form className="space-y-4" onSubmit={onSubmit}>
        <input className="input-field" placeholder="Email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
        <input className="input-field" placeholder="Password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        <button className="btn-primary w-full" type="submit">Continue</button>
      </form>
      <div className="mt-4 text-sm text-gray-600">
        <Link href="/forgot-password" className="hover:underline">Forgot password?</Link>
      </div>
    </div>
  )
}
