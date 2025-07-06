# Laravel API JSON Structures for Vue App

## Headers Required for All Authenticated Requests
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}" // Optional, falls back to user's company
}
```

---

## Authentication Endpoints

### 1. Login
**POST** `/api/v1/auth/login`

**Request:**
```json
{
  "email": "admin@company.com",
  "password": "password123",
  "remember": false
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "data": {
    "user": {
      "id": "uuid-here",
      "email": "admin@company.com",
      "role": "admin",
      "is_active": true,
      "employee": {
        "id": "uuid-here",
        "first_name": "John",
        "last_name": "Doe",
        "full_name": "John Doe",
        "avatar": "https://app.com/storage/avatars/photo.jpg",
        "job_title": "System Administrator"
      }
    },
    "company": {
      "id": "uuid-here",
      "name": "Acme Corporation",
      "logo": "https://app.com/storage/logos/logo.png",
      "timezone": "America/New_York",
      "currency": {
        "code": "USD",
        "symbol": "$"
      }
    },
    "token": "1|laravel_sanctum_token_here",
    "token_type": "Bearer",
    "expires_in": 1440
  },
  "meta": {
    "company_id": "uuid-here",
    "user_role": "admin",
    "permissions": [
      "employees.*",
      "departments.*",
      "payroll.*"
    ]
  }
}
```

### 2. Register Company
**POST** `/api/v1/auth/register-company`

**Request:**
```json
{
  "company_name": "New Company",
  "company_email": "contact@newcompany.com",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@newcompany.com",
  "password": "password123",
  "password_confirmation": "password123",
  "terms_accepted": true,
  "privacy_accepted": true,
  "timezone": "America/New_York",
  "currency_id": 1
}
```

### 3. Get Current User
**GET** `/api/v1/auth/me`

**Response (200):**
```json
{
  "data": {
    "user": {
      "id": "uuid-here",
      "email": "admin@company.com",
      "role": "admin",
      "employee": { /* employee data */ }
    },
    "company": { /* company data */ },
    "permissions": ["employees.*", "departments.*"]
  },
  "meta": {
    "company_id": "uuid-here",
    "user_role": "admin"
  }
}
```

### 4. Logout
**POST** `/api/v1/auth/logout`

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

## Employee Management

### 1. Get Employees List
**GET** `/api/v1/employees?per_page=15&status=active&search=john`

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-here",
      "employee_id": "EMP001",
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "email": "john@company.com",
      "phone": "+1234567890",
      "avatar": "https://app.com/storage/avatars/john.jpg",
      "hire_date": "2023-01-15",
      "job_title": "Software Developer",
      "employment_type": "full_time",
      "status": "active",
      "department": {
        "id": "uuid-here",
        "name": "Engineering"
      },
      "position": {
        "id": "uuid-here",
        "title": "Senior Developer"
      },
      "manager": {
        "id": "uuid-here",
        "full_name": "Jane Smith"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 67
  }
}
```

### 2. Create Employee
**POST** `/api/v1/employees`

**Request:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@company.com",
  "phone": "+1234567890",
  "hire_date": "2023-12-01",
  "job_title": "Software Developer",
  "employment_type": "full_time",
  "department_id": "uuid-here",
  "position_id": "uuid-here",
  "manager_id": "uuid-here",
  "salary": 75000,
  "create_user_account": true,
  "password": "password123",
  "role": "employee"
}
```

**Response (201):**
```json
{
  "message": "Employee created successfully",
  "data": {
    "id": "uuid-here",
    "employee_id": "EMP001",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@company.com",
    "hire_date": "2023-12-01",
    "job_title": "Software Developer",
    "status": "active"
  }
}
```

---

## Attendance Management

### 1. Clock In
**POST** `/api/v1/attendance/checkin`

**Request:**
```json
{
  "location": "Office - Main Building",
  "notes": "Started early today"
}
```

**Response (201):**
```json
{
  "message": "Check-in successful",
  "data": {
    "id": "uuid-here",
    "employee": {
      "id": "uuid-here",
      "full_name": "John Doe"
    },
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": null,
    "status": "present",
    "location": "Office - Main Building",
    "notes": "Started early today"
  }
}
```

### 2. Get Today's Attendance
**GET** `/api/v1/attendance/today`

**Response (200):**
```json
{
  "data": {
    "id": "uuid-here",
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": "17:30:00",
    "worked_hours": 8.5,
    "status": "present",
    "is_late": false,
    "overtime_hours": 0.5
  }
}
```

---

## Leave Management

### 1. Apply for Leave
**POST** `/api/v1/leaves`

**Request:**
```json
{
  "leave_type_id": "uuid-here",
  "start_date": "2023-12-25",
  "end_date": "2023-12-29",
  "reason": "Christmas vacation with family",
  "emergency_contact": "Jane Doe",
  "emergency_phone": "+1234567890"
}
```

**Response (201):**
```json
{
  "message": "Leave application submitted successfully",
  "data": {
    "id": "uuid-here",
    "start_date": "2023-12-25",
    "end_date": "2023-12-29",
    "days": 5,
    "reason": "Christmas vacation with family",
    "status": "pending",
    "status_label": "Pending Approval",
    "status_color": "orange",
    "leave_type": {
      "id": "uuid-here",
      "name": "Annual Leave",
      "is_paid": true
    }
  }
}
```

### 2. Get Leave Balance
**GET** `/api/v1/leaves/balance`

**Response (200):**
```json
{
  "data": {
    "annual_leave": {
      "allocated": 20,
      "used": 5,
      "remaining": 15
    },
    "sick_leave": {
      "allocated": 10,
      "used": 2,
      "remaining": 8
    },
    "personal_leave": {
      "allocated": 5,
      "used": 0,
      "remaining": 5
    }
  }
}
```

---

## Dashboard Data

### 1. Get Dashboard Stats
**GET** `/api/v1/dashboard`

**Response (200):**
```json
{
  "data": {
    "employees": {
      "total": 150,
      "active": 145,
      "on_leave": 5,
      "new_this_month": 3
    },
    "attendance": {
      "present_today": 140,
      "late_today": 3,
      "absent_today": 2,
      "average_attendance": 96.5
    },
    "leaves": {
      "pending_requests": 8,
      "approved_this_month": 25,
      "most_used_type": "Annual Leave"
    },
    "payroll": {
      "last_run": "2023-11-30",
      "total_amount": 450000,
      "next_run": "2023-12-31"
    }
  }
}
```

---

## Error Responses

### Validation Error (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (401):
```json
{
  "message": "Unauthenticated"
}
```

### Forbidden (403):
```json
{
  "message": "Insufficient permissions",
  "code": "INSUFFICIENT_PERMISSIONS"
}
```

### Not Found (404):
```json
{
  "message": "Employee not found",
  "code": "NOT_FOUND"
}
```

### Rate Limit (429):
```json
{
  "message": "Too Many Attempts",
  "retry_after": 60,
  "rate_limit": {
    "limit": 60,
    "remaining": 0,
    "reset": 1701456789
  }
}
```

---

## Standard Response Headers

**Success Response Headers:**
```
Content-Type: application/json
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1701456789
```

**Error Response Headers:**
```
Content-Type: application/json
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
```











# Dashboard, Employees & Users API Endpoints

## Dashboard Endpoints

### 1. Get Dashboard Overview
**GET** `/api/v1/dashboard`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "employees": {
      "total": 150,
      "active": 145,
      "inactive": 3,
      "terminated": 2,
      "new_this_month": 5,
      "birthdays_this_month": 8
    },
    "attendance": {
      "present_today": 140,
      "late_today": 3,
      "absent_today": 2,
      "on_leave_today": 5,
      "average_attendance_this_month": 96.5,
      "total_hours_this_week": 5600
    },
    "leaves": {
      "pending_requests": 12,
      "approved_this_month": 25,
      "rejected_this_month": 2,
      "most_used_type": "Annual Leave",
      "total_days_taken_this_year": 450
    },
    "payroll": {
      "last_run_date": "2023-11-30",
      "last_run_amount": 450000,
      "next_run_date": "2023-12-31",
      "employees_processed": 145,
      "pending_approvals": 0
    },
    "departments": {
      "total": 8,
      "largest": {
        "name": "Engineering",
        "employee_count": 45
      },
      "newest": {
        "name": "AI Research",
        "created_at": "2023-11-15"
      }
    },
    "recent_activities": [
      {
        "type": "employee_added",
        "message": "New employee John Doe added to Marketing",
        "timestamp": "2023-12-01T10:30:00Z",
        "icon": "user-plus",
        "color": "green"
      },
      {
        "type": "leave_approved",
        "message": "Leave request approved for Jane Smith",
        "timestamp": "2023-12-01T09:15:00Z",
        "icon": "calendar-check",
        "color": "blue"
      }
    ],
    "quick_actions": [
      {
        "label": "Add Employee",
        "action": "create_employee",
        "icon": "user-plus",
        "color": "blue"
      },
      {
        "label": "Process Payroll",
        "action": "process_payroll",
        "icon": "credit-card",
        "color": "green"
      }
    ]
  }
}
```

