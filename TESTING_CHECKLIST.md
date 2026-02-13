# ✅ TESTING CHECKLIST - Bug Fixes Verification

**Purpose:** Verify all bug fixes are working correctly  
**Date:** February 12, 2026  
**Status:** Ready for Testing

---

## 🎯 PRE-TESTING SETUP

Before testing, ensure:

```bash
# 1. Run the migration
php artisan migrate

# 2. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 3. Verify database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

---

## 🔴 CRITICAL BUG FIXES - TESTING

### ✅ Test #1: Cart Total Calculation

**Bug Fixed:** Incorrect total price calculation in checkout

**Test Steps:**

1. Add 2-3 different products to cart with different quantities
2. Note the prices shown in cart
3. Proceed to checkout
4. Check the order in database: `SELECT * FROM orders ORDER BY id DESC LIMIT 1;`
5. Verify `total_price` = sum of (product prices × quantities) + shipping

**Expected Result:**

- ✅ Total price matches manual calculation
- ✅ No errors during checkout

---

### ✅ Test #2: Cost Price Field

**Bug Fixed:** Missing harga_modal (cost price) field

**Test Steps:**

1. Login as admin
2. Go to Create Product page
3. Try to submit WITHOUT cost price (harga_modal)
4. Should see validation error
5. Fill all fields including cost price
6. Submit and verify product created
7. Check database: `SELECT nama, harga_per_kg, harga_modal FROM produks WHERE id = [new_id];`

**Expected Result:**

- ✅ Validation error if cost price missing
- ✅ Cost price saved correctly in database
- ✅ Profit calculations work

---

### ✅ Test #3: Stock Manipulation Protection

**Bug Fixed:** Admin could set stock below reserved amount

**Test Steps:**

1. Create an order as customer (creates reserved stock)
2. Login as admin
3. Go to edit the product that was ordered
4. Note the `reserved_stock` value in database
5. Try to set `stok` lower than `reserved_stock`
6. Should see validation error

**Expected Result:**

- ✅ Cannot set stock below reserved_stock
- ✅ Clear error message shown
- ✅ Form retains input values

**SQL to check:**

```sql
SELECT nama, stok, reserved_stock FROM produks WHERE id = [id];
```

---

### ✅ Test #4: Admin Deletion Protection

**Bug Fixed:** Admin accounts could be deleted

**Test Steps:**

1. Login as admin
2. Go to User Management
3. Try to delete another admin user
4. Should see error message
5. Try to delete a regular customer WITH active orders
6. Should see error about active orders
7. Delete customer WITHOUT active orders
8. Should succeed

**Expected Result:**

- ✅ Cannot delete admin accounts
- ✅ Cannot delete users with active orders
- ✅ Can delete customers without active orders

---

### ✅ Test #5: Low Stock Threshold Required

**Bug Fixed:** Products could be created without threshold

**Test Steps:**

1. Go to Create Product
2. Try to submit without `low_stock_threshold`
3. Should see validation error
4. Set threshold to 10
5. Submit and verify
6. Check database: `SELECT nama, stok, low_stock_threshold FROM produks WHERE id = [id];`

**Expected Result:**

- ✅ Validation enforces threshold field
- ✅ Default value of 10 if not specified
- ✅ Low stock alerts will work

---

## 🟠 HIGH-PRIORITY FIXES - TESTING

### ✅ Test #6: Strong Random Password

**Bug Fixed:** Weak default password 'password123'

**Test Steps:**

1. Login as admin
2. Go to User Management
3. Reset a user's password
4. Note the displayed password (should be like FM202602121234)
5. Logout and try to login as that user with new password
6. Should be forced to change password

**Expected Result:**

- ✅ Random strong password generated
- ✅ Password contains date and random numbers
- ✅ User forced to change on first login

---

### ✅ Test #7: Rate Limiting

**Bug Fixed:** No protection against brute force/spam

**Test Steps:**

**Login Rate Limit (5 per minute):**

1. Go to login page
2. Try to login with wrong password 6 times rapidly
3. 6th attempt should be blocked with 429 error

**Chat Rate Limit (30 per minute):**

1. Login as customer
2. Open chat
3. Try to send 31 messages very quickly
4. Should be rate limited

**Payment Upload Rate Limit (10 per minute):**

1. Create an order
2. Try to upload payment proof 11 times
3. Should be rate limited

**Expected Result:**

- ✅ Rate limits enforced
- ✅ 429 Too Many Requests error shown
- ✅ Rate limit resets after 1 minute

---

### ✅ Test #8: XSS Protection in Chat

**Bug Fixed:** HTML/JavaScript could be injected

**Test Steps:**

1. Login as customer
2. Open chat with admin
3. Send message: `<script>alert('XSS')</script>Hello`
4. Check in admin chat view
5. Verify script tags are stripped

**Expected Result:**

- ✅ Only "Hello" appears in message
- ✅ Script tags completely removed
- ✅ No JavaScript execution

---

### ✅ Test #9: Chat Pagination

**Bug Fixed:** All messages loaded at once

**Test Steps:**

1. Login as customer
2. Open chat (or create 60+ messages first)
3. Check browser Network tab
4. Verify only latest 50 messages loaded
5. Database query should have `LIMIT 50`

**Expected Result:**

- ✅ Only 50 messages loaded initially
- ✅ Fast page load even with many messages
- ✅ Messages in correct order

---

### ✅ Test #10: Stock Validation on Manual Status Update

**Bug Fixed:** Status could be changed without checking stock

**Test Steps:**

1. Create order as customer (reserves stock)
2. Login as admin, edit that product
3. Reduce actual stock below reserved amount (via database)
4. Try to manually change order status to 'paid'
5. Should see error about insufficient stock

**Expected Result:**

- ✅ Cannot confirm order if stock insufficient
- ✅ Clear error message
- ✅ Stock reservation protected

**SQL to test:**

```sql
-- Simulate low stock
UPDATE produks SET stok = 5 WHERE id = 1;
UPDATE produks SET reserved_stock = 10 WHERE id = 1;
-- Now try to verify payment in admin panel
```

---

### ✅ Test #11: Cart Quantity Limits

**Bug Fixed:** No maximum limit on cart quantities

**Test Steps:**

1. Go to product page
2. Try to add 600 Kg to cart
3. Should see validation error
4. Add 500 Kg (should succeed)
5. Try to update cart to 501 Kg
6. Should see validation error

**Expected Result:**

- ✅ Maximum 500 Kg per product enforced
- ✅ Clear error message
- ✅ Minimum 0.5 Kg still works

---

## 🎯 FULL WORKFLOW TESTING

### Complete Order Flow Test:

**Customer Side:**

1. [ ] Browse products
2. [ ] Add to cart (multiple products)
3. [ ] Update quantities
4. [ ] Remove items
5. [ ] Checkout
6. [ ] Upload payment proof
7. [ ] Track order status
8. [ ] Add review after completion
9. [ ] Use wishlist
10. [ ] Chat with admin

**Admin Side:**

1. [ ] View new orders
2. [ ] Verify payment proof
3. [ ] Confirm order with delivery details
4. [ ] Update status to 'out_for_delivery'
5. [ ] Complete order
6. [ ] Check dashboard statistics
7. [ ] Review low stock alerts
8. [ ] Manage users
9. [ ] Generate reports
10. [ ] Chat with customers

**Expected Result:**

- ✅ All steps complete without errors
- ✅ Stock properly managed
- ✅ Notifications sent
- ✅ Data accurate in database

---

## 🔍 DATABASE INTEGRITY CHECKS

Run these SQL queries to verify data integrity:

```sql
-- 1. Check for products without cost price (should be 0)
SELECT COUNT(*) FROM produks WHERE harga_modal IS NULL;

