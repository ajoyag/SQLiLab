# SQL Injection Assessment — Report Template

**Project:** SQLiLab  
**Date:** YYYY-MM-DD  
**Assessor:** Your Name

## Executive Summary
- Short description of findings and remediation status.

## Scope
- Target: Local lab vulnerable app
- Limitations: Demo-only, no external systems tested

## Findings
1. High: SQL Injection in `index.php` via `username` parameter.
   - Location: /index.php
   - Vulnerable code excerpt: `$query = "SELECT ... WHERE username = '$search' ...";`
   - Evidence: sample response with payload `' OR '1'='1' -- ` returned all users.

## Impact
- Unauthorized data disclosure: user list (username/email)
- Potential for further exploitation when combined with UNION/stacked queries

## Reproduction Steps
1. Start Docker stack: `docker-compose up --build -d`
2. Request: `GET /?username=' OR '1'='1' --`
3. Observe returned user table in response body.

## Remediation
- Implement prepared statements (see patched_index.php)
- Apply least-privilege DB account
- Add WAF rules and monitoring

## Test Plan
- Test parameterized query to confirm fix: use same payloads and verify no data leak
- Regression: scan entire app for other concatenated SQL

## Timeline & Conclusion
- Suggested immediate actions and follow-ups

Appendix: raw outputs, logs, and scripts used during testing
