# ClickUp-Style Task Manager

A modern, Laravel 13-based task management system inspired by ClickUp's clean and professional design.

## Features

### ✅ Completed (Specs 1-4)

#### Authentication & Authorization
- **Disabled Public Registration** - Admin-controlled user access
- **Three Role System**: Admin, Manager, Member with granular permissions
- **Laravel Breeze** authentication scaffolding
- **Spatie Laravel Permission** for role management

#### Workspace Management
- Multi-tenant workspace architecture
- Workspace creation and management
- User-workspace associations
- Soft deletes for data recovery

#### Project Management
- Projects organized within workspaces
- Project creation with workspace context
- Customizable project settings
- Comprehensive project views

#### Full Task Management System
- **Multiple Views**: Kanban Board, List View, Calendar View, My Tasks
- **Task Features**:
  - Multiple assignees and collaborators support
  - Priority levels (Urgent, High, Normal, Low)
  - Custom statuses with colors
  - Due dates and reminders
  - Descriptions and checklists
  - File attachments
  - Tags for organization
  - Comments and activity logging
- **Drag & Drop** functionality on Kanban board
- **Advanced Filtering** by status, assignee, priority
- **Real-time Updates** with Livewire

### 🎨 Design System

#### ClickUp-Inspired Interface
- **Purple/Pink Gradient Theme** - Modern and professional
- **Light Mode Only** - Clean, distraction-free workspace
- **Collapsible Sidebar** - Maximizes workspace real estate
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Smooth Animations** - Polished user experience

#### Component Library
- Reusable Blade components
- Consistent button styles
- Standardized form inputs
- Clean card designs
- Professional modals

## Tech Stack

- **Laravel 13** - Latest PHP framework
- **Livewire 3** - Dynamic, reactive components
- **Alpine.js** - Lightweight JavaScript framework
- **Tailwind CSS** - Utility-first styling
- **SQLite** - Lightweight database (development)
- **Pest PHP** - Modern testing framework
- **Spatie Packages**:
  - Laravel Permission (roles & permissions)
  - Laravel Activity Log (audit trail)

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd taskmanager
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

### Default Users (After Seeding)
- **Admin**: admin@example.com / password
- **Manager**: manager@example.com / password
- **Member**: member@example.com / password

## Database Architecture

### Core Tables
- `workspaces` - Multi-tenant workspaces
- `projects` - Projects within workspaces
- `tasks` - Tasks with full feature set
- `task_statuses` - Customizable status workflow
- `task_lists` - Task organization (auto-created)
- `task_assignees` - Multiple assignee support
- `task_attachments` - File uploads
- `tags` - Task categorization
- `comments` - Task discussions
- `checklist_items` - Sub-tasks

### Key Features
- **Soft Deletes** on workspaces, projects, tasks
- **Foreign Keys** with cascade rules
- **Indexes** on frequently queried columns
- **Polymorphic Relations** for comments, attachments, activity logs
- **UTC Timestamps** for all dates

## Code Standards

### PSR-12 Compliant
- `declare(strict_types=1)` on all PHP files
- Laravel Pint for code formatting

### Architecture Patterns
- **Thin Controllers/Components** - Business logic in Actions/Services
- **Enums** for fixed values (Priority, Status types)
- **Policies** for authorization
- **Factories** for testing and seeding
- **Form Requests** for validation

### Performance
- **N+1 Prevention** - Eager loading relationships
- **Pagination** on all lists
- **Queue System** for emails and notifications
- **Database Queue Driver**

### Security
- Internal tool - no public registration
- Private storage for attachments
- Rate limiting on auth endpoints
- No hardcoded secrets
- Blade escaping by default

## Testing

Run the test suite:
```bash
vendor/bin/pest
```

Run with coverage:
```bash
vendor/bin/pest --coverage
```

## Documentation

- **DESIGN_UPDATES.md** - Complete design system documentation
- **CLAUDE.md** - Project rules and conventions
- **AGENTS.md** - Laravel Boost integration

## Roadmap

### Completed ✅
- [x] Spec 1: Foundation (Auth, Roles, Workspaces)
- [x] Spec 2: Core Structure (Projects, Lists, Tasks, Statuses)
- [x] Spec 3: Task Management UI (Kanban, List, Calendar, My Tasks)
- [x] Spec 4: Advanced Features (Multiple assignees, collaborators)
- [x] ClickUp-style design system implementation
- [x] Dark mode removal and purple/pink gradient theme

### Planned 🚧
- [ ] Spec 5: Real-time notifications
- [ ] Spec 6: Team collaboration features
- [ ] Spec 7: Advanced reporting and analytics
- [ ] Spec 8: API for third-party integrations
- [ ] Mobile responsive improvements
- [ ] Advanced search functionality
- [ ] Task templates
- [ ] Time tracking
- [ ] Gantt chart view

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
