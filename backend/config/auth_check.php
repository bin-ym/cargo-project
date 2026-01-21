<?php
// backend/config/auth_check.php

require_once __DIR__ . '/session.php';

/**
 * Centralized authentication check.
 * This file should be included at the top of protected pages.
 * It ensures the user is logged in and has the appropriate role.
 */

// If a specific role is required, define $required_role before including this file.
// Example: $required_role = 'admin';

if (isset($required_role)) {
    require_role($required_role);
} else {
    require_session();
}
