# OSINT Tools — Simple & Effective

## Username & Email Search

| Tool | Command | Purpose |
|------|---------|---------|
| **Sherlock** | `sherlock username` | Search 300+ social media for username |
| **holehe** | `holehe email@example.com` | Email/phone lookup across services |
| **phoneinfoga** | `phoneinfoga +8801685419598` | Phone number OSINT |

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
holehe rahat.rab@outlook.com

# Phone number OSINT
phoneinfoga +8801685419598

# DNS recon
dnsrecon -d monash.edu

# Subdomain enumeration
sublist3r -d neogov.com

# Email/subdomain gathering
theHarvester -d monash.edu -l 500 -b google
```

All tools: simple, one-command, effective.
