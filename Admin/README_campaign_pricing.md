# Campaign Pricing System

## Overview
The Campaign Pricing System allows administrators to manage pricing options for campaign requests. Admins can set different pricing tiers based on campaign duration (e.g., 50 pesos for 3 days, 100 pesos for 7 days).

## Features

### 1. Pricing Management
- **Add New Pricing Options**: Admins can create new pricing tiers with custom duration and price
- **Edit Existing Options**: Modify duration, price, and description of existing pricing options
- **Delete Options**: Soft delete pricing options (sets is_active to 0)
- **View All Options**: See all current pricing options in a table format

### 2. Database Structure

#### Table: `campaign_pricing`
```sql
CREATE TABLE `campaign_pricing` (
  `pricing_id` int(11) NOT NULL AUTO_INCREMENT,
  `duration_days` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pricing_id`)
);
```

### 3. API Endpoints

#### GET `/Admin/api/get_campaign_pricing.php`
- **Purpose**: Fetch all active pricing options
- **Response**: JSON array of pricing options
- **Authentication**: Not required

#### POST `/Admin/api/add_campaign_pricing.php`
- **Purpose**: Add new pricing option
- **Parameters**: 
  - `duration_days` (int): Number of days
  - `price` (decimal): Price in pesos
  - `description` (string): Optional description
- **Authentication**: Admin session required

#### PUT `/Admin/api/update_campaign_pricing.php`
- **Purpose**: Update existing pricing option
- **Parameters**: 
  - `pricing_id` (int): ID of pricing option
  - `duration_days` (int): Number of days
  - `price` (decimal): Price in pesos
  - `description` (string): Optional description
- **Authentication**: Admin session required

#### DELETE `/Admin/api/delete_campaign_pricing.php`
- **Purpose**: Soft delete pricing option
- **Parameters**: 
  - `pricing_id` (int): ID of pricing option
- **Authentication**: Admin session required

### 4. User Interface

#### Access
- Navigate to Admin Dashboard → Campaigns
- Click "Manage Pricing" button

#### Features
- **Add Form**: Input fields for duration, price, and description
- **Pricing Table**: Shows all current options with edit/delete actions
- **Edit Modal**: Popup form for editing existing options
- **Responsive Design**: Works on desktop and mobile devices

### 5. Default Pricing Options
The system comes with these default pricing options:
- 3 Days: ₱50.00
- 7 Days: ₱100.00
- 14 Days: ₱180.00
- 30 Days: ₱300.00

### 6. Files Structure
```
Admin/
├── api/
│   ├── get_campaign_pricing.php
│   ├── add_campaign_pricing.php
│   ├── update_campaign_pricing.php
│   └── delete_campaign_pricing.php
├── assets/
│   ├── css/
│   │   └── pricing.css
│   └── js/
│       └── pricing.js
├── includes/
│   └── campaigns.php (updated)
└── README_campaign_pricing.md
```

### 7. Installation

1. **Database Setup**:
   ```sql
   -- Run the campaign_pricing.sql file
   source campaign_pricing.sql;
   ```

2. **File Upload**:
   - Upload all PHP files to their respective directories
   - Upload CSS and JS files to the assets folder

3. **Access**:
   - Navigate to `/Admin/includes/campaigns.php`
   - Click "Manage Pricing" button

### 8. Security Features
- **Admin Authentication**: All modification operations require admin session
- **Input Validation**: Server-side validation for all inputs
- **Soft Delete**: Pricing options are soft deleted (not permanently removed)
- **CSRF Protection**: Form submissions include session validation

### 9. Future Enhancements
- Integration with campaign approval process
- Payment gateway integration
- Campaign analytics based on pricing tiers
- Bulk pricing operations
- Pricing history tracking

## Usage Example

1. **Adding a New Pricing Option**:
   - Click "Manage Pricing"
   - Fill in duration (e.g., 5 days)
   - Fill in price (e.g., 75.00)
   - Add description (e.g., "5 Days Campaign")
   - Click "Add Pricing Option"

2. **Editing an Existing Option**:
   - Click the edit button (pencil icon) next to any pricing option
   - Modify the values in the popup form
   - Click "Update Pricing"

3. **Deleting an Option**:
   - Click the delete button (trash icon) next to any pricing option
   - Confirm the deletion

## Troubleshooting

### Common Issues
1. **Modal not opening**: Check if Bootstrap JS is loaded
2. **API errors**: Verify admin session is active
3. **Database errors**: Ensure campaign_pricing table exists
4. **Styling issues**: Check if pricing.css is properly linked

### Error Messages
- "Unauthorized": Admin session required
- "Invalid data": Required fields missing
- "Failed to add/update/delete": Database connection issue 