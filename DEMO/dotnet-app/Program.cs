using Microsoft.AspNetCore.Mvc;
using System.Diagnostics;
using System.Runtime.InteropServices;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddControllersWithViews();

var app = builder.Build();

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseRouting();
app.UseAuthorization();

// API Routes
app.MapGet("/", () => Results.Redirect("/Home"));

app.MapGet("/api/info", () =>
{
    var info = new
    {
        timestamp = DateTime.UtcNow,
        platform = RuntimeInformation.OSDescription,
        framework = RuntimeInformation.FrameworkDescription,
        architecture = RuntimeInformation.OSArchitecture.ToString(),
        hostname = Environment.MachineName,
        dotnet_version = Environment.Version.ToString(),
        environment_vars = new
        {
            JAVA_HOME = Environment.GetEnvironmentVariable("JAVA_HOME") ?? "Not set",
            PATH = Environment.GetEnvironmentVariable("PATH")?.Split(':').Take(5).ToArray() ?? new string[0],
            DOTNET_ROOT = Environment.GetEnvironmentVariable("DOTNET_ROOT") ?? "Not set"
        }
    };
    return Results.Json(info);
});

app.MapGet("/api/hello/{name}", (string name) =>
{
    var response = new
    {
        message = $"Hello, {name}!",
        timestamp = DateTime.UtcNow,
        visitor = name
    };
    return Results.Json(response);
});

app.MapGet("/health", () =>
{
    var health = new
    {
        status = "healthy",
        timestamp = DateTime.UtcNow,
        app = ".NET Core Demo App"
    };
    return Results.Json(health);
});

// VULNERABLE ENDPOINTS - FOR DEMO/TESTING PURPOSES ONLY
// DO NOT USE IN PRODUCTION

app.MapGet("/vulnerable/file-read", ([FromQuery] string filename = "appsettings.json") =>
{
    // Debug logging
    Console.WriteLine($"[DEBUG] file-read endpoint called with filename: '{filename}' (length: {filename?.Length ?? 0})");
    
    try
    {
        // VULNERABILITY: Path Traversal - No path sanitization
        string filePath;
        
        // If filename starts with /, treat as absolute path
        if (filename.StartsWith("/"))
        {
            filePath = filename;
        }
        else
        {
            // Try to find the file in the dotnet-app directory first, then /demos
            var appFilePath = Path.Combine("/demos/dotnet-app", filename);
            var demosFilePath = Path.Combine("/demos", filename);
            
            if (File.Exists(appFilePath))
            {
                filePath = appFilePath;
            }
            else if (File.Exists(demosFilePath))
            {
                filePath = demosFilePath;
            }
            else
            {
                filePath = appFilePath; // Default to app directory for error reporting
            }
        }
        
        Console.WriteLine($"[DEBUG] Attempting to read file: '{filePath}'");
        var content = System.IO.File.ReadAllText(filePath);
        
        return Results.Json(new
        {
            filename = filename,
            content = content,
            warning = "This endpoint has a path traversal vulnerability for demo purposes"
        });
    }
    catch (Exception ex)
    {
        return Results.BadRequest(new
        {
            error = ex.Message,
            filename = filename,
            warning = "This endpoint has a path traversal vulnerability for demo purposes"
        });
    }
});

