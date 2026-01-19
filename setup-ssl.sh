#!/bin/bash

# SSL Certificate Setup Script for MPWA
# Supports self-signed certificates and Let's Encrypt

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_header() {
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}================================================${NC}"
}

# Create SSL directory
mkdir -p docker/ssl

print_header "SSL Certificate Setup"
echo ""
echo "Choose SSL certificate type:"
echo "  1) Self-signed certificate (for development/testing)"
echo "  2) Let's Encrypt certificate (for production)"
echo "  3) Use existing certificate files"
echo ""
read -p "Enter your choice (1-3): " choice

case $choice in
    1)
        print_header "Generating Self-Signed Certificate"
        
        read -p "Enter domain name (e.g., localhost or your-domain.com): " domain
        
        print_info "Generating private key..."
        openssl genrsa -out docker/ssl/key.pem 2048
        
        print_info "Generating certificate..."
        openssl req -new -x509 -key docker/ssl/key.pem -out docker/ssl/cert.pem -days 365 \
            -subj "/C=US/ST=State/L=City/O=Organization/CN=${domain}"
        
        print_success "Self-signed certificate generated successfully!"
        print_warning "Note: Self-signed certificates will show security warnings in browsers"
        ;;
        
    2)
        print_header "Setting Up Let's Encrypt Certificate"
        
        read -p "Enter your domain name: " domain
        read -p "Enter your email address: " email
        
        print_info "Installing certbot..."
        if ! command -v certbot &> /dev/null; then
            if [[ "$OSTYPE" == "linux-gnu"* ]]; then
                sudo apt-get update
                sudo apt-get install -y certbot
            elif [[ "$OSTYPE" == "darwin"* ]]; then
                brew install certbot
            else
                print_error "Please install certbot manually"
                exit 1
            fi
        fi
        
        print_info "Obtaining certificate from Let's Encrypt..."
        print_warning "Make sure your domain points to this server's IP address"
        print_warning "Port 80 must be accessible from the internet"
        
        read -p "Press Enter to continue or Ctrl+C to cancel..."
        
        sudo certbot certonly --standalone -d ${domain} --email ${email} --agree-tos --non-interactive
        
        print_info "Copying certificates..."
        sudo cp /etc/letsencrypt/live/${domain}/fullchain.pem docker/ssl/cert.pem
        sudo cp /etc/letsencrypt/live/${domain}/privkey.pem docker/ssl/key.pem
        sudo chown $(whoami):$(whoami) docker/ssl/*.pem
        
        print_success "Let's Encrypt certificate installed successfully!"
        
        # Setup auto-renewal
        print_info "Setting up auto-renewal..."
        (crontab -l 2>/dev/null; echo "0 0 * * 0 certbot renew --quiet && cp /etc/letsencrypt/live/${domain}/fullchain.pem $(pwd)/docker/ssl/cert.pem && cp /etc/letsencrypt/live/${domain}/privkey.pem $(pwd)/docker/ssl/key.pem && docker-compose restart nginx-proxy") | crontab -
        
        print_success "Auto-renewal configured (runs weekly)"
        ;;
        
    3)
        print_header "Using Existing Certificate"
        
        read -p "Enter path to certificate file (cert.pem): " cert_path
        read -p "Enter path to private key file (key.pem): " key_path
        
        if [ ! -f "$cert_path" ]; then
            print_error "Certificate file not found: $cert_path"
            exit 1
        fi
        
        if [ ! -f "$key_path" ]; then
            print_error "Private key file not found: $key_path"
            exit 1
        fi
        
        print_info "Copying certificate files..."
        cp "$cert_path" docker/ssl/cert.pem
        cp "$key_path" docker/ssl/key.pem
        
        print_success "Certificate files copied successfully!"
        ;;
        
    *)
        print_error "Invalid choice"
        exit 1
        ;;
esac

# Set proper permissions
chmod 600 docker/ssl/key.pem
chmod 644 docker/ssl/cert.pem

echo ""
print_header "SSL Setup Complete"
echo ""
print_info "Certificate location: docker/ssl/cert.pem"
print_info "Private key location: docker/ssl/key.pem"
echo ""
print_success "You can now deploy with SSL using:"
echo -e "  ${BLUE}docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d${NC}"
echo ""
