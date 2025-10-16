# Profile API - Backend Wizards Stage 0

A simple Laravel API that returns profile information with dynamic cat facts.

## Features

- GET `/me` endpoint returning profile data
- Dynamic cat fact integration from Cat Facts API
- ISO 8601 timestamp format
- Error handling with fallback cat fact
- Basic logging for debugging
- Rate limiting (60 requests per minute)

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- Composer
- Laravel 11

### Installation

1. Clone the repository:
```bash
git clone https://github.com/tulbadex/hng13-profile-api-stage-0.git
cd profile-api
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoint

### GET /me

Returns profile information with a dynamic cat fact.

**Response Format:**
```json
{
  "status": "success",
  "user": {
    "email": "tulbadex@gmail.com",
    "name": "Ibrahim Adedayo",
    "stack": "Laravel/PHP"
  },
  "timestamp": "2025-01-15T12:34:56.789Z",
  "fact": "Random cat fact from Cat Facts API"
}
```

**Features:**
- Dynamic timestamp (current UTC time in ISO 8601 format)
- Fresh cat fact on every request
- Fallback cat fact if external API fails
- 5-second timeout for external API calls
- Request logging for debugging
- Rate limiting protection

## Testing

Test the endpoint:
```bash
curl http://localhost:8000/me
# Or test live deployment:
curl http://3.88.114.190/me
```

## Dependencies

- Laravel Framework 12.x
- Guzzle HTTP Client (included with Laravel)
- Carbon (for timestamp formatting)

## Environment Variables

No additional environment variables required for basic functionality.

## AWS EC2 Deployment Guide

### Step 1: Create EC2 Instance

1. **AWS Console** → EC2 → Launch Instance
2. **Choose**: Ubuntu Server 22.04 LTS
3. **Instance Type**: t2.micro (free tier)
4. **Key Pair**: Create new or use existing
5. **Security Group**: Allow HTTP (80), HTTPS (443), SSH (22)
6. **Launch Instance**

### Step 2: Connect to EC2

```bash
ssh -i your-key.pem ubuntu@your-ec2-ip
```

### Step 3: Install Server Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Add PHP repository
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP, Nginx, Composer
sudo apt install nginx php8.1-fpm php8.1-cli php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip unzip git -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 4: Setup Application

```bash
# Create web directory
sudo mkdir -p /var/www
sudo chown ubuntu:ubuntu /var/www

# Clone your repository
cd /var/www
git clone https://github.com/tulbadex/hng13-profile-api-stage-0.git profile-api
cd profile-api

# Install dependencies
composer install --no-dev --optimize-autoloader

# Setup environment
cp .env.example .env
php artisan key:generate

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 5: Configure Nginx

```bash
# Copy nginx config
sudo cp nginx.conf /etc/nginx/sites-available/profile-api

# Enable site
sudo ln -s /etc/nginx/sites-available/profile-api /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Test and restart nginx
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### Step 6: Setup SSH Key for GitHub Actions

**On your EC2 server:**
```bash
# Generate SSH key for GitHub Actions
ssh-keygen -t rsa -b 4096 -f ~/.ssh/github_actions -N ""

# Add public key to authorized_keys
cat ~/.ssh/github_actions.pub >> ~/.ssh/authorized_keys

# Display private key (copy this for GitHub secrets)
cat ~/.ssh/github_actions
```

**In GitHub Repository:**
1. **Go to**: Settings → Secrets and Variables → Actions
2. **Add secrets**:
   - `EC2_HOST`: `3.88.114.190`
   - `EC2_USER`: `ubuntu`
   - `EC2_SSH_KEY`: Paste the private key content from above

**Make deploy script executable:**
```bash
chmod +x deploy.sh
```

**Setup GitHub SSH Key (Optional - for Git operations):**
```bash
# Generate SSH key for GitHub
ssh-keygen -t ed25519 -C "ubuntu@3.88.114.190" -f ~/.ssh/github_key

# Add public key to GitHub (Settings → SSH and GPG keys)
cat ~/.ssh/github_key.pub

# Configure SSH for GitHub
echo "Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_key
    IdentitiesOnly yes" >> ~/.ssh/config

chmod 600 ~/.ssh/config

# Test GitHub connection
ssh -T git@github.com
```

**Test GitHub Actions SSH Connection:**
```bash
# Test if GitHub Actions can connect to your server
ssh -i ~/.ssh/github_actions ubuntu@3.88.114.190 "echo 'GitHub Actions connection successful'"
```

### Step 7: Test Deployment

1. **Push to main branch**
2. **Check GitHub Actions** tab for deployment status
3. **Visit**: `http://3.88.114.190/me`

### Troubleshooting

```bash
# Check nginx status
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.1-fpm

# Check nginx logs
sudo tail -f /var/log/nginx/error.log

# Check Laravel logs
tail -f /var/www/profile-api/storage/logs/laravel.log
```