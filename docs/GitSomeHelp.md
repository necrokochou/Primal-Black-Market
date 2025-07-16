## MAIN FLOW SCENARIO

1) a. Create a role branch from the main branch
> To make sure you are in the main branch
```
git checkout main
```
> To pull the remote main branch to your local branch
```
git pull origin main
```
> To create a new branch
```
git checkout -b <role-branch-name>
```
> To push the new local branch to remote branch
```
git push origin <role-branch-name>
```
Example usage :
```
git checkout main
git pull origin main
git checkout -b backend
git push origin backend
```
<br>

1) b. If you have an already existing branch, update your local branch
> To make sure you are in your branch
```
git checkout <role-branch-name>
```
> To pull the remote branch to your local branch
```
git pull origin <role-branch-name>
```
Example usage :
```
git checkout backend
git pull origin backend
```
<br>

2) Create a new branch for a new feature
> To make sure you are in the correct role branch <br>
> and get the latest remote role branch to your local role branch
```
git checkout <role-branch>
git pull origin <role-branch>
```
> To create a new feature branch
```
git checkout -b <feature-branch-name>
```
Example usage :
```
git checkout backend
git pull origin backend
git checkout -b backend-user-auth
```
<br>

3) Code some new feature etc. type shit in the feature branch

<br>

4) Commit new changes
> To stage/add the changes
```
git add .
```
> To commit changes and add a commit message (refer to git commit commands above)
```
git commit
```
<br>

5) Push the changes from your local branch to the remote branch
```
git push origin <feature-branch-name>
```
Example usage :
```
git push origin backend-user-auth
```
<br>

6) Open a pull request in GitHub to merge your feature branch to the role branch
    1) Go to [Primal Black Market](https://github.com/necrokochou/Primal-Black-Market)
    2) Open the _Pull requests_ tab
    3) Click _New pull request_ button
    4) Select the _base_ branch - the branch where you plan to merge your feature branch
    5) Select the _compare_ branch - the branch that you will merge to the base branch <br>

    Example: Merging backend-user-auth branch to the main branch
    ```
    [base: main] <- [compare: backend-user-auth]
    ```

    6) Add a title "Merge \<compare-branch> to \<base-branch>"

    Example:
    ```
    Merge backend-user-auth to main
    ```
    7) Click _Create pull request_
    8) DO NOT CLICK _MERGE_. Wait for QA or the respective branch developer to check for conflicts and wait for them to accept/decline the merge pull request.
7) Your QA shall now check for merging conflicts and review role-specific changes. QA shall accept or decline this pull request accordingly and then merge your feature branch to the main branch if accepted.

8) QA shall now open pull requests to merge the new main branch to all other branches and their respective developers shall check for conflicts.

<br>

### SUMMARY
1) Create role branch from main or pull remote role branch to existing role branch.
2) Create a feature branch then code stuff.
3) Commit new changes then push to remote feature branch.
4) Open a pull request to merge feature branch with the main branch.
5) QA checks pull request then makes another pull request to merge main branch other branches.
---
<br>


## BRANCH MANAGEMENT

After a pull request of merging main to role branch is accepted, the developers of the branch shall update their role branch (i.e. frontend, database, backend).
> To go to your role branch
```
git checkout <role-branch-name>
```
> To pull the remote role branch and update your local role branch
```
git pull origin <role-branch-name>
```
Example usage :
```
git checkout backend
git pull origin backend
```


---
<br>


## GIT COMMANDS

This command lets you 'check out' a branch and becomes your active branch.
```
git checkout <branch-name>
```
Adding '-b' flag will create a branch then lets you 'check it out' immediately.
```
git checkout -b <branch-name>
```
---

This command pulls the remote branch and updates your local branch.
```
git pull origin <branch-name>
```
---

This command adds every changes to the current 'Staged Changes'.
```
git add .
```
---

This command opens a tab on your IDE/editor and lets you write commit messages.
```
git commit
```
Adding '-m' flag lets you write a commit message on the terminal/CLI.
```
git commit -m "<commit-message>"
```
Multi-line commit messages.
```
git commit -m "<commit-message>" \
            -m "<commit-message>" \
            -m "<commit-message>" \
            -m "<commit-message>"
```
---

This command pushes your local branch to the remote branch.
```
git push origin <branch-name>
```
---
<br>


## PULL REQUESTS

- When merging your branch to the main branch, you must open a pull request and QA will be checking for merging conflicts and approve/decline the PR accordingly.
- When QA approves of this PR and your branch has successfully merged with the main branch, the QA shall open another pull request to merge the updated main branch to the rest of the branches.
- Then it is the responsibility of the developers of those branches to check for conflicts and either accept/decline this request of merging the main branch to the role branch.


---
<br>


## NAMING CONVENTIONS
Role Branches
```
main
frontend  -or-  Frontend
backend  -or-  Backend
database  -or-  Database
```
Feature Branches
```
frontend-navbar  -or-  Frontend-NavBar
backend-user-auth  -or-  Backend-UserAuth
database-users-table  -or-  Database-UsersTable
```
Optionally, you can also use two-letter acronyms on feature branches (NOT ROLE BRANCHES)
```
FE -> frontend
BE -> backend
DB -> database

FE-nav-bar  -or-  FE-NavBar
BE-user-auth  -or-  BE-UserAuth
DB-users-table  -or-  DB-UsersTable
```
---
<br>


## COMMIT MESSAGE and TITLE FORMATS

### Git Commits
```
feat -> New files are added

refactor -> Change lines of code or remove files/lines of code

fix -> Fix an error or bug in the program. Doesn't matter if it also counts as feat or refactor

docs -> Document-related files such as .md, .pdf, .docx, etc.
```

### Commit Messages
Pick a format :
- Format 1
```
Add user authentication feature

feat: added auth.handler.php
refactor: modified auth.util.php
docs: improved authentication notes
```
- Format 2
```
fix: authentication handler
- fixed error in auth.handler.php

refactor: authentication utilities
- modified auth.util.php

docs: authentication notes
- improved authentication notes
```

### Pull Request Titles

Role branch merging into main branch
```
<role-branch> : <main changes>
```
Example usages :
```
frontend: fix unresponsive nav bar on mobile
backend: add user authentication feature
database: modify migration scripts
```
<br>

Feature branch merging into role branch
```
<feature-branch> : <main changes>
```
Example usages :
```
frontend-nav-bar: fix unresponsive nav bar on mobile
backend-user-auth: add login and registration logic
database-users-table: modify migration scripts
```
Description is optionally used for specifying changes or differences.

### Merge Titles

Approving merge pull request of role branch to main branch
```
Merge <role-branch> into main : <main-changes>
```
Example usage :
```
Merge backend into main : add user authentication feature
```
<br>

Approving merge pull request of main to role branch
```
Sync main to <role-branch>
```
Example usages :
```
Sync main to frontend
Sync main to database
```
<br>

Approving merge pull request of feature branch to role branch
```
<feature-branch> : <main-changes>
```
Example usages :
```
frontend-nav-bar : fix unresponsive nav bar on mobile
backend-user-auth : add user authentication feature
database-users-table : modify migration scripts
```
