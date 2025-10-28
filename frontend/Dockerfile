FROM node:20-alpine

# Create app directory
WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install

# Copy application source
COPY . .
# Include environment configuration
COPY .env .env

EXPOSE 3000
CMD ["npm", "run", "dev"]
