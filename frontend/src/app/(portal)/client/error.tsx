"use client"

export default function Error({ error }: { error: Error }) {
  return (
    <div>
      <h2 className="text-red-600 font-semibold">Something went wrong</h2>
      <pre className="text-sm mt-2 bg-gray-50 p-3 rounded border border-gray-200 overflow-auto">{error.message}</pre>
    </div>
  )
}
