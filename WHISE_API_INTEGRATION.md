# Whise API Integration for Immo Ley

This document describes the integration of the Whise API into the Immo Ley real estate website.

## Overview

The Whise API integration provides dynamic real estate listings with filtering capabilities. The system fetches property data from the Whise platform and displays it in a responsive grid layout with advanced filtering options.

## Features

- **Dynamic Property Listings**: Real-time property data from Whise API
- **Advanced Filtering**: Filter by purpose (sale/rent), city, property type, and price range
- **Responsive Design**: Mobile-friendly filter interface
- **Real-time Data**: Always fresh data without caching for immediate results
- **Admin Configuration**: Easy setup through WordPress admin panel
- **Error Handling**: Graceful fallbacks and user-friendly error messages
- **Debug Tools**: Comprehensive testing and debugging capabilities

## Installation & Setup

### 1. API Configuration

1. Go to **WordPress Admin > Global > Whise API Settings**
2. Configure the following fields:
   - **API URL**: `https://api.whise.eu` (default)
   - **Username**: Your Whise API username
   - **Password**: Your Whise API password
   - **Client ID**: Your Whise client ID

### 2. Authentication Flow

The system uses a two-step authentication process:

1. **Initial Authentication**: Uses username/password to get an access token
2. **Client Token**: Uses the access token to get a client-specific token for API calls

### 3. Debug Tools

- **Filter Test Page**: Use `page-whise-filter-test.php` template for comprehensive testing
- **Debug Page**: Use `page-whise-debug.php` template for detailed API debugging
- **Test Script**: Run `test-whise-filters.php` for quick filter validation

## File Structure

```
includes/
├── WhiseAPI.php          # Main API integration class
└── whise-config.php      # ACF fields and admin configuration

gb-blocks/
└── filter-with-grid.php  # Updated block template with form-based filtering

page-templates/
├── page-whise-debug.php      # Debug page template
└── page-whise-filter-test.php # Filter test page template

test-files/
└── test-whise-filters.php    # Standalone test script
```

## API Endpoints Used

### Authentication
- `POST /token` - Get access token
- `POST /v1/admin/clients/token` - Get client token

### Data Retrieval
- `POST /v1/estates/list` - Get property listings
- `POST /v1/estates/usedcities/list` - Get available cities

## API Request Structure

