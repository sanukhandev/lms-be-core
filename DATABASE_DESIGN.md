# LMS Database Design - 3NF Normalized Schema

## Overview
This document describes the complete database schema for the multi-tenant Learning Management System (LMS). The database follows Third Normal Form (3NF) principles for optimal data integrity and performance.

## Architecture Principles
- **Multi-tenancy**: Row-level isolation using `tenant_id` in all tables
- **3NF Normalization**: Elimination of transitive dependencies
- **Referential Integrity**: Proper foreign key constraints
- **Performance Optimization**: Strategic indexing
- **Audit Trail**: Comprehensive logging capability

---

## Core System Tables

### 1. tenants
**Purpose**: Central tenant management for multi-tenant architecture
```sql
- id (varchar, primary key)
- data (json) - tenant configuration
- created_at, updated_at (timestamps)
```
**Indexes**: Primary key on `id`

### 2. domains
**Purpose**: Domain mapping for tenant routing
```sql
- id (bigint, auto-increment primary key)
- domain (varchar, unique)
- tenant_id (varchar, foreign key → tenants.id)
- created_at, updated_at (timestamps)
```
**Relationships**: 
- Many-to-one with `tenants`

---

## Package & Subscription Management

### 3. packages
**Purpose**: SaaS subscription packages/plans
```sql
- id (bigint, auto-increment primary key)
- name (varchar, unique)
- description (text, nullable)
- price (decimal 10,2)
- billing_period (enum: monthly, yearly, lifetime)
- is_active (boolean, default true)
- sort_order (integer, default 0)
- created_at, updated_at (timestamps)
```
**Business Rules**: Base subscription tiers

### 4. package_features
**Purpose**: Features included in each package (normalized)
```sql
- id (bigint, auto-increment primary key)
- package_id (bigint, foreign key → packages.id)
- feature_name (varchar)
- feature_value (varchar, nullable)
- is_enabled (boolean, default true)
- created_at, updated_at (timestamps)
```
**Relationships**: Many-to-one with `packages`
**Unique Constraint**: `package_id + feature_name`

### 5. package_quotas
**Purpose**: Resource limits per package (normalized)
```sql
- id (bigint, auto-increment primary key)
- package_id (bigint, foreign key → packages.id)
- resource_type (varchar) - e.g., 'max_courses', 'max_users'
- quota_limit (integer, nullable) - null = unlimited
- created_at, updated_at (timestamps)
```
**Relationships**: Many-to-one with `packages`
**Unique Constraint**: `package_id + resource_type`

