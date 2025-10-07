# SQLiLab

A complete, hands-on SQL Injection lab: vulnerable web application, exploit scripts (lab-only), patched code, remediation guidance, and a professional assessment template.

This repository is designed for learning, internship assignments, and controlled security testing. It gives you a safe, repeatable environment to demonstrate how SQL injection works, how to exploit it in a lab, and how to remediate the issue correctly.

> Important: run this only in an isolated environment (VM or container) and never against systems you do not own or have written authorization to test.

---

## Table of contents

- What this project is  
- Why this project exists  
- Purpose and learning objectives  
- Working principle (how the lab functions)  
- Repo structure (what files are included)  
- Prerequisites  
- Installation (step-by-step)  
- How to run the vulnerable app (demo)  
- How to run the exploit (lab-only)  
- How to apply the patch (remediation)  
- Expected outputs and sample evidence  
- Test plan (what to present to reviewers)  
- Remediation checklist & best practices  
- Limitations and scope  
- Conclusion  
- Disclaimer and ethics

---

## What this project is

SQLiLab is a self-contained lab that demonstrates SQL Injection (SQLi) end-to-end:

- A deliberately vulnerable PHP web application (MySQL backend)
- An automated exploit script (for safe, local demonstration)
- A patched version of the vulnerable code (prepared statements)
- A remediation guide and formal security assessment template

This lets you show a real exploit and a real fix in a short demo without touching external networks.

---

## Why this project exists

- SQL Injection remains one of the most common and severe web vulnerabilities.
- Employers expect interns and junior security engineers to understand both exploitation and remediation.
- This lab gives you a compact, safe, and repeatable way to learn and demonstrate those skills during interviews and in reports.

---

## Purpose and learning objectives

By completing this lab you will be able to:

- Explain how unsanitized input leads to SQL injection
- Reproduce a SQLi exploit in a controlled environment
- Apply correct remediation (prepared statements / parameterized queries)
- Produce a professional assessment: evidence, impact, remediation, and testing notes
- Understand testing safety and how to run labs using Docker

---

## Working principle

1. `docker-compose` brings up two containers:
   - `db` (MySQL) initialized with sample data
   - `web` (PHP + Apache) hosting the vulnerable app
2. The vulnerable page concatenates a `username` GET parameter into a SQL query — this is the insecure code path.
3. The exploit submits a crafted `username` payload (for example `' OR '1'='1' --`) to bypass the filter and retrieve data.
4. The patched page rewrites the query using prepared statements to prevent injection.
5. Evidence is captured as HTML output, terminal logs, and a filled assessment template.

---

## Repo structure

```

SQLiLab/
├── README.md
├── docker-compose.yml
├── web/
│   ├── Dockerfile
│   ├── index.php               # Vulnerable app
│   ├── patched_index.php       # Patched app (prepared statements)
│   └── init.sql                # DB schema + seed data
├── exploit/
│   ├── exploit.py              # Lab-only exploit script (Python)
│   └── test_cases.txt
├── remediation.md
├── report_template.md
└── .gitignore

````

---

## Prerequisites

- Docker & Docker Compose (for Linux/macOS/Windows)
  - Install Docker Desktop for Windows/macOS, or apt/yum packaged Docker on Linux
- Python 3.8+ (for running the exploit script)
  - `requests` package (install with `pip install requests`)
- A modern web browser to view the app at `http://localhost:8080/`

No internet access is required to run the lab (except to download Docker images the first time).

---

## Installation — get the lab running (step-by-step)

1. Clone the repo:
   ```bash
   git clone https://github.com/<your-username>/SQLiLab.git
   cd SQLiLab
````

2. Build and start the containers:

   ```bash
   docker-compose up --build -d
   ```

   * The `db` service uses `mysql:8.0` and initializes schema/data from `web/init.sql`.
   * The `web` service builds a PHP+Apache image containing the vulnerable page.

3. Confirm services are running:

   ```bash
   docker-compose ps
   ```

4. Visit the app:

   * Open `http://localhost:8080/` in your browser.

---

## How to use the lab — step-by-step demo

### A — Normal usage (expected behavior)

1. Open the app: `http://localhost:8080/`
2. Try searching for an existing user, for example `alice`. The app will return her `id`, `username`, and `email`.

### B — Demonstrate SQL Injection (lab-only)

1. Open a terminal in the repo and run the exploit script:

   ```bash
   pip install requests
   python3 exploit/exploit.py --url http://localhost:8080/ --payload "' OR '1'='1' -- "
   ```
2. Output shows the HTTP request and the HTML snippet returned by the server. If successful you will see user rows returned even though the payload was not a valid username.

Alternatively, you can demonstrate the attack in a browser by visiting:

