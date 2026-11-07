# Deployment Guide: Optional Insurance Feature

## 📋 Pre-Deployment Checklist

### Files Changed (Ready to Deploy):
- ✅ `app/controllers/LoanController.php` - Captures insurance checkbox
- ✅ `app/models/LoanModel.php` - Conditional insurance logic
- ✅ `app/views/loans/form.php` - Insurance checkbox UI
- ✅ `database/migrations/add_insurance_applicable_flag.sql` - Database schema

---

## 🚀 Deployment Steps

### Step 1: Commit Changes to Git

```bash
cd "c:\xampp\htdocs\empower management-sys.live"

# Stage the insurance-related files
git add app/controllers/LoanController.php
git add app/models/LoanModel.php
git add app/views/loans/form.php
git add database/migrations/add_insurance_applicable_flag.sql

# Commit with clear message
git commit -m "feat: Add optional insurance checkbox for historical loans

- Add insurance_applicable column to loans table
- Add checkbox to loan form (checked by default)
- Update LoanModel to conditionally apply 4% insurance
- Allows recording loans from before insurance implementation
"

# Push to your remote repository
git push origin main
```

---

### Step 2: Pull Changes on Live Server

**SSH into your DirectAdmin server:**

```bash
ssh your_username@your_server.com
cd ~/public_html  # or wherever your app is

# Pull latest changes
git pull origin main
```

---

### Step 3: Run Database Migration on Live Server

**Option A: Via phpMyAdmin (Recommended)**

1. Log into DirectAdmin → phpMyAdmin
2. Select your live database
3. Click **SQL** tab
4. Run this SQL:

```sql
-- Check if column already exists
SHOW COLUMNS FROM loans LIKE 'insurance_applicable';

-- If it doesn't exist, add it:
ALTER TABLE loans 
ADD COLUMN insurance_applicable TINYINT(1) DEFAULT 1 
COMMENT 'Whether insurance applies to this loan (0=no insurance, 1=insurance applies)';

-- Verify it was added:
SHOW COLUMNS FROM loans LIKE 'insurance_applicable';
```

**Option B: Via SSH/Terminal**

```bash
# SSH into server
cd ~/public_html

# Run the migration
php run_insurance_applicable_migration.php
```

---

### Step 4: Verify Deployment

**1. Check File Upload:**
- Go to your live site: `https://yourdomain.com`
- Log in as admin
- Go to **Loans → New Loan**
- **Look for** the "Insurance Applicable" checkbox after Processing Fee field

**2. Check Database:**
```sql
-- In phpMyAdmin, run:
DESCRIBE loans;
-- You should see "insurance_applicable" column
```

**3. Test Functionality:**

**Test A: New Loan WITH Insurance**
1. Create a test loan
2. Leave insurance checkbox **CHECKED**
3. Amount: Shs 100,000
4. Disburse it
5. Check that member receives: Shs 96,000 (after 4% insurance)

**Test B: Old Loan WITHOUT Insurance**
1. Create another test loan
2. **UNCHECK** insurance checkbox
3. Amount: Shs 100,000
4. Disburse it
5. Check that member receives: Shs 100,000 (full amount)

---

## 🔧 Troubleshooting

### Issue: Column already exists error
```sql
-- Just verify it's there:
SELECT insurance_applicable FROM loans LIMIT 1;
```
If it returns without error, you're good!

### Issue: Checkbox not showing
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)
- Check file permissions on server

### Issue: Git conflicts
```bash
# Backup your changes first
git stash

# Pull latest
git pull origin main

# Re-apply your changes
git stash pop
```

---

## 📝 Rollback Plan (If Needed)

If something goes wrong:

```sql
-- Remove the column:
ALTER TABLE loans DROP COLUMN insurance_applicable;
```

```bash
# Revert Git changes:
git revert HEAD
git push origin main
```

---

## ✅ Post-Deployment Verification

- [ ] Loan form shows insurance checkbox
- [ ] Checkbox is checked by default
- [ ] Can uncheck for old loans
- [ ] Insurance calculated when checked
- [ ] No insurance when unchecked
- [ ] Disbursement amounts correct
- [ ] Journal entries correct

---

## 📞 Support Notes

**What this feature does:**
- Adds optional insurance control per loan
- Default: Insurance ON (4% deduction)
- Can be turned OFF for historical loans
- No impact on existing loans

**Why we need it:**
- Some loans from last month were issued BEFORE insurance started
- Those old loans should not have insurance deducted
- This checkbox lets you control it per loan

**Database impact:**
- 1 new column: `insurance_applicable`
- Default value: 1 (insurance ON)
- No data loss
- Backward compatible
