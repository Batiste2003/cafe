# Mobile Dockerfile - React Native Expo
FROM node:22-alpine

# Install basic dependencies
RUN apk add --no-cache git bash

# Install Expo CLI globally
RUN npm install -g expo-cli @expo/ngrok

# Set working directory
WORKDIR /app

# Copy package files first for better caching
COPY package.json package-lock.json ./

# Install dependencies
RUN npm ci

# Copy application code
COPY . .

# Expose Expo ports
# 8081: Metro bundler
# 19000: Expo Dev Server
# 19001: Expo Dev Server (alternate)
EXPOSE 8081 19000 19001

# Set environment variables for Expo
ENV EXPO_DEVTOOLS_LISTEN_ADDRESS=0.0.0.0
ENV REACT_NATIVE_PACKAGER_HOSTNAME=0.0.0.0

# Start Expo development server
CMD ["npx", "expo", "start", "--tunnel"]