-- 2. Check for negative available stock (should be 0)
SELECT COUNT(*) FROM produks WHERE (stok - reserved_stock) < 0;

-- 3. Check for products without threshold (should be 0)
SELECT COUNT(*) FROM produks WHERE low_stock_threshold IS NULL;

-- 4. Verify order totals match items
SELECT o.id, o.order_number, o.total_price, o.shipping_cost,
       SUM(oi.subtotal) as items_total,
       (SUM(oi.subtotal) + o.shipping_cost) as calculated_total
FROM orders o
INNER JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
HAVING ABS(o.total_price - calculated_total) > 0.01;
-- Should return 0 rows

-- 5. Check admin accounts (should have at least 1)
SELECT COUNT(*) FROM users WHERE role = 'admin';
```

**Expected Results:**

- All counts should be appropriate
- No data integrity issues
- Orders calculate correctly

---

## 📋 SECURITY VERIFICATION

### Security Checklist:

**Application Settings:**

- [ ] `APP_DEBUG=false` in production .env
- [ ] `APP_ENV=production` in production .env
- [ ] Strong `APP_KEY` generated
- [ ] HTTPS enabled (production)
- [ ] Secure session cookies configured

**Route Protection:**

- [ ] Admin routes require 'admin' middleware
- [ ] User routes require 'auth' middleware
- [ ] Guest routes properly protected
- [ ] Rate limiting active on critical routes

**Input Validation:**

- [ ] All forms validate input
- [ ] File uploads validate type and size
- [ ] Chat messages sanitized
- [ ] SQL injection prevented (using Eloquent)

**Password Security:**

- [ ] Passwords hashed (bcrypt)
- [ ] Strong random passwords generated
- [ ] Password change forced after reset

---

## 🚨 ERROR TESTING

Test error handling:

1. **Database Connection Error:**
    - Temporarily change DB password in .env
    - Try to load page
    - Should show error page (not raw exception)

2. **File Upload Error:**
    - Try to upload 20MB image
    - Should show validation error

3. **Invalid Order Access:**
    - Try to access another user's order URL
    - Should get 403 Forbidden

4. **Expired Payment:**
    - Create order, wait for payment deadline
    - Try to upload payment
    - Should show appropriate message

**Expected Results:**

- ✅ Graceful error handling
- ✅ User-friendly messages
- ✅ No sensitive data exposed

---

## 📊 PERFORMANCE TESTING

### Load Testing (Optional):

**Chat Performance:**

1. Create 100 messages in database
2. Load chat page
3. Check query count (should only fetch 50)
4. Verify page load time < 2 seconds

**Dashboard Performance:**

1. Create 1000+ orders
2. Load admin dashboard
3. Check query optimization
4. Verify charts load < 3 seconds

**Product Listing:**

1. Create 100+ products
2. Load catalog page
3. Verify pagination works
4. Check page load time < 2 seconds

---

## ✅ FINAL VERIFICATION

After completing all tests:

```bash
# Check Laravel logs for errors
tail -n 100 storage/logs/laravel.log

