export default function TestCSSPage() {
  return (
    <div className="min-h-screen bg-blue-500 p-8">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-4xl font-bold text-white mb-4">CSS Test Page</h1>
        <div className="bg-white rounded-lg p-6 shadow-lg">
          <h2 className="text-2xl font-semibold text-gray-800 mb-4">Tailwind CSS Test</h2>
          <p className="text-gray-600 mb-4">
            If you can see this styled text, Tailwind CSS is working correctly.
          </p>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="bg-red-100 p-4 rounded border border-red-200">
              <h3 className="font-semibold text-red-800">Red Box</h3>
              <p className="text-red-600">This should be red text</p>
            </div>
            <div className="bg-green-100 p-4 rounded border border-green-200">
              <h3 className="font-semibold text-green-800">Green Box</h3>
              <p className="text-green-600">This should be green text</p>
            </div>
            <div className="bg-blue-100 p-4 rounded border border-blue-200">
              <h3 className="font-semibold text-blue-800">Blue Box</h3>
              <p className="text-blue-600">This should be blue text</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
