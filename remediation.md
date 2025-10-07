# SQL Injection Remediation & Best Practices

1. Use parameterized queries / prepared statements
   - Example (PHP mysqli):
     ```php
     $stmt = $mysqli->prepare("SELECT id, username, email FROM users WHERE username = ? LIMIT 10");
     $stmt->bind_param("s", $username);
     ```
2. Use ORMs or query builders which parameterize queries by default.
3. Validate and normalize input
   - Accept known patterns only (whitelist) where possible.
4. Least privilege DB user
   - Use an account with read-only access for queries that don't modify data.
5. Web app firewall / WAF
   - Use WAF rules to detect generic SQL injection patterns.
6. Logging & monitoring
   - Log suspicious inputs (rate-limited) and set alerts on abnormal query volume.
7. Prepared statements for all queries including dynamic SQL
   - If dynamic SQL is unavoidable, build using parameterized procedures or carefully validate inputs.
8. Escaping is last resort
   - Use proper escaping libraries if parameterization is impossible, but avoid relying on escaping alone.
9. Regular security testing
   - Run periodic static analysis and dynamic scanning (in authorized environments).

Patch example: `web/patched_index.php` included in repo.
