# Git Quick Cheat Sheet

Simple, essential commands for daily work.

---

## 🚀 Daily Push (Upload Code)

```bash
# 1. Stage all changes
git add .

# 2. Commit with message
git commit -m "Your update message here"

# 3. Push to server
git push
```

---

## 📥 Daily Pull (Get Latest Code)

```bash
git pull origin main
```

---

## 🛠️ First-Time Setup (Run Once)

```bash
# 1. Set your identity
git config --global user.name "Your Name"
git config --global user.email "sathish8654@gmail.com"

# 2. Connect repository & create first commit
git init
git branch -M main
git remote add origin git@github.com:sathish8654/supportportal.git

git add .
git commit -m "Initial commit"
git push -u origin main
```

---

## 📋 Useful Commands

| Goal | Command |
| :--- | :--- |
| **Check changed files** | `git status` |
| **See exact code changes** | `git diff` |
| **View recent commits** | `git log --oneline -n 5` |
| **Discard changes in a file** | `git restore <file>` |
| **Discard all uncommitted changes** | `git restore .` |
| **Undo last commit (keep code)** | `git reset --soft HEAD~1` |
| **Create & switch branch** | `git switch -c <branch_name>` |
