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
1. Run the test script: `test-whise-api.php` in your browser
2. Or use the "Test Connection" feature in the admin panel

### 3. Verify Frontend
1. Visit your website page with the filter block
2. Check if properties are loading
3. Test the filter functionality

## Expected Results

✅ **Authentication**: Should work with your credentials  
✅ **Client Token**: Should be obtained successfully  
✅ **Estates**: Should load real property data  
✅ **Cities**: Should populate the city dropdown  
✅ **Filters**: Should work for all filter types  

## Troubleshooting

### If Authentication Fails
- Double-check username/password
- Ensure API URL is correct
- Clear cache and retry

### If Client Token Fails
- Contact WHISE at `api@whise.eu`
- Include Client ID: `12889`
- Request account activation

### If No Properties Show
- Check if estates exist in your WHISE account
- Verify Office ID if filtering by office
- Test with different filters

## API Endpoints Used

Based on the [Whise API documentation](https://api.whise.eu/WebsiteDesigner.html#section/Statuses):

1. **Authentication**: `POST /token`
2. **Client Token**: `POST /v1/admin/clients/token`
3. **Estates List**: `POST /v1/estates/list`
4. **Cities List**: `POST /v1/estates/usedcities/list`

## Filter Mapping

| Filter | API Field | Values |
|--------|-----------|---------|
| Te koop/Te huur | `PurposeId` | 1 (Te koop), 2 (Te huur) |
| Gemeente | `City` | Dynamic from API |
| Type | `CategoryId` | 1-6 (Appartement, Huis, Villa, etc.) |
| Prijs | `PriceMin`/`PriceMax` | € ranges |

## Cache Settings

- **Authentication Tokens**: 1 hour
- **Property Data**: 30 minutes
- **City List**: 1 hour
- **Manual Clear**: Available in admin

## Support

If you encounter issues:
1. Check the test script output
2. Review browser console for errors
3. Contact WHISE support if API-related
4. Check the full documentation in `WHISE_API_INTEGRATION.md` 