### 2. Get Dashboard Stats
**GET** `/api/v1/dashboard/stats`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "employees": {
      "total": 150,
      "active": 145,
      "new_this_month": 5
    },
    "recent_activities": [],
    "quick_actions": [
      {
        "label": "Mark Attendance",
        "action": "mark_attendance"
      }
    ]
  }
}
```

---

## Employee Endpoints

### 1. Get Employees List
**GET** `/api/v1/employees`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
```
?page=1
?per_page=15
?status=active
?search=john
?department_id=uuid
?position_id=uuid
?employment_type=full_time
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-employee-1",
      "employee_id": "EMP001",
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "email": "john@company.com",
      "phone": "+1234567890",
      "date_of_birth": "1990-05-15",
      "gender": "male",
      "avatar": "https://app.com/storage/avatars/john.jpg",
      "address": "123 Main St",
      "city": "New York",
      "state": "NY",
      "country": "USA",
      "postal_code": "10001",
      "hire_date": "2023-01-15",
      "job_title": "Software Developer",
      "employment_type": "full_time",
      "status": "active",
      "salary": 75000,
      "years_of_service": 1,
      "department": {
        "id": "uuid-dept-1",
        "name": "Engineering"
      },
      "position": {
        "id": "uuid-pos-1",
        "title": "Senior Developer"
      },
      "manager": {
        "id": "uuid-manager-1",
        "full_name": "Jane Smith"
      },
      "user": {
        "id": "uuid-user-1",
        "email": "john@company.com",
        "role": "employee",
        "is_active": true
      },
      "created_at": "2023-01-15T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### 2. Create Employee
**POST** `/api/v1/employees`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "first_name": "Alice",
  "last_name": "Johnson",
  "email": "alice@company.com",
  "phone": "+1234567890",
  "date_of_birth": "1992-08-20",
  "gender": "female",
  "address": "456 Oak Ave",
  "city": "Boston",
  "state": "MA",
  "country": "USA",
  "postal_code": "02101",
  "hire_date": "2023-12-01",
  "department_id": "uuid-dept-1",
  "position_id": "uuid-pos-1",
  "manager_id": "uuid-manager-1",
  "job_title": "Frontend Developer",
  "employment_type": "full_time",
  "salary": 70000,
  "currency_id": 1,
  "create_user_account": true,
  "password": "password123",
  "role": "employee"
}
```

**Success Response (201):**
```json
{
  "message": "Employee created successfully",
  "data": {
    "id": "uuid-employee-new",
    "employee_id": "EMP151",
    "first_name": "Alice",
    "last_name": "Johnson",
    "full_name": "Alice Johnson",
    "email": "alice@company.com",
    "phone": "+1234567890",
    "hire_date": "2023-12-01",
    "job_title": "Frontend Developer",
    "employment_type": "full_time",
    "status": "active",
    "department": {
      "id": "uuid-dept-1",
      "name": "Engineering"
    },
    "created_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["An employee with this email already exists"],
    "hire_date": ["The hire date is required"],
    "department_id": ["The selected department does not exist"]
  }
}
```

### 3. Get Employee Details
**GET** `/api/v1/employees/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-employee-1",
    "employee_id": "EMP001",
    "first_name": "John",
    "last_name": "Doe",
    "full_name": "John Doe",
    "email": "john@company.com",
    "phone": "+1234567890",
    "date_of_birth": "1990-05-15",
    "gender": "male",
    "avatar": "https://app.com/storage/avatars/john.jpg",
    "address": "123 Main St",
    "city": "New York",
    "state": "NY",
    "country": "USA",
    "postal_code": "10001",
    "hire_date": "2023-01-15",
    "job_title": "Software Developer",
    "employment_type": "full_time",
    "status": "active",
    "salary": 75000,
    "years_of_service": 1,
    "department": {
      "id": "uuid-dept-1",
      "name": "Engineering",
      "description": "Software development team"
    },
    "position": {
      "id": "uuid-pos-1",
      "title": "Senior Developer",
      "description": "Senior software development role"
    },
    "manager": {
      "id": "uuid-manager-1",
      "full_name": "Jane Smith",
      "job_title": "Engineering Manager"
    },
    "user": {
      "id": "uuid-user-1",
      "email": "john@company.com",
      "role": "employee",
      "is_active": true,
      "last_login_at": "2023-12-01T09:00:00.000000Z"
    },
    "created_at": "2023-01-15T10:00:00.000000Z",
    "updated_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

**Error Response (404):**
```json
{
  "message": "Employee not found"
}
```

### 4. Update Employee
**PUT** `/api/v1/employees/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1234567891",
  "job_title": "Senior Software Developer",
  "department_id": "uuid-dept-1",
  "salary": 80000,
  "status": "active"
}
```

