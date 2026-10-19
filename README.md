# FeeLedger: SuperrDev PHP assignment

FeeLedger is the fee-management app of Vidya Coaching Centre in Pune. The front desk enrols students into batches, the app creates their monthly installments, staff record payments at the counter, parents pay online through a payment gateway, and the owner reads a monthly collection report. It's a small **CodeIgniter 3** app on **PHP 8.1 + MySQL**.

Users have reported some problems and the owner wants a few improvements. **Your tickets, and the link to submit your work, are in your Internshala assignment message. Keep it open; you'll need it in Part B.**

**Read this whole page once before you do anything.** There are two parts (on Windows, Part A has its own section: [Part A on Windows](#part-a-on-windows)):

| | Part A: Setup | Part B: The assignment |
|---|---|---|
| **When** | Any time before you start | When you're ready to work without interruptions |
| **Time** | About 30 minutes. **Not counted** | **2 hours**, timed |
| **You end with** | The app running on your computer, and OpenCode working | Your fixes and features pushed to GitHub, and a zip to upload |

---

## Part A: Setup (do this first; it doesn't count towards your 2 hours)

**On Windows? Skip A1 to A7 and follow [Part A on Windows](#part-a-on-windows) instead.** It's the same setup, step by step for Windows. The steps below are for macOS and Linux.

Tick each box before moving on. Each step says what you should see when it worked.

### A1. PHP 8.1 and MySQL

**Already have PHP 8.0 or 8.1 and MySQL or MariaDB?** Skip to the check below.

Otherwise, the easiest way is **XAMPP 8.1.x** from apachefriends.org (8.1.25 for Windows and Linux, 8.1.17 for macOS). It includes PHP and MySQL (MariaDB). PHP 8.2 or newer runs, but CodeIgniter 3 doesn't officially support it.

**macOS with Homebrew** instead of XAMPP:
```bash
brew install php@8.1 mysql@8.0          # if brew refuses php@8.1, use php@8.2
brew services start mysql@8.0
export PATH="$(brew --prefix php@8.1)/bin:$(brew --prefix mysql@8.0)/bin:$PATH"   # add this line to ~/.zshrc to keep it
```

With XAMPP, open the XAMPP Control Panel and click **Start** next to **MySQL**; it turns green when it's running. (You only need Apache for phpMyAdmin in A3.)

Check PHP in a terminal:

```bash
php -v
```

- [ ] It prints `PHP 8.1.x` (or 8.0.x). If it prints another version, you have more than one PHP: use the full path to the 8.1 one (for XAMPP on Windows, `C:\xampp\php\php.exe`).

**Windows: "php is not recognized"?** XAMPP doesn't add PHP to your PATH. Either type the full path every time (`C:\xampp\php\php.exe`), or add it **in every new terminal window** you open:
- Command Prompt: `set PATH=C:\xampp\php;C:\xampp\mysql\bin;%PATH%`
- PowerShell: `$env:Path = "C:\xampp\php;C:\xampp\mysql\bin;" + $env:Path`

### A2. Git and GitHub

- [ ] `git --version` prints a version, and you have a GitHub account.
- [ ] You can **push** to GitHub from this computer (you've pushed before, or you're signed in through Git Credential Manager or a token). Check this now: a first-time GitHub login in the middle of Part B eats into your 2 hours.

### A3. Get a copy of the app and load the database

1. On this repo's GitHub page, click **Code → Download ZIP** and unzip it anywhere. You get a folder called `superrdev-feeledger-main`. This copy is **only for setup**; your real copy comes in Part B. Open a terminal **inside** that folder (it's the one that contains `index.php` and the `database` folder).
2. Create the database and load the data. Pick **one** way:

   **phpMyAdmin** (easiest on Windows; start Apache in XAMPP too, then open http://localhost/phpmyadmin):
   1. Click **New**, name it `feeledger`, choose collation `utf8mb4_unicode_ci`, click **Create**.
   2. With `feeledger` selected, click **Import**, choose `database/schema.sql`, click **Import** (at the bottom).
   3. Import again, this time `database/seed.sql`.

   **Command line** (in the `superrdev-feeledger-main` folder):
   ```bash
   mysql -u root -e "CREATE DATABASE feeledger CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   mysql -u root --default-character-set=utf8mb4 feeledger < database/schema.sql
   mysql -u root --default-character-set=utf8mb4 feeledger < database/seed.sql
   ```
   - If your MySQL `root` user has a password, add `-p` after `-u root` (it will ask for it).
   - On Windows use **Command Prompt** for these lines, not PowerShell (PowerShell doesn't support `<`). In PowerShell, wrap each line in `cmd /c "..."`, for example `cmd /c "mysql -u root --default-character-set=utf8mb4 feeledger < database\schema.sql"`. With XAMPP, `mysql` is `C:\xampp\mysql\bin\mysql.exe` unless you added it to PATH (A1).

- [ ] The `feeledger` database has 5 tables: `batches`, `students`, `guardians`, `installments`, `payments`. Check with `mysql -u root feeledger -e "SHOW TABLES"`, or look in phpMyAdmin.

**You only do this once.** The database stays on your computer, and your Part B copy uses the same one.

### A4. Run the app

In the `superrdev-feeledger-main` folder (the one with `index.php` in it):

```bash
php -S localhost:8000
```

Open **http://localhost:8000/** in your browser. (Windows may ask whether PHP can use the network: click **Allow**.)

- [ ] You see the **Dashboard** (it opens on "NEET 2027") with a "Recent payments" table. Click **Students** and **Reports** to look around. (Use the app's links. If you type addresses by hand, they need `index.php/`, for example `http://localhost:8000/index.php/students`.)

Stop the app with **Ctrl+C**.

**The app's database login** is XAMPP's default: host `127.0.0.1`, port `3306`, user `root`, no password, database `feeledger`. If yours is different, set it in the terminal **before** `php -S`. It lasts until you close that terminal window, so do it again in every new window you start the app from:

| Your terminal | Example (password "secret") |
|---|---|
| macOS / Linux | `export DB_PASS=secret` then `php -S localhost:8000` |
| Command Prompt | `set DB_PASS=secret` then `php -S localhost:8000` |
| PowerShell | `$env:DB_PASS="secret"` then `php -S localhost:8000` |

The same works for `DB_HOST`, `DB_PORT`, `DB_USER` and `DB_NAME`. **Don't put your password in `application/config/database.php`**: your repo will be public.

If you serve the app any other way than `php -S localhost:8000` (another port, or XAMPP's `htdocs`), also set `BASE_URL` to the address you open (for example `export BASE_URL=http://localhost:8080/`; Command Prompt: `set BASE_URL=http://localhost:8080/`; PowerShell: `$env:BASE_URL="http://localhost:8080/"`), so the app's links work.

### A5. Install OpenCode 2.0.29

You'll do the assignment with **OpenCode**, a free AI coding tool, and **one free model** that everyone uses. You don't need an account, a credit card or an API key.

Install exactly version **2.0.29**, even if you already have OpenCode:

- **macOS or Linux:**
  ```bash
  curl -fsSL https://opencode.ai/v2/install | bash -s -- --version 2.0.29
  ```
  Then close and reopen the terminal.
- **Windows:** you need Node.js 18 or newer (check with `node --version`; get it from nodejs.org). Then:
  ```bash
  npm install -g @opencode/cli@2.0.29
  ```
  If PowerShell says "running scripts is disabled on this system", run the npm command in **Command Prompt** instead.
- **If the macOS/Linux installer fails,** the npm command above works there too.

```bash
opencode --version
```

- [ ] It prints `opencode v2.0.29`. If it prints `1.x`, you have the older OpenCode from `opencode.ai/install` without `/v2`: run the command above again. If you really can't get 2.0.29, any `2.0.x` will do; say which version in the form.

### A6. Check OpenCode works

In any folder:

```bash
opencode run -m opencode/space-bunny-free "Reply with just the word ready."
```

- [ ] You see `space-bunny-free` and the reply `ready`. It can take up to a minute the first time.

Check the backup model too (you'll only need it if the main one fails):

```bash
opencode run -m opencode/deepseek-v4.1-flash-free "Reply with just the word ready."
```

- [ ] You see `deepseek-v4.1-flash-free` and `ready`.

### A7. If you normally use another AI tool

You can keep your usual editor (VS Code, PhpStorm, Cursor, Sublime...), but **during Part B all AI help must come from OpenCode**, so it shows in your transcript:

- Turn off AI features in your editor: GitHub Copilot, Cursor Tab and chat, JetBrains AI, Codeium, Windsurf and similar.
- Don't use ChatGPT, Claude, Gemini or other chat apps on the side.
- Run OpenCode in a terminal. Your editor's built-in terminal is fine.

- [ ] AI features in your editor are off.

**All boxes ticked? Then you're ready.** Delete the unzipped setup copy if you like (the database stays). Start Part B when you have 2 uninterrupted hours: [skip to Part B](#part-b-the-assignment-2-hours) (the next section is for Windows).

---

## Part A on Windows

**On Windows, follow this section from start to finish instead of A1 to A7.** It covers everything Part A does, written for Windows 10 and 11 with XAMPP. When every box here is ticked, go to Part B.

**Use Command Prompt for every command, not PowerShell.** Some of the commands below don't work in PowerShell. To open Command Prompt: Start menu, type `cmd`, press Enter. To open it **inside a folder**: open the folder in File Explorer, click the address bar, type `cmd` and press Enter. (Windows Terminal is fine too, as long as the tab is "Command Prompt".)

### W1. Install XAMPP, Git and Node.js

1. **XAMPP 8.1.25** from apachefriends.org (if the main download is a newer version, look under "More Downloads" for 8.1.25). Install it to the default folder, `C:\xampp`. If you already have PHP 8.0 or 8.1 with MySQL or MariaDB, you can use that instead and adjust the paths below.
2. **Git for Windows** from git-scm.com. Keep the default options; they include Git Credential Manager, which lets you sign in to GitHub through your browser.
3. **Node.js LTS** (version 18 or newer) from nodejs.org. Keep the default options.
4. Close every Command Prompt window you have open, so the new ones know about Git and Node.

### W2. Start MySQL and put PHP on your PATH

1. Open the **XAMPP Control Panel** (Start menu, type `XAMPP`). Click **Start** next to **MySQL**, and next to **Apache** if you want to use phpMyAdmin in W4. Each turns green when it's running. If Windows Firewall asks about them, click **Allow**. If Apache won't start, that's fine: you'll use the command line in W4 instead.
2. XAMPP doesn't put `php` and `mysql` on your PATH. Add them once, permanently:
   1. Start menu, type `environment`, open **Edit the environment variables for your account**.
   2. Select **Path**, click **Edit**, then **New** and type `C:\xampp\php`. Click **New** again and type `C:\xampp\mysql\bin`. Click **OK** and **OK**.
   3. Open a **new** Command Prompt (old windows don't see the change).

   (If you can't change settings on this computer, run `set PATH=C:\xampp\php;C:\xampp\mysql\bin;%PATH%` instead, in **every** new Command Prompt window you open.)
3. In the new Command Prompt, run these one at a time:
   ```
   php -v
   mysql --version
   git --version
   node --version
   ```
- [ ] `php -v` prints `PHP 8.1.x` (or 8.0.x). If it prints another version, another PHP comes first on your PATH: in step 2, select `C:\xampp\php` and click **Move Up** until it's at the top.
- [ ] `mysql --version` prints a version (XAMPP's says MariaDB).
- [ ] `git --version` prints a version, and `node --version` prints v18 or newer.

### W3. Git and GitHub

1. Tell Git your name and email (once per computer; use the email of your GitHub account):
   ```
   git config --global user.name "Your Name"
   git config --global user.email "you@example.com"
   ```
2. Make sure you can **push** to GitHub from this computer. If you haven't pushed from it before, test now: create a throwaway repo on GitHub with a README, then
   ```
   git clone https://github.com/<your-username>/<throwaway-repo>.git
   cd <throwaway-repo>
   echo test>> README.md
   git commit -am "test"
   git push
   ```
   The first push opens a browser window to sign in to GitHub; finish it. Delete the throwaway repo afterwards.
- [ ] `git push` worked. (A first-time GitHub login in the middle of Part B eats into your 2 hours.)

### W4. Get a copy of the app and load the database

1. On this repo's GitHub page, click **Code → Download ZIP**. Right-click the zip, choose **Extract All**, then **Extract**.
2. Windows puts a folder inside a folder: `superrdev-feeledger-main\superrdev-feeledger-main`. **The inner one is the right one**: it contains `index.php` and a `database` folder. This copy is **only for setup**; your real copy comes in Part B.
3. Open Command Prompt **in the inner folder** (address bar, `cmd`, Enter) and check you're in the right place:
   ```
   dir index.php
   ```
   It should list the file, not say "File Not Found".
4. Create the database and load the data. Pick **one** way:

   **phpMyAdmin** (needs Apache started in W2; open http://localhost/phpmyadmin):
   1. Click **New**, name it `feeledger`, choose collation `utf8mb4_unicode_ci`, click **Create**.
   2. With `feeledger` selected, click **Import**, choose `database\schema.sql`, click **Import** (at the bottom).
   3. Import again, this time `database\seed.sql`.

   **Command line** (in Command Prompt, in the inner folder):
   ```
   mysql -u root -e "CREATE DATABASE feeledger CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   mysql -u root --default-character-set=utf8mb4 feeledger < database\schema.sql
   mysql -u root --default-character-set=utf8mb4 feeledger < database\seed.sql
   ```
   XAMPP's `root` user has no password. If you gave yours one, add `-p` after `-u root` (it will ask for it).
- [ ] The `feeledger` database has 5 tables: `batches`, `students`, `guardians`, `installments`, `payments`. Check with `mysql -u root feeledger -e "SHOW TABLES"`, or look in phpMyAdmin.

**You only do this once.** The database stays on your computer, and your Part B copy uses the same one.

### W5. Run the app

In Command Prompt, in the inner folder:
```
php -S localhost:8000
```
Open **http://localhost:8000/** in your browser. If Windows Firewall asks whether PHP can use the network, click **Allow**.

- [ ] You see the **Dashboard** (it opens on "NEET 2027") with a "Recent payments" table. Click **Students** and **Reports** to look around. (Use the app's links. If you type addresses by hand, they need `index.php/`, for example `http://localhost:8000/index.php/students`.)

Stop the app with **Ctrl+C**.

- **The page doesn't load?** Stop the app and run `set BASE_URL=http://127.0.0.1:8000/` and then `php -S 127.0.0.1:8000`, and open http://127.0.0.1:8000/ instead.
- **Your MySQL has a password** (XAMPP's doesn't, unless you set one): run `set DB_PASS=yourpassword` before `php -S`. `DB_HOST`, `DB_PORT`, `DB_USER` and `DB_NAME` work the same way. Write it exactly like that: no quotes and no spaces around `=`.
- **Port 8000 is busy** ("Address already in use"): run `set BASE_URL=http://localhost:8080/` and then `php -S localhost:8080`, and use 8080 everywhere the guide says 8000.
- A `set` lasts only until you close that Command Prompt window, so repeat it in every new window you start the app from. **Don't put your password in `application\config\database.php`**: your repo will be public.

### W6. Install OpenCode 2.0.29

You'll do the assignment with **OpenCode**, a free AI coding tool, and **one free model** that everyone uses. You don't need an account, a credit card or an API key. Install exactly version **2.0.29**, even if you already have OpenCode:

```
npm install -g @opencode/cli@2.0.29
```

Then close Command Prompt, open a new one, and run:

```
opencode --version
```

- [ ] It prints `opencode v2.0.29`. If `npm` or `opencode` "is not recognized", open a new Command Prompt and try again; if it still fails, reinstall Node.js (W1). If you really can't get 2.0.29, any `2.0.x` will do; say which version in the form.

### W7. Check OpenCode works

In any folder:

```
opencode run -m opencode/space-bunny-free "Reply with just the word ready."
```

- [ ] You see `space-bunny-free` and the reply `ready`. It can take up to a minute the first time.

Check the backup model too (you'll only need it if the main one fails):

```
opencode run -m opencode/deepseek-v4.1-flash-free "Reply with just the word ready."
```

- [ ] You see `deepseek-v4.1-flash-free` and `ready`.

### W8. If you normally use another AI tool

You can keep your usual editor (VS Code, PhpStorm, Cursor, Sublime...), but **during Part B all AI help must come from OpenCode**, so it shows in your transcript: turn off GitHub Copilot, Cursor Tab and chat, JetBrains AI, Codeium, Windsurf and similar, and don't use ChatGPT, Claude, Gemini or other chat apps on the side.

- [ ] AI features in your editor are off.

**All boxes ticked? Then you're ready.** Delete the unzipped setup copy if you like (the database stays). Start Part B when you have 2 uninterrupted hours.

### Part B on Windows

Part B's commands all work in Command Prompt as written. Three things to know:

- **You need two Command Prompt windows, both in your repo folder:** one runs the app (`php -S localhost:8000`), the other runs OpenCode. If you used a `set` command in W2 or W5, run it again in each new window.
- **The webhook command** in B2 works as written: `php tools/send_test_webhook.php http://localhost:8000/index.php/ 7 400000`.
- **At the end (B4)**, quit OpenCode with Ctrl+C and run `php tools/finish.php` in that window. The zip lands in the folder that **contains** your repo folder; the command prints its full path.

---

## Part B: The assignment (2 hours)

### B1. Start the clock

1. On this repo's GitHub page, click **Use this template → Create a new repository**. Give it any name and keep it **Public**. **Your 2-hour clock starts now.**
2. Clone **your new repo** and go into it:
   ```bash
   git clone https://github.com/<your-username>/<your-repo>.git
   cd <your-repo>
   ```
3. **Stop the setup copy's app if it's still running** (Ctrl+C in its terminal). Then start the app from **your repo folder**: `php -S localhost:8000` (with your `DB_...` settings if you needed them in A4). It uses the database you loaded in A3. Leave this terminal running the app.
4. Open a **second terminal in the same folder** and start OpenCode there:
   ```bash
   opencode
   ```
   Always start OpenCode **inside your repo folder**. The repo's `opencode.json` selects the free model automatically. **Don't edit or delete `opencode.json`.**

### B2. Work on the tickets

The tickets and the feature menu are in your Internshala message. Fix the reported problems, build at least one feature from the menu, and build one feature of your own that you think the centre needs. The centre's rules are in **`docs/FEE_POLICY.md`**.

- **Commit after each fix or feature** with a message that says what and why, and **push after every commit** (`git push`).
- **Use one OpenCode session for the whole assignment.** If you close OpenCode or your laptop restarts, go back to your repo folder and continue the same session with `opencode -c`.
- **Online payments:** the gateway calls `payments/webhook`. To simulate one, run this with your app's address, a student ID and an amount in paise (the gateway sends paise; 400000 = ₹4,000):
  ```bash
  php tools/send_test_webhook.php http://localhost:8000/index.php/ 7 400000
  ```
- **If the AI is slow or stops responding,** wait or ask again. We measure model delays from your transcript and add that time back automatically, up to 3 hours after you created your repo.
- **If the model keeps failing for more than 5 minutes,** switch to the backup model inside OpenCode: press **Ctrl+X, then M** to open the model list, choose `deepseek-v4.1-flash-free`, and mention it in NOTES.md.

**How we grade:** we grade how you work, not only what you ship. A single prompt that fixes everything will score low even if the code is right. Work the way you would on a real job with an AI assistant.

### B3. NOTES.md

Add a file called `NOTES.md` in the root of your repo (under 500 words) with these four headings:

```markdown
## What I fixed and how I found it
## What I chose not to change
## My own feature
## Biggest remaining risk
```

Under **My own feature**, explain why it was needed (who has the problem, and what in the app made you think of it) and why you built it the way you did (what you considered and rejected).

Write it in your own words. Using AI to tidy the writing is fine; the thinking should be yours. Commit and push it.

### B4. Finish and submit

1. Make sure your last commit is pushed.
2. **Quit OpenCode** (Ctrl+C in its terminal).
3. In that same terminal (still in your repo folder; the other one is busy running the app), run:
   ```bash
   php tools/finish.php
   ```
   It doesn't change your code or NOTES.md. It checks everything is committed and pushed, exports your OpenCode session into a zip **next to** your repo folder (not inside it), and prints a short summary. If it says something is missing, fix that and run it again.
4. Open the **submission link in your Internshala message**: paste the summary and upload the zip.

Don't commit the zip or any exported session files, and don't edit them.

---

## What we record, and why

We look at your repo, its commit history and timestamps, and the full record of your OpenCode session: everything you typed, everything the AI replied, and every file it read or changed, with times. We use it only to evaluate your application, keep it for 6 months, and delete it sooner if you ask. Don't paste passwords, API keys or personal details into OpenCode.

## Problems

| What you see | What to do |
|---|---|
| `php` or `mysql` "is not recognized" (Windows) | See the PATH note in A1 (it has to be repeated in every new window), or use the full paths `C:\xampp\php\php.exe` and `C:\xampp\mysql\bin\mysql.exe`. |
| Git says "Please tell me who you are" | Run the two `git config --global` lines in W3 with your name and GitHub email, then commit again. |
| XAMPP's Apache won't start (port 80 in use) | You don't need Apache: load the database with the command-line steps in A3 instead of phpMyAdmin. |
| "Access denied for user 'root'" | Your MySQL has a password: add `-p` to the `mysql` commands (A3) and set `DB_PASS` for the app (A4). |
| Every page shows a database error, or "Unable to connect" | MySQL isn't running (start it in XAMPP), the database wasn't loaded (A3), or your login differs (A4 table). |
| "Address already in use" on port 8000 | Something else uses that port. Use `php -S localhost:8080`, set `BASE_URL=http://localhost:8080/`, and use that address in the webhook command too. |
| The app doesn't open at `localhost:8000` (Windows) | Try `php -S 127.0.0.1:8000`, set `BASE_URL=http://127.0.0.1:8000/`, and open that address. |
| `opencode: command not found` | Reopen the terminal. On Windows, check Node.js is installed and rerun the npm command. |
| `opencode --version` isn't 2.0.29 | Rerun the install command in A5. |
| `Model unavailable` | Make sure you started OpenCode inside your repo folder. If it persists, use the backup model (B2) and say so in NOTES.md. |
| Lost your OpenCode session after a crash | `opencode -c` in your repo folder continues it. `opencode session list` shows your sessions. |
| `php tools/finish.php` says something is missing | Do what it says (usually `git push`, or adding NOTES.md), then run it again. |
| `finish.php` can't find `php` or `opencode` | Run it in the same terminal you used for the app. If it still fails: run `opencode session list`, then in **Command Prompt** (not PowerShell) or macOS/Linux run `opencode session export <session-ID> > ../<session-ID>.json` from your repo folder, zip that file, and say so in the form. |
| Anything else | Write it in the "setup problems" box of the submission form. Setup problems won't count against you. |
