# GVA-custom-reports
Custom report viewing interface for a WP site. Passes the name/slug of a particular post-object to a looker studio report to be used as a filtering parameter. Enables paying members to log in and see custom analytics reports tailored to their page

Requires:
- Page created on site with the slug `member-dashboard`.
- Custom user role `member` is used for business members.
- Users must have custom permission `edit-assigned-listings` to access logged-in area.
- Report URL is hard-coded in statistics.php (preventing admins from altering it.)
- post-object slug is collected and passed to report URL in statistics.php via an ACF added custom field, 'connected_businesses'.