**Success Response (200):**
```json
{
  "message": "Employee updated successfully",
  "data": {
    "id": "uuid-employee-1",
    "employee_id": "EMP001",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "+1234567891",
    "job_title": "Senior Software Developer",
    "salary": 80000,
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 5. Delete Employee
**DELETE** `/api/v1/employees/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Employee deleted successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Cannot delete employee with active records",
  "error": "Employee has active attendance or payroll records"
}
```

### 6. Upload Employee Avatar
**POST** `/api/v1/employees/{id}/avatar`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "multipart/form-data",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request (Form Data):**
```json
{
  "avatar": "[FILE_OBJECT]"
}
```

**Success Response (200):**
```json
{
  "message": "Avatar uploaded successfully",
  "data": {
    "id": "uuid-employee-1",
    "first_name": "John",
    "last_name": "Doe",
    "avatar": "https://app.com/storage/avatars/new-avatar.jpg",
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 7. Import Employees
**POST** `/api/v1/employees/import`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "multipart/form-data",
  "Accept": "application/json"
}
```

**Request (Form Data):**
```json
{
  "file": "[CSV_FILE_OBJECT]",
  "options": {
    "update_existing": true,
    "send_welcome_emails": false
  }
}
```

**Success Response (202):**
```json
{
  "message": "Employee import started. You will be notified when complete.",
  "import_status": "processing"
}
```

### 8. Export Employees
**POST** `/api/v1/employees/export`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "format": "csv",
  "filters": {
    "status": "active",
    "department_id": "uuid-dept-1"
  }
}
```

**Success Response (202):**
```json
{
  "message": "Employee export started. You will be notified when complete.",
  "export_status": "processing"
}
```

---

## User Endpoints

### 1. Get Users List
**GET** `/api/v1/users`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?page=1
?per_page=15
?role=admin
?is_active=true
?search=john
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-user-1",
      "email": "admin@company.com",
      "role": "admin",
      "is_active": true,
      "email_verified_at": "2023-01-15T10:00:00.000000Z",
      "last_login_at": "2023-12-01T09:00:00.000000Z",
      "employee": {
        "id": "uuid-employee-1",
        "full_name": "John Doe",
        "job_title": "System Administrator",
        "avatar": "https://app.com/storage/avatars/john.jpg"
      },
      "company": {
        "id": "uuid-company-1",
        "name": "Acme Corporation"
      },
      "created_at": "2023-01-15T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

### 2. Create User
**POST** `/api/v1/users`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "email": "newuser@company.com",
  "password": "password123",
  "role": "hr",
  "employee_id": "uuid-employee-2",
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "message": "User created successfully",
  "data": {
    "id": "uuid-user-new",
    "email": "newuser@company.com",
    "role": "hr",
    "is_active": true,
    "employee": {
      "id": "uuid-employee-2",
      "full_name": "Jane Smith"
    },
    "company": {
      "id": "uuid-company-1",
      "name": "Acme Corporation"
    },
    "created_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 3. Get User Details
**GET** `/api/v1/users/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-user-1",
    "email": "admin@company.com",
    "role": "admin",
    "is_active": true,
    "email_verified_at": "2023-01-15T10:00:00.000000Z",
    "last_login_at": "2023-12-01T09:00:00.000000Z",
    "employee": {
      "id": "uuid-employee-1",
      "employee_id": "EMP001",
      "full_name": "John Doe",
      "job_title": "System Administrator",
      "department": {
        "name": "IT"
      }
    },
    "company": {
      "id": "uuid-company-1",
      "name": "Acme Corporation"
    },
    "created_at": "2023-01-15T10:00:00.000000Z",
    "updated_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 4. Update User
**PUT** `/api/v1/users/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "role": "manager",
  "is_active": true
}
```

**Success Response (200):**
```json
{
  "message": "User updated successfully",
  "data": {
    "id": "uuid-user-1",
    "email": "admin@company.com",
    "role": "manager",
    "is_active": true,
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 5. Delete User
**DELETE** `/api/v1/users/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "User deleted successfully"
}
```

### 6. Activate User
**POST** `/api/v1/users/{id}/activate`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "User activated successfully",
  "data": {
    "id": "uuid-user-1",
    "email": "admin@company.com",
    "is_active": true,
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 7. Deactivate User
**POST** `/api/v1/users/{id}/deactivate`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "User deactivated successfully",
  "data": {
    "id": "uuid-user-1",
    "email": "admin@company.com",
    "is_active": false,
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 8. Reset User Password
**POST** `/api/v1/users/{id}/reset-password`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Success Response (200):**
```json
{
  "message": "Password reset successfully"
}
```

---

## Required Headers Summary

**All authenticated requests must include:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**For JSON requests, also include:**
```json
{
  "Content-Type": "application/json"
}
```

**For file uploads:**
```json
{
  "Content-Type": "multipart/form-data"
}










# Company, Departments, Positions, Attendance & Leaves API Endpoints

## Company Endpoints

### 1. Get Company Information
**GET** `/api/v1/company`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-company-id",
    "name": "Acme Corporation",
    "email": "contact@acme.com",
    "phone": "+1234567890",
    "website": "https://acme.com",
    "logo": "https://app.com/storage/logos/acme-logo.png",
    "address": "123 Business Ave",
    "city": "New York",
    "state": "NY",
    "country": "USA",
    "postal_code": "10001",
    "tax_id": "12-3456789",
    "registration_number": "REG123456",
    "timezone": "America/New_York",
    "currency": {
      "id": 1,
      "code": "USD",
      "name": "US Dollar",
      "symbol": "$"
    },
    "is_active": true,
    "subscription_status": "active",
    "employee_limit": 500,
    "settings": {
      "working_hours": {
        "start": "09:00",
        "end": "17:00"
      },
      "payroll_frequency": "monthly"
    },
    "total_employees": 150,
    "total_departments": 8,
    "total_positions": 25,
    "created_at": "2023-01-01T10:00:00.000000Z",
    "updated_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 2. Update Company Information
**PUT** `/api/v1/company`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "Acme Corporation Ltd",
  "email": "info@acme.com",
  "phone": "+1234567891",
  "website": "https://acme.com",
  "address": "456 Business Plaza",
  "city": "New York",
  "state": "NY",
  "country": "USA",
  "postal_code": "10002",
  "tax_id": "12-3456789",
  "timezone": "America/New_York",
  "currency_id": 1
}
```

**Success Response (200):**
```json
{
  "message": "Company information updated successfully",
  "data": {
    "id": "uuid-company-id",
    "name": "Acme Corporation Ltd",
    "email": "info@acme.com",
    "phone": "+1234567891",
    "website": "https://acme.com",
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 3. Upload Company Logo
**POST** `/api/v1/company/logo`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "multipart/form-data",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request (Form Data):**
```json
{
  "logo": "[IMAGE_FILE_OBJECT]"
}
```

**Success Response (200):**
```json
{
  "message": "Company logo uploaded successfully",
  "data": {
    "logo_url": "https://app.com/storage/logos/new-logo.png",
    "logo_path": "logos/new-logo.png"
  }
}
```

### 4. Get Company Settings
**GET** `/api/v1/company/settings`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "company_info": {
      "name": "Acme Corporation",
      "email": "contact@acme.com",
      "phone": "+1234567890",
      "website": "https://acme.com",
      "timezone": "America/New_York",
      "currency_id": 1
    },
    "business_settings": {
      "tax_id": "12-3456789",
      "registration_number": "REG123456",
      "employee_limit": 500
    },
    "system_settings": {
      "working_hours": {
        "start": "09:00",
        "end": "17:00"
      },
      "leave_policies": {
        "auto_approve": false,
        "max_consecutive_days": 30
      },
      "payroll_settings": {
        "auto_process": false,
        "overtime_rate": 1.5
      }
    },
    "subscription": {
      "status": "active",
      "employee_limit": 500,
      "features": {
        "employee_management": true,
        "payroll_processing": true,
        "advanced_reporting": true
      }
    }
  }
}
```

### 5. Update Company Settings
**PUT** `/api/v1/company/settings`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "settings": {
    "working_hours": {
      "start": "08:30",
      "end": "17:30"
    },
    "leave_policies": {
      "auto_approve": false,
      "max_consecutive_days": 25,
      "advance_notice_days": 7
    },
    "payroll_settings": {
      "auto_process": true,
      "tax_rate": 0.15,
      "overtime_rate": 1.5
    }
  }
}
```

**Success Response (200):**
```json
{
  "message": "Company settings updated successfully",
  "data": {
    "working_hours": {
      "start": "08:30",
      "end": "17:30"
    },
    "leave_policies": {
      "auto_approve": false,
      "max_consecutive_days": 25
    }
  }
}
```

### 6. Get Company Statistics
**GET** `/api/v1/company/stats`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "employees": {
      "total": 150,
      "active": 145,
      "new_this_month": 5,
      "by_department": {
        "Engineering": 45,
        "Sales": 30,
        "Marketing": 25
      }
    },
    "departments": {
      "total": 8,
      "active": 8
    },
    "positions": {
      "total": 25,
      "filled": 22,
      "vacant": 3
    },
    "payroll": {
      "total_monthly": 450000,
      "average_salary": 75000
    }
  }
}
```

### 7. Get Organization Structure
**GET** `/api/v1/company/organization-structure`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "departments": [
      {
        "id": "uuid-dept-1",
        "name": "Engineering",
        "description": "Software development team",
        "manager": {
          "id": "uuid-emp-1",
          "full_name": "Jane Smith"
        },
        "employees_count": 45
      }
    ],
    "positions": [
      {
        "id": "uuid-pos-1",
        "title": "Senior Developer",
        "department": {
          "name": "Engineering"
        },
        "employees_count": 12
      }
    ],
    "organizational_chart": [
      {
        "id": "uuid-emp-ceo",
        "name": "John CEO",
        "title": "Chief Executive Officer",
        "department": "Executive",
        "avatar": "https://app.com/storage/avatars/ceo.jpg",
        "subordinates": [
          {
            "id": "uuid-emp-cto",
            "name": "Jane CTO",
            "title": "Chief Technology Officer",
            "department": "Engineering",
            "subordinates": []
          }
        ]
      }
    ]
  }
}
```

---

## Department Endpoints

### 1. Get Departments List
**GET** `/api/v1/departments`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-dept-1",
      "name": "Engineering",
      "description": "Software development and technical operations",
      "is_active": true,
      "manager": {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "first_name": "Jane",
        "last_name": "Smith",
        "full_name": "Jane Smith",
        "job_title": "Engineering Manager"
      },
      "employees_count": 45,
      "created_at": "2023-01-01T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    },
    {
      "id": "uuid-dept-2",
      "name": "Sales",
      "description": "Revenue generation and client acquisition",
      "is_active": true,
      "manager": {
        "id": "uuid-emp-2",
        "full_name": "Bob Johnson"
      },
      "employees_count": 30,
      "created_at": "2023-01-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "total": 8,
    "active": 8,
    "inactive": 0
  }
}
```

### 2. Create Department
**POST** `/api/v1/departments`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "Human Resources",
  "description": "Employee relations and talent management",
  "manager_id": "uuid-emp-5",
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "message": "Department created successfully",
  "data": {
    "id": "uuid-dept-new",
    "name": "Human Resources",
    "description": "Employee relations and talent management",
    "is_active": true,
    "manager": {
      "id": "uuid-emp-5",
      "full_name": "Sarah Wilson"
    },
    "employees_count": 0,
    "created_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 3. Get Department Details
**GET** `/api/v1/departments/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-dept-1",
    "name": "Engineering",
    "description": "Software development and technical operations",
    "is_active": true,
    "manager": {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "full_name": "Jane Smith",
      "job_title": "Engineering Manager",
      "email": "jane@company.com"
    },
    "employees": [
      {
        "id": "uuid-emp-2",
        "full_name": "John Doe",
        "job_title": "Senior Developer"
      }
    ],
    "employees_count": 45,
    "created_at": "2023-01-01T10:00:00.000000Z",
    "updated_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 4. Update Department
**PUT** `/api/v1/departments/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "Engineering & Technology",
  "description": "Software development, DevOps and technical operations",
  "manager_id": "uuid-emp-1",
  "is_active": true
}
```

**Success Response (200):**
```json
{
  "message": "Department updated successfully",
  "data": {
    "id": "uuid-dept-1",
    "name": "Engineering & Technology",
    "description": "Software development, DevOps and technical operations",
    "manager": {
      "id": "uuid-emp-1",
      "full_name": "Jane Smith"
    },
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 5. Delete Department
**DELETE** `/api/v1/departments/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Department deleted successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Cannot delete department with active employees",
  "active_employees": 5
}
```

### 6. Get Department Employees
**GET** `/api/v1/departments/{id}/employees`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?status=active
?search=john
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "first_name": "John",
      "last_name": "Doe",
      "full_name": "John Doe",
      "email": "john@company.com",
      "job_title": "Senior Developer",
      "status": "active",
      "hire_date": "2023-01-15",
      "position": {
        "id": "uuid-pos-1",
        "title": "Senior Developer"
      },
      "manager": {
        "id": "uuid-mgr-1",
        "full_name": "Jane Smith"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 45
  }
}
```

---

## Position Endpoints

### 1. Get Positions List
**GET** `/api/v1/positions`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?department_id=uuid-dept-1
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-pos-1",
      "title": "Senior Software Developer",
      "description": "Experienced developer role with leadership responsibilities",
      "requirements": "5+ years experience, React, Node.js",
      "min_salary": 80000,
      "max_salary": 120000,
      "is_active": true,
      "department": {
        "id": "uuid-dept-1",
        "name": "Engineering"
      },
      "employees_count": 12,
      "created_at": "2023-01-01T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "total": 25,
    "active": 23,
    "inactive": 2,
    "by_department": {
      "Engineering": 15,
      "Sales": 6,
      "Marketing": 4
    }
  }
}
```

### 2. Create Position
**POST** `/api/v1/positions`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "title": "Frontend Developer",
  "department_id": "uuid-dept-1",
  "description": "Build and maintain user-facing web applications",
  "requirements": "3+ years React experience, JavaScript, CSS",
  "min_salary": 60000,
  "max_salary": 90000,
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "message": "Position created successfully",
  "data": {
    "id": "uuid-pos-new",
    "title": "Frontend Developer",
    "description": "Build and maintain user-facing web applications",
    "min_salary": 60000,
    "max_salary": 90000,
    "is_active": true,
    "department": {
      "id": "uuid-dept-1",
      "name": "Engineering"
    },
    "employees_count": 0,
    "created_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 3. Get Position Details
**GET** `/api/v1/positions/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-pos-1",
    "title": "Senior Software Developer",
    "description": "Experienced developer role with leadership responsibilities",
    "requirements": "5+ years experience, React, Node.js, team leadership",
    "min_salary": 80000,
    "max_salary": 120000,
    "is_active": true,
    "department": {
      "id": "uuid-dept-1",
      "name": "Engineering",
      "description": "Software development team"
    },
    "employees": [
      {
        "id": "uuid-emp-1",
        "full_name": "John Doe",
        "salary": 95000
      }
    ],
    "employees_count": 12,
    "created_at": "2023-01-01T10:00:00.000000Z",
    "updated_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

### 4. Update Position
**PUT** `/api/v1/positions/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "title": "Lead Software Developer",
  "description": "Senior developer with team leadership and mentoring responsibilities",
  "min_salary": 90000,
  "max_salary": 130000,
  "is_active": true
}
```

**Success Response (200):**
```json
{
  "message": "Position updated successfully",
  "data": {
    "id": "uuid-pos-1",
    "title": "Lead Software Developer",
    "description": "Senior developer with team leadership and mentoring responsibilities",
    "min_salary": 90000,
    "max_salary": 130000,
    "updated_at": "2023-12-01T10:30:00.000000Z"
  }
}
```

### 5. Delete Position
**DELETE** `/api/v1/positions/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Position deleted successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Cannot delete position with active employees",
  "active_employees": 5
}
```

### 6. Get Position Employees
**GET** `/api/v1/positions/{id}/employees`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "full_name": "John Doe",
      "email": "john@company.com",
      "hire_date": "2023-01-15",
      "salary": 95000,
      "status": "active",
      "department": {
        "name": "Engineering"
      },
      "manager": {
        "full_name": "Jane Smith"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 12
  }
}
```

### 7. Get Position Salary Statistics
**GET** `/api/v1/positions/{id}/salary-stats`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "position": {
      "id": "uuid-pos-1",
      "title": "Senior Software Developer",
      "min_salary": 80000,
      "max_salary": 120000
    },
    "salary_statistics": {
      "employee_count": 12,
      "average_salary": 95000.00,
      "min_salary": 85000,
      "max_salary": 115000,
      "salary_deviation": 8500.50,
      "defined_min_salary": 80000,
      "defined_max_salary": 120000
    }
  }
}
```

---

## Attendance Endpoints

### 1. Get Attendance Records
**GET** `/api/v1/attendance`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?employee_id=uuid-emp-1
?date_from=2023-12-01
?date_to=2023-12-31
?status=present
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-att-1",
      "employee": {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "full_name": "John Doe",
        "avatar": "https://app.com/storage/avatars/john.jpg"
      },
      "date": "2023-12-01",
      "check_in": "09:00:00",
      "check_out": "17:30:00",
      "break_duration": 60,
      "worked_hours": 7.5,
      "status": "present",
      "notes": "Regular workday",
      "location": "Office - Main Building",
      "is_late": false,
      "is_early_departure": false,
      "overtime_hours": 0,
      "status_color": "green",
      "status_label": "Present",
      "created_at": "2023-12-01T09:00:00.000000Z",
      "updated_at": "2023-12-01T17:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 67
  }
}
```

