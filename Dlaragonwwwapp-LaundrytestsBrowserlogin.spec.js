const { test } = require('@playwright/test');
test('login and check dashboard', async ({ page }) => {
  page.on('console', msg => console.log('[CONSOLE]', msg.type(), msg.text()));
  page.on('pageerror', err => console.log('[PAGE_ERR]', err.message));
  
  await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
  console.log('Page loaded');
  
  await page.fill('input[name=email]', 'admin@laundry.com');
  await page.fill('input[name=password]', 'password123');
  await page.click('button[type=submit]');
  console.log('Submitted');
  
  await page.waitForURL('**/dashboard', { timeout: 15000 });
  console.log('On dashboard');
  
  await page.waitForTimeout(3000);
  
  const orders = await page.textContent('#statOrders');
  const revenue = await page.textContent('#statRevenue');
  console.log('statOrders:', orders);
  console.log('statRevenue:', revenue);
});
