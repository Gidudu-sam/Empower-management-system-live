# How to Upload Early Payment Waiver Feature to Live Server

## 📦 Files to Upload

### Modified Files (3 files - REQUIRED):

1. **app/controllers/RepaymentController.php**
   - Added `waive_interest` parameter collection

2. **app/models/RepaymentModel.php**
   - Added interest waiver logic in repayment allocation

3. **app/views/repayments/form.php**
   - Added checkbox UI
   - Added JavaScript for auto-detection and calculation

### Documentation Files (2 files - OPTIONAL):

4. **FEATURE_EARLY_PAYMENT_INTEREST_WAIVER.md**
   - Complete feature documentation
   - For reference only (not required for functionality)

5. **README_FIX_LNS000059.txt**
   - One-time SQL fix instructions
   - Already used, can be kept for records

## 🚀 Upload Methods

### Method 1: Using Git (Recommended)

#### Step 1: Stage the Files
```bash
cd "c:\xampp\htdocs\empower management-sys.live"

# Add only the required files
git add app/controllers/RepaymentController.php
git add app/models/RepaymentModel.php
git add app/views/repayments/form.php

# Optionally add documentation
git add FEATURE_EARLY_PAYMENT_INTEREST_WAIVER.md
```

#### Step 2: Commit the Changes
```bash
git commit -m "Add early payment interest waiver feature

- Added waive_interest checkbox in repayment form
- Updated RepaymentModel to handle interest waiver
- Updated RepaymentController to collect waiver flag
- Added smart detection and auto-calculation in UI
- Allows staff to waive interest on early installment payments"
```

#### Step 3: Push to Server
```bash
git push origin main
```

#### Step 4: On Your Live Server (SSH)
```bash
# SSH into your server
ssh username@your-server.com

# Navigate to the project directory
cd /path/to/empower-system

# Pull the changes
git pull origin main

# Verify files were updated
ls -la app/controllers/RepaymentController.php
ls -la app/models/RepaymentModel.php
ls -la app/views/repayments/form.php
```

---

### Method 2: Using FTP/SFTP (Alternative)

#### Using FileZilla or WinSCP:

1. **Connect to your server:**
   - Host: your-server.com
   - Username: your-username
   - Password: your-password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Navigate to the project folder:**
   - Remote: `/home/username/public_html/` (or your path)
   - Local: `C:\xampp\htdocs\empower management-sys.live\`

3. **Upload these files (drag and drop):**
   ```
   Local → Remote (overwrite existing)
   
   app/controllers/RepaymentController.php
   → /home/username/public_html/app/controllers/RepaymentController.php
   
   app/models/RepaymentModel.php
   → /home/username/public_html/app/models/RepaymentModel.php
   
   app/views/repayments/form.php
   → /home/username/public_html/app/views/repayments/form.php
   ```

4. **Verify file permissions (if needed):**
   - Right-click each file → File permissions
   - Set to: 644 (Owner: Read+Write, Group: Read, Public: Read)

---

### Method 3: Using cPanel File Manager

1. **Login to cPanel:**
   - Go to: https://your-server.com/cpanel
   - Enter credentials

2. **Open File Manager:**
   - Click "File Manager" icon
   - Navigate to `public_html` or your installation directory

3. **Upload files one by one:**

   **For RepaymentController.php:**
   - Navigate to: `app/controllers/`
   - Click "Upload" button
   - Select: `C:\xampp\htdocs\empower management-sys.live\app\controllers\RepaymentController.php`
   - Overwrite existing file

   **For RepaymentModel.php:**
   - Navigate to: `app/models/`
   - Upload: `RepaymentModel.php`
   - Overwrite

   **For form.php:**
   - Navigate to: `app/views/repayments/`
   - Upload: `form.php`
   - Overwrite

4. **Set permissions (if needed):**
   - Select each file → Permissions → Set to 644

---

## ✅ Verification Steps (After Upload)

### 1. Check Files Are Updated

**Via SSH:**
```bash
# Check file modification dates
ls -lh app/controllers/RepaymentController.php
ls -lh app/models/RepaymentModel.php
ls -lh app/views/repayments/form.php

# Check file sizes (should be larger than before)
du -h app/views/repayments/form.php
```

**Via Browser:**
- Visit: `https://your-server.com/index.php?page=repayment-add`
- Check if page loads without errors