### 2. Check In
**POST** `/api/v1/attendance/checkin`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "location": "Office - Main Building",
  "notes": "Starting work for the day"
}
```

**Success Response (201):**
```json
{
  "message": "Check-in successful",
  "data": {
    "id": "uuid-att-new",
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    },
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": null,
    "status": "present",
    "location": "Office - Main Building",
    "notes": "Starting work for the day",
    "is_late": false,
    "worked_hours": 0,
    "created_at": "2023-12-01T09:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "Check-in failed",
  "error": "You have already checked in today"
}
```

### 3. Check Out
**POST** `/api/v1/attendance/checkout`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "notes": "Completed all tasks for today"
}
```

**Success Response (200):**
```json
{
  "message": "Check-out successful",
  "data": {
    "id": "uuid-att-1",
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    },
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": "17:30:00",
    "worked_hours": 7.5,
    "status": "present",
    "notes": "Completed all tasks for today",
    "overtime_hours": 0,
    "updated_at": "2023-12-01T17:30:00.000000Z"
  }
}
```

### 4. Get Today's Attendance
**GET** `/api/v1/attendance/today`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-att-1",
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": "17:30:00",
    "worked_hours": 7.5,
    "status": "present",
    "location": "Office - Main Building",
    "notes": "Regular workday",
    "is_late": false,
    "overtime_hours": 0,
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    }
  }
}
```

**Response when no attendance (200):**
```json
{
  "data": null
}
```

### 5. Create/Update Attendance Record (Admin)
**POST** `/api/v1/attendance`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-1",
  "date": "2023-12-01",
  "check_in": "09:00:00",
  "check_out": "17:30:00",
  "break_duration": 60,
  "status": "present",
  "notes": "Manual entry by admin",
  "location": "Office"
}
```

**Success Response (201):**
```json
{
  "message": "Attendance record created successfully",
  "data": {
    "id": "uuid-att-new",
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    },
    "date": "2023-12-01",
    "check_in": "09:00:00",
    "check_out": "17:30:00",
    "worked_hours": 7.5,
    "status": "present"
  }
}
```

### 6. Update Attendance Record
**PUT** `/api/v1/attendance/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "check_out": "18:00:00",
  "notes": "Updated checkout time",
  "status": "present"
}
```

**Success Response (200):**
```json
{
  "message": "Attendance record updated successfully",
  "data": {
    "id": "uuid-att-1",
    "check_out": "18:00:00",
    "worked_hours": 8.0,
    "overtime_hours": 0.5,
    "notes": "Updated checkout time",
    "updated_at": "2023-12-01T18:30:00.000000Z"
  }
}
```

### 7. Get Employee Attendance Records
**GET** `/api/v1/attendance/employee/{employeeId}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?date_from=2023-12-01
?date_to=2023-12-31
?status=present
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-att-1",
      "date": "2023-12-01",
      "check_in": "09:00:00",
      "check_out": "17:30:00",
      "worked_hours": 7.5,
      "status": "present",
      "is_late": false,
      "overtime_hours": 0
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 22
  }
}
```

