# Database Migration & Seeding Guide

## 🚨 IMPORTANT
Run these commands in terminal whenever you use Seeder, Migrate, and Reset  
**⚠️ (With Approval of Head Admin (LJ))**

---

## 📋 Pre-Requirements & Setup

### 1. **Ensure Docker Services are Running**
```bash
# Check if services are running
docker-compose ps

# Start services if not running
docker-compose up -d
```

### 2. **Install Dependencies**
```bash
# Install Composer dependencies
composer install
```

### 3. **Environment Configuration**
Ensure `.env` file exists with proper database configuration:
```
PG_HOST=postgresql
PG_PORT=5432
PG_DB=primal-black-market
PG_USER=user
PG_PASS=password
```

### 4. **Copy Files to Docker Container** (if needed)
```bash
# Copy migration/seeder files to container
docker cp utils/ primal-black-market-service:/var/www/html/
docker cp staticData/ primal-black-market-service:/var/www/html/
docker cp .env primal-black-market-service:/var/www/html/
```

### 5. **Database Connection Test**
```bash
# Test database connection
docker exec primal-black-market-service php utils/dbVerifyTablesSimple.util.php
```

---

## 🚀 Quick Start Commands (Recommended)

### **🔥 Complete Database Reset (3-Step Process)**
```bash
# Step 1: Reset database (DELETE ALL DATA)
docker exec primal-black-market-service php utils/dbResetPostgresql.util.php

# Step 2: Run all migrations 
docker exec primal-black-market-service php utils/dbMigrateAllPostgresql.util.php

# Step 3: Run all seeders
docker exec primal-black-market-service php utils/dbSeederAllPostgresql.util.php
```

### **🧱 Migrate All Tables**
```bash
# Run all migrations in correct order
docker exec primal-black-market-service php utils/dbMigrateAllPostgresql.util.php
```

### **🌱 Seed All Tables**
```bash
# Run all seeders in correct order
docker exec primal-black-market-service php utils/dbSeederAllPostgresql.util.php
```

---

## 🔥 Database Reset (Reset Only)

### **🚨 DANGER ZONE: Reset Database**
```bash
# This will DELETE ALL DATA only (no migrations/seeders)
docker exec primal-black-market-service php utils/dbResetPostgresql.util.php
```

**⚠️ Use this when:**
- Starting fresh development
- Database is corrupted
- Need to clear all data

**✅ This script will:**
- Give you 5 seconds to cancel
- Drop all existing tables
- Clear all data completely
- Show next steps to run


---

## 🔍 Testing & Verification

### **Quick Database Check**
```bash
# Check table counts using verification script
docker exec primal-black-market-service php utils/dbVerifyTablesSimple.util.php
```

### **Manual Database Check** (Alternative)
```bash
# Check table counts manually
docker exec primal-black-market-service php -r "
try {
    \$pdo = new PDO('pgsql:host=postgresql;port=5432;dbname=primal-black-market', 'user', 'password');
    \$tables = ['users', 'categories', 'listings', 'feedbacks', 'messages', 'transactions'];
    foreach(\$tables as \$table) {
        \$stmt = \$pdo->query(\"SELECT COUNT(*) FROM \$table\");
        echo \"\$table: \" . \$stmt->fetchColumn() . \" records\n\";
    }
} catch(Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}
"
```

---

## 🆘 Troubleshooting

### **Common Issues:**
1. **Connection Error**: Check if Docker services are running
2. **File Not Found**: Copy files to container using `docker cp`
3. **Foreign Key Error**: Run migrations in correct order (Users first!)
4. **Permission Error**: Ensure proper file permissions
5. **Foreign Key Constraint Violation**: When re-seeding, dependent tables must be cleared first

### **🔄 Re-seeding Database (Important!)**
If you need to re-seed data, follow this order to avoid foreign key violations:

```bash
# Clear and re-seed in dependency order
docker exec primal-black-market-service php utils/dbSeederTransactionsPostgresql.util.php
docker exec primal-black-market-service php utils/dbSeederFeedbacksPostgresql.util.php  
docker exec primal-black-market-service php utils/dbSeederListingsPostgresql.util.php
docker exec primal-black-market-service php utils/dbSeederMessagesPostgresql.util.php
docker exec primal-black-market-service php utils/dbSeederCategoriesPostgresql.util.php
docker exec primal-black-market-service php utils/dbSeederUsersPostgresql.util.php
```

**⚠️ Note**: The listings seeder now automatically clears dependent tables (transactions, feedbacks) before clearing listings to prevent foreign key violations.

### **Database Verification**
```bash
# Use the verification script to check all tables
docker exec primal-black-market-service php utils/dbVerifyTablesSimple.util.php
```

### **🔥 Complete Database Reset** (Nuclear Option)
```bash
# ⚠️ DANGER: This will delete ALL data and recreate everything!
# Use the comprehensive reset script
docker exec primal-black-market-service php utils/dbResetPostgresql.util.php
```

**What the reset script does:**
1. **5-second countdown warning** - Gives you time to cancel
2. **Drops all tables** - Complete clean slate
3. **Runs all migrations** - Recreates table structure  
4. **Runs all seeders** - Populates with fresh data
5. **Verifies results** - Shows final record counts

### **Manual Database Reset** (Alternative)
```bash
# ⚠️ DANGER: This will delete all data!
docker exec primal-black-market-service php -r "
\$pdo = new PDO('pgsql:host=postgresql;port=5432;dbname=primal-black-market', 'user', 'password');
\$pdo->exec('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');
echo 'Database reset complete!';
"
```

---

## 📊 Expected Results
- **Users**: 8 records (phantommorphus, necrokochou, etc.)
- **Categories**: 12 records (Weapons, Hunting Equipment, etc.)
- **Listings**: ~45 records (Raptor Claw Knife, etc.)
- **Feedbacks**: 4 records (ratings and comments)
- **Messages**: 5 records (prehistoric chat messages)
- **Transactions**: 3 records (purchase history)