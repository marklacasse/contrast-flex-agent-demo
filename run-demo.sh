#!/bin/bash

# Script to build and run the demo Docker container

set -e  # Exit on any error

CONTAINER_NAME="demo-flex-agent"
IMAGE_NAME="demo-ubuntu"
DEMO_DIR="./DEMO"

# Load personal configuration if available
if [ -f "config.local" ]; then
    echo "📝 Loading personal configuration from config.local"
    source config.local
fi

# Function to show usage
show_usage() {
    echo "Usage: $0 [AGENT_TOKEN]"
    echo ""
    echo "Setup Instructions:"
    echo "  1. Copy config template: cp config.template config.local"
    echo "  2. Edit config.local with your Contrast agent token"
    echo "  3. Run this script"
    echo ""
    echo "Options:"
    echo "  AGENT_TOKEN    Base64 encoded Contrast agent token (optional)"
    echo ""
    echo "Environment Variables:"
    echo "  CONTRAST_AGENT_TOKEN    Alternative way to provide the agent token"
    echo ""
    echo "Examples:"
    echo "  $0                                    # Use token from config.local"
    echo "  $0 <base64-token>                     # Use provided token"
    echo "  CONTRAST_AGENT_TOKEN=<token> $0       # Use environment variable"
    echo ""
    echo "The script will:"
    echo "  1. Build Docker image with Contrast agent"
    echo "  2. Start container with port forwarding"
    echo "  3. Open TeamServer URL in VS Code Simple Browser"
    echo "  4. Connect to container shell"
    exit 1
}

# Handle help option
if [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    show_usage
fi

# Determine which agent token to use
AGENT_TOKEN=""
if [ -n "$1" ]; then
    AGENT_TOKEN="$1"
    echo "📝 Using agent token from command line argument"
elif [ -n "$CONTRAST_AGENT_TOKEN" ]; then
    AGENT_TOKEN="$CONTRAST_AGENT_TOKEN"
    echo "📝 Using agent token from environment variable CONTRAST_AGENT_TOKEN"
else
    echo "❌ No Contrast agent token provided!"
    echo ""
    echo "To get started:"
    echo "  1. Copy the config template: cp config.template config.local"
    echo "  2. Edit config.local and add your Contrast agent token"
    echo "  3. Run this script again"
    echo ""
    echo "Get your token from: Contrast TeamServer -> Organization Settings -> Agent"
    echo ""
    exit 1
fi


echo "🐳 Building Docker image: $IMAGE_NAME"
cd "$DEMO_DIR"
if [ -n "$AGENT_TOKEN" ]; then
    echo "🔧 Building with Contrast agent token..."
    docker build --build-arg AGENT_TOKEN="$AGENT_TOKEN" -t "$IMAGE_NAME" .
else
    echo "🔧 Building without Contrast agent..."
    docker build -t "$IMAGE_NAME" .
fi
cd ..

echo "🧹 Cleaning up any existing container with name: $CONTAINER_NAME"
docker stop "$CONTAINER_NAME" 2>/dev/null || true
docker rm "$CONTAINER_NAME" 2>/dev/null || true

echo "🚀 Starting container in background with volume mount and port forwarding"
docker run -d \
    --name "$CONTAINER_NAME" \
    -v "$(pwd)/$DEMO_DIR:/demos" \
    -p 8080:8080 \
    -p 8181:8181 \
    -p 9090:9090 \
    -p 3030:3030 \
    "$IMAGE_NAME"

echo "✅ Container started successfully!"
echo "📁 Local $DEMO_DIR folder is mounted to /demos in the container"
echo "🌐 Port forwarding enabled:"
echo "   - http://localhost:8080 -> container:8080 (Apache Tomcat)"
echo "   - http://localhost:8181 -> container:8181 (.NET Core)" 
echo "   - http://localhost:9090 -> container:9090 (Python Flask)"
echo "   - http://localhost:3030 -> container:3030 (Node.js Express)"
echo "🔗 Connecting to container shell..."

# Shell into the container
docker exec -it "$CONTAINER_NAME" /bin/bash