---

## Leave Endpoints

### 1. Get Leave Requests
**GET** `/api/v1/leaves`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?employee_id=uuid-emp-1
?status=pending
?leave_type_id=uuid-type-1
?date_from=2023-12-01
?date_to=2023-12-31
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-leave-1",
      "employee": {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "full_name": "John Doe",
        "avatar": "https://app.com/storage/avatars/john.jpg"
      },
      "leave_type": {
        "id": "uuid-type-1",
        "name": "Annual Leave",
        "description": "Yearly vacation days",
        "days_per_year": 20,
        "is_paid": true
      },
      "start_date": "2023-12-25",
      "end_date": "2023-12-29",
      "days": 5,
      "reason": "Christmas vacation with family",
      "emergency_contact": "Jane Doe",
      "emergency_phone": "+1234567890",
      "status": "pending",
      "status_label": "Pending Approval",
      "status_color": "orange",
      "applied_at": "2023-12-01T10:00:00.000000Z",
      "approved_by": null,
      "approved_at": null,
      "rejected_by": null,
      "rejected_at": null,
      "comments": null,
      "attachments": [],
      "can_edit": true,
      "can_cancel": true,
      "created_at": "2023-12-01T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

### 2. Apply for Leave
**POST** `/api/v1/leaves`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "leave_type_id": "uuid-type-1",
  "start_date": "2023-12-25",
  "end_date": "2023-12-29",
  "reason": "Christmas vacation with family",
  "emergency_contact": "Jane Doe",
  "emergency_phone": "+1234567890"
}
```

**Success Response (201):**
```json
{
  "message": "Leave application submitted successfully",
  "data": {
    "id": "uuid-leave-new",
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    },
    "leave_type": {
      "id": "uuid-type-1",
      "name": "Annual Leave",
      "is_paid": true
    },
    "start_date": "2023-12-25",
    "end_date": "2023-12-29",
    "days": 5,
    "reason": "Christmas vacation with family",
    "status": "pending",
    "status_label": "Pending Approval",
    "status_color": "orange",
    "applied_at": "2023-12-01T10:00:00.000000Z",
    "can_edit": true,
    "can_cancel": true,
    "created_at": "2023-12-01T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "start_date": ["Start date cannot be in the past"],
    "leave_type_id": ["Selected leave type does not exist"]
  }
}
```

### 3. Get Leave Request Details
**GET** `/api/v1/leaves/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-leave-1",
    "employee": {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "full_name": "John Doe",
      "job_title": "Software Developer",
      "department": {
        "name": "Engineering"
      }
    },
    "leave_type": {
      "id": "uuid-type-1",
      "name": "Annual Leave",
      "description": "Yearly vacation days",
      "days_per_year": 20,
      "is_paid": true,
      "requires_approval": true
    },
    "start_date": "2023-12-25",
    "end_date": "2023-12-29",
    "days": 5,
    "reason": "Christmas vacation with family",
    "emergency_contact": "Jane Doe",
    "emergency_phone": "+1234567890",
    "status": "approved",
    "status_label": "Approved",
    "status_color": "green",
    "applied_at": "2023-12-01T10:00:00.000000Z",
    "approved_by": {
      "id": "uuid-emp-mgr",
      "full_name": "Jane Smith",
      "job_title": "Engineering Manager"
    },
    "approved_at": "2023-12-01T14:30:00.000000Z",
    "comments": "Approved. Enjoy your vacation!",
    "attachments": [
      "https://app.com/storage/leave-attachments/medical-cert.pdf"
    ],
    "can_edit": false,
    "can_cancel": true,
    "created_at": "2023-12-01T10:00:00.000000Z",
    "updated_at": "2023-12-01T14:30:00.000000Z"
  }
}
```

### 4. Approve Leave Request
**POST** `/api/v1/leaves/{id}/approve`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "comments": "Approved. Enjoy your vacation!"
}
```

**Success Response (200):**
```json
{
  "message": "Leave request approved successfully",
  "data": {
    "id": "uuid-leave-1",
    "status": "approved",
    "status_label": "Approved",
    "status_color": "green",
    "approved_by": {
      "id": "uuid-emp-mgr",
      "full_name": "Jane Smith"
    },
    "approved_at": "2023-12-01T14:30:00.000000Z",
    "comments": "Approved. Enjoy your vacation!",
    "employee": {
      "full_name": "John Doe"
    },
    "leave_type": {
      "name": "Annual Leave"
    }
  }
}
```

### 5. Reject Leave Request
**POST** `/api/v1/leaves/{id}/reject`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "comments": "Cannot approve due to project deadlines in December"
}
```

**Success Response (200):**
```json
{
  "message": "Leave request rejected",
  "data": {
    "id": "uuid-leave-1",
    "status": "rejected",
    "status_label": "Rejected",
    "status_color": "red",
    "rejected_by": {
      "id": "uuid-emp-mgr",
      "full_name": "Jane Smith"
    },
    "rejected_at": "2023-12-01T14:30:00.000000Z",
    "comments": "Cannot approve due to project deadlines in December",
    "employee": {
      "full_name": "John Doe"
    },
    "leave_type": {
      "name": "Annual Leave"
    }
  }
}
```

### 6. Get Leave Balance
**GET** `/api/v1/leaves/balance`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "annual_leave": {
      "allocated": 20,
      "used": 5,
      "remaining": 15,
      "pending": 2
    },
    "sick_leave": {
      "allocated": 10,
      "used": 2,
      "remaining": 8,
      "pending": 0
    },
    "personal_leave": {
      "allocated": 5,
      "used": 0,
      "remaining": 5,
      "pending": 0
    },
    "maternity_leave": {
      "allocated": 90,
      "used": 0,
      "remaining": 90,
      "pending": 0
    }
  }
}
```

### 7. Get Leave Types
**GET** `/api/v1/leaves/types`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-type-1",
      "name": "Annual Leave",
      "description": "Yearly vacation days",
      "days_per_year": 20,
      "is_paid": true,
      "requires_approval": true,
      "is_active": true,
      "created_at": "2023-01-01T10:00:00.000000Z",
      "updated_at": "2023-01-01T10:00:00.000000Z"
    },
    {
      "id": "uuid-type-2",
      "name": "Sick Leave",
      "description": "Medical leave for illness",
      "days_per_year": 10,
      "is_paid": true,
      "requires_approval": false,
      "is_active": true,
      "created_at": "2023-01-01T10:00:00.000000Z"
    },
    {
      "id": "uuid-type-3",
      "name": "Personal Leave",
      "description": "Personal time off",
      "days_per_year": 5,
      "is_paid": false,
      "requires_approval": true,
      "is_active": true,
      "created_at": "2023-01-01T10:00:00.000000Z"
    }
  ]
}
```

---

## Common Headers for All Endpoints

**Required for all authenticated requests:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**For JSON requests (POST/PUT):**
```json
{
  "Content-Type": "application/json"
}
```

**For file uploads:**
```json
{
  "Content-Type": "multipart/form-data"
}
```

















# Payroll, Benefits, Performance & Training API Endpoints

## Payroll Endpoints

### 1. Get Payroll Periods
**GET** `/api/v1/payroll/periods`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?year=2023
?status=processed
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-period-1",
      "name": "December 2023",
      "start_date": "2023-12-01",
      "end_date": "2023-12-31",
      "pay_date": "2024-01-05",
      "status": "processed",
      "description": "Monthly payroll for December 2023",
      "total_gross": 450000.00,
      "total_deductions": 67500.00,
      "total_net": 382500.00,
      "total_employees": 150,
      "processed_employees": 150,
      "processed_at": "2024-01-02T10:00:00.000000Z",
      "processed_by": {
        "id": "uuid-user-1",
        "email": "hr@company.com",
        "employee": {
          "full_name": "Jane Smith"
        }
      },
      "error_message": null,
      "can_process": false,
      "can_export": true,
      "created_at": "2023-11-25T10:00:00.000000Z",
      "updated_at": "2024-01-02T10:00:00.000000Z"
    },
    {
      "id": "uuid-period-2",
      "name": "January 2024",
      "start_date": "2024-01-01",
      "end_date": "2024-01-31",
      "pay_date": "2024-02-05",
      "status": "draft",
      "total_gross": 0,
      "total_net": 0,
      "total_employees": 0,
      "processed_employees": 0,
      "can_process": true,
      "can_export": false,
      "created_at": "2023-12-20T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 24
  }
}
```

