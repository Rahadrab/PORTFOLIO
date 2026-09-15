# Red Team Tools — Installed & Verified

## Cellular / Mobile Tracking

| Tool | Purpose | Status | Install |
|------|---------|--------|---------|
| **horst** | WiFi monitoring, cell tower scanning | ✅ Working | `apt install horst` |
| **aircrack-ng** | WiFi attacks, packet injection | ✅ Working | Pre-installed |
| **wireshark** | Packet analysis, traffic inspection | ✅ Working | Pre-installed |
| **stingwatch** | IMSI catcher detection | ✅ Cloned | `/home/kali/stingwatch` |
| **mitmproxy** | Intercept location API calls | ✅ Working | Pre-installed |
| **Frida/Objection** | Runtime instrumentation, location hooks | ✅ Working | Pre-installed |

## Exploitation

| Tool | Purpose | Status |
|------|---------|--------|
| **Metasploit (msfconsole)** | Exploitation, post-exploitation, C2 | ✅ Working |
| **BeEF-XSS** | Browser exploitation, hook injection | ✅ Working |

## AI Security

| Tool | Purpose | Status |
|------|---------|--------|
| **Garak** | LLM vulnerability scanning (100+ probes) | ✅ Ready |
| **Ollama secassist-hermes** | Local AI model (8B Llama) | ✅ Running |

## Building

| Tool | Purpose | Status |
|------|---------|--------|
| **srsRAN** | Software-defined radio, cell tower simulation | ⚠️ Building |

## Running Commands

```bash
# Cellular tracking test
sudo horst -i wlan0

# WiFi packet capture
sudo airodump-ng wlan0

# Intercept location API
mitmproxy --mode transparent

# Frida location hook
frida -U -f com.target.app -l location-hook.js

# Run Garak
/home/kali/run-garak.sh

# Metasploit
msfconsole

# BeEF
sudo beef-xss-start
```

## Screenshots
All tests to be documented with screenshots and pushed to GitHub.
