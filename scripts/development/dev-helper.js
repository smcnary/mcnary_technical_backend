#!/usr/bin/env node

// Quick development helper for testing the audit wizard
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const commands = {
  'test-quick': 'npm run test:quick',
  'test-full': 'npm run test:audit-wizard',
  'test-ui': 'npm run test:ui',
  'test-headed': 'npm run test:headed',
  'test-debug': 'npm run test:debug',
  'dev': 'npm run dev',
  'build': 'npm run build',
  'lint': 'npm run lint'
};

function showHelp() {
  console.log(`
🚀 Audit Wizard Development Helper

Available commands:
  test-quick    - Run quick development tests
  test-full     - Run full audit wizard test suite
  test-ui       - Run tests with Playwright UI
  test-headed    - Run tests in headed mode (see browser)
  test-debug     - Run tests in debug mode
  dev           - Start development server
  build         - Build for production
  lint          - Run linting

Usage: node dev-helper.js <command>
Example: node dev-helper.js test-quick
`);
}

function runCommand(command) {
  try {
    console.log(`\n🔄 Running: ${command}`);
    execSync(command, { stdio: 'inherit', cwd: process.cwd() });
  } catch (error) {
    console.error(`\n❌ Error running: ${command}`);
    process.exit(1);
  }
}

function main() {
  const args = process.argv.slice(2);
  
  if (args.length === 0 || args.includes('--help') || args.includes('-h')) {
    showHelp();
    return;
  }

  const command = args[0];
  
  if (commands[command]) {
    runCommand(commands[command]);
  } else {
    console.error(`❌ Unknown command: ${command}`);
    showHelp();
    process.exit(1);
  }
}

main();
