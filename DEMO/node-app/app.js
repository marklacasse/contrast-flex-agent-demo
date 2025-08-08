const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const fs = require('fs');
const { exec } = require('child_process');
const os = require('os');

const app = express();
const PORT = process.env.PORT || 3030;

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(express.static('public'));

// Set EJS as template engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Routes

// Home page
app.get('/', (req, res) => {
    res.render('index', { 
        title: 'Node.js Security Demo with Contrast',
        timestamp: new Date().toISOString()
    });
});

// API Routes
app.get('/api/info', (req, res) => {
    const info = {
        timestamp: new Date().toISOString(),
        platform: os.platform(),
        nodejs_version: process.version,
        architecture: os.arch(),
        hostname: os.hostname(),
        uptime: os.uptime(),
        memory: {
            total: Math.round(os.totalmem() / 1024 / 1024) + ' MB',
            free: Math.round(os.freemem() / 1024 / 1024) + ' MB'
        },
        environment_vars: {
            NODE_ENV: process.env.NODE_ENV || 'Not set',
            PATH: process.env.PATH ? process.env.PATH.split(':').slice(0, 5) : [],
            CONTRAST_URL: process.env.CONTRAST_URL || 'Not configured'
        }
    };
    res.json(info);
});

app.get('/api/hello/:name?', (req, res) => {
    const name = req.params.name || 'World';
    const response = {
        message: `Hello, ${name}!`,
        timestamp: new Date().toISOString(),
        visitor: name
    };
    res.json(response);
});

app.get('/health', (req, res) => {
    const health = {
        status: 'healthy',
        timestamp: new Date().toISOString(),
        app: 'Node.js Demo App',
        uptime: process.uptime()
    };
    res.json(health);
});

// VULNERABLE ENDPOINTS - FOR DEMO/TESTING PURPOSES ONLY
// DO NOT USE IN PRODUCTION

app.get('/vulnerable/file-read', (req, res) => {
    const filename = req.query.filename || 'package.json';
    
    console.log(`[DEBUG] file-read endpoint called with filename: '${filename}' (length: ${filename?.length || 0})`);
    
    try {
        // VULNERABILITY: Path Traversal - No path sanitization
        let filePath;
        
        // If filename starts with /, treat as absolute path
        if (filename.startsWith('/')) {
            filePath = filename;
        } else {
            // Try to find the file in the node-app directory first, then /demos
            const appFilePath = path.join('/demos/node-app', filename);
            const demosFilePath = path.join('/demos', filename);
            
            if (fs.existsSync(appFilePath)) {
                filePath = appFilePath;
            } else if (fs.existsSync(demosFilePath)) {
                filePath = demosFilePath;
            } else {
                filePath = appFilePath; // Default to app directory for error reporting
            }
        }
        
        console.log(`[DEBUG] Attempting to read file: '${filePath}'`);
        const content = fs.readFileSync(filePath, 'utf8');
        
        res.json({
            filename: filename,
            content: content,
            warning: 'This endpoint has a path traversal vulnerability for demo purposes'
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
            filename: filename,
            warning: 'This endpoint has a path traversal vulnerability for demo purposes'
        });
    }
});

app.get('/vulnerable/command', (req, res) => {
    const cmd = req.query.cmd || '';
    
    console.log(`[DEBUG] command endpoint called with cmd: '${cmd}' (length: ${cmd?.length || 0})`);
    
    if (!cmd) {
        return res.status(400).json({
            error: "No command provided in 'cmd' parameter",
            warning: 'This endpoint has a command injection vulnerability for demo purposes'
        });
    }

    try {
        // VULNERABILITY: Command Injection - Direct process execution
        exec(cmd, { timeout: 10000 }, (error, stdout, stderr) => {
            if (error) {
                return res.status(400).json({
                    error: error.message,
                    command: cmd,
                    warning: 'This endpoint has a command injection vulnerability for demo purposes'
                });
            }

            res.json({
                command: cmd,
                stdout: stdout,
                stderr: stderr,
                warning: 'This endpoint has a command injection vulnerability for demo purposes'
            });
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
            command: cmd,
            warning: 'This endpoint has a command injection vulnerability for demo purposes'
        });
    }
});

app.get('/vulnerable/ping', (req, res) => {
    const host = req.query.host || 'localhost';
    
    console.log(`[DEBUG] ping endpoint called with host: '${host}' (length: ${host?.length || 0})`);
    
    try {
        // VULNERABILITY: Command Injection via string concatenation
        const command = `ping -c 4 ${host}`;
        
        exec(command, { timeout: 15000 }, (error, stdout, stderr) => {
            res.json({
                host: host,
                command: command,
                output: stdout,
                error: stderr || (error ? error.message : null),
                warning: 'This endpoint has a command injection vulnerability for demo purposes'
            });
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
            host: host,
            warning: 'This endpoint has a command injection vulnerability for demo purposes'
        });
    }
});

// Contrast agent information
app.get('/contrast', (req, res) => {
    const contrastInfo = {
        agent: 'Contrast Node.js Agent',
        version: '4.5.0',
        mode: ['Assess', 'Inventory'],
        status: 'Active',
        teamserver: process.env.CONTRAST_URL || 'Not configured',
        features: [
            'Real-time vulnerability detection',
            'Runtime application security monitoring',
            'Code-level security insights',
            'Attack detection and blocking'
        ],
        documentation: 'https://docs.contrastsecurity.com'
    };
    res.json(contrastInfo);
});

// Contrast page
app.get('/contrast-info', (req, res) => {
    res.render('contrast', { 
        title: 'Contrast Security Information'
    });
});

// Debug endpoint
app.get('/debug/files', (req, res) => {
    const info = {
        current_directory: process.cwd(),
        views_exist: fs.existsSync('views'),
        index_view_exists: fs.existsSync('views/index.ejs'),
        contrast_view_exists: fs.existsSync('views/contrast.ejs'),
        package_json_exists: fs.existsSync('package.json'),
        files_in_current_dir: fs.readdirSync('.').slice(0, 10)
    };
    res.json(info);
});

// Start server
app.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 Starting Node.js app on port ${PORT}`);
    console.log(`🌐 Access the app at: http://localhost:${PORT}`);
    console.log(`📊 Health check: http://localhost:${PORT}/health`);
    console.log(`🔧 System info API: http://localhost:${PORT}/api/info`);
    console.log(`⚠️ Vulnerability testing available on home page`);
    console.log(`🔄 View updated at: ${new Date()}`);
});
