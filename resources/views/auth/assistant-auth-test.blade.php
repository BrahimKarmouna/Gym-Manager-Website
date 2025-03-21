<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assistant Authentication Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .response-box {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assistant Authentication Test</h1>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Direct Login Test
                    </div>
                    <div class="card-body">
                        <button id="directLoginBtn" class="btn btn-primary">Test Direct Login</button>
                        <div id="directLoginResponse" class="response-box"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        Get Assistant Info Test
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="authToken" class="form-label">Authorization Token:</label>
                            <input type="text" class="form-control" id="authToken" placeholder="Enter token from login response">
                        </div>
                        <button id="getMeBtn" class="btn btn-success">Test Get Me</button>
                        <div id="getMeResponse" class="response-box"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        Assistant Login Test
                    </div>
                    <div class="card-body">
                        <form id="loginForm" method="POST" action="{{ route('assistant.login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" id="email" value="test.assistant@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" name="password" class="form-control" id="password" value="password123">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <button type="submit" class="btn btn-info">Login with Form Submit</button>
                        </form>
                        
                        <hr>
                        
                        <h5>Or test using AJAX:</h5>
                        <button id="ajaxLoginBtn" class="btn btn-secondary">Test Login via AJAX</button>
                        <div id="loginResponse" class="response-box"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Direct Login Test
            document.getElementById('directLoginBtn').addEventListener('click', async function() {
                const responseEl = document.getElementById('directLoginResponse');
                responseEl.innerHTML = 'Loading...';
                
                try {
                    const response = await fetch('/assistant-direct-login');
                    const data = await response.json();
                    
                    responseEl.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    
                    // Auto-fill token field if available
                    if (data.token) {
                        document.getElementById('authToken').value = data.token;
                    }
                } catch (error) {
                    responseEl.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
                }
            });
            
            // Get Assistant Info Test
            document.getElementById('getMeBtn').addEventListener('click', async function() {
                const responseEl = document.getElementById('getMeResponse');
                const token = document.getElementById('authToken').value;
                
                if (!token) {
                    responseEl.innerHTML = '<div class="text-danger">Please enter a token</div>';
                    return;
                }
                
                responseEl.innerHTML = 'Loading...';
                
                try {
                    const response = await fetch('/assistant/me', {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    
                    responseEl.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                } catch (error) {
                    responseEl.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
                }
            });
            
            // Login Test via AJAX
            document.getElementById('ajaxLoginBtn').addEventListener('click', async function(e) {
                e.preventDefault();
                
                const responseEl = document.getElementById('loginResponse');
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const remember = document.getElementById('remember').checked;
                
                responseEl.innerHTML = 'Loading...';
                
                try {
                    // First get a CSRF token
                    await fetch('/sanctum/csrf-cookie', {
                        method: 'GET',
                        credentials: 'include',
                    });
                    
                    // Attempt login
                    const response = await fetch('/assistant/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ 
                            email, 
                            password,
                            remember
                        }),
                        credentials: 'include'
                    });
                    
                    const data = await response.json();
                    
                    responseEl.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    
                    // Auto-fill token field if available
                    if (data.token) {
                        document.getElementById('authToken').value = data.token;
                    }
                } catch (error) {
                    responseEl.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
                }
            });
        });
    </script>
</body>
</html>
