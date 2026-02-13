# gvv-invitation


todo:

- alter db so that invited_users can store first and lastname from invitee


/project-root
│
├── /public                → Only web-accessible files
│   ├── index.php          → Landing page (header + admin login)
│   ├── login.php
│   ├── logout.php
│   ├── register.php       → Registration via invite
│   ├── dashboard.php      → Admin area
│   └── export.php         → CSV download
│
├── /app
│   ├── config.php         → DB credentials
│   ├── db.php             → PDO connection
│   │
│   ├── /controllers
│   │   ├── AuthController.php
│   │   ├── InviteController.php
│   │   └── ExportController.php
│   │
│   ├── /models
│   │   ├── Admin.php
│   │   ├── Invite.php
│   │   └── User.php
│   │
│   ├── /services
│   │   ├── AuthService.php
│   │   ├── InviteService.php
│   │   └── CsvService.php
│   │
│   └── helpers.php
│
├── /templates
│   ├── header.php
│   ├── footer.php
│   └── components/
│
└── /storage
    └── logs/