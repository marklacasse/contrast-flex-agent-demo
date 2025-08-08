#!/bin/bash

echo "Building Spring Boot Application for Contrast Security Demo..."

# Check if Maven is installed
if ! command -v mvn &> /dev/null; then
    echo "Error: Maven is not installed. Please install Maven first."
    echo "On macOS: brew install maven"
    echo "On Ubuntu: sudo apt-get install maven"
    exit 1
fi

# Check if Java is installed
if ! command -v java &> /dev/null; then
    echo "Error: Java is not installed. Please install Java 11 or higher."
    echo "On macOS: brew install openjdk@11"
    exit 1
fi

# Display Java version
echo "Using Java version:"
java -version

cd "$(dirname "$0")"

echo "Cleaning previous builds..."
mvn clean

echo "Compiling and packaging Spring Boot application..."
mvn package -DskipTests

if [ $? -eq 0 ]; then
    echo "Build successful!"
    echo "WAR file created: target/contrast-demo.war"
    
    # Deploy to Tomcat webapps directory
    TOMCAT_WEBAPPS="../apache-tomcat-9.0.95/webapps"
    if [ -d "$TOMCAT_WEBAPPS" ]; then
        echo "Deploying to Tomcat..."
        cp target/contrast-demo.war "$TOMCAT_WEBAPPS/"
        echo "WAR file deployed to: $TOMCAT_WEBAPPS/contrast-demo.war"
    else
        echo "Warning: Tomcat webapps directory not found at $TOMCAT_WEBAPPS"
        echo "Please manually copy target/contrast-demo.war to your Tomcat webapps directory"
    fi
    
    echo ""
    echo "Deployment complete!"
    echo "Start Tomcat and access the application at: http://localhost:8080/contrast-demo"
    echo ""
    echo "To run as standalone Spring Boot app:"
    echo "java -jar target/contrast-demo.war"
else
    echo "Build failed!"
    exit 1
fi
