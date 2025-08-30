# 📚 LMS SaaS Backend

A **multi-tenant, role-based Learning Management System backend** built on **Laravel + Strapi CMS + Box + YouTube + Google Gemini AI + Stripe**.  
Supports **white-label frontends**, per-tenant customization, **package-based subscriptions**, and **AI-powered learning assistance**.

---

## 🚀 Features

### 🔑 Multi-Tenancy
- Row-level tenant isolation (`tenant_id` on all models)  
- Tenant-scoped settings, branding, quotas, and integrations  
- Domain, API key, or JWT-based tenant resolution  

### 👥 Role-Based Access Control (RBAC)
- **Super Admin**: Platform-level management, packages, integrations  
- **Tenant Admin**: Course planning, users, pricing, reports, AI assistant config  
- **Instructor**: Courses, class scheduling, assignments, grading, earnings  
- **Student**: Enrollment, progress, classes, submissions, certificates, payments  

### 📦 Course & Content
- Course hierarchy managed in **Strapi CMS** (Categories → Courses → Modules → Chapters)  
- Business logic (enrollment, scheduling, progress, analytics) in **Laravel**  
- Media storage via **Box** (tenant-scoped) + **YouTube** (unlisted videos)  

### 🤖 AI & Personalization
- Per-tenant **Google Gemini AI assistants**  
- Knowledge base training (FAQs, policies, course catalog)  
- Intelligent chat, recommendations, adaptive learning paths  

### 💳 Payments & Subscriptions
- Integrated **Stripe** subscriptions and billing  
- Package-based features, quotas, usage limits  
- Invoicing, refunds, dunning, multi-currency support  

### 🛠️ Integrations
- **Strapi CMS** for structured content  
- **Box Storage** for files & assignments  
- **YouTube API** for video lessons  
- **Gemini AI** for tenant-specific AI assistants  
- **Stripe** for global + tenant payments  

---

## 🏗️ Architecture

```
Frontend (White Label) ↔ Laravel API ↔ Strapi CMS
                                    ↔ Box Storage (per-tenant files)
                                    ↔ YouTube API (video content)
                                    ↔ Google Gemini AI (AI assistant)
                                    ↔ Stripe (payments)
                                    ↔ Redis (cache)
                                    ↔ MySQL (core data)
```

---

## 📋 Getting Started

### Prerequisites
- PHP 8.2 + Composer
- MySQL 8+
- Redis
- Node.js (for Strapi)
- Stripe, Box, YouTube, Gemini API keys

### Setup
```bash
# clone repo
git clone https://github.com/your-org/lms-saas-backend.git
cd lms-saas-backend

# install dependencies
composer install

# copy env
cp .env.example .env

# migrate database
php artisan migrate --seed

# run dev server
php artisan serve
```

---

## 📖 Documentation
- `/docs/lms-multitenant-rbac.md` → Multi-tenancy, roles, feature matrix  
- `/docs/api` → Swagger UI for API endpoints  
- `/docs/architecture` → System diagrams and integrations  

---

## ✅ Roadmap
- [x] Phase 1: Code optimization & base services  
- [x] Phase 2: Instructor endpoints  
- [ ] Phase 2: Student endpoints  
- [ ] Phase 3: Tenant & Super Admin dashboards  
- [ ] Phase 4: Subscription system  
- [ ] Phase 5: AI learning assistance  

---

## 🤝 Contributing
1. Fork this repo  
2. Create a feature branch (`git checkout -b feature/my-feature`)  
3. Commit changes (`git commit -m 'Add my feature'`)  
4. Push to branch (`git push origin feature/my-feature`)  
5. Create a Pull Request  

---

## 📜 License
MIT © Your Company
