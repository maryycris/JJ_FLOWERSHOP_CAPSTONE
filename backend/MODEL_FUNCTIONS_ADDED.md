# Model Functions Added ✅

## Summary
All database models now have comprehensive helper methods, accessors, relationships, and query scopes for easier data manipulation and display.

## Models Enhanced

### 1. **SalesOrder** Model
**Added Functions:**
- `getFormattedSubtotalAttribute()` - Formatted subtotal display
- `getFormattedShippingFeeAttribute()` - Formatted shipping fee
- `getFormattedTotalAmountAttribute()` - Formatted total amount
- `isConfirmed()` - Check if confirmed
- `isShipped()` - Check if shipped
- `isDelivered()` - Check if delivered
- `scopeConfirmed()` - Query scope for confirmed orders
- `scopeDateRange()` - Query scope for date range filtering

### 2. **CustomizeItem** Model
**Added Functions:**
- `getFormattedPriceAttribute()` - Formatted price (uses inventory price if linked)
- `getDisplayPriceAttribute()` - Display price (prefers inventory price)
- `isAvailable()` - Check if item is active and approved
- `scopeActive()` - Query scope for active items
- `scopeApproved()` - Query scope for approved items
- `scopeByCategory()` - Query scope by category

### 3. **Product** Model
**Added Functions:**
- `getFormattedPriceAttribute()` - Formatted price display
- `getFormattedCostPriceAttribute()` - Formatted cost price
- `isOutOfStock()` - Check if out of stock
- `needsReorder()` - Check if needs reorder
- `isLowStock()` - Check if low stock
- `scopeByCategory()` - Query scope by category
- `scopeApproved()` - Query scope for approved products
- `scopeLowStock()` - Query scope for low stock products

### 4. **Invoice** Model
**Added Functions:**
- `getFormattedSubtotalAttribute()` - Formatted subtotal
- `getFormattedShippingFeeAttribute()` - Formatted shipping fee
- `getFormattedTotalAmountAttribute()` - Formatted total amount
- `getTotalPaidAttribute()` - Total paid from payments
- `isFullyPaid()` - Check if invoice is fully paid
- `scopePaid()` - Query scope for paid invoices
- `scopePending()` - Query scope for pending invoices

### 5. **Payment** Model
**Added Functions:**
- `order()` - Relationship to order
- `getFormattedAmountAttribute()` - Formatted amount
- `isCompleted()` - Check if payment completed
- `isPending()` - Check if payment pending
- `getFormattedPaymentDateAttribute()` - Formatted payment date
- `scopeCompleted()` - Query scope for completed payments
- `scopeDateRange()` - Query scope for date range filtering

### 6. **Address** Model
**Added Functions:**
- `user()` - Relationship to user
- `getFullAddressAttribute()` - Full address string
- `getAddressWithLabelAttribute()` - Address with label
- `isDefault()` - Check if default address
- `markAsDefault()` - Mark as default address
- `scopeDefault()` - Query scope for default addresses
- `scopeForUser()` - Query scope for user addresses

### 7. **Delivery** Model
**Added Functions:**
- `getFormattedShippingFeeAttribute()` - Formatted shipping fee
- `getFullAddressAttribute()` - Full delivery address
- `getFormattedDeliveryDateAttribute()` - Formatted delivery date
- `hasProof()` - Check if has proof of delivery
- `getProofOfDeliveryUrlAttribute()` - Proof of delivery URL
- `isCompleted()` - Check if delivery completed
- `scopeByStatus()` - Query scope by status
- `scopePending()` - Query scope for pending deliveries
- `scopeCompleted()` - Query scope for completed deliveries

### 8. **LoyaltyCard** Model
**Added Functions:**
- `getFormattedStampsCountAttribute()` - Formatted stamps count (X / 5)
- `isEligibleForRedemption()` - Check if eligible for redemption
- `getStampsNeededAttribute()` - Stamps needed for next redemption
- `getProgressPercentageAttribute()` - Progress percentage (0-100%)
- `isActive()` - Check if card is active
- `scopeActive()` - Query scope for active cards

### 9. **Order** Model
**Added Functions:**
- `invoice()` - Relationship to invoice
- `payments()` - Relationship to payments
- `paymentTracking()` - Relationship to payment tracking
- `getFormattedTotalPriceAttribute()` - Formatted total price
- `getFormattedOrderNumberAttribute()` - Formatted order number (00001)
- `getSoNumberAttribute()` - Get SO number from sales order
- `isPending()` - Check if order is pending
- `isApproved()` - Check if order is approved
- `isCompleted()` - Check if order is completed
- `isPaid()` - Check if order is paid
- `scopeByStatus()` - Query scope by status
- `scopePending()` - Query scope for pending orders
- `scopeApproved()` - Query scope for approved orders
- `scopeCompleted()` - Query scope for completed orders
- `scopeByType()` - Query scope by order type

