# API Documentation

## Overview

The Universal Portfolio & Web Tools platform provides a RESTful API for programmatic access to QR code generation, file management, and other features.

## Base URL

```
https://yourdomain.com/api/v1/
```

## Authentication

Currently, the API uses session-based authentication with CSRF tokens for write operations.

### Getting CSRF Token

```http
GET /api/v1/csrf-token
```

**Response:**
```json
{
  "csrf_token": "abc123...",
  "expires_at": "2024-01-01T12:00:00Z"
}
```

## Endpoints

### QR Code Generation

#### Generate QR Code for URL

```http
POST /api/v1/qr/generate-url
Content-Type: application/json
X-CSRF-Token: your-csrf-token
```

**Request Body:**
```json
{
  "url": "https://example.com",
  "size": 300,
  "format": "png",
  "error_correction": "M"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "qr_code_url": "/qr-code/view-qr.php?file=link_qr_1234567890_abc123.png",
    "filename": "link_qr_1234567890_abc123.png",
    "original_url": "https://example.com",
    "size": 300,
    "format": "png",
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

#### Generate QR Code for File

```http
POST /api/v1/qr/generate-file
Content-Type: multipart/form-data
X-CSRF-Token: your-csrf-token
```

**Form Data:**
- `file`: File to upload
- `size`: QR code size (optional, default: 300)
- `format`: QR code format (optional, default: png)

**Response:**
```json
{
  "success": true,
  "data": {
    "qr_code_url": "/qr-code/view-qr.php?file=file_dl_qr_1234567890_abc123.png",
    "download_url": "/qr-code/download.php?file=1234567890_abc123_filename.ext",
    "filename": "file_dl_qr_1234567890_abc123.png",
    "original_filename": "document.pdf",
    "file_size": 1024000,
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

### File Management

#### List Files

```http
GET /api/v1/files
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 20, max: 100)
- `type`: Filter by file type (optional)

**Response:**
```json
{
  "success": true,
  "data": {
    "files": [
      {
        "id": "1234567890_abc123_document.pdf",
        "original_name": "document.pdf",
        "size": 1024000,
        "type": "application/pdf",
        "uploaded_at": "2024-01-01T12:00:00Z",
        "download_count": 5,
        "qr_code_url": "/qr-code/view-qr.php?file=file_dl_qr_1234567890_abc123.png"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_files": 87,
      "has_next": true,
      "has_previous": false
    }
  }
}
```

#### Delete File

```http
DELETE /api/v1/files/{file_id}
X-CSRF-Token: your-csrf-token
```

**Response:**
```json
{
  "success": true,
  "message": "File deleted successfully"
}
```

### URL Shortener

#### Create Short URL

```http
POST /api/v1/shorts/create
Content-Type: application/json
X-CSRF-Token: your-csrf-token
```

**Request Body:**
```json
{
  "url": "https://example.com/very/long/url",
  "custom_code": "mylink",
  "expires_at": "2024-12-31T23:59:59Z"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "short_url": "https://yourdomain.com/s/mylink",
    "short_code": "mylink",
    "original_url": "https://example.com/very/long/url",
    "clicks": 0,
    "created_at": "2024-01-01T12:00:00Z",
    "expires_at": "2024-12-31T23:59:59Z"
  }
}
```

#### Get Short URL Stats

```http
GET /api/v1/shorts/{short_code}/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "short_code": "mylink",
    "original_url": "https://example.com/very/long/url",
    "total_clicks": 25,
    "unique_clicks": 18,
    "created_at": "2024-01-01T12:00:00Z",
    "last_clicked": "2024-01-02T15:30:00Z",
    "click_history": [
      {
        "date": "2024-01-01",
        "clicks": 5
      },
      {
        "date": "2024-01-02",
        "clicks": 20
      }
    ]
  }
}
```

## Error Handling

All API endpoints return consistent error responses:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The provided data is invalid",
    "details": {
      "url": ["The URL field is required"],
      "size": ["Size must be between 100 and 1000"]
    }
  }
}
```

### Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request data validation failed |
| `AUTHENTICATION_REQUIRED` | User must be authenticated |
| `INSUFFICIENT_PERMISSIONS` | User lacks required permissions |
| `RESOURCE_NOT_FOUND` | Requested resource doesn't exist |
| `RATE_LIMIT_EXCEEDED` | Too many requests |
| `FILE_TOO_LARGE` | Uploaded file exceeds size limit |
| `INVALID_FILE_TYPE` | File type not allowed |
| `STORAGE_ERROR` | File storage operation failed |
| `INTERNAL_ERROR` | Server error occurred |

## Rate Limiting

API requests are rate limited to prevent abuse:

- **Authenticated users**: 1000 requests per hour
- **Anonymous users**: 100 requests per hour
- **File uploads**: 50 uploads per hour

Rate limit headers are included in responses:

```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## SDKs and Libraries

### PHP SDK

```php
use PortfolioTools\SDK\Client;

$client = new Client('https://yourdomain.com', 'your-api-key');

// Generate QR code
$qr = $client->qr()->generateUrl('https://example.com');
echo $qr['qr_code_url'];

// Upload file and generate QR
$file = $client->qr()->generateFile('/path/to/file.pdf');
echo $file['download_url'];
```

### JavaScript/Node.js

```javascript
const PortfolioTools = require('portfolio-tools-sdk');

const client = new PortfolioTools('https://yourdomain.com', 'your-api-key');

// Generate QR code
const qr = await client.qr.generateUrl('https://example.com');
console.log(qr.qr_code_url);

// Create short URL
const short = await client.shorts.create('https://example.com/long-url');
console.log(short.short_url);
```

### Python

```python
from portfolio_tools import Client

client = Client('https://yourdomain.com', 'your-api-key')

# Generate QR code
qr = client.qr.generate_url('https://example.com')
print(qr['qr_code_url'])

# Upload file
with open('document.pdf', 'rb') as f:
    file_qr = client.qr.generate_file(f)
    print(file_qr['download_url'])
```

## Webhooks

Configure webhooks to receive notifications about events:

### Available Events

- `qr.generated` - QR code was generated
- `file.uploaded` - File was uploaded
- `file.downloaded` - File was downloaded
- `short.created` - Short URL was created
- `short.clicked` - Short URL was accessed

### Webhook Configuration

```http
POST /api/v1/webhooks
Content-Type: application/json
X-CSRF-Token: your-csrf-token
```

**Request Body:**
```json
{
  "url": "https://yourapp.com/webhooks/portfolio-tools",
  "events": ["qr.generated", "file.uploaded"],
  "secret": "webhook-secret-key"
}
```

### Webhook Payload

```json
{
  "event": "qr.generated",
  "timestamp": "2024-01-01T12:00:00Z",
  "data": {
    "qr_code_url": "/qr-code/view-qr.php?file=link_qr_1234567890_abc123.png",
    "original_url": "https://example.com",
    "user_id": "user123"
  }
}
```

## Testing

Use the built-in API explorer at `/api/docs` to test endpoints interactively.

### Example cURL Requests

```bash
# Get CSRF token
curl -X GET https://yourdomain.com/api/v1/csrf-token

# Generate QR code
curl -X POST https://yourdomain.com/api/v1/qr/generate-url \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: your-token" \
  -d '{"url": "https://example.com"}'

# Upload file and generate QR
curl -X POST https://yourdomain.com/api/v1/qr/generate-file \
  -H "X-CSRF-Token: your-token" \
  -F "file=@document.pdf"
```

## Versioning

The API uses semantic versioning. Current version is `v1`.

Breaking changes will result in a new version (e.g., `v2`). Non-breaking changes and new features are added to the current version.

## Support

For API support:
- 📖 Check the documentation
- 🐛 Report issues on GitHub
- 💬 Join our Discord community
- 📧 Email: api-support@yourdomain.com
