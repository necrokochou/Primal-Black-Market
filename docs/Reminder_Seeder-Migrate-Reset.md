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

## 🗃️ Database Migration & Seeding Commands

### **⚠️ IMPORTANT: Run in this exact order!**

#### 1. **Users Database** (Run First)
```bash
# Create users table
docker exec primal-black-market-service php utils/dbMigrateUsersPostgresql.util.php

# Seed users data
docker exec primal-black-market-service php utils/dbSeederUsersPostgresql.util.php
```

#### 2. **Categories Database**
```bash
# Create categories table
docker exec primal-black-market-service php utils/dbMigrateCategoriesPostgresql.util.php

# Seed categories data
docker exec primal-black-market-service php utils/dbSeederCategoriesPostgresql.util.php
```

#### 3. **Listings Database** (Requires Users)
```bash
# Create listings table
docker exec primal-black-market-service php utils/dbMigrateListingsPostgresql.util.php

# Seed listings data
docker exec primal-black-market-service php utils/dbSeederListingsPostgresql.util.php
```

#### 4. **Feedbacks Database** (Requires Users)
```bash
# Create feedbacks table
docker exec primal-black-market-service php utils/dbMigrateFeedbacksPostgresql.util.php

# Seed feedbacks data
docker exec primal-black-market-service php utils/dbSeederFeedbacksPostgresql.util.php
```

#### 5. **Messages Database** (Requires Users)
```bash
# Create messages table
docker exec primal-black-market-service php utils/dbMigrateMessagesPostgresql.util.php

# Seed messages data
docker exec primal-black-market-service php utils/dbSeederMessagesPostgresql.util.php
```

#### 6. **Transactions Database** (Requires Users & Listings)
```bash
# Create transactions table
docker exec primal-black-market-service php utils/dbMigrateTransactionsPostgresql.util.php

# Seed transactions data
docker exec primal-black-market-service php utils/dbSeederTransactionsPostgresql.util.php
```

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

### **Reset Database** (Nuclear Option)
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