# CSE370-Project
### Directory structure
```
student_alumni/
│── config/
│   └── db.php              # Database connection file
│
│── includes/
│   ├── header.php          # Common header/navigation
│   ├── footer.php          # Common footer
│   ├── auth.php            # Session/login check
│   ├── admin_auth.php      
│   ├── admin_header.php     
│
│── public/                 # Public-facing pages
│   ├── index.php           # Homepage
│   ├── login.php           # User login
│   ├── register.php        # User registration
│   ├── events.php          # Show events
│   ├── chat.php            # Chat interface
│   ├── profile.php         # View/edit user profile
│   ├── logout.php          # User Logout
│
│── student/                # Student-specific pages
│   ├── dashboard.php
│   ├── verification.php
│
│── alumni/                 # Alumni-specific pages
│   ├── dashboard.php
│   ├── verification.php
│
│── admin/                  # Admin-only pages
│   ├── dashboard.php
│   ├── admin_login.php
│   ├── event_verification.php
│   ├── student_alumni_info.php
│   ├── verification_request.php
│   ├── admin_logout.php
│
│── css/
│   └── style.css           # Main styling
│
│── assets/
│   ├── images/             # Images
│   └── uploads/            # User uploads
│
│── project370.sql          # Your final schema
│
└── README.md               # Project documentation
```
