# Whise API Integration for Immo Ley

This document describes the integration of the Whise API into the Immo Ley real estate website.

## Overview

The Whise API integration provides dynamic real estate listings with filtering capabilities. The system fetches property data from the Whise platform and displays it in a responsive grid layout with advanced filtering options.

## Features

- **Dynamic Property Listings**: Real-time property data from Whise API
- **Advanced Filtering**: Filter by purpose (sale/rent), city, property type, and price range
- **Responsive Design**: Mobile-friendly filter interface
- **Caching System**: Optimized performance with intelligent caching
- **Admin Configuration**: Easy setup through WordPress admin panel
- **Error Handling**: Graceful fallbacks and user-friendly error messages

## Installation & Setup

### 1. API Configuration

1. Go to **WordPress Admin > Global > Whise API Settings**
2. Configure the following fields:
   - **API URL**: `https://api.whise.eu` (default)
   - **Username**: Your Whise API username
   - **Password**: Your Whise API password
   - **Client ID**: Your Whise client ID
   - **Cache Duration**: How long to cache API responses (default: 3600 seconds)

### 2. Authentication Flow

The system uses a two-step authentication process:

1. **Initial Authentication**: Uses username/password to get an access token
2. **Client Token**: Uses the access token to get a client-specific token for API calls

### 3. Cache Management

- **Authentication Tokens**: Cached for 1 hour
- **Property Data**: Cached for 30 minutes
- **City List**: Cached for 1 hour
- **Manual Cache Clear**: Available in admin panel

## File Structure

```
includes/
├── WhiseAPI.php          # Main API integration class
└── whise-config.php      # ACF fields and admin configuration

js/
└── whise-api.js          # Frontend JavaScript for filtering

css/
└── filter-with-grid.css  # Styles for filter interface

gb-blocks/
└── filter-with-grid.php  # Updated block template
```

## API Endpoints Used

### Authentication
- `POST /token` - Get access token
- `POST /v1/admin/clients/token` - Get client token

### Data Retrieval
- `POST /v1/estates/list` - Get property listings
- `POST /v1/estates/usedcities/list` - Get available cities

## Filter Options

### Purpose (Te koop/Te huur)
- **API Field**: `PurposeId`
- **Values**: 1 (Te koop), 2 (Te huur)

### City (Gemeente)
- **API Field**: `City`
- **Source**: Dynamic from `/v1/estates/usedcities/list`

### Property Type
- **API Field**: `CategoryId`
- **Values**: 
  - 1: Appartement
  - 2: Huis
  - 3: Villa
  - 4: Kantoor
  - 5: Winkel
  - 6: Grond

### Price Range
- **API Fields**: `PriceMin`, `PriceMax`
- **Ranges**:
  - 0 - €500.000
  - €500.000 - €1.000.000
  - €1.000.000 - €1.500.000
  - €1.500.000+

## JavaScript API

### Initialization
```javascript
// Automatically initialized when filter elements are present
if ($('.whise-filter').length > 0) {
    WhiseAPI.init();
}
```

### Available Methods
- `WhiseAPI.loadEstates()` - Load estates with current filters
- `WhiseAPI.loadFilterOptions()` - Load filter dropdown options
- `WhiseAPI.getFilters()` - Get current filter values

### Events
- `change` on `.whise-filter` - Automatically triggers estate reload
- `click` on `.whise-search-btn` - Manual search trigger

## CSS Classes

### Filter Elements
- `.whise-filter` - Base class for all filter elements
- `.whise-purpose-select` - Purpose dropdown
- `.whise-city-select` - City dropdown
- `.whise-category-select` - Property type dropdown
- `.whise-price-range` - Price range dropdown
- `.whise-search-btn` - Search button

### States
- `.loading` - Applied during API calls
- `.no-results` - When no properties match filters
- `.error-message` - When API errors occur

## Error Handling

### Frontend Errors
- Network errors show user-friendly messages
- No results state with helpful suggestions
- Loading states with spinner animation

### Backend Errors
- Authentication failures logged
- API rate limiting handled gracefully
- Fallback to cached data when possible

## Performance Optimization

### Caching Strategy
- **Short-term**: API responses cached for 30 minutes
- **Medium-term**: Authentication tokens cached for 1 hour
- **Long-term**: Static data (cities, categories) cached for 1 hour

### Loading Optimization
- Lazy loading for property images
- Progressive enhancement for filter interface
- Minimal initial page load with AJAX data fetching

## Troubleshooting

### Common Issues

1. **Authentication Failed**
   - Check API credentials in admin panel
   - Verify API URL is correct
   - Clear cache and retry

2. **No Properties Displayed**
   - Check if client ID is correct
   - Verify API permissions
   - Test connection using admin tools

3. **Filters Not Working**
   - Check browser console for JavaScript errors
   - Verify AJAX endpoints are accessible
   - Clear browser cache

### Debug Tools

1. **Connection Test**: Available in admin panel
2. **Cache Management**: Clear cache button in admin
3. **Browser Console**: Check for JavaScript errors
4. **Network Tab**: Monitor API requests

## Security Considerations

- API credentials stored securely in WordPress options
- All user inputs sanitized before API calls
- Nonce verification for AJAX requests
- Rate limiting through caching
- HTTPS required for API communication

## Future Enhancements

- **Advanced Search**: More detailed property criteria
- **Saved Searches**: User account integration
- **Property Details**: Individual property pages
- **Contact Forms**: Lead generation integration
- **Analytics**: Search behavior tracking
- **Multi-language**: Internationalization support

## Support

For technical support or questions about the Whise API integration:

1. Check the troubleshooting section above
2. Review browser console for errors
3. Test API connection in admin panel
4. Contact development team with specific error messages

## Changelog

### Version 1.0.0
- Initial Whise API integration
- Basic filtering functionality
- Caching system implementation
- Admin configuration panel
- Responsive design support 