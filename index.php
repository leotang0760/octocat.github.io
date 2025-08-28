<?php
session_start();

// 数据库配置
$db_host = "mysql5.sqlpub.com:3310";
$db_name = "sql_leafnode";
$db_user = "ln_admin";
$db_pass = "85basZfusYeiZRoM";

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8mb4");

// 登录验证
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // 只有admin可以登录，密码无需加密
        if ($username === 'admin' && $password === '198182') {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = 'admin';
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $login_error = "用户名或密码错误！";
        }
    }
    
    // 显示登录页面
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>phpLeafNode - 登录</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3c 100%);
                color: #f8f9fa;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            
            .login-container {
                width: 100%;
                max-width: 400px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 15px;
                padding: 30px;
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .login-header h1 {
                font-size: 2.5rem;
                font-weight: bold;
                color: #4361ee;
                text-shadow: 0 0 10px rgba(67, 97, 238, 0.5);
                margin-bottom: 10px;
            }
            
            .login-header p {
                color: #a0a0b8;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #a0a0b8;
            }
            
            .form-group input {
                width: 100%;
                padding: 12px 15px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 5px;
                background: rgba(255, 255, 255, 0.05);
                color: white;
                font-size: 16px;
                transition: all 0.3s;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #4361ee;
                box-shadow: 0 0 10px rgba(67, 97, 238, 0.5);
            }
            
            .btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 500;
                font-size: 16px;
                transition: all 0.3s;
            }
            
            .btn-primary {
                background: #4361ee;
                color: white;
            }
            
            .btn-primary:hover {
                background: #3a56d4;
                box-shadow: 0 0 10px rgba(67, 97, 238, 0.5);
            }
            
            .error-message {
                background: rgba(230, 57, 70, 0.2);
                color: #e63946;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                border-left: 3px solid #e63946;
            }
            
            .login-footer {
                text-align: center;
                margin-top: 20px;
                color: #a0a0b8;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>phpLeafNode</h1>
                <p>数据库管理系统</p>
            </div>';
            
    if (isset($login_error)) {
        echo '<div class="error-message">'.$login_error.'</div>';
    }
    
    echo '<form method="POST">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">登录</button>
            </form>
            <div class="login-footer">
                只有管理员可以访问该系统
            </div>
        </div>
    </body>
    </html>';
    exit();
}

// 登出功能
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// 基础函数定义
function executeQuery($sql) {
    global $conn;
    $result = $conn->query($sql);
    return $result;
}

function getUserById($id) {
    $sql = "SELECT * FROM accounts WHERE id = $id";
    $result = executeQuery($sql);
    return $result->fetch_assoc();
}