### 10. **CustomBouquet** Model
**Added Functions:**
- `user()` - Relationship to user (type hint added)
- `getFormattedMoneyAmountAttribute()` - Formatted money amount
- `getUnitPriceAttribute()` - Unit price calculation
- `isActive()` - Check if bouquet is active
- `orders()` - Relationship to orders
- `scopeActive()` - Query scope for active bouquets
- `scopeByType()` - Query scope by bouquet type

### 11. **BouquetOccasion** Model
**Added Functions:**
- `isActive()` - Check if occasion is active
- `getFormattedBasePriceAttribute()` - Formatted base price
- `scopeActive()` - Query scope for active occasions
- `scopeBySlug()` - Query scope by slug

### 12. **CartItem** Model
**Added Functions:**
- `getFormattedTotalPriceAttribute()` - Formatted total price
- `getItemNameAttribute()` - Get item name
- `isCustomBouquet()` - Check if is custom bouquet
- `isProduct()` - Check if is product
- `scopeForUser()` - Query scope for user cart items

### 13. **Store** Model
**Added Functions:**
- `fillable` array defined
- `getFormattedContactNumberAttribute()` - Formatted contact number
- `getFullInfoAttribute()` - Full store information string
- `scopeByName()` - Query scope by store name

### 14. **User** Model
**Added Functions:**
- `getFullNameAttribute()` - Full name from first_name + last_name
- `getProfilePictureUrlAttribute()` - Profile picture URL (supports external URLs)
- `isAdmin()` - Check if user is admin
- `isClerk()` - Check if user is clerk
- `isCustomer()` - Check if user is customer
- `isDriver()` - Check if user is driver
- `isVerified()` - Check if user is verified
- `getFormattedContactNumberAttribute()` - Formatted contact number
- `scopeByRole()` - Query scope by role
- `scopeVerified()` - Query scope for verified users
- `loyaltyCard()` - Relationship to loyalty card
- `customBouquets()` - Relationship to custom bouquets

### 15. **OrderStatusHistory** Model
**Added Functions:**
- `changedBy()` - Relationship to user who changed status
- `getFormattedDateAttribute()` - Formatted created date
- `scopeForOrder()` - Query scope for specific order
- `scopeByStatus()` - Query scope by status

### 16. **LoyaltyStamp** Model
**Added Functions:**
- `getFormattedEarnedDateAttribute()` - Formatted earned date
- `scopeForCard()` - Query scope for loyalty card
- `scopeDateRange()` - Query scope by date range

### 17. **LoyaltyRedemption** Model
**Added Functions:**
- `getFormattedDiscountAmountAttribute()` - Formatted discount amount
- `getFormattedRedeemedDateAttribute()` - Formatted redeemed date
- `scopeForCard()` - Query scope for loyalty card
- `scopeDateRange()` - Query scope by date range

### 18. **Message** Model
**Added Functions:**
- `isRead()` - Check if message is read
- `markAsRead()` - Mark message as read
- `getFormattedSentDateAttribute()` - Formatted sent date
- `scopeUnread()` - Query scope for unread messages
- `scopeForUser()` - Query scope for user messages
- `scopeConversation()` - Query scope for conversation between users

### 19. **Favorite** Model
**Added Functions:**
- `isProduct()` - Check if favorite is for product
- `isCatalogProduct()` - Check if favorite is for catalog product
- `getItemAttribute()` - Get the favorited item
- `scopeForUser()` - Query scope for user favorites
- `scopeProducts()` - Query scope for product favorites
- `scopeCatalogProducts()` - Query scope for catalog product favorites

## Categories of Functions Added

### 1. **Accessors (Formatted Attributes)**
- All price fields now have `getFormattedXXXAttribute()` methods
- All date fields have formatted date methods
- Full address/formatted string methods

### 2. **Relationship Methods**
- All missing relationships added
- Proper type hints added
- Relationship methods completed

### 3. **Helper/Check Methods**
- Status checking methods (`isPending()`, `isApproved()`, etc.)
- Availability checking (`isAvailable()`, `isOutOfStock()`, etc.)
- Type checking (`isAdmin()`, `isCustomBouquet()`, etc.)

### 4. **Query Scopes**
- Filtering scopes (`scopeByStatus()`, `scopeByCategory()`, etc.)
- Date range scopes (`scopeDateRange()`)
- User-specific scopes (`scopeForUser()`)
- Status-specific scopes (`scopePending()`, `scopeCompleted()`, etc.)

### 5. **Business Logic Methods**
- Calculation methods (`getUnitPriceAttribute()`, `getProgressPercentageAttribute()`)
- Action methods (`markAsDefault()`, `markAsRead()`)
- Validation methods (`isEligibleForRedemption()`, `needsReorder()`)

## Benefits

1. **Consistency**: All models now follow the same patterns
2. **Reusability**: Common operations are now reusable methods
3. **Readability**: Code is cleaner and easier to understand
4. **Type Safety**: Proper return types and type hints added
5. **Query Efficiency**: Scopes make database queries more efficient
6. **Display Ready**: All models have formatted display methods

---

**Date**: 2025-10-29  
**Status**: ✅ Complete  
**Models Enhanced**: 19+ models