### 2. Create Payroll Period
**POST** `/api/v1/payroll/periods`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "February 2024",
  "start_date": "2024-02-01",
  "end_date": "2024-02-29",
  "pay_date": "2024-03-05",
  "description": "Monthly payroll for February 2024"
}
```

**Success Response (201):**
```json
{
  "message": "Payroll period created successfully",
  "data": {
    "id": "uuid-period-new",
    "name": "February 2024",
    "start_date": "2024-02-01",
    "end_date": "2024-02-29",
    "pay_date": "2024-03-05",
    "status": "draft",
    "description": "Monthly payroll for February 2024",
    "total_gross": 0,
    "total_net": 0,
    "total_employees": 0,
    "can_process": true,
    "created_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "end_date": ["End date must be after start date"],
    "pay_date": ["Pay date must be on or after end date"]
  }
}
```

### 3. Get Payroll Period Details
**GET** `/api/v1/payroll/periods/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-period-1",
    "name": "December 2023",
    "start_date": "2023-12-01",
    "end_date": "2023-12-31",
    "pay_date": "2024-01-05",
    "status": "processed",
    "description": "Monthly payroll for December 2023",
    "total_gross": 450000.00,
    "total_deductions": 67500.00,
    "total_net": 382500.00,
    "total_employees": 150,
    "processed_employees": 150,
    "processed_at": "2024-01-02T10:00:00.000000Z",
    "processed_by": {
      "id": "uuid-user-1",
      "email": "hr@company.com",
      "employee": {
        "full_name": "Jane Smith",
        "job_title": "HR Manager"
      }
    },
    "payroll_summary": {
      "base_salary_total": 375000.00,
      "overtime_total": 25000.00,
      "bonus_total": 50000.00,
      "deductions_total": 67500.00,
      "tax_total": 45000.00
    },
    "can_process": false,
    "can_export": true,
    "created_at": "2023-11-25T10:00:00.000000Z",
    "updated_at": "2024-01-02T10:00:00.000000Z"
  }
}
```

### 4. Process Payroll Period
**POST** `/api/v1/payroll/periods/{id}/process`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (202):**
```json
{
  "message": "Payroll processing started. You will be notified when complete.",
  "status": "processing",
  "estimated_completion": "2024-01-02T10:30:00.000000Z"
}
```

**Error Response (422):**
```json
{
  "message": "Cannot process payroll",
  "error": "Payroll period has already been processed"
}
```

### 5. Get Period Payrolls
**GET** `/api/v1/payroll/periods/{id}/payrolls`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?per_page=15
?search=john
?department_id=uuid-dept-1
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-payroll-1",
      "employee": {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "full_name": "John Doe",
        "job_title": "Software Developer",
        "department": {
          "name": "Engineering"
        }
      },
      "payroll_period": {
        "id": "uuid-period-1",
        "name": "December 2023",
        "pay_date": "2024-01-05"
      },
      "basic_salary": 5000.00,
      "overtime_amount": 500.00,
      "bonus_amount": 1000.00,
      "allowances": 200.00,
      "gross_pay": 6700.00,
      "tax_amount": 670.00,
      "deductions": 300.00,
      "net_pay": 5730.00,
      "working_days": 22,
      "worked_days": 22,
      "attendance_percentage": 100.00,
      "status": "processed",
      "paid_at": "2024-01-05T10:00:00.000000Z",
      "can_download_slip": true,
      "slip_url": "https://app.com/download/payslip/uuid-payroll-1",
      "created_at": "2024-01-02T10:00:00.000000Z",
      "updated_at": "2024-01-05T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### 6. Get Employee Payrolls
**GET** `/api/v1/payroll/employee/{employeeId}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?year=2023
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-payroll-1",
      "payroll_period": {
        "id": "uuid-period-1",
        "name": "December 2023",
        "start_date": "2023-12-01",
        "end_date": "2023-12-31",
        "pay_date": "2024-01-05"
      },
      "basic_salary": 5000.00,
      "overtime_amount": 500.00,
      "bonus_amount": 1000.00,
      "gross_pay": 6700.00,
      "tax_amount": 670.00,
      "deductions": 300.00,
      "net_pay": 5730.00,
      "working_days": 22,
      "worked_days": 22,
      "status": "processed",
      "paid_at": "2024-01-05T10:00:00.000000Z",
      "slip_url": "https://app.com/download/payslip/uuid-payroll-1"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 12
  }
}
```

### 7. Get Payroll Details
**GET** `/api/v1/payroll/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-payroll-1",
    "employee": {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "full_name": "John Doe",
      "job_title": "Software Developer",
      "department": {
        "name": "Engineering"
      },
      "hire_date": "2023-01-15"
    },
    "payroll_period": {
      "id": "uuid-period-1",
      "name": "December 2023",
      "start_date": "2023-12-01",
      "end_date": "2023-12-31",
      "pay_date": "2024-01-05"
    },
    "earnings": {
      "basic_salary": 5000.00,
      "overtime_hours": 10,
      "overtime_amount": 500.00,
      "bonus_amount": 1000.00,
      "commission": 0.00,
      "allowances": {
        "transport": 100.00,
        "meal": 100.00
      },
      "gross_pay": 6700.00
    },
    "deductions": {
      "tax_amount": 670.00,
      "social_security": 200.00,
      "health_insurance": 100.00,
      "total_deductions": 970.00
    },
    "attendance": {
      "working_days": 22,
      "worked_days": 22,
      "absent_days": 0,
      "late_days": 2,
      "overtime_hours": 10,
      "attendance_percentage": 100.00
    },
    "net_pay": 5730.00,
    "status": "processed",
    "paid_at": "2024-01-05T10:00:00.000000Z",
    "payment_method": "bank_transfer",
    "created_at": "2024-01-02T10:00:00.000000Z",
    "updated_at": "2024-01-05T10:00:00.000000Z"
  }
}
```

### 8. Get Pay Slip
**GET** `/api/v1/payroll/{id}/payslip`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "payroll": {
      "id": "uuid-payroll-1",
      "employee": {
        "full_name": "John Doe",
        "employee_id": "EMP001",
        "job_title": "Software Developer",
        "department": "Engineering"
      },
      "period": {
        "name": "December 2023",
        "start_date": "2023-12-01",
        "end_date": "2023-12-31",
        "pay_date": "2024-01-05"
      },
      "earnings": {
        "basic_salary": 5000.00,
        "overtime_amount": 500.00,
        "bonus_amount": 1000.00,
        "allowances": 200.00,
        "gross_pay": 6700.00
      },
      "deductions": {
        "tax_amount": 670.00,
        "social_security": 200.00,
        "health_insurance": 100.00,
        "total_deductions": 970.00
      },
      "net_pay": 5730.00
    },
    "company": {
      "name": "Acme Corporation",
      "address": "123 Business Ave, New York, NY 10001",
      "logo": "https://app.com/storage/logos/acme-logo.png"
    },
    "generated_at": "2024-01-05T10:00:00.000000Z"
  }
}
```

---

## Benefits Endpoints