function getAllUsers() {
    $sql = "SELECT * FROM accounts ORDER BY id DESC";
    $result = executeQuery($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

function addUser($username, $password, $permission_level, $max_rows, $max_connections, $memory_limit, $requests_per_hour) {
    $sql = "INSERT INTO accounts (username, password, permission_level, max_rows_allowed, max_connections, memory_limit_mb, requests_per_hour) 
            VALUES ('$username', '$password', '$permission_level', $max_rows, $max_connections, $memory_limit, $requests_per_hour)";
    return executeQuery($sql);
}

function updateUser($id, $username, $password, $permission_level, $max_rows, $max_connections, $memory_limit, $requests_per_hour) {
    $sql = "UPDATE accounts SET 
            username = '$username', 
            password = '$password', 
            permission_level = '$permission_level', 
            max_rows_allowed = $max_rows, 
            max_connections = $max_connections, 
            memory_limit_mb = $memory_limit, 
            requests_per_hour = $requests_per_hour 
            WHERE id = $id";
    return executeQuery($sql);
}

function deleteUser($id) {
    $sql = "DELETE FROM accounts WHERE id = $id";
    return executeQuery($sql);
}

function getServerStats() {
    global $conn;
    $stats = [];
    
    // 获取用户总数
    $result = executeQuery("SELECT COUNT(*) as total_users FROM accounts");
    $stats['total_users'] = $result->fetch_assoc()['total_users'];
    
    // 获取活跃用户数
    $result = executeQuery("SELECT COUNT(*) as active_users FROM accounts WHERE is_active = 1");
    $stats['active_users'] = $result->fetch_assoc()['active_users'];
    
    // 获取管理员数量
    $result = executeQuery("SELECT COUNT(*) as admin_users FROM accounts WHERE permission_level = 'admin'");
    $stats['admin_users'] = $result->fetch_assoc()['admin_users'];
    
    return $stats;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $permission_level = $_POST['permission_level'];
        $max_rows = $_POST['max_rows_allowed'];
        $max_connections = $_POST['max_connections'];
        $memory_limit = $_POST['memory_limit_mb'];
        $requests_per_hour = $_POST['requests_per_hour'];
        
        addUser($username, $password, $permission_level, $max_rows, $max_connections, $memory_limit, $requests_per_hour);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['update_user'])) {
        $id = $_POST['user_id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $permission_level = $_POST['permission_level'];
        $max_rows = $_POST['max_rows_allowed'];
        $max_connections = $_POST['max_connections'];
        $memory_limit = $_POST['memory_limit_mb'];
        $requests_per_hour = $_POST['requests_per_hour'];
        
        updateUser($id, $username, $password, $permission_level, $max_rows, $max_connections, $memory_limit, $requests_per_hour);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['delete_user'])) {
        $id = $_POST['user_id'];
        deleteUser($id);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['run_query'])) {
        $query = $_POST['custom_query'];
        $query_result = executeQuery($query);
    }
}