The system now uses the correct Whise API request structure as per the [official documentation](https://api.whise.eu/WebsiteDesigner.html#tag/Contacts/operation/):

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

## Filter Options

### Purpose (Te koop/Te huur)
- **API Field**: `Filter.PurposeIds[]`
- **Values**: 1 (Te koop), 2 (Te huur)

### City (Gemeente)
- **API Field**: `Filter.City`
- **Source**: Dynamic from `/v1/estates/usedcities/list`

### Property Type
- **API Field**: `Filter.CategoryIds[]`
- **Values**: 
  - 1: Appartement
  - 2: Huis
  - 3: Villa
  - 4: Kantoor
  - 5: Winkel
  - 6: Grond

### Price Range
- **API Field**: `Filter.PriceRange.Min/Max`
- **Ranges**:
  - 0 - €500.000
  - €500.000 - €1.000.000
  - €1.000.000 - €1.500.000
  - €1.500.000+

## Frontend Implementation

### Form-Based Filtering
The system now uses standard HTML forms with GET parameters instead of AJAX:

```html
<form method="GET" action="<?php echo esc_url($current_url); ?>" class="filter-form">
    <select name="purpose" class="whise-purpose-select">
        <option value="">Alle</option>
        <!-- Options populated from API -->
    </select>
    <!-- Other filter fields -->
    <button type="submit" class="whise-search-btn">ZOEKEN</button>
</form>
```

### JavaScript Enhancement
Minimal JavaScript for enhanced user experience:

```javascript
// Auto-submit on filter changes
$('.whise-purpose-select, .whise-city-select, .whise-category-select').on('change', function() {
    $(this).closest('form').submit();
});

// Price range handling
$('.whise-price-range').on('change', function() {
    const selectedRange = $(this).val();
    if (selectedRange) {
        const [min, max] = selectedRange.split('-');
        $('input[name="price_min"]').val(min || '');
        $('input[name="price_max"]').val(max || '');
    }
    $(this).closest('form').submit();
});
```

## CSS Classes

### Filter Elements
- `.filter-form` - Main filter form container
- `.whise-purpose-select` - Purpose dropdown
- `.whise-city-select` - City dropdown
- `.whise-category-select` - Property type dropdown
- `.whise-price-range` - Price range dropdown
- `.whise-search-btn` - Search button

### States
- `.no-results` - When no properties match filters
- `.error-message` - When API errors occur

## Error Handling

### Frontend Errors
- Network errors show user-friendly messages
- No results state with helpful suggestions
- Form validation for user inputs

### Backend Errors
- Authentication failures logged with detailed error messages
- API rate limiting handled gracefully
- Comprehensive logging for debugging

## Performance Optimization

### Real-time Data
- **No Caching**: Always fresh data from API
- **Immediate Results**: Filters applied instantly
- **Direct API Calls**: No intermediate caching layer

### Loading Optimization
- Lazy loading for property images
- Progressive enhancement for filter interface
- Efficient form submission handling

## Debugging Tools

### 1. Filter Test Page (`page-whise-filter-test.php`)
- Comprehensive filter testing with 5 different scenarios
- Results comparison table
- Sample estate data analysis
- Automatic detection of filter issues

### 2. Debug Page (`page-whise-debug.php`)
- API connection testing
- Individual filter testing
- Raw API response viewing
- Configuration verification

### 3. Test Script (`test-whise-filters.php`)
- Standalone testing without WordPress
- Quick validation of filter functionality
- Detailed logging output

## Troubleshooting

### Common Issues

1. **Authentication Failed**
   - Check API credentials in admin panel
   - Verify API URL is correct
   - Check error logs for detailed messages

2. **No Properties Displayed**
   - Check if client ID is correct
   - Verify API permissions
   - Test connection using debug tools

3. **Filters Not Working**
   - Use the filter test page to validate functionality
   - Check WordPress error logs for API communication
   - Verify request structure matches API documentation

4. **Always Same Number of Estates**
   - Check if API request structure is correct
   - Verify filter parameters are being sent properly
   - Use debug tools to examine raw API responses

### Debug Tools

1. **Filter Test Page**: Comprehensive testing with visual results
2. **Debug Page**: Detailed API communication analysis
3. **Test Script**: Quick standalone validation
4. **Error Logs**: Detailed logging for troubleshooting

## Security Considerations

- API credentials stored securely in WordPress options
- All user inputs sanitized before API calls
- Form-based submission with proper validation
- HTTPS required for API communication
- No sensitive data in client-side code

## Future Enhancements

- **Advanced Search**: More detailed property criteria
- **Saved Searches**: User account integration
- **Property Details**: Individual property pages
- **Contact Forms**: Lead generation integration
- **Analytics**: Search behavior tracking
- **Multi-language**: Internationalization support
- **Pagination**: Handle large result sets
- **Sorting Options**: Multiple sort criteria

## Support

For technical support or questions about the Whise API integration:

1. Use the debug tools provided
2. Check the troubleshooting section above
3. Review WordPress error logs for detailed messages
4. Test with the filter test page
5. Contact development team with specific error messages

## Changelog

### Version 2.0.0
- **Removed caching system** for real-time data
- **Corrected API request structure** to match official documentation
- **Added comprehensive debug tools** for troubleshooting
- **Switched to form-based filtering** from AJAX
- **Enhanced error logging** and debugging capabilities
- **Added filter test page** for validation
- **Updated documentation** with new structure and tools

### Version 1.0.0
- Initial Whise API integration
- Basic filtering functionality
- Caching system implementation
- Admin configuration panel
- Responsive design support 