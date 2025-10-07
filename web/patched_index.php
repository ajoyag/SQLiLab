<?php
// patched_index.php - Secure version using prepared statements
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'sqli_lab';
$user = getenv('DB_USER') ?: 'labuser';
$pass = getenv('DB_PASS') ?: 'labpass';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

$search = $_GET['username'] ?? '';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>SQLiLab — User Lookup (Patched)</title></head>
<body>
  <h1>SQLiLab — User Lookup (Patched)</h1>
  <form method="get" action="/">
    <label>Username: <input name="username" value="<?php echo htmlspecialchars($search); ?>"></label>
    <button type="submit">Search</button>
  </form>
  <hr/>
  <h2>Results</h2>
<?php
if ($search !== '') {
    // Secure: use prepared statement with parameter binding
    $stmt = $mysqli->prepare("SELECT id, username, email FROM users WHERE username = ? LIMIT 10");
    if (!$stmt) {
        echo "<p><strong>Prepare failed:</strong> " . htmlspecialchars($mysqli->error) . "</p>";
    } else {
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            echo "<p>No users found</p>";
        } else {
            echo "<table border='1' cellpadding='6'><tr><th>id</th><th>username</th><th>email</th></tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['id']) . "</td><td>"
                    . htmlspecialchars($row['username']) . "</td><td>"
                    . htmlspecialchars($row['email']) . "</td></tr>";
            }
            echo "</table>";
        }
        $res->free();
        $stmt->close();
    }
}
$mysqli->close();
?>
</body>
</html>
