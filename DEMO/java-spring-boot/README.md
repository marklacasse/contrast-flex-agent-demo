# Java Spring Boot Demo Application

This is a Spring Boot web application designed for security testing with Contrast Security. It includes intentionally vulnerable endpoints to demonstrate various security issues.

## Features

- **User Management**: CRUD operations for user data
- **Vulnerable Search**: SQL injection testing endpoint
- **File Operations**: Path traversal vulnerability testing
- **Command Execution**: Command injection testing endpoint
- **System Information**: Application and environment details

## Technologies

- Java 11
- Spring Boot 2.7.18
- Spring Data JPA
- H2 Database (in-memory)
- Thymeleaf templating
- Maven build system

## Building and Running

### Prerequisites

- Java 11 or higher
- Maven 3.6 or higher

### Build WAR file

```bash
./build.sh
```

This will create `target/contrast-demo.war` that can be deployed to Tomcat.

### Deploy to Tomcat

1. Copy `target/contrast-demo.war` to your Tomcat `webapps` directory
2. Start Tomcat
3. Access the application at `http://localhost:8080/contrast-demo`

### Run as Standalone Application

```bash
java -jar target/contrast-demo.war
```

Access the application at `http://localhost:8080`

## Application Structure

```
src/
├── main/
│   ├── java/com/contrast/demo/
│   │   ├── ContrastDemoApplication.java     # Main application class
│   │   ├── controller/
│   │   │   └── DemoController.java          # Web controllers
│   │   ├── model/
│   │   │   └── User.java                    # User entity
│   │   ├── repository/
│   │   │   └── UserRepository.java          # Data access layer
│   │   └── service/
│   │       └── UserService.java             # Business logic
│   └── resources/
│       ├── templates/                       # Thymeleaf templates
│       ├── application.properties           # Configuration
│       └── data.sql                         # Sample data
└── target/
    └── contrast-demo.war                    # Built WAR file
```

## Security Testing Endpoints

⚠️ **Warning**: This application contains intentionally vulnerable code for security testing purposes.

### Vulnerable Endpoints

1. **SQL Injection**: `/search?query=<malicious_sql>`
2. **Path Traversal**: `/file?filename=../../../etc/passwd`
3. **Command Injection**: `/command?cmd=ls; cat /etc/passwd`

### Safe Endpoints

1. **Home Page**: `/`
2. **User Management**: `/users`
3. **System Info**: `/info`

## Contrast Security Integration

This application is designed to be monitored by Contrast Security agents to detect:

- SQL injection vulnerabilities
- Path traversal attacks
- Command injection attempts
- Input validation issues
- Authentication bypasses

## Development

### Database Console

The H2 database console is available at `/h2-console` for debugging:

- **JDBC URL**: `jdbc:h2:mem:testdb`
- **Username**: `sa`
- **Password**: (empty)

### Maven Commands

```bash
# Clean build
mvn clean

# Compile only
mvn compile

# Run tests
mvn test

# Package WAR
mvn package

# Skip tests during build
mvn package -DskipTests
```

## License

This demo application is for educational and testing purposes only.