# Verify migrations
php artisan migrate:status

# Check queue jobs (if using)
php artisan queue:failed

# Verify scheduled tasks
php artisan schedule:list
```

---

## 📝 TEST RESULTS TEMPLATE

```
=== BUG FIX TESTING REPORT ===
Date: _______________
Tester: _______________

CRITICAL BUGS:
[ ] Test #1: Cart Total Calculation - PASS/FAIL
[ ] Test #2: Cost Price Field - PASS/FAIL
[ ] Test #3: Stock Manipulation - PASS/FAIL
[ ] Test #4: Admin Deletion - PASS/FAIL
[ ] Test #5: Low Stock Threshold - PASS/FAIL

HIGH PRIORITY:
[ ] Test #6: Strong Password - PASS/FAIL
[ ] Test #7: Rate Limiting - PASS/FAIL
[ ] Test #8: XSS Protection - PASS/FAIL
[ ] Test #9: Chat Pagination - PASS/FAIL
[ ] Test #10: Stock Validation - PASS/FAIL
[ ] Test #11: Cart Limits - PASS/FAIL

WORKFLOW:
[ ] Complete Customer Flow - PASS/FAIL
[ ] Complete Admin Flow - PASS/FAIL

DATABASE INTEGRITY:
[ ] All queries return expected results - PASS/FAIL

SECURITY:
[ ] All security checks passed - PASS/FAIL

ISSUES FOUND:
1. ______________________________
2. ______________________________
3. ______________________________

OVERALL STATUS: ✅ READY / ⚠️ NEEDS ATTENTION / ❌ NOT READY

Notes:
_________________________________
_________________________________
_________________________________
```

---

**Good luck with testing!** 🚀

If you find any issues, check:

1. Laravel logs (`storage/logs/laravel.log`)
2. Database migrations status
3. Cache cleared
4. Configuration synced