### 1. Get Benefits List
**GET** `/api/v1/benefits`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?type=health
?is_active=true
?is_mandatory=false
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-benefit-1",
      "name": "Health Insurance Premium",
      "description": "Comprehensive health coverage for employees and dependents",
      "type": "health",
      "type_label": "Health Insurance",
      "company_contribution": 500.00,
      "employee_contribution": 200.00,
      "is_mandatory": false,
      "is_active": true,
      "enrolled_employees": 120,
      "enrollment_rate": 80.00,
      "total_cost": 84000.00,
      "created_at": "2023-01-01T10:00:00.000000Z",
      "updated_at": "2023-06-01T10:00:00.000000Z"
    },
    {
      "id": "uuid-benefit-2",
      "name": "Dental Insurance",
      "description": "Basic dental coverage",
      "type": "dental",
      "type_label": "Dental Insurance",
      "company_contribution": 100.00,
      "employee_contribution": 50.00,
      "is_mandatory": false,
      "is_active": true,
      "enrolled_employees": 95,
      "enrollment_rate": 63.33,
      "total_cost": 14250.00,
      "created_at": "2023-01-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 18
  }
}
```

### 2. Create Benefit
**POST** `/api/v1/benefits`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "Vision Insurance",
  "description": "Eye care and vision correction coverage",
  "type": "vision",
  "company_contribution": 75.00,
  "employee_contribution": 25.00,
  "is_mandatory": false,
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "message": "Benefit created successfully",
  "data": {
    "id": "uuid-benefit-new",
    "name": "Vision Insurance",
    "description": "Eye care and vision correction coverage",
    "type": "vision",
    "type_label": "Vision Insurance",
    "company_contribution": 75.00,
    "employee_contribution": 25.00,
    "is_mandatory": false,
    "is_active": true,
    "enrolled_employees": 0,
    "enrollment_rate": 0.00,
    "total_cost": 0.00,
    "created_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

### 3. Get Benefit Details
**GET** `/api/v1/benefits/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-benefit-1",
    "name": "Health Insurance Premium",
    "description": "Comprehensive health coverage for employees and dependents. Includes medical, prescription drugs, and emergency care.",
    "type": "health",
    "type_label": "Health Insurance",
    "company_contribution": 500.00,
    "employee_contribution": 200.00,
    "is_mandatory": false,
    "is_active": true,
    "enrolled_employees": 120,
    "enrollment_rate": 80.00,
    "total_cost": 84000.00,
    "enrollment_details": {
      "eligible_employees": 150,
      "enrolled_employees": 120,
      "pending_enrollments": 5,
      "declined_enrollments": 25
    },
    "created_at": "2023-01-01T10:00:00.000000Z",
    "updated_at": "2023-06-01T10:00:00.000000Z"
  }
}
```

### 4. Update Benefit
**PUT** `/api/v1/benefits/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "name": "Enhanced Health Insurance Premium",
  "description": "Comprehensive health coverage with dental and vision included",
  "company_contribution": 550.00,
  "employee_contribution": 200.00,
  "is_active": true
}
```

**Success Response (200):**
```json
{
  "message": "Benefit updated successfully",
  "data": {
    "id": "uuid-benefit-1",
    "name": "Enhanced Health Insurance Premium",
    "description": "Comprehensive health coverage with dental and vision included",
    "company_contribution": 550.00,
    "employee_contribution": 200.00,
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### 5. Delete Benefit
**DELETE** `/api/v1/benefits/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Benefit deleted successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Cannot delete benefit with enrolled employees",
  "enrolled_employees": 120
}
```

### 6. Enroll Employee in Benefit
**POST** `/api/v1/benefits/{id}/enroll`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-1",
  "effective_date": "2024-02-01",
  "employee_contribution": 200.00
}
```

**Success Response (200):**
```json
{
  "message": "Employee enrolled in benefit successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Employee already enrolled in this benefit"
}
```

### 7. Unenroll Employee from Benefit
**POST** `/api/v1/benefits/{id}/unenroll`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-1",
  "end_date": "2024-03-31"
}
```

**Success Response (200):**
```json
{
  "message": "Employee unenrolled from benefit successfully"
}
```

### 8. Get Employee Benefits
**GET** `/api/v1/benefits/employee/{employeeId}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?status=active
?type=health
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-benefit-1",
      "name": "Health Insurance Premium",
      "type": "health",
      "type_label": "Health Insurance",
      "company_contribution": 500.00,
      "employee_contribution": 200.00,
      "enrollment": {
        "enrolled_at": "2023-01-15T10:00:00.000000Z",
        "effective_date": "2023-02-01",
        "status": "active",
        "employee_contribution": 200.00
      },
      "created_at": "2023-01-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 3
  }
}
```

---

## Performance Endpoints

### 1. Get Performance Reviews
**GET** `/api/v1/performance`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?employee_id=uuid-emp-1
?reviewer_id=uuid-emp-2
?status=submitted
?year=2023
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-review-1",
      "employee": {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "full_name": "John Doe",
        "job_title": "Software Developer",
        "department": {
          "name": "Engineering"
        }
      },
      "reviewer": {
        "id": "uuid-emp-mgr",
        "employee_id": "EMP100",
        "full_name": "Jane Smith",
        "job_title": "Engineering Manager"
      },
      "review_period_start": "2023-07-01",
      "review_period_end": "2023-12-31",
      "goals": "Complete React migration project, mentor 2 junior developers",
      "achievements": "Successfully migrated 3 major components, mentored team effectively",
      "areas_for_improvement": "Focus on code documentation and testing coverage",
      "goals_next_period": "Lead new mobile app project, improve test coverage to 90%",
      "comments": "Excellent performance this period. Strong technical skills and leadership.",
      "ratings": {
        "overall_rating": 4,
        "technical_skills": 5,
        "communication_skills": 4,
        "teamwork": 5,
        "leadership": 4,
        "punctuality": 4,
        "average_rating": 4.33
      },
      "status": "approved",
      "status_label": "Approved",
      "status_color": "green",
      "submitted_at": "2024-01-10T10:00:00.000000Z",
      "approved_by": {
        "id": "uuid-emp-hr",
        "full_name": "Sarah Wilson"
      },
      "approved_at": "2024-01-12T14:30:00.000000Z",
      "can_edit": false,
      "can_submit": false,
      "can_approve": false,
      "created_at": "2024-01-05T10:00:00.000000Z",
      "updated_at": "2024-01-12T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 67
  }
}
```

### 2. Create Performance Review
**POST** `/api/v1/performance`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-1",
  "reviewer_id": "uuid-emp-mgr",
  "review_period_start": "2024-01-01",
  "review_period_end": "2024-06-30",
  "goals": "Implement new microservices architecture, improve team processes",
  "achievements": "",
  "areas_for_improvement": "",
  "overall_rating": null,
  "technical_skills": null,
  "communication_skills": null,
  "teamwork": null,
  "leadership": null,
  "punctuality": null,
  "comments": "",
  "goals_next_period": ""
}
```

