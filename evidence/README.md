# Evidence — Red Team Tool Testing

## Cell Tower Location Tracking (PRECISE)

### Tool: findcellid.com + CellMapper + OpenCellID/Google Maps Geolocation

**Purpose:** Track approximate device location via cell tower triangulation
**Precision:** Uses MCC/MNC/LAC/CellID for specific tower lookup (not general area map)

### Workflow
1. Identify carrier from phone number → MCC/MNC
2. Get specific Cell ID, LAC from phone
3. Query OpenCellID/Google Maps Geolocation API → precise lat/lon + accuracy radius
4. Triangulation via multiple towers → precise approximate location

### Bangladesh Carrier Mapping
| Carrier | MCC | MNC | Example Prefix |
|---------|-----|-----|---------------|
| Grameenphone (GP) | 470 | 03 | 013-018 |
| Banglalink | 470 | 02 | 012, 019 |
| **Robi** | **470** | **01** | **016, 018** |

### User Phone Tested
- **Number:** +8801685419598
- **Carrier:** Robi
- **MCC:** 470, **MNC:** 01
- **Approximate Location:** Dhaka, Bangladesh (23.81°N, 90.41°E)

### Screenshots (Precise Tool Testing)
| Screenshot | Description |
|------------|-------------|
| `findcellid-robi-search.png` | findcellid.com with Robi carrier info |
| `cellmapper-bangladesh-banglalink.png` | CellMapper Bangladesh map |
| `cellmapper-net-overview.png` | CellMapper overview |
| `opencellid-bangladesh.png` | OpenCellID attempt (403) |

### Google Maps Geolocation API
- **Most precise approach:** Takes MCC/MNC/LAC/CellID → returns lat/lon + accuracy
- **Endpoint:** `POST https://www.googleapis.com/geolocation/v1/geolocate`
- **Requires:** API key (free tier: $200/month)
- **Response:** `{location: {lat, lng}, accuracy: meters}`

### OpenCellID API
- **Precise cell tower lookup:** GET `/cell/get?key=...&mcc=...&mnc=...&lac=...&cellid=...`
- **Returns:** lat, lon, accuracy, range
- **Requires:** API key (free tier available)
- **Status:** 403 (blocked without key)

### Red Team Use Case
In authorized engagements, cell tower triangulation proves:
- Physical location of target device
- Movement patterns between towers
- Device presence at specific locations
- Coverage area analysis

## OpenCellID Registration (FREE API Key)

**URL:** https://my.opencellid.org/register
**Status:** Registration submitted ✅
**Email:** rahat.rab@outlook.com
**Name:** Rahad Rabbani
**Purpose:** Get free API key for precise cell tower geolocation

### After Registration:
1. Check email for confirmation → activate account
2. Get API key from dashboard → https://my.opencellid.org/api
3. Use API: `GET /cell/get?key=YOUR_KEY&mcc=470&mnc=1&lac=10250&cellid=26511&format=json`
4. Returns: `{lat, lon, accuracy, range, samples}` — **PRECISE location**

### Free Alternatives for Precise Location:
| Service | Cost | Precision | How |
|---------|------|-----------|-----|
| **OpenCellID API** | Free tier | ~50-500m | Register at my.opencellid.org |
| **Google Maps Geolocation API** | Free tier ($200/mo) | ~100-500m | Google Cloud Console → enable Geolocation API |
| **UnwiredLabs API** | Free tier | ~50-500m | https://unwiredlabs.com/locationapi |
| **findcellid.com** | Free | ~1km | Web interface, no API key needed |

### Precision Comparison:
- **CellMapper (general):** Shows ALL towers → ~1-5km accuracy
- **findcellid.com (specific):** Specific tower lookup → ~500m-1km accuracy
- **OpenCellID/Google Maps API:** MCC/MNC/LAC/CellID → **~50-200m accuracy** ✅ PRECISE

### Key Takeaway:
The PRECISE tool = OpenCellID/Google Maps Geolocation API with specific cell tower IDs (MCC/MNC/LAC/CellID) → returns lat/lon + accuracy radius.