```
http://localhost:8080/?username=' OR '1'='1' --
```

You should see the user table return all rows.

**Why this works (concise):**

* The vulnerable code builds a SQL string by inserting the raw `username` value, so adding `OR '1'='1'` makes the WHERE clause always true and returns all rows.

---

## How to patch the app (apply remediation)

1. Replace the vulnerable script with the patched version:

   * Option A: copy `web/patched_index.php` to `web/index.php`
   * Option B: edit `web/index.php` and replace the vulnerable query with the prepared-statement code

2. Restart the web container:

   ```bash
   docker-compose restart web
   ```

3. Re-run the same exploit (exploit script or browser):

   * The patched app uses parameterized queries and binding, so the payload will be treated as data, not SQL, and will not return all records.

---

## Expected outputs / evidence to collect

For a demo or submission gather:

1. **Before patch (vulnerable):**

   * Screenshot of `http://localhost:8080/?username=' OR '1'='1' --` showing multiple user rows
   * Terminal output from `exploit/exploit.py` showing HTTP request and snippet of the response

2. **After patch (patched_index.php):**

   * Screenshot showing that the same payload returns "No users found" or does not return unintended rows
   * Re-run `exploit.py` — output should not show data leakage

3. **Report artifacts:**

   * Fill `report_template.md` with:

     * Reproduction steps (commands and URLs)
     * Vulnerable code excerpt
     * Evidence screenshots
     * Remediation steps and verification

Sample exploit output (trimmed):

```
[HTTP 200] Request: http://localhost:8080/?username=%27+OR+%271%27%3D%271%27+--+
<html> ... <table> <tr><td>1</td><td>alice</td><td>alice@example.com</td></tr> ...
```

Sample patched output:

```
[HTTP 200] Request: http://localhost:8080/?username=%27+OR+%271%27%3D%271%27+--+
<html> ... No users found ...
```

---

## Test plan — what to present to reviewers

A reviewer or evaluator will want clear, reproducible evidence:

1. Start: `docker-compose up --build -d`
2. Show normal app use (search `alice`)
3. Run the exploit and show it returns user data
4. Apply patch (copy patched file or swap)
5. Restart web service
6. Re-run exploit — show no data leak
7. Provide `report_template.md` filled with steps and screenshots

If required, include OWASP ZAP or `sqlmap` scan results against `http://localhost:8080/` (only in lab).

---

## Remediation checklist & best practices

When you fix SQL injection vulnerabilities, validate the following:

* [ ] All user-supplied inputs are treated as data, not SQL
* [ ] Use prepared statements / parameterized queries for every DB operation
* [ ] Use ORM or database layers that parameterize by default where possible
* [ ] Apply least-privilege to DB accounts (avoid admin/root DB user for web app)
* [ ] Validate and canonicalize input (white-list where feasible)
* [ ] Output-encode results in HTML to prevent XSS (use `htmlspecialchars` in PHP)
* [ ] Add logging/monitoring for suspicious queries and rate-limit input
* [ ] Add unit and integration tests that check for injection patterns in critical endpoints
* [ ] Add WAF rules if appropriate and available
* [ ] Include SQLi tests in CI pipelines for web projects

`remediation.md` contains the detailed code examples and references.

---

## Limitations & scope

Be upfront with reviewers — this lab demonstrates SQLi and remediation but is intentionally scoped:

* The app is intentionally small and simplified for teaching; real apps have more complex flows.
* The exploit provided demonstrates classic SQLi; modern apps may require multi-step attacks or be protected by WAFs.
* This lab does not simulate authentication bypass or chained attacks beyond basic data disclosure, unless you extend it.
* Do not use the exploit scripts against external targets.

---

## Conclusion

SQLiLab gives you everything you need to:

* Demonstrate a real SQL Injection in a local lab
* Explain the root cause and the mitigation
* Produce a polished assessment report for internship or coursework

This is a practical, safe, and recruiter-friendly project for any security portfolio.

---

## Disclaimer & ethics

* This repository contains intentionally vulnerable code for educational use only.
* Do not deploy the vulnerable application on public networks.
* Do not attempt any attacks outside of this controlled environment.
* The author is not responsible for misuse of the code. Use responsibly and with authorization.

---

## References & further reading

* OWASP SQL Injection Cheat Sheet: [https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
* OWASP Top 10: [https://owasp.org/www-project-top-ten/](https://owasp.org/www-project-top-ten/)
* PHP mysqli prepared statement docs: [https://www.php.net/manual/en/mysqli.prepare.php](https://www.php.net/manual/en/mysqli.prepare.php)
* SQLi testing tools: `sqlmap`, OWASP ZAP