app.MapPost("/vulnerable/command", async ([FromBody] CommandRequest request) =>
{
    if (string.IsNullOrEmpty(request.Command))
    {
        return Results.BadRequest(new
        {
            error = "No command provided",
            warning = "This endpoint has a command injection vulnerability for demo purposes"
        });
    }

    try
    {
        // VULNERABILITY: Command Injection - Direct process execution
        var processInfo = new ProcessStartInfo
        {
            FileName = "/bin/bash",
            Arguments = $"-c \"{request.Command}\"",
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        using var process = Process.Start(processInfo);
        if (process != null)
        {
            await process.WaitForExitAsync();
            var stdout = await process.StandardOutput.ReadToEndAsync();
            var stderr = await process.StandardError.ReadToEndAsync();

            return Results.Json(new
            {
                command = request.Command,
                stdout = stdout,
                stderr = stderr,
                return_code = process.ExitCode,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
        else
        {
            return Results.BadRequest(new
            {
                error = "Failed to start process",
                command = request.Command,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
    }
    catch (Exception ex)
    {
        return Results.BadRequest(new
        {
            error = ex.Message,
            command = request.Command,
            warning = "This endpoint has a command injection vulnerability for demo purposes"
        });
    }
});

// GET version of command endpoint for easier testing
app.MapGet("/vulnerable/command", ([FromQuery] string cmd = "") =>
{
    Console.WriteLine($"[DEBUG] command endpoint called with cmd: '{cmd}' (length: {cmd?.Length ?? 0})");
    
    if (string.IsNullOrEmpty(cmd))
    {
        return Results.BadRequest(new
        {
            error = "No command provided in 'cmd' parameter",
            warning = "This endpoint has a command injection vulnerability for demo purposes"
        });
    }

    try
    {
        // VULNERABILITY: Command Injection - Direct process execution
        var processInfo = new ProcessStartInfo
        {
            FileName = "/bin/bash",
            Arguments = $"-c \"{cmd}\"",
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        using var process = Process.Start(processInfo);
        if (process != null)
        {
            process.WaitForExit(10000); // 10 second timeout
            var stdout = process.StandardOutput.ReadToEnd();
            var stderr = process.StandardError.ReadToEnd();

            return Results.Json(new
            {
                command = cmd,
                stdout = stdout,
                stderr = stderr,
                return_code = process.ExitCode,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
        else
        {
            return Results.BadRequest(new
            {
                error = "Failed to start process",
                command = cmd,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
    }
    catch (Exception ex)
    {
        return Results.BadRequest(new
        {
            error = ex.Message,
            command = cmd,
            warning = "This endpoint has a command injection vulnerability for demo purposes"
        });
    }
});

app.MapGet("/vulnerable/ping", ([FromQuery] string host = "localhost") =>
{
    Console.WriteLine($"[DEBUG] ping endpoint called with host: '{host}' (length: {host?.Length ?? 0})");
    
    try
    {
        // VULNERABILITY: Command Injection via string concatenation
        var processInfo = new ProcessStartInfo
        {
            FileName = "/bin/bash",
            Arguments = $"-c \"ping -c 4 {host}\"",
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        using var process = Process.Start(processInfo);
        if (process != null)
        {
            process.WaitForExit(15000); // 15 second timeout
            var output = process.StandardOutput.ReadToEnd();
            var error = process.StandardError.ReadToEnd();

            return Results.Json(new
            {
                host = host,
                command = $"ping -c 4 {host}",
                output = output,
                error = error,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
        else
        {
            return Results.BadRequest(new
            {
                error = "Failed to start ping process",
                host = host,
                warning = "This endpoint has a command injection vulnerability for demo purposes"
            });
        }
    }
    catch (Exception ex)
    {
        return Results.BadRequest(new
        {
            error = ex.Message,
            host = host,
            warning = "This endpoint has a command injection vulnerability for demo purposes"
        });
    }
});

// Additional API routes
app.MapGet("/api/hello", () =>
{
    var response = new
    {
        message = "Hello from .NET Core API!",
        timestamp = DateTime.UtcNow,
        tip = "Add a name to the URL like /api/hello/YourName"
    };
    return Results.Json(response);
});

app.MapGet("/debug/files", () =>
{
    var info = new
    {
        current_directory = Directory.GetCurrentDirectory(),
        views_exist = Directory.Exists("Views"),
        home_view_exists = File.Exists("Views/Home/Index.cshtml"),
        appsettings_exists = File.Exists("appsettings.json"),
        demo_config_exists = File.Exists("demo-config.txt"),
        files_in_current_dir = Directory.GetFiles(".", "*", SearchOption.TopDirectoryOnly).Take(10).ToArray()
    };
    return Results.Json(info);
});

app.MapGet("/contrast", () =>
{
    var contrastInfo = new
    {
        agent = "Contrast .NET Core Agent",
        version = "4.5.0.0",
        mode = new string[] { "Assess", "Inventory" },
        status = "Active",
        teamserver = Environment.GetEnvironmentVariable("CONTRAST_URL") ?? "Not configured",
        features = new string[]
        {
            "Real-time vulnerability detection",
            "Runtime application security monitoring", 
            "Code-level security insights",
            "Attack detection and blocking"
        },
        documentation = "https://docs.contrastsecurity.com"
    };
    return Results.Json(contrastInfo);
});

// Controller routes
app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}");

// Get port from environment variable or default to 8181
var port = Environment.GetEnvironmentVariable("PORT") ?? "8181";
Console.WriteLine($"🚀 Starting .NET Core app on port {port}");
Console.WriteLine($"🌐 Access the app at: http://localhost:{port}");
Console.WriteLine($"📊 Health check: http://localhost:{port}/health");
Console.WriteLine($"🔧 System info API: http://localhost:{port}/api/info");
Console.WriteLine($"⚠️ Vulnerability testing available on home page");
Console.WriteLine($"🔄 View updated at: {DateTime.Now}");

app.Run($"http://0.0.0.0:{port}");

// Request models
public record CommandRequest(string Command);