### 2. Test the Feature

1. **Login to the system**
   - Go to your live site
   - Login as admin or loans officer

2. **Navigate to Record Repayment**
   - Menu: Repayments → Record Repayment

3. **Select a loan with outstanding balance**
   - Search for any active loan
   - Select it

4. **Check if checkbox appears**
   - Enter an amount close to principal
   - Look for: ✅ "Early Payment - Waive Remaining Interest"
   - If visible, feature is working! ✓

5. **Test calculation (without saving)**
   - Check the checkbox
   - Verify interest shows 0.00
   - Uncheck it
   - Verify interest calculates normally

### 3. Check for Errors

**PHP Error Log:**
```bash
# On server, check for PHP errors
tail -f /path/to/error.log

# Or via cPanel → Error Log
```

**Browser Console:**
- Open browser DevTools (F12)
- Go to Console tab
- Look for JavaScript errors

---

## 🔧 Troubleshooting

### Issue 1: Checkbox Not Appearing

**Check:**
1. Verify `form.php` uploaded correctly
2. Clear browser cache (Ctrl + F5)
3. Check JavaScript console for errors

**Fix:**
```bash
# Re-upload form.php
# Clear server cache if using caching
```

### Issue 2: Interest Not Being Waived

**Check:**
1. Verify `RepaymentModel.php` uploaded correctly
2. Check PHP error log
3. Verify database connection

**Fix:**
```bash
# Re-upload RepaymentModel.php
# Restart PHP-FPM if needed
```

### Issue 3: Page Won't Load

**Check:**
1. PHP syntax errors
2. File permissions
3. Server logs

**Fix:**
```bash
# Check syntax
php -l app/controllers/RepaymentController.php
php -l app/models/RepaymentModel.php

# Fix permissions
chmod 644 app/controllers/RepaymentController.php
chmod 644 app/models/RepaymentModel.php
chmod 644 app/views/repayments/form.php
```

---

## 📋 Quick Checklist

Before upload:
- [ ] Backup current files on server
- [ ] Test feature on local XAMPP (works ✓)
- [ ] Identify upload method (Git/FTP/cPanel)

During upload:
- [ ] Upload RepaymentController.php
- [ ] Upload RepaymentModel.php
- [ ] Upload form.php
- [ ] Check file permissions (644)

After upload:
- [ ] Visit repayment form page
- [ ] Check for errors
- [ ] Test checkbox visibility
- [ ] Test calculation (don't save)
- [ ] Verify with actual payment (optional)
- [ ] Monitor for 24 hours

---

## 🎯 Recommended Approach

**For your situation, I recommend:**

### Option A: Git Push (If your server uses Git)
```bash
# Local
git add app/controllers/RepaymentController.php app/models/RepaymentModel.php app/views/repayments/form.php FEATURE_EARLY_PAYMENT_INTEREST_WAIVER.md
git commit -m "Add early payment interest waiver feature"
git push origin main

# Server (SSH)
ssh username@server
cd /path/to/project
git pull origin main
```

### Option B: FTP (If no Git access)
1. Open FileZilla
2. Connect to server
3. Navigate to folders
4. Drag and drop 3 files
5. Overwrite when prompted
6. Done!

---

## 💾 Backup Instructions (IMPORTANT!)

**Before uploading, backup these files on server:**

```bash
# On server
cd /path/to/project
cp app/controllers/RepaymentController.php app/controllers/RepaymentController.php.backup
cp app/models/RepaymentModel.php app/models/RepaymentModel.php.backup
cp app/views/repayments/form.php app/views/repayments/form.php.backup
```

**Or download them via FTP before overwriting.**

---

## 📞 Need Help?

If you encounter issues during upload:

1. **Check this document first**
2. **Check server error logs**
3. **Restore from backup if needed**
4. **Test on staging server first (if available)**

---

## ✨ Success Indicators

You'll know it worked when:
- ✅ Repayment form loads without errors
- ✅ Checkbox appears when entering principal amount
- ✅ Interest calculation updates when checking/unchecking
- ✅ Payment can be recorded with waived interest
- ✅ Loan completes properly with 0 interest on that installment

---

**Ready to upload? Follow Method 1 (Git) or Method 2 (FTP) above!** 🚀
