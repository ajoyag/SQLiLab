<?php
// index.php - Vulnerable example (for lab use only)
// Connect using environment variables
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
<head><meta charset="utf-8"><title>SQLiLab — User Lookup (Vulnerable)</title></head>
<body>
  <h1>SQLiLab — User Lookup (Vulnerable)</h1>
  <form method="get" action="/">
    <label>Username: <input name="username" value="<?php echo htmlspecialchars($search); ?>"></label>
    <button type="submit">Search</button>
  </form>
  <hr/>
  <h2>Results</h2>
<?php
if ($search !== '') {
    // VULNERABLE: direct concatenation of user input into query
    $query = "SELECT id, username, email FROM users WHERE username = '$search' LIMIT 10";
    echo "<p><em>Debug: running query:</em> " . htmlspecialchars($query) . "</p>";
    $res = $mysqli->query($query);
    if (!$res) {
        echo "<p><strong>Query error:</strong> " . htmlspecialchars($mysqli->error) . "</p>";
    } else {
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
    }
}
$mysqli->close();
?>
</body>
</html>
