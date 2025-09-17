"use client";

export default function TestMinimal() {
  const handleClick = () => {
    alert('Button clicked! JavaScript is working!');
  };

  return (
    <div style={{ padding: '20px', backgroundColor: '#f0f0f0', minHeight: '100vh' }}>
      <h1>Minimal JavaScript Test</h1>
      <button 
        onClick={handleClick}
        style={{ 
          padding: '10px 20px', 
          fontSize: '16px', 
          backgroundColor: '#007bff', 
          color: 'white', 
          border: 'none', 
          borderRadius: '4px',
          cursor: 'pointer'
        }}
      >
        Click Me - Test JavaScript
      </button>
      <p>If this button works, JavaScript is functioning properly.</p>
    </div>
  );
}
