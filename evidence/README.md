# Evidence — Red Team Tool Testing

## Cell Tower Location Tracking

### CellMapper (cellmapper.net)
- **Service:** Cellular tower mapping and triangulation
- **Country:** Bangladesh (MCC=470)
- **Carrier:** Banglalink (MNC=3)
- **Location:** Dhaka (23.81°N, 90.41°E)
- **Method:** Cell tower lookup by MCC/MNC/LAC/CellID → approximate triangulated location

### Tools Tested
| Tool | Status | Evidence |
|------|--------|----------|
| CellMapper | ✅ Working | `cellmapper-bangladesh-banglalink.png` |
| CellMapper Overview | ✅ Working | `cellmapper-net-overview.png` |
| OpenCellID | ❌ 403 Forbidden | `opencellid-bangladesh.png` |

### Methodology
1. Identified carrier (MCC=470, MNC=3 for Bangladesh)
2. Used CellMapper to map cell towers near Dhaka
3. Tower triangulation gives approximate device location
4. Red team use: proving physical location of target device within cell coverage area

### Phone Number Tested
- **+8801685419598** (Bangladesh)
- **Carrier:** Grameenphone/Banglalink
- **Approximate location:** Dhaka, Bangladesh

### Screenshots
- `cellmapper-bangladesh-banglalink.png` — Banglalink tower map near Dhaka
- `cellmapper-net-overview.png` — CellMapper overview
- `opencellid-bangladesh.png` — OpenCellID attempt (403)
