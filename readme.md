# Work Order System

Created a web-based system to collect maintenance request, IT trouble tickets, and other problem tickets for a 50 unit restaurant chain. Saved the company approximately $8k a year by moving them to this new system. Technologies used: php, html, css, javascript, and ajax. Integrated application as a WordPress plugin when we migrated our website to WordPress.

##Date
2010

This is the initial code created for the project. I do not have access to the code that was ported to Wordpress and includes ajax calls. 

## Maintenance / Work order for stores

* General managers would receive emails for only their store, and when logged in would only be able to see request for their store
* District managers would receive emails for their stores, and when logged in would only be able to view requests for their stores
* Maintenance technicians would receive emails for their stores, and when logged in would only be able to view requests for their stores
* Maintenance techs were allowed a view all button to see all requests in the system.
* The maintenance supervisor would receive emails for all requests and be able to view all requests when logged in. (Design spec did not want to have maintenance supervisor emailed on for maintenance technician work; they determined all emails okay.)
## Other

* System administrators, when logged in would see all requests. No emails sent to general system administrators.
* Employees or groups of people could be assigned work order categories. For example, a request for deposit slips would be emailed to the person assigned that category. When this person logged in, they would only be able to see requests that they were assigned. IT related requests would only be emailed IT related requests.
## Additional features:

* Given the view restrictions above, a user could search work orders by store (if they could view more than one store) and by categories and sub-categories, and by work order status.
* The ability to export work orders based on date range, district, or by store.