### 6. subscriptions
**Purpose**: Active tenant subscriptions
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- package_id (bigint, foreign key → packages.id)
- status (enum: active, cancelled, expired, suspended)
- started_at (timestamp)
- expires_at (timestamp, nullable)
- auto_renew (boolean, default true)
- stripe_subscription_id (varchar, nullable)
- created_at, updated_at (timestamps)
```
**Relationships**: 
- Many-to-one with `tenants`
- Many-to-one with `packages`

### 7. subscription_usage
**Purpose**: Track resource usage against quotas
```sql
- id (bigint, auto-increment primary key)
- subscription_id (bigint, foreign key → subscriptions.id)
- resource_type (varchar)
- current_usage (integer, default 0)
- last_reset_at (timestamp, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `subscription_id + resource_type`

---

## Tenant Configuration

### 8. tenant_settings
**Purpose**: Tenant-specific configuration settings
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- setting_key (varchar)
- setting_value (text, nullable)
- setting_type (enum: string, integer, boolean, json)
- is_encrypted (boolean, default false)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + setting_key`

### 9. tenant_branding
**Purpose**: Tenant white-label customization
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id, unique)
- logo_url (varchar, nullable)
- favicon_url (varchar, nullable)
- primary_color (varchar, nullable)
- secondary_color (varchar, nullable)
- custom_css (longtext, nullable)
- email_template_header (text, nullable)
- email_template_footer (text, nullable)
- created_at, updated_at (timestamps)
```
**Relationship**: One-to-one with `tenants`

---

## Integration Management

### 10. integration_providers
**Purpose**: Available third-party integrations
```sql
- id (bigint, auto-increment primary key)
- name (varchar, unique)
- display_name (varchar)
- description (text, nullable)
- icon_url (varchar, nullable)
- configuration_schema (json, nullable)
- is_active (boolean, default true)
- created_at, updated_at (timestamps)
```

### 11. tenant_integrations
**Purpose**: Tenant-specific integration configurations
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- provider_id (bigint, foreign key → integration_providers.id)
- configuration (json, nullable)
- is_enabled (boolean, default true)
- last_sync_at (timestamp, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + provider_id`

---

## User Management

### 12. users (Extended Laravel default)
**Purpose**: System users with role-based access
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- email (varchar, unique per tenant)
- email_verified_at (timestamp, nullable)
- password (varchar)
- first_name (varchar)
- last_name (varchar)
- phone (varchar, nullable)
- avatar_url (varchar, nullable)
- bio (text, nullable)
- timezone (varchar, default 'UTC')
- language (varchar, default 'en')
- status (enum: active, inactive, suspended)
- last_login_at (timestamp, nullable)
- email_notifications (boolean, default true)
- sms_notifications (boolean, default false)
- two_factor_enabled (boolean, default false)
- remember_token (varchar, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + email`

---

## Content Organization

### 13. categories
**Purpose**: Course categorization (hierarchical)
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- parent_id (bigint, foreign key → categories.id, nullable)
- name (varchar)
- slug (varchar)
- description (text, nullable)
- icon_url (varchar, nullable)
- sort_order (integer, default 0)
- is_active (boolean, default true)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + slug`

### 14. tags
**Purpose**: Content tagging system
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- name (varchar)
- slug (varchar)
- color (varchar, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + slug`

---

## Course Structure

### 15. courses
**Purpose**: Main course entity
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- strapi_course_id (varchar, nullable) - CMS integration
- category_id (bigint, foreign key → categories.id)
- instructor_id (bigint, foreign key → users.id)
- title (varchar)
- slug (varchar)
- short_description (text, nullable)
- description (longtext, nullable)
- thumbnail_url (varchar, nullable)
- level (enum: beginner, intermediate, advanced)
- status (enum: draft, review, published, archived)
- price (decimal 10,2, default 0)
- estimated_duration_hours (integer, nullable)
- language (varchar, default 'en')
- is_featured (boolean, default false)
- is_free (boolean, default false)
- sort_order (integer, default 0)
- published_at (timestamp, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + slug`

### 16. course_tags
**Purpose**: Many-to-many relationship between courses and tags
```sql
- id (bigint, auto-increment primary key)
- course_id (bigint, foreign key → courses.id)
- tag_id (bigint, foreign key → tags.id)
- created_at (timestamp)
```
**Unique Constraint**: `course_id + tag_id`

### 17. course_learning_objectives
**Purpose**: Normalized learning objectives per course
```sql
- id (bigint, auto-increment primary key)
- course_id (bigint, foreign key → courses.id)
- objective (text)
- sort_order (integer, default 0)
- created_at, updated_at (timestamps)
```

### 18. course_prerequisites
**Purpose**: Normalized prerequisites per course
```sql
- id (bigint, auto-increment primary key)
- course_id (bigint, foreign key → courses.id)
- prerequisite (text)
- is_required (boolean, default true)
- sort_order (integer, default 0)
- created_at, updated_at (timestamps)
```

### 19. modules
**Purpose**: Course modules/sections
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- course_id (bigint, foreign key → courses.id)
- title (varchar)
- description (text, nullable)
- sort_order (integer, default 0)
- is_published (boolean, default false)
- created_at, updated_at (timestamps)
```

### 20. content_types
**Purpose**: Supported content types
```sql
- id (bigint, auto-increment primary key)
- name (varchar, unique)
- mime_type (varchar)
- icon_url (varchar, nullable)
- is_active (boolean, default true)
- created_at, updated_at (timestamps)
```

### 21. chapters
**Purpose**: Individual lessons/chapters within modules
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- module_id (bigint, foreign key → modules.id)
- content_type_id (bigint, foreign key → content_types.id)
- title (varchar)
- content (longtext, nullable)
- video_url (varchar, nullable)
- duration_minutes (integer, nullable)
- sort_order (integer, default 0)
- is_published (boolean, default false)
- is_free_preview (boolean, default false)
- created_at, updated_at (timestamps)
```

### 22. media
**Purpose**: Media assets for courses
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- course_id (bigint, foreign key → courses.id, nullable)
- chapter_id (bigint, foreign key → chapters.id, nullable)
- filename (varchar)
- original_filename (varchar)
- file_path (varchar)
- file_size (bigint)
- mime_type (varchar)
- storage_provider (enum: local, box, s3, youtube)
- external_id (varchar, nullable)
- metadata (json, nullable)
- created_at, updated_at (timestamps)
```

---

## Student Progress & Enrollment

### 23. enrollments
**Purpose**: Student course enrollments
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- course_id (bigint, foreign key → courses.id)
- student_id (bigint, foreign key → users.id)
- enrolled_at (timestamp)
- completed_at (timestamp, nullable)
- progress_percentage (decimal 5,2, default 0.00)
- last_accessed_at (timestamp, nullable)
- status (enum: active, completed, dropped, suspended)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + course_id + student_id`

### 24. chapter_progress
**Purpose**: Individual chapter completion tracking
```sql
- id (bigint, auto-increment primary key)
- enrollment_id (bigint, foreign key → enrollments.id)
- chapter_id (bigint, foreign key → chapters.id)
- started_at (timestamp, nullable)
- completed_at (timestamp, nullable)
- time_spent_minutes (integer, default 0)
- progress_percentage (decimal 5,2, default 0.00)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `enrollment_id + chapter_id`

---

## Live Classes

### 25. class_sessions
**Purpose**: Scheduled live class sessions
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- course_id (bigint, foreign key → courses.id)
- instructor_id (bigint, foreign key → users.id)
- title (varchar)
- description (text, nullable)
- scheduled_start (timestamp)
- scheduled_end (timestamp)
- actual_start (timestamp, nullable)
- actual_end (timestamp, nullable)
- meeting_url (varchar, nullable)
- meeting_id (varchar, nullable)
- status (enum: scheduled, live, completed, cancelled)
- max_participants (integer, nullable)
- created_at, updated_at (timestamps)
```

### 26. class_attendance
**Purpose**: Student attendance tracking for live sessions
```sql
- id (bigint, auto-increment primary key)
- session_id (bigint, foreign key → class_sessions.id)
- student_id (bigint, foreign key → users.id)
- joined_at (timestamp, nullable)
- left_at (timestamp, nullable)
- attendance_duration_minutes (integer, default 0)
- status (enum: present, absent, late, left_early)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `session_id + student_id`

---

## Assignments & Assessment

### 27. assignment_types
**Purpose**: Different types of assignments
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- name (varchar)
- description (text, nullable)
- icon_url (varchar, nullable)
- default_points (decimal 8,2, nullable)
- is_active (boolean, default true)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + name`

### 28. assignments
**Purpose**: Course assignments and assessments
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- course_id (bigint, foreign key → courses.id)
- assignment_type_id (bigint, foreign key → assignment_types.id)
- title (varchar)
- description (longtext, nullable)
- instructions (longtext, nullable)
- due_date (timestamp, nullable)
- points_possible (decimal 8,2, default 0.00)
- attempts_allowed (integer, default 1)
- time_limit_minutes (integer, nullable)
- is_published (boolean, default false)
- allow_late_submission (boolean, default true)
- late_penalty_percent (decimal 5,2, default 0.00)
- created_at, updated_at (timestamps)
```

### 29. assignment_submissions
**Purpose**: Student assignment submissions
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- assignment_id (bigint, foreign key → assignments.id)
- student_id (bigint, foreign key → users.id)
- attempt_number (integer, default 1)
- status (enum: draft, submitted, graded, returned, late)
- content (longtext, nullable)
- submitted_at (timestamp, nullable)
- graded_at (timestamp, nullable)
- points_earned (decimal 8,2, nullable)
- points_possible (decimal 8,2, nullable)
- instructor_feedback (text, nullable)
- is_late (boolean, default false)
- late_penalty_applied (decimal 5,2, default 0.00)
- time_spent_minutes (integer, nullable)
- created_at, updated_at (timestamps)
```
**Unique Constraint**: `tenant_id + assignment_id + student_id + attempt_number` (custom name: `assign_sub_tenant_assign_student_attempt_unique`)

---

## E-commerce & Orders

### 30. orders
**Purpose**: Course purchase orders
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- user_id (bigint, foreign key → users.id)
- order_number (varchar, unique)
- status (enum: pending, paid, failed, refunded, cancelled)
- subtotal (decimal 10,2)
- tax_amount (decimal 10,2, default 0.00)
- discount_amount (decimal 10,2, default 0.00)
- total_amount (decimal 10,2)
- currency (varchar, default 'USD')
- payment_method (varchar, nullable)
- stripe_payment_intent_id (varchar, nullable)
- paid_at (timestamp, nullable)
- created_at, updated_at (timestamps)
```

### 31. order_items
**Purpose**: Individual items within orders
```sql
- id (bigint, auto-increment primary key)
- order_id (bigint, foreign key → orders.id)
- course_id (bigint, foreign key → courses.id)
- price (decimal 10,2)
- quantity (integer, default 1)
- created_at, updated_at (timestamps)
```

---

## Certificates

### 32. certificates
**Purpose**: Course completion certificates
```sql
- id (bigint, auto-increment primary key)
- tenant_id (varchar, foreign key → tenants.id)
- enrollment_id (bigint, foreign key → enrollments.id)
- certificate_number (varchar, unique)
- issued_at (timestamp)
- expires_at (timestamp, nullable)
- template_data (json, nullable)
- pdf_path (varchar, nullable)
- verification_url (varchar, nullable)
- is_revoked (boolean, default false)
- revoked_at (timestamp, nullable)
- revoked_reason (text, nullable)
- created_at, updated_at (timestamps)
```

---

## Security & Permissions

### 33. Permission Tables (Spatie Laravel-Permission)
Generated by package migration:
- `permissions`
- `roles`
- `model_has_permissions`
- `model_has_roles`
- `role_has_permissions`

---

## Performance Indexes Summary

### Strategic Indexing for Query Optimization:

1. **Tenant Isolation**: All tables have `tenant_id` indexes
2. **Foreign Key Performance**: All foreign keys are indexed
3. **Search Optimization**: Text fields used in search have indexes
4. **Status Filtering**: Status enums combined with dates
5. **Sorting Optimization**: `sort_order` fields indexed
6. **Unique Constraints**: Business logic enforcement

### Example Complex Indexes:
```sql
-- Course discovery
INDEX(tenant_id, status, published_at)
INDEX(tenant_id, category_id, status)
INDEX(level, is_featured)

-- Student progress
INDEX(enrollment_id, completed_at)
INDEX(tenant_id, student_id)

-- Assignment tracking
INDEX(assignment_id, status)
INDEX(submitted_at, graded_at)
```

---

## Data Integrity Rules

### Cascade Rules:
- **Tenant deletion**: CASCADE on all tenant-scoped data
- **Course deletion**: CASCADE on dependent content
- **User deletion**: RESTRICT on active enrollments
- **Category deletion**: RESTRICT if courses exist

### Business Constraints:
- Unique slugs per tenant
- Valid email format per tenant
- Price must be >= 0
- Progress percentage 0-100
- Enrollment before assignment submission

---

## Future Extensibility

The schema supports future enhancements:
- **Multi-language content**: Language field in courses
- **Advanced analytics**: JSON metadata fields
- **External integrations**: Provider configuration schema
- **Custom fields**: JSON settings in tenant_settings
- **Audit trails**: Timestamps and user tracking ready

This design provides a solid foundation for a scalable, multi-tenant LMS with proper normalization and performance optimization.
