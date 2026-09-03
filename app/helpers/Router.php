<?php

/**
 * ============================================================
 * Router Sederhana
 * ============================================================
 * Menangani routing berdasarkan parameter ?url=
 * yang dikirim oleh .htaccess rewrite rule.
 */

class Router
{
    /** @var array Daftar route yang terdaftar */
    private array $routes = [];

    /**
     * Daftarkan route GET
     */
    public function get(string $path, string $controller, string $method): void
    {
        $this->routes[] = [
            'method'     => 'GET',
            'path'       => $path,
            'controller' => $controller,
            'action'     => $method,
        ];
    }

    /**
     * Daftarkan route POST
     */
    public function post(string $path, string $controller, string $method): void
    {
        $this->routes[] = [
            'method'     => 'POST',
            'path'       => $path,
            'controller' => $controller,
            'action'     => $method,
        ];
    }

    /**
     * Jalankan router — cocokkan URL dengan daftar route
     */
    public function dispatch(): void
    {
        if (isset($_GET['url']) && $_GET['url'] !== '') {
            $url = trim($_GET['url'], '/');
            if (strpos($url, 'public/') === 0) {
                $url = substr($url, 7);
            } elseif ($url === 'public') {
                $url = '';
            }
            $url = trim($url, '/');
        } else {
            // Fallback jika web server (Apache/Nginx/LiteSpeed) tidak mem-passing parameter ?url=
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
            $scriptDir  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

            // Jika script dieksekusi dari subfolder /public (misal: /new-stripmap/public/index.php)
            if (substr($scriptDir, -7) === '/public') {
                if (strpos($requestUri, $scriptDir) === 0) {
                    $requestUri = substr($requestUri, strlen($scriptDir));
                } else {
                    $parentDir = substr($scriptDir, 0, -7);
                    if ($parentDir !== '' && strpos($requestUri, $parentDir) === 0) {
                        $requestUri = substr($requestUri, strlen($parentDir));
                    }
                }
            } elseif ($scriptDir !== '' && $scriptDir !== '.' && strpos($requestUri, $scriptDir) === 0) {
                $requestUri = substr($requestUri, strlen($scriptDir));
            }

            $url = trim($requestUri, '/');
            if ($url === 'index.php') {
                $url = '';
            }
        }
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            // Ubah pola route menjadi regex, misal {id} → ([0-9]+)
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([0-9]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Buang full match

                $controllerFile = BASE_PATH . '/app/controllers/' . $route['controller'] . '.php';

                if (!file_exists($controllerFile)) {
                    die("Controller [{$route['controller']}] tidak ditemukan.");
                }

                require_once $controllerFile;

                $controllerInstance = new $route['controller']();
                $action = $route['action'];

                if (!method_exists($controllerInstance, $action)) {
                    die("Method [{$action}] tidak ditemukan di [{$route['controller']}].");
                }

                // Panggil method controller dengan parameter dari URL
                call_user_func_array([$controllerInstance, $action], $matches);
                return;
            }
        }

        // 404 — Tidak ada route yang cocok
        http_response_code(404);
        view('errors.404');
    }
}
