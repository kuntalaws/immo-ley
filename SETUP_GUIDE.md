# 🚀 Quick Setup Guide - Whise API Integration

## Your API Credentials

Use these exact credentials in your WordPress admin:

### **API Configuration**
- **API URL**: `https://api.whise.eu`
- **Username**: `arnab@anushaweb.com`
- **Password**: `A!aH6Hcnx6eJz7?g`
- **Client ID**: `12889`
- **Office ID**: `15795` (optional)

## Setup Steps

### 1. Configure API Settings
1. Go to **WordPress Admin → Global → Whise API Settings**
2. Enter the credentials above
3. Save the settings

### 2. Test the Connection
1. Run the test script: `test-whise-filters.php` in your browser
2. Or use the "Test Connection" feature in the admin panel
3. Check the debug page: Create a page with template "Whise API Debug"

### 3. Verify Frontend
1. Visit your website page with the filter block
2. Check if properties are loading
3. Test the filter functionality

## Expected Results

✅ **Authentication**: Should work with your credentials  
✅ **Client Token**: Should be obtained successfully  
✅ **Estates**: Should load real property data  
✅ **Cities**: Should populate the city dropdown  
✅ **Filters**: Should work for all filter types with different results  

## API Request Structure

The system now uses the correct Whise API request structure:

```json
{
  "Filter": {
    "PurposeIds": [1],
    "CategoryIds": [2],
    "PriceRange": {
      "Min": 100000,
      "Max": 500000
    },
    "City": "Antwerp"
  },
  "Field": {
    "Excluded": ["longDescription"]
  },
  "Page": {
    "Limit": 100,
    "Offset": 0
  }
}
```

## Filter Mapping

| Filter | API Field | Values |
|--------|-----------|---------|
| Te koop/Te huur | `Filter.PurposeIds[]` | 1 (Te koop), 2 (Te huur) |
| Gemeente | `Filter.City` | Dynamic from API |
| Type | `Filter.CategoryIds[]` | 1-6 (Appartement, Huis, Villa, etc.) |
| Prijs | `Filter.PriceRange.Min/Max` | € ranges |

## Debugging Tools

### 1. Filter Test Page
- Create a new page in WordPress
- Set template to "Whise Filter Test"
- This will run comprehensive filter tests automatically

### 2. Debug Page
- Create a new page in WordPress  
- Set template to "Whise API Debug"
- Provides detailed API communication analysis

### 3. Test Script
- Run `test-whise-filters.php` directly in browser
- Standalone testing without WordPress
- Quick validation of filter functionality

## Troubleshooting

### If Authentication Fails
- Double-check username/password
- Ensure API URL is correct
- Check WordPress error logs for detailed messages

### If Client Token Fails
- Contact WHISE at `api@whise.eu`
- Include Client ID: `12889`
- Request account activation

### If No Properties Show
- Check if estates exist in your WHISE account
- Verify Office ID if filtering by office
- Test with different filters

### If Filters Always Return Same Count
- Use the filter test page to validate functionality
- Check if API request structure is correct
- Verify filter parameters are being sent properly
- Use debug tools to examine raw API responses

## Performance Notes

- **No Caching**: System now provides real-time data without caching
- **Always Fresh**: Every request fetches latest data from API
- **Immediate Results**: Filters applied instantly with fresh data
- **Direct API Calls**: No intermediate caching layer

## Testing Checklist

Before going live, verify:

- [ ] Authentication works with your credentials
- [ ] Client token is obtained successfully
- [ ] Estates load with real property data
- [ ] City dropdown populates correctly
- [ ] Purpose filter (Te koop/Te huur) works
- [ ] Category filter (Type) works
- [ ] Price range filter works
- [ ] Multiple filters work together
- [ ] Different filter combinations return different results
- [ ] No JavaScript errors in browser console
- [ ] Form submission works correctly

## Support

If you encounter issues:
1. **Use the debug tools** provided (test page, debug page, test script)
2. **Check WordPress error logs** for detailed API communication
3. **Test with filter test page** to validate functionality
4. **Contact WHISE support** if API-related issues persist
5. **Check the full documentation** in `WHISE_API_INTEGRATION.md`

## Recent Updates (v2.0.0)

- ✅ **Removed caching system** for real-time data
- ✅ **Corrected API request structure** to match official documentation
- ✅ **Added comprehensive debug tools** for troubleshooting
- ✅ **Switched to form-based filtering** from AJAX
- ✅ **Enhanced error logging** and debugging capabilities
- ✅ **Added filter test page** for validation 