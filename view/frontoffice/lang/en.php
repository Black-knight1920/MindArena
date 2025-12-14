<?php
return [
    // General site
    'site_title' => 'GameForGood',
    'home' => 'Home',
    'live_stats' => 'Live Statistics',
    'admin_area' => 'Admin Area',
    'copyright' => '© 2024 GameForGood - All rights reserved.',
    'tagline' => 'Play for a good cause',
    
    // Hero section
    'game_for_good' => 'GameForGood',
    'welcome' => 'Welcome to our donation platform dedicated to charitable organizations',
    'slogan' => 'Your game can change the world',
    'support_cause' => 'Support a cause',
    'see_live_stats' => 'See live stats',
    
    // Organizations
    'our_partners' => 'Our Partners',
    'choose_cause' => 'Choose a cause you care about and donate now',
    'progress' => 'Progress',
    'collected' => 'collected',
    'goal' => 'Goal',
    'remaining' => 'Remaining',
    'to_collect' => 'to collect',
    'goal_reached' => 'Goal reached!',
    'make_donation' => 'Make a donation',
    'visit_website' => 'Visit website',
    
    // Donations
    'donations' => 'Donations',
    'donors' => 'Donors',
    'last_donation' => 'Last donation',
    'donation_success' => 'Thank you for your donation!',
    
    // Language & Currency
    'current_language' => 'Current language',
    'currency' => 'Currency',
    'currency_symbol' => '$',
    'currency_code' => 'USD',
    'decimal_separator' => '.',
    'thousands_separator' => ',',
    
    // Form labels
    'name' => 'Name',
    'email' => 'Email',
    'amount' => 'Amount',
    'message' => 'Message (optional)',
    'submit' => 'Submit',
    'select_organisation' => 'Select organization',
    
    // Messages
    'required_field' => 'This field is required',
    'invalid_email' => 'Invalid email',
    'min_amount' => 'Minimum amount is :amount',
    'thank_you' => 'Thank you for your generosity',
    'donation_recorded' => 'Your donation has been successfully recorded',
    
    // Plural forms
    'donor_one' => ':count donor',
    'donor_many' => ':count donors',
    'donation_one' => ':count donation',
    'donation_many' => ':count donations',
    'dollar_one' => ':count dollar',
    'dollar_many' => ':count dollars',
    
    // Stats
    'total_donations' => 'Total donations',
    'total_donors' => 'Total donors',
    'average_donation' => 'Average donation',
    'top_donor' => 'Top donor',
    'recent_donations' => 'Recent donations',
    'live_updates' => 'Live updates',
    
    // Time
    'just_now' => 'Just now',
    'minutes_ago' => ':count minute(s) ago',
    'hours_ago' => ':count hour(s) ago',
    'days_ago' => ':count day(s) ago',
    
    // Months (for formatDate)
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 
                'July', 'August', 'September', 'October', 'November', 'December'],
    
    // Days (for formatDate)
    'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
    // Donation form specific
'make_donation' => 'Make a Donation',
'make_difference' => 'Your donation makes a difference',
'error_no_org_selected' => 'No organization selected. Please choose an organization from the home page.',
'donor_lastname' => 'Donor Last Name',
'placeholder_lastname' => 'Your last name',
'donor_firstname' => 'Donor First Name',
'placeholder_firstname' => 'Your first name',
'donor_email' => 'Donor Email',
'optional' => 'optional',
'donation_amount' => 'Donation Amount',
'custom_amount' => 'Custom amount',
'donation_date' => 'Donation Date',
'date_auto_today' => 'Automatic date (today)',
'donation_type' => 'Donation Type',
'select_type' => 'Select type',
'monetary' => 'Monetary',
'material' => 'Material',
'back_home' => 'Back Home',
'complete_donation' => 'Complete Donation',
'secure_payment' => 'Secure Payment',
'data_protected' => 'Data Protected',
'back_to_home' => 'Back to Home',
'back_to_home_to_select' => 'Back to Home to Select',
'processing' => 'Processing',