**Success Response (201):**
```json
{
  "message": "Performance review created successfully",
  "data": {
    "id": "uuid-review-new",
    "employee": {
      "id": "uuid-emp-1",
      "full_name": "John Doe"
    },
    "reviewer": {
      "id": "uuid-emp-mgr",
      "full_name": "Jane Smith"
    },
    "review_period_start": "2024-01-01",
    "review_period_end": "2024-06-30",
    "goals": "Implement new microservices architecture, improve team processes",
    "status": "draft",
    "status_label": "Draft",
    "status_color": "gray",
    "can_edit": true,
    "can_submit": true,
    "created_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

### 3. Get Performance Review Details
**GET** `/api/v1/performance/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-review-1",
    "employee": {
      "id": "uuid-emp-1",
      "employee_id": "EMP001",
      "full_name": "John Doe",
      "job_title": "Software Developer",
      "hire_date": "2023-01-15",
      "department": {
        "name": "Engineering"
      }
    },
    "reviewer": {
      "id": "uuid-emp-mgr",
      "employee_id": "EMP100",
      "full_name": "Jane Smith",
      "job_title": "Engineering Manager"
    },
    "review_period_start": "2023-07-01",
    "review_period_end": "2023-12-31",
    "goals": "Complete React migration project, mentor 2 junior developers, improve code quality metrics",
    "achievements": "Successfully migrated 3 major components to React, mentored team effectively, increased test coverage by 40%",
    "areas_for_improvement": "Focus on code documentation and testing coverage, improve time estimation skills",
    "goals_next_period": "Lead new mobile app project, improve test coverage to 90%, mentor additional team member",
    "comments": "Excellent performance this period. Strong technical skills and growing leadership capabilities.",
    "ratings": {
      "overall_rating": 4,
      "technical_skills": 5,
      "communication_skills": 4,
      "teamwork": 5,
      "leadership": 4,
      "punctuality": 4,
      "average_rating": 4.33
    },
    "status": "approved",
    "status_label": "Approved",
    "status_color": "green",
    "submitted_at": "2024-01-10T10:00:00.000000Z",
    "approved_by": {
      "id": "uuid-emp-hr",
      "full_name": "Sarah Wilson",
      "job_title": "HR Manager"
    },
    "approved_at": "2024-01-12T14:30:00.000000Z",
    "can_edit": false,
    "can_submit": false,
    "can_approve": false,
    "created_at": "2024-01-05T10:00:00.000000Z",
    "updated_at": "2024-01-12T14:30:00.000000Z"
  }
}
```

### 4. Update Performance Review
**PUT** `/api/v1/performance/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "achievements": "Successfully migrated 3 major components, mentored 2 junior developers effectively",
  "areas_for_improvement": "Focus on code documentation and improve time estimation",
  "overall_rating": 4,
  "technical_skills": 5,
  "communication_skills": 4,
  "teamwork": 5,
  "leadership": 4,
  "punctuality": 4,
  "comments": "Excellent performance this period. Strong technical and leadership skills."
}
```

**Success Response (200):**
```json
{
  "message": "Performance review updated successfully",
  "data": {
    "id": "uuid-review-1",
    "achievements": "Successfully migrated 3 major components, mentored 2 junior developers effectively",
    "ratings": {
      "overall_rating": 4,
      "technical_skills": 5,
      "average_rating": 4.33
    },
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### 5. Delete Performance Review
**DELETE** `/api/v1/performance/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Performance review deleted successfully"
}
```

### 6. Submit Performance Review
**POST** `/api/v1/performance/{id}/submit`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Performance review submitted successfully",
  "data": {
    "id": "uuid-review-1",
    "status": "submitted",
    "status_label": "Submitted",
    "status_color": "blue",
    "submitted_at": "2024-01-15T10:30:00.000000Z",
    "can_edit": false,
    "can_submit": false,
    "employee": {
      "full_name": "John Doe"
    },
    "reviewer": {
      "full_name": "Jane Smith"
    }
  }
}
```

### 7. Approve Performance Review
**POST** `/api/v1/performance/{id}/approve`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Performance review approved successfully",
  "data": {
    "id": "uuid-review-1",
    "status": "approved",
    "status_label": "Approved",
    "status_color": "green",
    "approved_by": {
      "id": "uuid-emp-hr",
      "full_name": "Sarah Wilson"
    },
    "approved_at": "2024-01-15T10:30:00.000000Z",
    "employee": {
      "full_name": "John Doe"
    },
    "reviewer": {
      "full_name": "Jane Smith"
    }
  }
}
```

### 8. Get Employee Performance Reviews
**GET** `/api/v1/performance/employee/{employeeId}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?year=2023
?status=approved
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-review-1",
      "reviewer": {
        "full_name": "Jane Smith",
        "job_title": "Engineering Manager"
      },
      "review_period_start": "2023-07-01",
      "review_period_end": "2023-12-31",
      "ratings": {
        "overall_rating": 4,
        "average_rating": 4.33
      },
      "status": "approved",
      "status_label": "Approved",
      "approved_at": "2024-01-12T14:30:00.000000Z",
      "created_at": "2024-01-05T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 4
  }
}
```

---

## Training Endpoints

### 1. Get Training Programs
**GET** `/api/v1/training`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?status=scheduled
?start_date=2024-01-01
?end_date=2024-12-31
?trainer=john
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-training-1",
      "title": "React Advanced Patterns",
      "description": "Advanced React patterns and best practices for senior developers",
      "trainer": "John Expert",
      "start_date": "2024-02-15",
      "end_date": "2024-02-17",
      "duration_hours": 24,
      "location": "Conference Room A / Virtual",
      "cost": 2500.00,
      "max_participants": 20,
      "current_participants": 15,
      "status": "scheduled",
      "status_label": "Scheduled",
      "status_color": "blue",
      "participants": [
        {
          "id": "uuid-emp-1",
          "full_name": "John Doe",
          "job_title": "Software Developer"
        }
      ],
      "can_enroll": true,
      "is_full": false,
      "created_at": "2024-01-10T10:00:00.000000Z",
      "updated_at": "2024-01-12T10:00:00.000000Z"
    },
    {
      "id": "uuid-training-2",
      "title": "Leadership Skills Workshop",
      "description": "Essential leadership skills for team leads and managers",
      "trainer": "Jane Leadership",
      "start_date": "2024-03-01",
      "end_date": "2024-03-02",
      "duration_hours": 16,
      "location": "Training Center",
      "cost": 1500.00,
      "max_participants": 15,
      "current_participants": 12,
      "status": "scheduled",
      "status_label": "Scheduled",
      "status_color": "blue",
      "can_enroll": true,
      "is_full": false,
      "created_at": "2024-01-08T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 38
  }
}
```

### 2. Create Training Program
**POST** `/api/v1/training`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "title": "DevOps Fundamentals",
  "description": "Introduction to DevOps practices, CI/CD, and infrastructure automation",
  "trainer": "DevOps Expert Inc.",
  "start_date": "2024-04-15",
  "end_date": "2024-04-17",
  "duration_hours": 24,
  "location": "Training Lab B",
  "cost": 3000.00,
  "max_participants": 25,
  "status": "scheduled"
}
```

**Success Response (201):**
```json
{
  "message": "Training program created successfully",
  "data": {
    "id": "uuid-training-new",
    "title": "DevOps Fundamentals",
    "description": "Introduction to DevOps practices, CI/CD, and infrastructure automation",
    "trainer": "DevOps Expert Inc.",
    "start_date": "2024-04-15",
    "end_date": "2024-04-17",
    "duration_hours": 24,
    "location": "Training Lab B",
    "cost": 3000.00,
    "max_participants": 25,
    "current_participants": 0,
    "status": "scheduled",
    "status_label": "Scheduled",
    "status_color": "blue",
    "can_enroll": true,
    "is_full": false,
    "created_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

### 3. Get Training Program Details
**GET** `/api/v1/training/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": {
    "id": "uuid-training-1",
    "title": "React Advanced Patterns",
    "description": "Advanced React patterns including hooks, context, render props, and performance optimization. Hands-on workshop with real-world examples.",
    "trainer": "John Expert - Senior React Developer with 8+ years experience",
    "start_date": "2024-02-15",
    "end_date": "2024-02-17",
    "duration_hours": 24,
    "location": "Conference Room A / Virtual (Hybrid)",
    "cost": 2500.00,
    "max_participants": 20,
    "current_participants": 15,
    "status": "scheduled",
    "status_label": "Scheduled",
    "status_color": "blue",
    "participants": [
      {
        "id": "uuid-emp-1",
        "employee_id": "EMP001",
        "full_name": "John Doe",
        "job_title": "Software Developer",
        "department": {
          "name": "Engineering"
        },
        "enrollment": {
          "status": "enrolled",
          "enrolled_at": "2024-01-12T10:00:00.000000Z"
        }
      },
      {
        "id": "uuid-emp-2",
        "full_name": "Alice Johnson",
        "job_title": "Frontend Developer",
        "enrollment": {
          "status": "enrolled",
          "enrolled_at": "2024-01-10T14:00:00.000000Z"
        }
      }
    ],
    "agenda": [
      "Day 1: Advanced Hooks and Custom Hooks",
      "Day 2: Performance Optimization and Profiling",
      "Day 3: Testing Patterns and Best Practices"
    ],
    "prerequisites": [
      "2+ years React experience",
      "Familiarity with ES6+ JavaScript",
      "Basic understanding of state management"
    ],
    "can_enroll": true,
    "is_full": false,
    "created_at": "2024-01-10T10:00:00.000000Z",
    "updated_at": "2024-01-12T10:00:00.000000Z"
  }
}
```

### 4. Update Training Program
**PUT** `/api/v1/training/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "title": "React Advanced Patterns & Performance",
  "description": "Advanced React patterns with focus on performance optimization",
  "location": "Training Center Room 1 / Virtual",
  "max_participants": 25,
  "cost": 2750.00
}
```

**Success Response (200):**
```json
{
  "message": "Training program updated successfully",
  "data": {
    "id": "uuid-training-1",
    "title": "React Advanced Patterns & Performance",
    "description": "Advanced React patterns with focus on performance optimization",
    "location": "Training Center Room 1 / Virtual",
    "max_participants": 25,
    "cost": 2750.00,
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### 5. Delete Training Program
**DELETE** `/api/v1/training/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:** No body required

**Success Response (200):**
```json
{
  "message": "Training program deleted successfully"
}
```

### 6. Enroll in Training
**POST** `/api/v1/training/{id}/enroll`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-3"
}
```

**Success Response (200):**
```json
{
  "message": "Employee enrolled in training successfully"
}
```

**Error Response (422):**
```json
{
  "message": "Training program is full"
}
```

### 7. Mark Training as Complete
**POST** `/api/v1/training/{id}/complete`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Request:**
```json
{
  "employee_id": "uuid-emp-1",
  "score": 92,
  "feedback": "Excellent participation and understanding of advanced concepts"
}
```

**Success Response (200):**
```json
{
  "message": "Training marked as complete for employee"
}
```

### 8. Get Employee Training History
**GET** `/api/v1/training/employee/{employeeId}`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**Query Parameters:**
```
?status=completed
?year=2023
?per_page=15
```

**Request:** No body required

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "uuid-training-1",
      "title": "React Advanced Patterns",
      "trainer": "John Expert",
      "start_date": "2024-02-15",
      "end_date": "2024-02-17",
      "duration_hours": 24,
      "cost": 2500.00,
      "status": "completed",
      "status_label": "Completed",
      "status_color": "green",
      "enrollment": {
        "status": "completed",
        "enrolled_at": "2024-01-12T10:00:00.000000Z",
        "completed_at": "2024-02-17T17:00:00.000000Z",
        "score": 92,
        "feedback": "Excellent participation and understanding of advanced concepts"
      },
      "created_at": "2024-01-10T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 8
  }
}
```

---

## Required Headers for All Endpoints

**Standard headers for authenticated requests:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "X-Company-ID": "{company_id}"
}
```

**For JSON requests (POST/PUT):**
```json
{
  "Content-Type": "application/json"
}
```

**For file uploads:**
```json
{
  "Content-Type": "multipart/form-data"
}
```