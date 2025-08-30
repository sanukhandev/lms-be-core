# 📊 High-Level Data Model & Relationships

This document describes the **core entities, relationships, and table structures** for the LMS SaaS backend.

---

## 🏗️ Core Entities

### 1. Tenant
Represents an organization (school, company, client).
- Has many: Users, Courses, Subscriptions, Integrations
- Scoped by: `tenant_id`

### 2. User
Represents all system users (admins, instructors, students).
- Belongs to: Tenant
- Has many: Enrollments, ClassSessions, AssignmentSubmissions
- Role: SuperAdmin (global) / TenantAdmin / Instructor / Student

### 3. Course (Mirrored from Strapi)
Course structure & metadata.
- Belongs to: Tenant
- Has many: Modules, Enrollments, Assignments, ClassSessions
- Synced from Strapi

### 4. Module & Chapter
Course breakdown.
- Module → belongs to Course
- Chapter → belongs to Module
- Chapters reference media (Box files, YouTube videos)

### 5. Enrollment
Link between Student ↔ Course.
- Belongs to: Tenant, User (student), Course
- Tracks: status, start, expiry, progress

### 6. ClassSession
Scheduled live/virtual classes.
- Belongs to: Tenant, Course, Instructor
- Has many: Attendance records

### 7. Attendance
Student participation in ClassSession.
- Belongs to: ClassSession, User (student)

### 8. Assignment
Instructor-created coursework.
- Belongs to: Course
- Has many: AssignmentSubmissions

### 9. AssignmentSubmission
Student’s uploaded solution.
- Belongs to: Assignment, Student (User), Tenant
- Stores: Box file ID, grade, feedback

### 10. Orders & Payments
Purchase records via Stripe.
- Order belongs to: Tenant, User
- Order has many: OrderItems (courses, packages)
- Subscription: tenant-level, package-driven

### 11. InstructorEarning
Instructor payout records.
- Belongs to: Instructor (User), Tenant
- Linked to: Orders and revenue share

### 12. Integrations
Tenant-specific configs (Box, YouTube, Gemini, Stripe).
- Belongs to: Tenant
- One-to-one with each service

---

## 🔗 Relationship Diagram (Conceptual)

```
Tenant 1---* User
Tenant 1---* Course 1---* Module 1---* Chapter
Course 1---* Enrollment *---1 User(Student)
Course 1---* ClassSession *---* User(Student)
Course 1---* Assignment 1---* AssignmentSubmission
Course 1---* Resource (Box/YT)
User(Instructor) 1---* ClassSession
User(Student) 1---* Enrollment
Order 1---* OrderItem (Course/Package)
Tenant 1---* Subscription (Stripe)
Instructor 1---* InstructorEarning
Tenant 1---1 Integrations
```

---

## 📋 Tables & Relations

| Table                   | PK         | FK(s)                                  | Relationships |
|--------------------------|------------|----------------------------------------|---------------|
| tenants                 | id         | –                                      | hasMany(users, courses, subscriptions, integrations) |
| tenant_settings         | id         | tenant_id                              | belongsTo(tenant) |
| tenant_integrations     | id         | tenant_id                              | belongsTo(tenant) |
| users                   | id         | tenant_id, role_id?                    | belongsTo(tenant); hasMany(enrollments, submissions) |
| roles                   | id         | tenant_id                              | belongsTo(tenant); hasMany(role_permissions) |
| role_permissions        | id         | role_id, tenant_id                     | belongsTo(role) |
| courses                 | id         | tenant_id, strapi_course_id            | belongsTo(tenant); hasMany(modules, enrollments, sessions) |
| modules                 | id         | tenant_id, course_id                   | belongsTo(course) |
| chapters                | id         | tenant_id, module_id                   | belongsTo(module) |
| enrollments             | id         | tenant_id, user_id, course_id          | belongsTo(user, course, tenant) |
| class_sessions          | id         | tenant_id, course_id, instructor_id    | belongsTo(course, instructor) |
| class_attendance        | id         | tenant_id, session_id, user_id         | belongsTo(class_session, user) |
| assignments             | id         | tenant_id, course_id                   | belongsTo(course) |
| assignment_submissions  | id         | tenant_id, assignment_id, student_id   | belongsTo(assignment, user) |
| orders                  | id         | tenant_id, user_id                     | belongsTo(tenant, user); hasMany(order_items) |
| order_items             | id         | order_id, tenant_id, item_ref_id       | belongsTo(order) |
| subscriptions           | id         | tenant_id, package_id                  | belongsTo(tenant, package) |
| instructor_earnings     | id         | tenant_id, instructor_id               | belongsTo(user) |
| audit_logs              | id         | tenant_id, actor_id                    | belongsTo(user) |

---

## ✅ Notes
- **tenant_id** required in all tenant-owned tables.  
- **Super Admin** operations bypass `tenant_id`.  
- **Laravel global scope** ensures tenant isolation.  
- Strapi is **source of truth** for course hierarchy, mirrored in Laravel for business logic.  
