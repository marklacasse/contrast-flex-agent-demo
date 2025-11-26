# Contrast Flex Agent Demo Environment

This repository contains a comprehensive demonstration environment for the Contrast Flex Agent, featuring multiple vulnerable applications for security testing and analysis.

## Repository Information

**Repository URL:** https://github.com/marklacasse/contrast-flex-agent-demo.git

## 🚀 Quick Start

### Prerequisites
- Docker Desktop installed and running
- macOS, Linux, or Windows with WSL2
- Contrast Security account with agent token

### Initial Setup

1. **Clone the repository:**
```bash
git clone https://github.com/marklacasse/contrast-flex-agent-demo.git
cd contrast-flex-agent-demo
```

2. **Configure your Contrast agent token:**
```bash
# Copy the configuration template
cp config.template config.local

# Edit config.local and add your token
# Get your token from: Contrast TeamServer -> Organization Settings -> Agent
nano config.local  # or use your preferred editor
```

3. **Start the demo:**
```bash
./run-demo.sh
```

**Alternative: If you need to set permissions first (rare):**
```bash
./setup.sh  # Sets executable permissions, then run ./run-demo.sh
```

That's it! The script will:
1. Build the Docker container with Contrast agent
2. Start all applications with proper port forwarding  
3. Connect you to the container shell
4. Make all scripts executable automatically

### Security Note
- Your personal configuration (`config.local`) is automatically git-ignored
- Never commit your actual Contrast agent token to version control

## 📚 Demo Applications

### 🐍 Python Flask Application (Port 9090)
- **Framework**: Flask with Jinja2 templates
- **Features**: Path traversal, command injection vulnerabilities
- **URL**: http://localhost:9090

### 🟢 Node.js Express Application (Port 3030)
- **Framework**: Express.js with EJS templates  
- **Features**: Professional UI, vulnerability testing, Contrast integration
- **URL**: http://localhost:3030

### 🔷 .NET Core Application (Port 8181)
- **Framework**: ASP.NET Core 8.0 with MVC and Minimal APIs
- **Features**: Professional UI, integrated vulnerability testing, Contrast info page
- **URL**: http://localhost:8181

### ☕ Apache Tomcat (Java) Application (Port 8080)
- **Framework**: Apache Tomcat 9.0.95 with Spring Boot
- **Application**: Custom Spring Boot Demo Application
- **Port**: 8080
- **URL**: http://localhost:8080/contrast-demo/
- **Description**: Custom Spring Boot application with intentionally vulnerable endpoints for security testing

### 🐘 PHP Drupal 10 Application (Port 7070)
- **Framework**: Drupal 10 with PHP 8.4 and Apache 2.4
- **Features**: SQL injection, XSS, command injection, path traversal, file upload vulnerabilities
- **URL**: http://localhost:7070/contrast-demo
- **Description**: Modern Drupal CMS with custom vulnerable module for comprehensive security testing

### 🔴 Laravel PHP Application (Port 5050)

- **Framework**: Laravel 10 with PHP 8.4, PHP-FPM, and Apache 2.4
- **Features**: SQL injection, XSS, command injection, path traversal, file upload vulnerabilities
- **URL**: <http://localhost:5050>
- **Description**: Laravel framework with intentionally vulnerable endpoints featuring professional UI, persistent navigation, and one-click exploit links

## 🛠 Demo Control Script

The `demo-control.sh` script provides unified management for all applications:

### Application Management Commands

```bash
# Individual Application Control
./demo-control.sh <app> <command>

# Available applications: python, node, netcore, tomcat, drupal, laravel
# Available commands: start, stop, restart, status, logs
```

### Examples

```bash
# Start individual applications
./demo-control.sh python start     # Start Python Flask
./demo-control.sh node start       # Start Node.js Express  
./demo-control.sh netcore start    # Start .NET Core
./demo-control.sh tomcat start     # Start Apache Tomcat
./demo-control.sh drupal start     # Start PHP Drupal
./demo-control.sh laravel start    # Start Laravel PHP

# Stop applications
./demo-control.sh python stop      # Stop Python Flask
./demo-control.sh node stop        # Stop Node.js Express
./demo-control.sh netcore stop     # Stop .NET Core
./demo-control.sh tomcat stop      # Stop Apache Tomcat
./demo-control.sh drupal stop      # Stop PHP Drupal
./demo-control.sh laravel stop     # Stop Laravel PHP

# Restart applications
./demo-control.sh python restart   # Restart Python Flask
./demo-control.sh node restart     # Restart Node.js Express
./demo-control.sh netcore restart  # Restart .NET Core
./demo-control.sh tomcat restart   # Restart Apache Tomcat
./demo-control.sh drupal restart   # Restart PHP Drupal
./demo-control.sh laravel restart  # Restart Laravel PHP

# Check application status
./demo-control.sh python status    # Show Python Flask status
./demo-control.sh node status      # Show Node.js Express status
./demo-control.sh netcore status   # Show .NET Core status  
./demo-control.sh tomcat status    # Show Apache Tomcat status
./demo-control.sh drupal status    # Show PHP Drupal status

# View application logs
./demo-control.sh python logs      # Show Python Flask logs
./demo-control.sh node logs        # Show Node.js Express logs
./demo-control.sh netcore logs     # Show .NET Core logs
./demo-control.sh tomcat logs      # Show Apache Tomcat logs
./demo-control.sh drupal logs      # Show PHP Drupal logs
```

### Bulk Operations

