<?php
/**
 * API Entry Point
 * Router untuk semua endpoint API
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/lib/response.php';

// CORS Headers
header('Access-Control-Allow-Origin: ' . CORS_ORIGIN);
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse request URI
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Remove 'api/' prefix if exists
if (strpos($path, 'api/') === 0) {
    $path = substr($path, 4);
}

$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', $path);

// Router
try {
    // Health check
    if ($path === 'health' || $path === '') {
        Response::json([
            'success' => true,
            'message' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s')
        ], 200);
    }

    // Auth routes
    if ($segments[0] === 'auth') {
        require_once __DIR__ . '/routes/auth.php';
        $authRoutes = new AuthRoutes();

        if ($segments[1] === 'register' && $method === 'POST') {
            $authRoutes->register();
        } elseif ($segments[1] === 'login' && $method === 'POST') {
            $authRoutes->login();
        } elseif ($segments[1] === 'me' && $method === 'GET') {
            $authRoutes->me();
        } else {
            Response::error('Auth endpoint not found', 404);
        }
    }

    // Users routes (admin/staff only)
    elseif ($segments[0] === 'users') {
        require_once __DIR__ . '/routes/auth.php';
        $authRoutes = new AuthRoutes();

        if (!isset($segments[1]) && $method === 'GET') {
            // GET /api/users - List all users
            $authRoutes->getUsers();
        } elseif (isset($segments[1]) && isset($segments[2]) && $segments[2] === 'role' && $method === 'PUT') {
            // PUT /api/users/:id/role - Update user role
            $authRoutes->updateUserRole($segments[1]);
        } elseif (isset($segments[1]) && $method === 'DELETE') {
            // DELETE /api/users/:id - Delete user
            $authRoutes->deleteUser($segments[1]);
        } else {
            Response::error('Users endpoint not found', 404);
        }
    }

    // Facilities routes
    elseif ($segments[0] === 'facilities') {
        require_once __DIR__ . '/routes/facilities.php';
        $facilitiesRoutes = new FacilitiesRoutes();

        if (!isset($segments[1]) && $method === 'GET') {
            $facilitiesRoutes->list();
        } elseif (!isset($segments[1]) && $method === 'POST') {
            $facilitiesRoutes->create();
        } elseif (isset($segments[1]) && $method === 'GET') {
            $facilitiesRoutes->detail($segments[1]);
        } elseif (isset($segments[1]) && $method === 'PUT') {
            $facilitiesRoutes->update($segments[1]);
        } elseif (isset($segments[1]) && $method === 'DELETE') {
            $facilitiesRoutes->delete($segments[1]);
        } else {
            Response::error('Facilities endpoint not found', 404);
        }
    }

    // Loans routes
    elseif ($segments[0] === 'loans') {
        require_once __DIR__ . '/routes/loans.php';
        $loansRoutes = new LoansRoutes();

        if (!isset($segments[1]) && $method === 'GET') {
            $loansRoutes->list();
        } elseif (!isset($segments[1]) && $method === 'POST') {
            $loansRoutes->create();
        } elseif (isset($segments[1]) && !isset($segments[2]) && $method === 'GET') {
            $loansRoutes->detail($segments[1]);
        } elseif (isset($segments[1]) && isset($segments[2]) && $segments[2] === 'status' && $method === 'PATCH') {
            $loansRoutes->updateStatus($segments[1]);
        } elseif (isset($segments[1]) && $method === 'DELETE') {
            $loansRoutes->delete($segments[1]);
        } else {
            Response::error('Loans endpoint not found', 404);
        }
    }

    // Bookings routes (public form submissions)
    elseif ($segments[0] === 'bookings') {
        require_once __DIR__ . '/routes/bookings.php';
        $bookingsRoutes = new BookingsRoutes();

        if (!isset($segments[1]) && $method === 'GET') {
            $bookingsRoutes->list();
        } elseif (!isset($segments[1]) && $method === 'POST') {
            $bookingsRoutes->create();
        } elseif (isset($segments[1]) && !isset($segments[2]) && $method === 'GET') {
            $bookingsRoutes->detail($segments[1]);
        } elseif (isset($segments[1]) && isset($segments[2]) && $segments[2] === 'status' && $method === 'PATCH') {
            $bookingsRoutes->updateStatus($segments[1]);
        } else {
            Response::error('Bookings endpoint not found', 404);
        }
    }

    // Comments routes
    elseif ($segments[0] === 'comments') {
        require_once __DIR__ . '/routes/comments.php';
        $commentsRoutes = new CommentsRoutes();

        if (isset($segments[1]) && !isset($segments[2]) && $method === 'GET') {
            $commentsRoutes->list($segments[1]);
        } elseif (isset($segments[1]) && !isset($segments[2]) && $method === 'POST') {
            $commentsRoutes->create($segments[1]);
        } elseif (isset($segments[1]) && isset($segments[2]) && $segments[2] === 'mark-returned' && $method === 'PATCH') {
            $commentsRoutes->markReturned($segments[1]);
        } elseif (isset($segments[1]) && $method === 'DELETE') {
            $commentsRoutes->delete($segments[1]);
        } else {
            Response::error('Comments endpoint not found', 404);
        }
    }

    // Reports routes
    elseif ($segments[0] === 'reports') {
        require_once __DIR__ . '/routes/reports.php';
        $reportsRoutes = new ReportsRoutes();

        if (isset($segments[1]) && $segments[1] === 'summary' && $method === 'GET') {
            $reportsRoutes->summary();
        } elseif (isset($segments[1]) && $segments[1] === 'user-stats' && $method === 'GET') {
            $reportsRoutes->userStats();
        } elseif (isset($segments[1]) && $segments[1] === 'facility-usage' && $method === 'GET') {
            $reportsRoutes->facilityUsage();
        } else {
            Response::error('Reports endpoint not found', 404);
        }
    }

    // Uploads (serve uploaded files)
    elseif ($segments[0] === 'uploads' && isset($segments[1])) {
        $filename = basename($segments[1]);
        $filepath = UPLOAD_DIR . $filename;

        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filepath);
            finfo_close($finfo);

            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            Response::error('File not found', 404);
        }
    }

    // Route not found
    else {
        Response::error('Endpoint not found', 404);
    }

} catch (Exception $e) {
    Response::error('Server error: ' . $e->getMessage(), 500);
}
