# Frontend Dockerfile - Vue.js with Vite
FROM node:22-alpine

# Install basic dependencies
RUN apk add --no-cache git

# Set working directory
WORKDIR /app

# Copy package files first for better caching
COPY package.json package-lock.json ./

# Install dependencies
RUN npm ci

# Copy application code
COPY . .

# Expose Vite dev server port
EXPOSE 5173

# Start Vite development server
# --host 0.0.0.0 allows access from outside the container
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