// 获取所有用户
$users = getAllUsers();
$server_stats = getServerStats();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>phpLeafNode 数据库管理器</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --bg-dark: #1e1e2e;
            --bg-light: #f4f6ff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, #2a2a3c 100%);
            color: var(--light);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1800px;
            margin: 0 auto;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary);
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            text-shadow: 0 0 10px rgba(67, 97, 238, 0.5);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #c1121f;
            box-shadow: 0 0 10px rgba(230, 57, 70, 0.5);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--info);
        }
        
        .stat-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--info);
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--success);
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-title {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th {
            background-color: rgba(var(--primary), 0.2);
            color: var(--info);
        }
        
        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            box-shadow: 0 0 10px rgba(67, 97, 238, 0.5);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c1121f;
            box-shadow: 0 0 10px rgba(230, 57, 70, 0.5);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #3a0ca3;
            box-shadow: 0 0 10px rgba(76, 201, 240, 0.5);
        }
        
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--info);
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
        
        .form-full {
            grid-column: 1 / -1;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .badge-admin {
            background: var(--warning);
            color: white;
        }
        
        .badge-moderator {
            background: var(--info);
            color: white;
        }
        
        .badge-user {
            background: var(--success);
            color: white;
        }
        
        .query-box {
            width: 100%;
            min-height: 150px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-family: monospace;
            resize: vertical;
        }
        
        .server-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }
        
        .info-item .label {
            font-size: 0.9rem;
            color: var(--info);
        }
        
        .info-item .value {
            font-size: 1.1rem;
            color: var(--success);
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .tab.active {
            opacity: 1;
            border-bottom: 2px solid var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .connection-status {
            display: flex;
            align-items: center;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .status-connected {
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
        }
        
        .status-disconnected {
            background: var(--danger);
            box-shadow: 0 0 10px var(--danger);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            background: var(--success);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(100%);
            transition: transform 0.3s;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.error {
            background: var(--danger);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            
            form {
                grid-template-columns: 1fr;
            }
            
            .server-info {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">phpLeafNode Admin</div>
            <div class="connection-status">
                <div class="status-indicator status-connected"></div>
                <span>已连接到数据库</span>
            </div>
            <div class="user-info">
                <div class="user-avatar">A</div>
                <span>管理员 (admin)</span>
                <a href="?logout=true" class="logout-btn">退出登录</a>
            </div>
        </header>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <h3>总用户数</h3>
                <div class="value"><?php echo $server_stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <h3>活跃用户</h3>
                <div class="value"><?php echo $server_stats['active_users']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-crown"></i></div>
                <h3>管理员</h3>
                <div class="value"><?php echo $server_stats['admin_users']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-database"></i></div>
                <h3>数据库版本</h3>
                <div class="value">MySQL 8.0</div>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active" onclick="switchTab('users')"><i class="fas fa-users"></i> 用户管理</div>
            <div class="tab" onclick="switchTab('query')"><i class="fas fa-code"></i> SQL查询</div>
            <div class="tab" onclick="switchTab('server')"><i class="fas fa-server"></i> 服务器信息</div>
            <div class="tab" onclick="switchTab('settings')"><i class="fas fa-cog"></i> 系统设置</div>
        </div>
        
        <div class="tab-content active" id="users-tab">
            <div class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">用户列表</h2>
                        <button class="btn btn-primary" onclick="resetForm()"><i class="fas fa-plus"></i> 添加新用户</button>
                    </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>权限</th>
                                    <th>最大行数</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['permission_level']; ?>">
                                            <?php echo $user['permission_level']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['max_rows_allowed']; ?></td>
                                    <td><?php echo $user['is_active'] ? '活跃' : '禁用'; ?></td>
                                    <td>
                                        <button class="btn btn-primary" onclick="editUser(<?php echo $user['id']; ?>)"><i class="fas fa-edit"></i> 编辑</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('确定要删除这个用户吗？')"><i class="fas fa-trash"></i> 删除</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title" id="form-title">添加用户</h2>
                    </div>
                    <form method="POST" id="user-form">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input type="text" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="permission_level">权限等级</label>
                            <select id="permission_level" name="permission_level" required>
                                <option value="user">用户</option>
                                <option value="moderator"> moderator</option>
                                <option value="admin">管理员</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="max_rows_allowed">最大行数</label>
                            <input type="number" id="max_rows_allowed" name="max_rows_allowed" value="1000" required>
                        </div>
                        <div class="form-group">
                            <label for="max_connections">最大连接数</label>
                            <input type="number" id="max_connections" name="max_connections" value="10" required>
                        </div>
                        <div class="form-group">
                            <label for="memory_limit_mb">内存限制(MB)</label>
                            <input type="number" id="memory_limit_mb" name="memory_limit_mb" value="50" required>
                        </div>
                        <div class="form-group">
                            <label for="requests_per_hour">每小时请求数</label>
                            <input type="number" id="requests_per_hour" name="requests_per_hour" value="1000" required>
                        </div>
                        <div class="form-group form-full text-center">
                            <button type="submit" name="add_user" id="submit-btn" class="btn btn-success"><i class="fas fa-save"></i> 添加用户</button>
                            <button type="button" onclick="resetForm()" class="btn btn-primary"><i class="fas fa-times"></i> 重置</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="query-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">SQL查询工具</h2>
                </div>
                <form method="POST">
                    <div class="form-group form-full">
                        <label for="custom_query">SQL查询</label>
                        <textarea id="custom_query" name="custom_query" class="query-box" placeholder="输入您的SQL查询语句..."></textarea>
                    </div>
                    <div class="form-group form-full text-center">
                        <button type="submit" name="run_query" class="btn btn-success"><i class="fas fa-play"></i> 执行查询</button>
                    </div>
                </form>
                
                <?php if (isset($query_result)): ?>
                <div class="card-header">
                    <h2 class="card-title">查询结果</h2>
                </div>
                <div style="margin-top: 20px; overflow-x: auto;">
                    <?php
                    if ($query_result === TRUE) {
                        echo "<div style='color: var(--success); padding: 10px; background: rgba(76, 201, 240, 0.1); border-radius: 5px;'><i class='fas fa-check-circle'></i> 查询成功执行</div>";
                    } else if ($query_result) {
                        echo "<table><thead><tr>";
                        // 输出表头
                        while ($field = $query_result->fetch_field()) {
                            echo "<th>" . $field->name . "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        
                        // 输出数据
                        while ($row = $query_result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>" . $value . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<div style='color: var(--danger); padding: 10px; background: rgba(230, 57, 70, 0.1); border-radius: 5px;'><i class='fas fa-exclamation-circle'></i> 查询执行失败: " . $conn->error . "</div>";
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="tab-content" id="server-tab">
            <div class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">服务器信息</h2>
                    </div>
                    <div class="server-info">
                        <div class="info-item">
                            <div class="label">服务器地址</div>
                            <div class="value"><?php echo $db_host; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">数据库名称</div>
                            <div class="value"><?php echo $db_name; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">数据库用户</div>
                            <div class="value"><?php echo $db_user; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">服务器所有者</div>
                            <div class="value">phpLeafNode</div>
                        </div>
                        <div class="info-item">
                            <div class="label">PHP版本</div>
                            <div class="value"><?php echo phpversion(); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">MySQL客户端版本</div>
                            <div class="value"><?php echo mysqli_get_client_info(); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">服务器时间</div>
                            <div class="value"><?php echo date('Y-m-d H:i:s'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">脚本运行时间</div>
                            <div class="value"><?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4); ?> 秒</div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">数据库表</h2>
                    </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>表名</th>
                                    <th>行数</th>
                                    <th>大小</th>
                                    <th>字符集</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tables = executeQuery("SHOW TABLE STATUS");
                                while ($table = $tables->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $table['Name']; ?></td>
                                    <td><?php echo $table['Rows']; ?></td>
                                    <td><?php echo round($table['Data_length'] / 1024, 2); ?> KB</td>
                                    <td><?php echo $table['Collation']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="settings-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">系统设置</h2>
                </div>
                <form>
                    <div class="form-group">
                        <label for="system_name">系统名称</label>
                        <input type="text" id="system_name" value="LeafNode command" disabled>
                    </div>
                    <div class="form-group">
                        <label for="max_upload">数据库大小限制</label>
                        <input type="text" id="max_upload" value="500MB" disabled>
                    </div>
                    <div class="form-group">
                        <label for="max_connections_setting">最大连接数</label>
                        <input type="number" id="max_connections_setting" value="30">
                    </div>
                    <div class="form-group">
                        <label for="max_requests">每小时最大请求数</label>
                        <input type="number" id="max_requests" value="36000">
                    </div>
                    <div class="form-group form-full text-center">
                        <button type="button" class="btn btn-success"><i class="fas fa-save"></i> 保存设置</button>
                        <button type="button" class="btn btn-primary"><i class="fas fa-undo"></i> 恢复默认</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // 隐藏所有标签内容
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // 取消所有标签的活动状态
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // 显示选中的标签内容
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // 设置选中的标签为活动状态
            event.currentTarget.classList.add('active');
        }
        
        function editUser(userId) {
            // 这里应该通过AJAX获取用户数据，但为了简化，我们使用PHP直接输出数据
            // 在实际应用中，您可能需要使用AJAX来获取用户数据
            showNotification('编辑功能将在完整版本中实现', 'info');
        }
        
        function resetForm() {
            document.getElementById('user-form').reset();
            document.getElementById('submit-btn').name = 'add_user';
            document.getElementById('submit-btn').textContent = '添加用户';
            document.getElementById('user_id').value = '';
            document.getElementById('form-title').textContent = '添加用户';
        }
        
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type === 'error' ? 'error' : ''}`;
            notification.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // 显示通知
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // 3秒后隐藏并移除通知
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // 初始化页面
        document.addEventListener('DOMContentLoaded', function() {
            // 添加图标到标签
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                const text = tab.textContent;
                if (text.includes('用户管理')) {
                    tab.innerHTML = '<i class="fas fa-users"></i> ' + text;
                } else if (text.includes('SQL查询')) {
                    tab.innerHTML = '<i class="fas fa-code"></i> ' + text;
                } else if (text.includes('服务器信息')) {
                    tab.innerHTML = '<i class="fas fa-server"></i> ' + text;
                } else if (text.includes('系统设置')) {
                    tab.innerHTML = '<i class="fas fa-cog"></i> ' + text;
                }
            });
        });
    </script>
</body>
</html>
<?php
// 关闭数据库连接
$conn->close();
?>
