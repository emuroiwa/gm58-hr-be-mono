# Complete Laravel API Endpoints

## Authentication (`/api/v1/auth`)
```
POST   /auth/login
POST   /auth/register-company
GET    /auth/me
POST   /auth/update-profile
POST   /auth/change-password
POST   /auth/refresh-token
POST   /auth/logout
POST   /auth/logout-all
POST   /auth/forgot-password
POST   /auth/reset-password
```

## Dashboard (`/api/v1/dashboard`)
```
GET    /dashboard
GET    /dashboard/stats
```

## Employees (`/api/v1/employees`)
```
GET    /employees
POST   /employees
GET    /employees/{id}
PUT    /employees/{id}
DELETE /employees/{id}
POST   /employees/{id}/avatar
POST   /employees/import
POST   /employees/export
```

## Users (`/api/v1/users`)
```
GET    /users
POST   /users
GET    /users/{id}
PUT    /users/{id}
DELETE /users/{id}
POST   /users/{id}/activate
POST   /users/{id}/deactivate
POST   /users/{id}/reset-password
```

## Company (`/api/v1/company`)
```
GET    /company
PUT    /company
POST   /company/logo
GET    /company/settings
PUT    /company/settings
GET    /company/stats
GET    /company/organization-structure
```

## Departments (`/api/v1/departments`)
```
GET    /departments
POST   /departments
GET    /departments/{id}
PUT    /departments/{id}
DELETE /departments/{id}
GET    /departments/{id}/employees
```

## Positions (`/api/v1/positions`)
```
GET    /positions
POST   /positions
GET    /positions/{id}
PUT    /positions/{id}
DELETE /positions/{id}
GET    /positions/{id}/employees
GET    /positions/{id}/salary-stats
```

## Attendance (`/api/v1/attendance`)
```
GET    /attendance
POST   /attendance/checkin
POST   /attendance/checkout
GET    /attendance/today
POST   /attendance
PUT    /attendance/{id}
GET    /attendance/employee/{employeeId}
```

## Leaves (`/api/v1/leaves`)
```
GET    /leaves
POST   /leaves
GET    /leaves/{id}
POST   /leaves/{id}/approve
POST   /leaves/{id}/reject
GET    /leaves/balance
GET    /leaves/types
```

## Payroll (`/api/v1/payroll`)
```
GET    /payroll/periods
POST   /payroll/periods
GET    /payroll/periods/{id}
POST   /payroll/periods/{id}/process
GET    /payroll/periods/{id}/payrolls
GET    /payroll/employee/{employeeId}
GET    /payroll/{id}
GET    /payroll/{id}/payslip
```

## Benefits (`/api/v1/benefits`)
```
GET    /benefits
POST   /benefits
GET    /benefits/{id}
PUT    /benefits/{id}
DELETE /benefits/{id}
POST   /benefits/{id}/enroll
POST   /benefits/{id}/unenroll
GET    /benefits/employee/{employeeId}
```

## Performance (`/api/v1/performance`)
```
GET    /performance
POST   /performance
GET    /performance/{id}
PUT    /performance/{id}
DELETE /performance/{id}
POST   /performance/{id}/submit
POST   /performance/{id}/approve
GET    /performance/employee/{employeeId}
```

## Training (`/api/v1/training`)
```
GET    /training
POST   /training
GET    /training/{id}
PUT    /training/{id}
DELETE /training/{id}
POST   /training/{id}/enroll
POST   /training/{id}/complete
GET    /training/employee/{employeeId}
```

## Timesheets (`/api/v1/timesheets`)
```
GET    /timesheets
POST   /timesheets
GET    /timesheets/{id}
PUT    /timesheets/{id}
DELETE /timesheets/{id}
POST   /timesheets/submit
POST   /timesheets/{id}/approve
POST   /timesheets/{id}/reject
GET    /timesheets/employee/{employeeId}
```

## Notifications (`/api/v1/notifications`)
```
GET    /notifications
GET    /notifications/unread
POST   /notifications/{id}/read
POST   /notifications/mark-all-read
DELETE /notifications/{id}
GET    /notifications/settings
PUT    /notifications/settings
```

## Reports (`/api/v1/reports`)
```
GET    /reports/dashboard
GET    /reports/employees
GET    /reports/attendance
GET    /reports/payroll
GET    /reports/leaves
GET    /reports/performance
POST   /reports/generate
GET    /reports/downloads
GET    /reports/download/{filename}
```

## Settings (`/api/v1/settings`)
```
GET    /settings
PUT    /settings
GET    /settings/currencies
GET    /settings/timezones
```

## System Admin (`/api/v1/system`)
```
POST   /system/backup
GET    /system/backups
GET    /system/logs
POST   /system/maintenance
GET    /system/health
```

## File Management (`/api/v1/files`)
```
POST   /files/upload
GET    /files/download/{fileId}
DELETE /files/{fileId}
```

## Web Downloads (for authenticated file downloads)
```
GET    /download/payslip/{payrollId}
GET    /download/report/{reportId}
GET    /download/document/{documentId}
```

---

## Common Query Parameters

Most listing endpoints support these filters:
```
?page=1
?per_page=15
?search=keyword
?status=active
?sort_by=created_at
?sort_order=desc
?date_from=2023-01-01
?date_to=2023-12-31
?department_id=uuid
?employee_id=uuid
```

## Response Patterns

### Success Response Structure:
```json
{
  "message": "Success message",
  "data": { /* resource data */ },
  "meta": { /* pagination/context */ }
}
```

### Paginated Response Structure:
```json
{
  "data": [ /* array of resources */ ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Error Response Structure:
```json
{
  "message": "Error message",
  "errors": { /* validation errors */ },
  "code": "ERROR_CODE"
}
```

---

## Required Headers for All Authenticated Requests:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
X-Company-ID: {company_id} // Optional, fallback to user's company
```