```bash
# Manage all applications at once
./demo-control.sh all start        # Start all applications
./demo-control.sh all stop         # Stop all applications  
./demo-control.sh all restart      # Restart all applications
./demo-control.sh all status       # Show status of all applications
```

### Advanced Features

#### Debug Mode
Enable detailed detection information for troubleshooting:

```bash
DEBUG=1 ./demo-control.sh python status
```

#### Status Detection
The script uses multiple methods to accurately detect application status:
- Process detection by PID and command pattern
- Port usage detection via `lsof` and `netstat`
- PID file validation
- Comprehensive error reporting

#### Smart Process Management
- **Node.js**: Handles both npm and node processes
- **Tomcat**: Waits for proper startup, uses Tomcat shutdown scripts
- **Python/NET**: Standard process and port-based management
- **Force Kill**: Automatic fallback to force termination if graceful shutdown fails

## 🔒 Security Vulnerabilities (Intentional)

Each application includes the following intentional vulnerabilities for testing:

### 1. Path Traversal
Test with payloads like:
- `../../../etc/passwd`
- `..\..\..\..\windows\system32\drivers\etc\hosts`

### 2. Command Injection  
Test with payloads like:
- `whoami`
- `ls -la`
- `cat /etc/passwd`

### 3. Network Command Injection (via ping)
Test with payloads like:
- `localhost; whoami`
- `127.0.0.1 && cat /etc/passwd`
- `google.com | ls -la`

## 🛡 Contrast Integration

# Contrast Flex Agent CMDS

```
Usage: contrast-flex [OPTIONS] <COMMAND>

Commands:
  app             List discovered application details
  app-agent       Set agent versions pinned to specific applications
  monitor         Monitors running applications
  auto-attach     Display or set the auto-attach status
  attach          Display or set agent attachment at app level
  agents          Display installed agents
  agent-injector  Agent Injector status
  config          Get configuration settings
  help            Print this message or the help of the given subcommand(s)

Options:
  -v, --verbose  Print verbose logs to standard out
  -h, --help     Print help (see more with '--help')
  -V, --version  Print version
  ```


### Agent Token Configuration

The demo supports multiple ways to configure your Contrast agent token:

```bash
# Method 1: Command line argument
./run-demo.sh <your-base64-token>

# Method 2: Environment variable
export CONTRAST_AGENT_TOKEN=<your-base64-token>
./run-demo.sh

# Method 3: Default token (for demo purposes)
./run-demo.sh  # Uses built-in demo token
```


## 🔧 Advanced Usage

### Manual Container Management

```bash
# Build the container manually
docker build -t demo-ubuntu ./DEMO

# Run with custom configuration
docker run -d \
    --name demo-flex-agent \
    -v "$(pwd)/DEMO:/demos" \
    -p 8080:8080 -p 8181:8181 -p 9090:9090 -p 3030:3030 \
    demo-ubuntu

# Connect to running container
docker exec -it demo-flex-agent /bin/bash
```

### File Permissions
The Dockerfile automatically sets executable permissions on all scripts, ensuring they work regardless of your host OS. No manual `chmod +x` required!

### Logs and Debugging
Application logs are stored in `/tmp/` within the container:
- `/tmp/python-demo.log`
- `/tmp/node-demo.log` 
- `/tmp/netcore-demo.log`
- `/tmp/tomcat-demo.log`

## 📁 Project Structure

```
contrast-flex-agent/
├── README.md                    # This comprehensive guide
├── run-demo.sh                 # Main startup script
├── DEMO-SUMMARY.md            # Quick reference guide
└── DEMO/                      # Demo applications directory
    ├── Dockerfile             # Multi-language container setup
    ├── demo-control.sh        # Unified application management script
    ├── python-app/           # Flask application
    ├── node-app/             # Express.js application  
    ├── dotnet-app/           # ASP.NET Core application
    └── apache-tomcat-9.0.95/ # Apache Tomcat application
```

## 🎯 Demo Workflow

### 1. Initial Setup
```bash
git clone <repository-url>
cd contrast-flex-agent
./run-demo.sh [your-agent-token]
```

### 2. Start Applications
```bash
# Inside the container
./demo-control.sh all start
```

### 3. Test Vulnerabilities
Visit each application and test the intentional vulnerabilities:
- http://localhost:9090 (Python Flask)
- http://localhost:3030 (Node.js Express)
- http://localhost:8181 (.NET Core)  
- http://localhost:8080 (Apache Tomcat)

### 4. Monitor in Contrast TeamServer
Watch real-time vulnerability detection and attack monitoring in your Contrast dashboard.

### 5. Demonstrate Protection
Show how Contrast can block attacks in real-time.

## 🛟 Troubleshooting

### Common Issues

**Container won't start:**
```bash
# Check Docker is running
docker --version

# Remove any existing container
docker rm -f demo-flex-agent
./run-demo.sh
```

**Applications won't start:**
```bash
# Check status with debug info
DEBUG=1 ./demo-control.sh <app> status

# View logs for errors
./demo-control.sh <app> logs

# Restart with fresh state
./demo-control.sh <app> stop
./demo-control.sh <app> start
```

**Port conflicts:**
```bash
# Check what's using ports
lsof -i :8080
lsof -i :8181  
lsof -i :9090
lsof -i :3030

# Stop conflicting services or change ports in run-demo.sh
```

**Script permissions on Windows/WSL:**
The Dockerfile automatically handles this, but if needed:
```bash
chmod +x run-demo.sh
chmod +x DEMO/demo-control.sh
```

### Debug Mode
For detailed troubleshooting information:
```bash
DEBUG=1 ./demo-control.sh all status
```
