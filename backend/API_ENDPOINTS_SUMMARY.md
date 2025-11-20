# API Endpoints Summary - JJ Flowershop

## 📍 Main API Routes File
**Location:** `backend/routes/api.php`

**Status:** ✅ **Consolidated** - All public API routes are now in `api.php`. Duplicates removed from `web.php`.

---

## 🔗 API Endpoints by Category

### 1. **Authentication & User APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/api/user` | Closure | Get authenticated user (Sanctum) |

---

### 2. **Test & Health Check APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/api/test` | Closure | Simple API test endpoint |
| GET | `/api/test-geocode` | Closure | Test geocode route |
| GET | `/api/map/test` | Closure | Test map API with timestamp |

---

### 3. **Map & Geocoding APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| POST | `/api/map/geocode` | `MapController@geocode` | Convert address to coordinates |
| POST | `/api/map/route` | `MapController@getRoute` | Get route between two points |
| POST | `/api/map/shipping-calculate` | `MapController@calculateShippingWithDistance` | Calculate shipping with distance |

---

### 4. **Shipping Fee APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| POST | `/api/calculate-shipping-fee` | `ShippingFeeController@calculate` | Calculate shipping fee |

---

### 5. **Analytics APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/api/analytics/compact` | Closure | Get compact analytics (daily/monthly revenue) |

---

### 6. **Product APIs**

#### Admin Product APIs
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/admin/api/products/pending` | `AdminProductApprovalController@getPendingProducts` | Get pending products |
| GET | `/admin/api/products/approved` | `AdminProductApprovalController@getApprovedProducts` | Get approved products |
| POST | `/admin/api/products/{product}/approve` | `AdminProductApprovalController@approveProduct` | Approve product |
| DELETE | `/admin/api/products/{product}/disapprove` | `AdminProductApprovalController@disapproveProduct` | Disapprove product |
| GET | `/admin/api/products/{product}/details` | `AdminProductApprovalController@getProductDetails` | Get product details |
| GET | `/admin/api/products/{product}/compositions` | `AdminProductApprovalController@getProductCompositions` | Get product compositions |

#### Product Changes APIs
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/admin/api/product-changes/pending` | `ProductController@getPendingProductChanges` | Get pending product changes |
| POST | `/admin/api/product-changes/{id}/approve` | `ProductController@approveProductChange` | Approve product change |
| POST | `/admin/api/product-changes/{id}/reject` | `ProductController@rejectProductChange` | Reject product change |
| GET | `/admin/api/product-changes/{id}/details` | `ProductController@getProductChangeDetails` | Get product change details |

#### Inventory & Category APIs
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/admin/api/categories` | `ProductController@getCategories` | Get all categories |
| GET | `/admin/api/inventory/{category?}` | `ProductController@getInventoryByCategory` | Get inventory by category |
| GET | `/api/inventory-items` | `ClerkController@getInventoryItems` | Get inventory items (API route) |

#### Clerk Product APIs
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/clerk/api/products/{product}/details` | `ClerkController@getProductDetails` | Get product details |
| GET | `/clerk/api/products/{product}/compositions` | `ClerkController@getProductCompositions` | Get product compositions |
| GET | `/clerk/api/categories` | `ProductController@getCategories` | Get categories |
| GET | `/clerk/api/inventory/{category?}` | `ProductController@getInventoryByCategory` | Get inventory by category |

---

### 7. **Payment APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/admin/api/payment-modes` | `InvoiceController@getPaymentModes` | Get payment modes |
| GET | `/clerk/api/payment-modes` | `InvoiceController@getPaymentModes` | Get payment modes |

---

### 8. **Favorites APIs** (Customer)
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/customer/api/favorites` | `FavoriteController@index` | Get user favorites |
| GET | `/customer/api/favorites/check/{product}` | `FavoriteController@check` | Check if product is favorited |
| POST | `/customer/api/favorites` | `FavoriteController@store` | Add to favorites |
| DELETE | `/customer/api/favorites/{product}` | `FavoriteController@destroy` | Remove from favorites |

---

### 9. **Geo Optimization APIs**
| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/api/geo/content` | `GeoOptimizationController@getLocationBasedContent` | Get location-based content |
| POST | `/api/geo/location` | `GeoOptimizationController@updateLocation` | Update user location |
| GET | `/api/geo/homepage-products` | `GeoOptimizationController@getHomepageProducts` | Get homepage products by location |

---

## 📂 Controllers Location
All controllers are located in: `backend/app/Http/Controllers/`

### Key Controllers:
- **MapController** - Map, geocoding, routing
- **ShippingFeeController** - Shipping fee calculations
- **ProductController** - Product management
- **GeoOptimizationController** - Location-based features
- **FavoriteController** - Customer favorites
- **AdminProductApprovalController** - Product approval workflow
- **InvoiceController** - Invoice and payment management

---

## 🔐 Authentication

### Sanctum Protected:
- `/api/user` - Requires `auth:sanctum` middleware

### Web Middleware Protected:
Most other API endpoints in `web.php` are protected by:
- `web` middleware
- `auth` middleware
- Role-specific middleware (`AdminMiddleware`, `ClerkMiddleware`, `CustomerMiddleware`)

---

## 📝 Notes

1. **✅ Consolidated Routes**: All public API routes have been moved to `api.php`. Duplicates removed from `web.php`.

2. **Route Prefix**: Routes in `api.php` are automatically prefixed with `/api` by `RouteServiceProvider`.

3. **Response Format**: Most APIs return JSON responses.

4. **Error Handling**: APIs use standard HTTP status codes (200, 404, 500, etc.)

5. **Role-Protected Routes**: Routes under `/admin/api/*`, `/clerk/api/*`, and `/customer/api/*` remain in `web.php` because they require specific middleware groups.

---

## 🚀 Quick Reference

### Most Used APIs:
- **Map Geocoding**: `POST /api/map/geocode`
- **Shipping Calculation**: `POST /api/calculate-shipping-fee`
- **Analytics**: `GET /api/analytics/compact`
- **Geo Content**: `GET /api/geo/content`
- **Product Categories**: `GET /admin/api/categories` (requires admin auth)
- **Inventory by Category**: `GET /admin/api/inventory/{category}` (requires admin auth)
- **Favorites**: `GET /customer/api/favorites` (requires customer auth)

---

## 🔄 Recent Changes

**✅ Consolidated Duplicate Routes (Latest Update)**
- Moved all public API routes from `web.php` to `api.php`
- Removed duplicate map routes from `web.php`
- Removed duplicate geo routes from `web.php`
- Removed duplicate analytics route from `web.php`
- Organized `api.php` with clear section headers
- All public APIs now accessible via `/api/*` prefix

**Last Updated:** After route consolidation