// Error messages
'error_amount_zero' => 'Amount must be greater than zero',
'error_invalid_date' => 'Invalid date',
'error_date_today' => 'Date must be today',
'error_donation_type' => 'Please select a donation type',
'error_select_org' => 'Please select an organization',
'error_donor_name' => 'Last name is required',
'error_invalid_name' => 'Invalid name (special characters not allowed)',
'error_name_min_length' => 'Last name must be at least 2 characters',
'error_name_max_length' => 'Last name cannot exceed 50 characters',
'error_donor_firstname' => 'First name is required',
'error_invalid_firstname' => 'Invalid first name (special characters not allowed)',
'error_firstname_min_length' => 'First name must be at least 2 characters',
'error_firstname_max_length' => 'First name cannot exceed 50 characters',
'error_invalid_email' => 'Invalid email address',
'donation_error' => 'An error occurred while recording the donation',
'validation_errors' => 'Validation errors',
'please_correct_errors' => 'Please correct the errors below',
'validation_please_correct' => 'Please correct the following errors:',
'error_occurred' => 'Error',
// JavaScript Validation Messages
    'validation_required' => 'This field is required',
    'validation_min_length' => 'Must be at least {min} characters',
    'validation_max_length' => 'Cannot exceed {max} characters',
    'validation_invalid_email' => 'Invalid email address',
    'validation_invalid_name' => 'Only letters, spaces, hyphens and apostrophes are allowed',
    'validation_invalid_amount' => 'Invalid amount',
    'validation_amount_min' => 'Minimum amount is {min}',
    'validation_amount_max' => 'Maximum amount is {max}',
    'validation_required_selection' => 'Please make a selection',
    'validation_processing' => 'Processing...',
    
    // Field-specific messages
    'validation_lastname_required' => 'Last name is required',
    'validation_firstname_required' => 'First name is required',
    'validation_amount_required' => 'Amount is required',
    'validation_type_required' => 'Donation type is required',
    'validation_date_required' => 'Date is required',
    
    // Success messages
    'validation_field_valid' => 'Field valid',
    'validation_form_valid' => 'Form valid',
    
    // Character counters
    'characters' => 'characters',
    'characters_remaining' => 'characters remaining',
    
    // Amount buttons
    'amount_10' => '10 $',
    'amount_25' => '25 $',
    'amount_50' => '50 $',
    'amount_100' => '100 $',
    'amount_250' => '250 $',
    'amount_500' => '500 $',
    
    // Placeholder updates
    'placeholder_select_type' => 'Select...',
    
    // Confirmation messages
    'confirm_submission' => 'Confirm submission',
    'confirm_submit_donation' => 'Are you sure you want to submit this donation?',
    'confirm_yes' => 'Yes, continue',
    'confirm_no' => 'No, cancel',
    
    // Loading states
    'loading' => 'Loading...',
    'saving' => 'Saving...',
    'redirecting' => 'Redirecting...',
    
    // Browser messages
    'browser_validation_email' => 'Please enter a valid email address',
    'browser_validation_required' => 'Please fill out this field',
    'browser_validation_pattern' => 'Incorrect format',
    
    // Tooltips
    'tooltip_name_format' => 'Letters, spaces, hyphens (-) and apostrophes (\') only',
    'tooltip_email_optional' => 'Optional - for receipt',
    'tooltip_amount_quick' => 'Click to select',
    'tooltip_amount_custom' => 'Enter custom amount',
    'tooltip_date_info' => 'Date is automatically set to today',
    'tooltip_type_info' => 'Select donation type',
    
    // Error summary
    'error_summary_title' => 'Your form contains errors',
    'error_summary_fix' => 'Please fix them before continuing',
    'error_summary_count' => '{count} error(s) found',
    
    // Accessibility
    'aria_required' => 'required field',
    'aria_invalid' => 'invalid field',
    'aria_valid' => 'valid field',
    
    // Formatting
    'currency_symbol' => '$',
    'decimal_separator' => '.',
    'thousands_separator' => ',',
    
    // Date format
    'date_format' => 'mm/dd/yyyy',
    'today' => 'Today',
    
    // Months for datepicker
    'january' => 'January',
    'february' => 'February',
    'march' => 'March',
    'april' => 'April',
    'may' => 'May',
    'june' => 'June',
    'july' => 'July',
    'august' => 'August',
    'september' => 'September',
    'october' => 'October',
    'november' => 'November',
    'december' => 'December',
    
    // Days for datepicker
    'monday' => 'Monday',
    'tuesday' => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday' => 'Thursday',
    'friday' => 'Friday',
    'saturday' => 'Saturday',
    'sunday' => 'Sunday',
    
    // Short days
    'mon' => 'Mon',
    'tue' => 'Tue',
    'wed' => 'Wed',
    'thu' => 'Thu',
    'fri' => 'Fri',
    'sat' => 'Sat',
    'sun' => 'Sun',
    
    // Button states
    'button_submit' => 'Submit',
    'button_submitting' => 'Submitting...',
    'button_reset' => 'Reset',
    'button_cancel' => 'Cancel',
    'button_back' => 'Back',
    'button_next' => 'Next',
    'button_confirm' => 'Confirm',
    
    // Progress indicators
    'progress_step' => 'Step {current} of {total}',
    'progress_complete' => 'Complete',
    
    // Notifications
    'notification_success' => 'Success',
    'notification_error' => 'Error',
    'notification_warning' => 'Warning',
    'notification_info' => 'Information',
    
    // Form sections
    'section_donor_info' => 'Donor Information',
    'section_donation_details' => 'Donation Details',
    'section_payment' => 'Payment',
    'section_confirmation' => 'Confirmation',
    // stats-live.php specific
    'site_title' => 'Mind Arena',
    'live_stats' => 'Live Statistics',
    'home' => 'Home',
    'admin_area' => 'Admin Area',
    'live_dashboard' => 'Live Dashboard',
    'last_update' => 'Last update',
    'next_update' => 'Next update',
    'refresh_now' => 'Refresh now',
    'statistics_dashboard' => 'Statistics Dashboard',
    'dashboard_subtitle' => 'Real-time analysis of donations and contributions',
    'collected_today' => 'Collected today',
    'today_donations' => 'Today\'s donations',
    'average_per_donation' => 'Average per donation',
    'total_general' => 'Total general',
    'detailed_analysis' => 'Detailed Analysis',
    'analysis_subtitle' => 'View trends and distributions',
    'trends' => 'Trends',
    'distribution' => 'Distribution',
    'organizations' => 'Organizations',
    'recent_donations' => 'Recent donations',
    '7_days_evolution' => '7 Days Evolution',
    'last_week' => 'Last week',
    'realtime_update' => 'Real-time update',
    'activity_by_hour' => 'Activity by hour',
    'today' => 'Today',
    'updated_every_30_seconds' => 'Updated every 30 seconds',
    'amount_distribution' => 'Amount Distribution',
    'all_donations' => 'All donations',
    'instant_update' => 'Instant update',
    'top_organizations' => 'Top Organizations',
    'by_amount_collected' => 'By amount collected',
    'live_data' => 'Live data',
    'performance_by_organization' => 'Performance by organization',
    'performance_subtitle' => 'Organization performance comparison',
    'latest_donations' => 'Latest donations',
    'realtime_feed' => 'Real-time feed',
    'anonymous_donor' => 'Anonymous donor',
    'updated_live' => 'Updated live',
    'realtime_dashboard' => 'Real-time dashboard',
    'auto_update_30_seconds' => 'Auto-update every 30 seconds',
    'current_language' => 'Current language',
    'currency' => 'Currency',
    'dashboard_updated_success' => 'Dashboard updated successfully!',
    'auto_update_in_progress' => 'Auto-update in progress...',
    'manual_update_in_progress' => 'Manual update in progress...',
    
    // For charts
    'total_amount' => 'Total amount',
    'number_of_donations' => 'Number of donations',
    'amount' => 'Amount',
    'amount_per_hour' => 'Amount per hour',
    'donations' => 'donations',
    'average' => 'Average',
    
    // For statistics
    'donations_count' => 'Donations count',
    'max_donation' => 'Max donation',
    'min_donation' => 'Min donation',
    'hourly_activity' => 'Hourly activity',
    'daily_activity' => 'Daily activity',
    'weekly_activity' => 'Weekly activity',
    'monthly_activity' => 'Monthly activity',
    
    // For tables
    'donor_name' => 'Donor name',
    'donation_date' => 'Donation date',
    'donation_amount' => 'Donation amount',
    'organization_name' => 'Organization name',
    
    // Messages
    'no_data_available' => 'No data available',
    'loading_data' => 'Loading data...',
    'data_loaded_successfully' => 'Data loaded successfully',
    'error_loading_data' => 'Error loading data',
    
    // Tooltips
    'click_to_refresh' => 'Click to refresh',
    'hover_for_details' => 'Hover for details',
    'view_details' => 'View details',
    
    // Filters
    'filter_by_date' => 'Filter by date',
    'filter_by_organization' => 'Filter by organization',
    'filter_by_amount' => 'Filter by amount',
    'apply_filters' => 'Apply filters',
    'reset_filters' => 'Reset filters',
    
    // Export
    'export_data' => 'Export data',
    'export_csv' => 'Export as CSV',
    'export_pdf' => 'Export as PDF',
    'export_excel' => 'Export as Excel',
    
    // Stats labels
    'stats_overview' => 'Stats overview',
    'daily_stats' => 'Daily stats',
    'weekly_stats' => 'Weekly stats',
    'monthly_stats' => 'Monthly stats',
    'yearly_stats' => 'Yearly stats',
    
    // Time periods
    'last_24_hours' => 'Last 24 hours',
    'last_7_days' => 'Last 7 days',
    'last_30_days' => 'Last 30 days',
    'last_3_months' => 'Last 3 months',
    'last_year' => 'Last year',
    'custom_range' => 'Custom range',
];