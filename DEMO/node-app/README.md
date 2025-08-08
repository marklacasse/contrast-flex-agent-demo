# Node.js Security Demo with Contrast Flex Agent

This Node.js application demonstrates the Contrast Flex Agent's security monitoring capabilities with intentional vulnerabilities for testing and educational purposes.

## Features

- **Express.js Web Application** - Modern Node.js web framework
- **EJS Templates** - Server-side rendering with professional styling
- **Vulnerable Endpoints** - Intentional security flaws for demonstration
- **Contrast Integration** - Real-time security monitoring
- **API Endpoints** - System information and health checks

## Security Vulnerabilities (Intentional)

⚠️ **WARNING**: These vulnerabilities are for demonstration purposes only!

1. **Path Traversal** (`/vulnerable/file-read`)
   - Read arbitrary files from the server
   - Examples: `../../../etc/passwd`, `package.json`

2. **Command Injection** (`/vulnerable/command`)
   - Execute arbitrary system commands
   - Examples: `whoami`, `ls -la`, `ps aux`

3. **Network Command Injection** (`/vulnerable/ping`)
   - Command injection via ping utility
   - Examples: `localhost; whoami`, `localhost | id`

## Running the Application

### Using the Control Script (Recommended)
```bash
# From the DEMO directory in the container
./node-express.sh start          # Start the application
./node-express.sh stop           # Stop the application  
./node-express.sh restart        # Restart the application
./node-express.sh status         # Check status
./node-express.sh logs           # View logs
./node-express.sh install        # Install dependencies only
```

### Manual Mode
```bash
cd /demos/node-app
npm install
npm start
```

The application will be available at: http://localhost:3030

## API Endpoints

- `GET /` - Home page with vulnerability testing interface
- `GET /api/info` - System information
- `GET /api/hello/:name?` - Greeting API
- `GET /health` - Health check
- `GET /contrast` - Contrast agent information (JSON)
- `GET /contrast-info` - Contrast agent information (HTML page)
- `GET /debug/files` - Debug file information

## Contrast Flex Agent

The application is designed to work with the Contrast Flex Agent for:

- **Real-time Vulnerability Detection** - Identify security issues during runtime
- **Attack Monitoring** - Track and analyze security events
- **Code-level Insights** - Detailed vulnerability reporting
- **Runtime Protection** - Block malicious requests in real-time

## Environment Variables

- `PORT` - Server port (default: 3030)
- `NODE_ENV` - Environment mode
- `CONTRAST_URL` - Contrast TeamServer URL
- `CONTRAST_API_KEY` - Contrast API key
- `CONTRAST_SERVICE_KEY` - Contrast service key
- `CONTRAST_USER_NAME` - Contrast username

## Security Notes

🚫 **DO NOT** deploy this application to production environments
🚫 **DO NOT** use these vulnerable endpoints on systems you don't own
✅ **DO** use this for security testing and educational purposes only
✅ **DO** review the Contrast findings in your TeamServer dashboard

## Architecture

```
node-app/
├── app.js              # Main application server
├── package.json        # Dependencies and scripts
├── views/              # EJS templates
│   ├── index.ejs       # Home page with vulnerability testing
│   └── contrast.ejs    # Contrast agent information page
└── README.md           # This file
```
