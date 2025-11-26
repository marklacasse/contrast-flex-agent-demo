<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class VulnerableController extends Controller
{
    private function getCommonStyles()
    {
        return "
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .navbar {
                display: flex;
                gap: 15px;
                padding: 15px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 10px;
                margin-bottom: 30px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                align-items: center;
            }
            .navbar a {
                color: #667eea;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 5px;
                transition: background 0.3s;
            }
            .navbar a:hover {
                background: #667eea;
                color: white;
            }
            .navbar .admin-link {
                margin-left: auto;
                background: #28a745;
                color: white;
            }
            .navbar .admin-link:hover {
                background: #218838;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            h1 {
                color: #667eea;
                border-bottom: 3px solid #764ba2;
                padding-bottom: 10px;
            }
            h2 {
                color: #555;
                margin-top: 30px;
            }
            .form-group {
                margin: 20px 0;
            }
            input[type='text'], textarea {
                width: 100%;
                padding: 10px;
                border: 2px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }
            input[type='file'] {
                padding: 10px;
                border: 2px solid #ddd;
                border-radius: 5px;
            }
            button {
                background: #667eea;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-right: 10px;
            }
            button:hover {
                background: #764ba2;
            }
            .result-box {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                border-radius: 5px;
            }
            .exploit-box {
                margin: 20px 0;
                padding: 15px;
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                border-radius: 5px;
            }
            .exploit-box h3 {
                color: #856404;
                margin-top: 0;
            }
            .exploit-link {
                display: inline-block;
                margin: 5px 10px 5px 0;
                padding: 8px 15px;
                background: #ffc107;
                color: #000;
                text-decoration: none;
                border-radius: 5px;
                font-size: 14px;
            }
            .exploit-link:hover {
                background: #e0a800;
            }
            .card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin-top: 30px;
            }
            .card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 30px;
                border-radius: 10px;
                color: white;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                transition: transform 0.3s;
            }
            .card:hover {
                transform: translateY(-5px);
            }
            .card h2 {
                color: white;
                margin-top: 0;
            }
            .card a {
                color: white;
                text-decoration: none;
                border: 2px solid white;
                padding: 10px 20px;
                border-radius: 5px;
                display: inline-block;
                margin-top: 15px;
                transition: all 0.3s;
            }
            .card a:hover {
                background: white;
                color: #667eea;
            }
        </style>
        ";
    }

    private function getNavBar()
    {
        return "
        <div class='navbar'>
            <a href='" . url('/') . "'>Home</a>
            <a href='" . url('/sql-injection') . "'>SQL Injection</a>
            <a href='" . url('/xss') . "'>XSS</a>
            <a href='" . url('/file-upload') . "'>File Upload</a>
            <a href='" . url('/command-injection') . "'>Command Injection</a>
            <a href='" . url('/path-traversal') . "'>Path Traversal</a>
            <a href='" . url('/info') . "'>System Info</a>
        </div>
        ";
    }

    public function home()
    {
        $html = "<!DOCTYPE html><html><head><title>Contrast Security Demo - Laravel</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>🛡️ Contrast Security Vulnerable Application - Laravel Edition</h1>";
        $html .= "<p>This application demonstrates common web vulnerabilities for testing and demonstration purposes.</p>";
        $html .= "<div class='card-grid'>";
        
        $html .= "<div class='card'><h2>💉 SQL Injection</h2>";
        $html .= "<p>Test SQL injection vulnerabilities with unsanitized queries.</p>";
        $html .= "<a href='" . url('/sql-injection') . "'>Try SQL Injection →</a></div>";
        
        $html .= "<div class='card'><h2>🔓 Cross-Site Scripting (XSS)</h2>";
        $html .= "<p>Execute JavaScript through unescaped user input.</p>";
        $html .= "<a href='" . url('/xss') . "'>Try XSS →</a></div>";
        
        $html .= "<div class='card'><h2>📁 Unrestricted File Upload</h2>";
        $html .= "<p>Upload files without validation or restrictions.</p>";
        $html .= "<a href='" . url('/file-upload') . "'>Try File Upload →</a></div>";
        
        $html .= "<div class='card'><h2>⚡ Command Injection</h2>";
        $html .= "<p>Execute system commands through unsanitized input.</p>";
        $html .= "<a href='" . url('/command-injection') . "'>Try Command Injection →</a></div>";
        
        $html .= "<div class='card'><h2>📂 Path Traversal</h2>";
        $html .= "<p>Access files outside the intended directory.</p>";
        $html .= "<a href='" . url('/path-traversal') . "'>Try Path Traversal →</a></div>";
        
        $html .= "<div class='card'><h2>ℹ️ System Information</h2>";
        $html .= "<p>View server configuration and environment details.</p>";
        $html .= "<a href='" . url('/info') . "'>View Info →</a></div>";
        
        $html .= "</div></div></body></html>";
        
        return $html;
    }

    public function sqlInjection(Request $request)
    {
        $html = "<!DOCTYPE html><html><head><title>SQL Injection Test</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>💉 SQL Injection Vulnerability</h1>";
        $html .= "<p>This endpoint is vulnerable to SQL injection attacks.</p>";
        
        $html .= "<div class='exploit-box'>";
        $html .= "<h3>Quick Exploits:</h3>";
        $html .= "<a class='exploit-link' href='/sql-injection?username=admin'>Normal Query</a>";
        $html .= "<a class='exploit-link' href='/sql-injection?username=" . urlencode("admin' OR '1'='1") . "'>OR 1=1</a>";
        $html .= "<a class='exploit-link' href='/sql-injection?username=" . urlencode("admin'--") . "'>Comment Out</a>";
        $html .= "<a class='exploit-link' href='/sql-injection?username=" . urlencode("admin' UNION SELECT 1,2,3--") . "'>UNION SELECT</a>";
        $html .= "</div>";
        
        $html .= "<form method='GET'>";
        $html .= "<div class='form-group'>";
        $html .= "<label>Username:</label>";
        $html .= "<input type='text' name='username' value='" . htmlspecialchars($request->input('username', '')) . "'>";
        $html .= "</div>";
        $html .= "<button type='submit'>Search User</button>";
        $html .= "</form>";
        
        if ($request->has('username')) {
            $username = $request->input('username');
            try {
                // VULNERABLE: Direct string concatenation in SQL query
                $query = "SELECT * FROM users WHERE username = '" . $username . "'";
                
                // Create users table if it doesn't exist
                DB::statement("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, email TEXT, role TEXT)");
                
                // Insert sample data if table is empty
                $count = DB::select("SELECT COUNT(*) as count FROM users")[0]->count;
                if ($count == 0) {
                    DB::insert("INSERT INTO users (username, email, role) VALUES (?, ?, ?)", ['admin', 'admin@example.com', 'administrator']);
                    DB::insert("INSERT INTO users (username, email, role) VALUES (?, ?, ?)", ['user', 'user@example.com', 'user']);
                    DB::insert("INSERT INTO users (username, email, role) VALUES (?, ?, ?)", ['guest', 'guest@example.com', 'guest']);
                }
                
                $results = DB::select($query);
                
                $html .= "<div class='result-box'>";
                $html .= "<h3>Query Executed:</h3>";
                $html .= "<code>" . htmlspecialchars($query) . "</code>";
                $html .= "<h3>Results:</h3>";
                if (count($results) > 0) {
                    $html .= "<table border='1' style='width:100%; border-collapse: collapse; margin-top: 10px;'>";
                    $html .= "<tr>";
                    foreach ((array)$results[0] as $key => $value) {
                        $html .= "<th style='padding: 8px; background: #667eea; color: white;'>" . htmlspecialchars($key) . "</th>";
                    }
                    $html .= "</tr>";
                    foreach ($results as $row) {
                        $html .= "<tr>";
                        foreach ((array)$row as $value) {
                            $html .= "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value) . "</td>";
                        }
                        $html .= "</tr>";
                    }
                    $html .= "</table>";
                } else {
                    $html .= "<p>No results found.</p>";
                }
                $html .= "</div>";
            } catch (\Exception $e) {
                $html .= "<div class='result-box'>";
                $html .= "<h3>Error:</h3>";
                $html .= "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
                $html .= "</div>";
            }
        }
        
        $html .= "</div></body></html>";
        return $html;
    }

    public function xssVulnerable(Request $request)
    {
        $html = "<!DOCTYPE html><html><head><title>XSS Test</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>🔓 Cross-Site Scripting (XSS) Vulnerability</h1>";
        $html .= "<p>This endpoint does not sanitize user input.</p>";
        
        $html .= "<div class='exploit-box'>";
        $html .= "<h3>Quick Exploits:</h3>";
        $html .= "<a class='exploit-link' href='" . url('/xss?comment=<script>alert(\"XSS\")</script>') . "'>Script Alert</a>";
        $html .= "<a class='exploit-link' href='" . url('/xss?comment=<img src=x onerror=alert(\"XSS\")>') . "'>IMG onerror</a>";
        $html .= "<a class='exploit-link' href='" . url('/xss?comment=<svg/onload=alert(\"XSS\")>') . "'>SVG onload</a>";
        $html .= "</div>";
        
        $html .= "<form method='GET'>";
        $html .= "<div class='form-group'>";
        $html .= "<label>Comment:</label>";
        $html .= "<textarea name='comment' rows='4'>" . htmlspecialchars($request->input('comment', '')) . "</textarea>";
        $html .= "</div>";
        $html .= "<button type='submit'>Post Comment</button>";
        $html .= "</form>";
        
        if ($request->has('comment')) {
            $comment = $request->input('comment');
            // VULNERABLE: Not escaping user input
            $html .= "<div class='result-box'>";
            $html .= "<h3>Your Comment:</h3>";
            $html .= "<div>" . $comment . "</div>";
            $html .= "</div>";
        }
        
        $html .= "</div></body></html>";
        return $html;
    }

    public function fileUpload(Request $request)
    {
        $html = "<!DOCTYPE html><html><head><title>File Upload Test</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>📁 Unrestricted File Upload Vulnerability</h1>";
        $html .= "<p>This endpoint accepts any file without validation.</p>";
        
        $html .= "<form method='POST' enctype='multipart/form-data'>";
        $html .= csrf_field();
        $html .= "<div class='form-group'>";
        $html .= "<label>Choose File:</label>";
        $html .= "<input type='file' name='uploaded_file'>";
        $html .= "</div>";
        $html .= "<button type='submit'>Upload File</button>";
        $html .= "</form>";
        
        if ($request->hasFile('uploaded_file')) {
            $file = $request->file('uploaded_file');
            // VULNERABLE: Using original filename without validation
            $originalName = $file->getClientOriginalName();
            $uploadPath = storage_path('app/uploads');
            
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $originalName);
            
            $html .= "<div class='result-box'>";
            $html .= "<h3>File Uploaded Successfully!</h3>";
            $html .= "<p><strong>Filename:</strong> " . htmlspecialchars($originalName) . "</p>";
            $html .= "<p><strong>Size:</strong> " . $file->getSize() . " bytes</p>";
            $html .= "<p><strong>Location:</strong> " . htmlspecialchars($uploadPath . '/' . $originalName) . "</p>";
            $html .= "</div>";
        }
        
        $html .= "</div></body></html>";
        return $html;
    }

    public function commandInjection(Request $request)
    {
        $html = "<!DOCTYPE html><html><head><title>Command Injection Test</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>⚡ Command Injection Vulnerability</h1>";
        $html .= "<p>This endpoint executes system commands with user input.</p>";
        
        $html .= "<div class='exploit-box'>";
        $html .= "<h3>Quick Exploits:</h3>";
        $html .= "<a class='exploit-link' href='" . url('/command-injection?host=127.0.0.1') . "'>Normal Ping</a>";
        $html .= "<a class='exploit-link' href='" . url('/command-injection?host=127.0.0.1; whoami') . "'>Semicolon</a>";
        $html .= "<a class='exploit-link' href='" . url('/command-injection?host=127.0.0.1 %26%26 id') . "'>AND (&&)</a>";
        $html .= "<a class='exploit-link' href='" . url('/command-injection?host=127.0.0.1 | cat /etc/passwd') . "'>Pipe</a>";
        $html .= "</div>";
        
        $html .= "<form method='GET'>";
        $html .= "<div class='form-group'>";
        $html .= "<label>Host to Ping:</label>";
        $html .= "<input type='text' name='host' value='" . htmlspecialchars($request->input('host', '')) . "'>";
        $html .= "</div>";
        $html .= "<button type='submit'>Ping Host</button>";
        $html .= "</form>";
        
        if ($request->has('host')) {
            $host = $request->input('host');
            // VULNERABLE: Direct command execution without sanitization
            $command = "ping -c 3 " . $host;
            $output = shell_exec($command . " 2>&1");
            
            $html .= "<div class='result-box'>";
            $html .= "<h3>Command Executed:</h3>";
            $html .= "<code>" . htmlspecialchars($command) . "</code>";
            $html .= "<h3>Output:</h3>";
            $html .= "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto;'>" . htmlspecialchars($output) . "</pre>";
            $html .= "</div>";
        }
        
        $html .= "</div></body></html>";
        return $html;
    }

    public function pathTraversal(Request $request)
    {
        $html = "<!DOCTYPE html><html><head><title>Path Traversal Test</title>" . 
                $this->getCommonStyles() . 
                "</head><body>";
        $html .= $this->getNavBar();
        $html .= "<div class='container'>";
        $html .= "<h1>📂 Path Traversal Vulnerability</h1>";
        $html .= "<p>This endpoint reads files without path validation.</p>";
        
        $html .= "<div class='exploit-box'>";
        $html .= "<h3>Quick Exploits:</h3>";
        $html .= "<a class='exploit-link' href='" . url('/path-traversal?file=/etc/passwd') . "'>/etc/passwd</a>";
        $html .= "<a class='exploit-link' href='" . url('/path-traversal?file=/etc/hosts') . "'>/etc/hosts</a>";
        $html .= "<a class='exploit-link' href='" . url('/path-traversal?file=/proc/version') . "'>/proc/version</a>";
        $html .= "<a class='exploit-link' href='" . url('/path-traversal?file=/proc/self/environ') . "'>environ</a>";
        $html .= "</div>";
        
        $html .= "<form method='GET'>";
        $html .= "<div class='form-group'>";
        $html .= "<label>File Path:</label>";
        $html .= "<input type='text' name='file' value='" . htmlspecialchars($request->input('file', '')) . "'>";
        $html .= "</div>";
        $html .= "<button type='submit'>Read File</button>";
        $html .= "</form>";
        
        if ($request->has('file')) {
            $file = $request->input('file');
            // VULNERABLE: No path validation
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $html .= "<div class='result-box'>";
                $html .= "<h3>File: " . htmlspecialchars($file) . "</h3>";
                $html .= "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; max-height: 400px;'>" . htmlspecialchars($content) . "</pre>";
                $html .= "</div>";
            } else {
                $html .= "<div class='result-box'>";
                $html .= "<p style='color: red;'>File not found or not accessible: " . htmlspecialchars($file) . "</p>";
                $html .= "</div>";
            }
        }
        
        $html .= "</div></body></html>";
        return $html;
    }

    public function systemInfo()
    {
        $info = [
            'PHP Version' => phpversion(),
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'System' => php_uname(),
            'Laravel Version' => app()->version(),
            'Loaded Extensions' => implode(', ', get_loaded_extensions()),
            'Current Directory' => getcwd(),
            'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        ];
        
        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
    }
}
