# OSINT Tools — Simple & Effective

## Username & Email Search

| Tool | Command | Purpose |
|------|---------|---------|
| **Sherlock** | `sherlock username` | Search 300+ social media for username |
| **holehe** | `holehe email@example.com` | Email/phone lookup across services |
| **phoneinfoga** | `phoneinfoga +8801XXXXXXXX` | Phone number OSINT |

## Domain & DNS Recon

| Tool | Command | Purpose |
|------|---------|---------|
| **dnsrecon** | `dnsrecon -d example.com` | DNS enumeration |
| **sublist3r** | `sublist3r -d example.com` | Subdomain enumeration |

## Email/Subdomain

| Tool | Command | Purpose |
|------|---------|---------|
| **theHarvester** | `theHarvester -d example.com -l 500 -b google` | Email/subdomain/username enumeration |

## Usage Examples

```bash
# Find all social media accounts for a username
sherlock rahadrab

# Check if email is used anywhere
holehe [REDACTED-EMAIL]

# Phone number OSINT
phoneinfoga +8801XXXXXXXX

# DNS recon
dnsrecon -d example.com

# Subdomain enumeration
sublist3r -d example.com

# Email/subdomain gathering
theHarvester -d example.com -l 500 -b google
```

All tools: simple, one-command, effective.
