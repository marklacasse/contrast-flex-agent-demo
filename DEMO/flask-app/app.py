from flask import Flask, render_template, request, jsonify
import datetime
import os
import platform
import subprocess

app = Flask(__name__)

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/api/info')
def system_info():
    info = {
        'timestamp': datetime.datetime.now().isoformat(),
        'platform': platform.platform(),
        'python_version': platform.python_version(),
        'hostname': platform.node(),
        'architecture': platform.architecture()[0],
        'processor': platform.processor() or 'Unknown',
        'environment_vars': {
            'JAVA_HOME': os.environ.get('JAVA_HOME', 'Not set'),
            'PATH': os.environ.get('PATH', '').split(':')[:5],  # Show first 5 PATH entries
            'PYTHONPATH': os.environ.get('PYTHONPATH', 'Not set')
        }
    }
    return jsonify(info)

@app.route('/api/hello/<name>')
def hello_name(name):
    return jsonify({
        'message': f'Hello, {name}!',
        'timestamp': datetime.datetime.now().isoformat(),
        'visitor': name
    })

@app.route('/health')
def health_check():
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.datetime.now().isoformat(),
        'app': 'Flask Demo App'
    })

@app.route('/contrast-demo')
def contrast_demo():
    """Demo page showing environment and application info"""
    return render_template('contrast.html')

# VULNERABLE ENDPOINTS - FOR DEMO/TESTING PURPOSES ONLY
# DO NOT USE IN PRODUCTION

@app.route('/vulnerable/file-read')
def vulnerable_file_read():
    """
    VULNERABLE: Path Traversal vulnerability
    This endpoint allows reading arbitrary files from the filesystem
    """
    filename = request.args.get('filename', 'default.txt')
    
    # VULNERABILITY: No path sanitization - allows ../../../etc/passwd
    try:
        file_path = os.path.join('/demos/flask-app', filename)
        with open(file_path, 'r') as f:
            content = f.read()
        return jsonify({
            'filename': filename,
            'content': content,
            'warning': 'This endpoint has a path traversal vulnerability for demo purposes'
        })
    except Exception as e:
        return jsonify({
            'error': str(e),
            'filename': filename,
            'warning': 'This endpoint has a path traversal vulnerability for demo purposes'
        }), 400

@app.route('/vulnerable/command', methods=['POST'])
def vulnerable_command():
    """
    VULNERABLE: Command injection vulnerability
    This endpoint executes user input directly as shell commands
    """
    command = request.json.get('command', '') if request.is_json else request.form.get('command', '')
    
    if not command:
        return jsonify({
            'error': 'No command provided',
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        }), 400
    
    try:
        # VULNERABILITY: Direct command execution without sanitization
        # This allows injection like: "ls; cat /etc/passwd"
        result = subprocess.run(command, shell=True, capture_output=True, text=True, timeout=10)
        return jsonify({
            'command': command,
            'stdout': result.stdout,
            'stderr': result.stderr,
            'return_code': result.returncode,
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        })
    except subprocess.TimeoutExpired:
        return jsonify({
            'error': 'Command timed out',
            'command': command,
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        }), 408
    except Exception as e:
        return jsonify({
            'error': str(e),
            'command': command,
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        }), 500

@app.route('/vulnerable/ping')
def vulnerable_ping():
    """
    VULNERABLE: Command injection via GET parameter
    This endpoint is vulnerable to command injection through the host parameter
    """
    host = request.args.get('host', 'localhost')
    
    try:
        # VULNERABILITY: String concatenation with user input
        # This allows injection like: "google.com; cat /etc/passwd"
        command = f"ping -c 4 {host}"
        result = subprocess.run(command, shell=True, capture_output=True, text=True, timeout=15)
        
        return jsonify({
            'host': host,
            'command': command,
            'output': result.stdout,
            'error': result.stderr,
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        })
    except Exception as e:
        return jsonify({
            'error': str(e),
            'host': host,
            'warning': 'This endpoint has a command injection vulnerability for demo purposes'
        }), 500

@app.route('/vulnerable/demo')
def vulnerable_demo():
    """Demo page showing the vulnerable endpoints"""
    return render_template('vulnerable.html')

if __name__ == '__main__':
    # Get port from environment variable or default to 9090
    port = int(os.environ.get('PORT', 9090))
    print(f"🚀 Starting Flask app on port {port}")
    print(f"🌐 Access the app at: http://localhost:{port}")
    print(f"📊 Health check: http://localhost:{port}/health")
    print(f"🔧 System info API: http://localhost:{port}/api/info")
    
    app.run(host='0.0.0.0', port=port, debug=True)
