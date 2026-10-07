# Sales & Lead Management Module Architecture & Implementation Plan

This document outlines the end-to-end workflow, role-based access control, database schema design, and production SQL execution code required to transition the Sales & Lead module to production.

---

## 1. Executive Summary & Codebase Integration

### Core Design Principles
- **Table Naming Convention**: All newly created sales tables use the prefix **`LRSales_`**.
- **Customer / Vendor Master Integration**: Use the existing **`LRvendor`** table and existing **`vendor.blade.php`** template (with modal **`modals/addCustomerAddress.blade.php`**). New customer addresses can be added directly via the existing "Add Address" button link.
- **Role Management**: Adopt and expand the existing **`userAdmins.blade.php`** view ([userAdmins.blade.php](file:///var/www/html/supportportal/resources/views/userAdmins.blade.php)) and modal ([manageAdmins.blade.php](file:///var/www/html/supportportal/resources/views/modals/manageAdmins.blade.php)) to manage roles 10 through 15 and menu permissions.

---

## 2. Role ID Mapping & Adoption in `userAdmins` (Role IDs 10 to X)

User roles for the sales module will be managed via `userAdmins.blade.php`:

| Role ID | Role Name | System Permissions & Responsibilities | `userAdmins` Dropdown Label |
| :--- | :--- | :--- | :--- |
| **1** | Disabled | Access disabled | Disable All Access |
| **2** | System Admin | Full administrative access across all modules | Support Portal Admin |
| **10** | Sales Executive | Creates leads, selects customer from `LRvendor`, drafts quotations | Sales Executive |
| **11** | Sales Head / Manager | Reviews executive leads, approves/rejects quotations | Sales Head / Manager |
| **12** | Accounts / Finance | Verifies payments, advance receipts, approves credit terms | Accounts / Finance |
| **13** | Managing Director (MD) | High-value quotation approvals, special discount overrides | Managing Director (MD) |
| **14** | Production Head | Receives confirmed orders, issues Work Orders (`LRSales_work_orders`) | Production / Dispatch Head |
| **15** | Service Engineer | Executes field installation tasks (`LRSales_installations`), updates setup status | Service / Field Engineer |

---

## 3. Database Schema Overview

```
+------------------+         +----------------------+         +---------------------------+
|     LRvendor     | <-------|    LRSales_leads     | <-------|  LRSales_lead_activities  |
+------------------+ 1     * +----------------------+ 1     * +---------------------------+
  (Existing Table)                      |
                                        | 1
                                        v *
                             +----------------------+ 1     * +---------------------------+
                             |  LRSales_quotations  |-------->|  LRSales_quotation_items  |
                             +----------------------+         +---------------------------+
                                        |
                                        | 1
                                        v 1
                             +----------------------+ 1     * +---------------------------+
                             |    LRSales_orders    |-------->|    LRSales_work_orders    |
                             +----------------------+         +---------------------------+
                                                                            |
                                                                            | 1
                                                                            v *
                                                              +---------------------------+
                                                              |   LRSales_installations   |
                                                              +---------------------------+
```

---

## 4. Production SQL Execution Script (Compatible with `LRvendor.id` & `users.id`)

> [!NOTE]
> `LRvendor.id` and `users.id` are defined as `INT` in your database. The foreign keys below use matching `INT` types to resolve MySQL Error #3780.

Run the following SQL script directly in MySQL for development and Production databases:

```sql
-- =============================================================================
-- Sales & Lead Module Production Database Creation Script
-- Engine: InnoDB | Character Set: utf8mb4_unicode_ci
-- Compatible with LRvendor (id INT) and users (id INT)
-- =============================================================================

-- 1. LRSales_leads Table
CREATE TABLE IF NOT EXISTS `LRSales_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `sales_executive_id` INT NOT NULL,
  `lead_source` VARCHAR(100) DEFAULT 'Website',
  `interested_products` TEXT NULL,
  `estimated_value` DECIMAL(12,2) DEFAULT 0.00,
  `status` ENUM('new', 'in_discussion', 'quotation_sent', 'confirmed', 'hold', 'rejected') DEFAULT 'new',
  `workflow_stage` ENUM('sales_executive', 'sales_head_review', 'accounts', 'md_approval', 'completed') DEFAULT 'sales_executive',
  `notes` TEXT NULL,
  `created_by` INT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_leads_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `LRvendor` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lrsales_leads_executive` FOREIGN KEY (`sales_executive_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. LRSales_lead_activities Table
CREATE TABLE IF NOT EXISTS `LRSales_lead_activities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `stage_from` VARCHAR(100) NULL,
  `stage_to` VARCHAR(100) NULL,
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_activities_lead` FOREIGN KEY (`lead_id`) REFERENCES `LRSales_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lrsales_activities_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. LRSales_quotations Table
CREATE TABLE IF NOT EXISTS `LRSales_quotations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `quotation_number` VARCHAR(50) NOT NULL UNIQUE,
  `lead_id` INT NOT NULL,
  `vendor_id` INT NOT NULL,
  `sales_executive_id` INT NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `gst_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `transport_charges` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `installation_charges` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('draft', 'submitted_to_head', 'approved_by_head', 'approved_by_accounts', 'approved_by_md', 'accepted_by_customer', 'rejected') DEFAULT 'draft',
  `created_by` INT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_quotations_lead` FOREIGN KEY (`lead_id`) REFERENCES `LRSales_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lrsales_quotations_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `LRvendor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. LRSales_quotation_items Table
CREATE TABLE IF NOT EXISTS `LRSales_quotation_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `quotation_id` INT NOT NULL,
  `product_id` INT NULL,
  `item_description` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `gst_rate` DECIMAL(5,2) NOT NULL DEFAULT 18.00,
  `total_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_qitems_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `LRSales_quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. LRSales_orders Table
CREATE TABLE IF NOT EXISTS `LRSales_orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `quotation_id` INT NOT NULL,
  `vendor_id` INT NOT NULL,
  `advance_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
  `status` ENUM('confirmed', 'in_production', 'ready_for_dispatch', 'dispatched', 'completed', 'cancelled') DEFAULT 'confirmed',
  `created_by` INT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_orders_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `LRSales_quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lrsales_orders_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `LRvendor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. LRSales_work_orders Table
CREATE TABLE IF NOT EXISTS `LRSales_work_orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `wo_number` VARCHAR(50) NOT NULL UNIQUE,
  `order_id` INT NOT NULL,
  `assigned_to` INT NULL,
  `quantity_units` INT NOT NULL DEFAULT 1,
  `status` ENUM('pending', 'in_assembly', 'testing_qc', 'completed') DEFAULT 'pending',
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_wo_order` FOREIGN KEY (`order_id`) REFERENCES `LRSales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. LRSales_installations Table
CREATE TABLE IF NOT EXISTS `LRSales_installations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `work_order_id` INT NOT NULL,
  `service_engineer_id` INT NULL,
  `scheduled_date` DATE NULL,
  `completion_date` DATE NULL,
  `status` ENUM('assigned', 'in_progress', 'completed', 'issues_reported') DEFAULT 'assigned',
  `customer_feedback` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lrsales_inst_wo` FOREIGN KEY (`work_order_id`) REFERENCES `LRSales_work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. Workflow Execution & Customer Address Creation

```
1. Add Customer Address (If not existing)
   └── Use `vendor.blade.php` -> Modal `modals/addCustomerAddress.blade.php` to insert record into `LRvendor`
       ▼
2. Lead Capture (Sales Exec - Role 10)
   └── Select Customer from `LRvendor` & Enter Lead details into `LRSales_leads`
       ▼
3. Draft Quotation (`LRSales_quotations` & `LRSales_quotation_items`)
       ▼
4. Multi-level Approvals (Sales Head -> Accounts -> MD)
       ▼
5. Convert to Sales Order (`LRSales_orders`)
       ▼
6. Generate Work Orders (`LRSales_work_orders`)
       ▼
7. Service Installation Task (`LRSales_installations`)
```

---

## 6. Next Steps

1. Execute the updated SQL script above in Production & local MySQL databases.
2. We will create Laravel migration files matching this exact SQL schema for local environment consistency.
3. Update `manageAdmins.blade.php` & `userAdmins.blade.php` with Roles 10–15.
4. Replace mock controllers with live Eloquent DB implementations.
