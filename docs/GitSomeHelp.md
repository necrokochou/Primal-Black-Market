# PULL REQUESTS

- When merging your branch to the main branch, you must open a pull request and QA will be approving the PR.
- When QA approves of this PR and your branch has successfully merged with the main branch, the QA shall open another pull request to merge the updated main branch to the rest of the branches.
- Then it is the responsibility of the developers of those branches to check for conflicts and either accept/decline this request.


---
<br>


# BRANCH MANAGEMENT

After a merge pull request is accepted, the developers of those branch shall update their role branch (i.e. frontend, database, backend).
```
// go to the role branch using 'git checkout <role-branch-name>'
git checkout backend

// pull the updated role branch using 'git pull origin <role-branch-name>'
git pull origin backend
```


---
<br>


# GIT COMMANDS

> This command lets you 'check out' a branch and becomes your active branch.
```
git checkout <branch-name>
```
> Adding '-b' flag will create a branch then lets you 'check it out' immediately.
```
git checkout -b <branch-name>
```
---

> This command pulls the remote branch and updates your local branch.
```
git pull origin <branch-name>
```
---

> This command adds every changes to the current 'Staged Changes'.
```
git add .
```
---

> This command opens a tab on your IDE/editor and lets you write commit messages.
```
git commit
```
> Adding '-m' flag lets you write a commit message on the terminal/CLI.
```
git commit -m "<commit-message>"
```
> Multi-line commit messages.
```
git commit -m "<commit-message>" \
            -m "<commit-message>" \
            -m "<commit-message>" \
            -m "<commit-message>"
```
---

> This command pushes your local branch to the remote branch.
```
git push origin <branch-name>
```
---
<br>

# MAIN FLOW SCENARIO

1) A. Create a new branch from the main branch
```
// To make sure you are in the main branch
git checkout main

// To pull the remote main branch to your local branch
git pull origin main

// To create a new branch
git checkout -b <branch-name>
```
1) B. If you have an already existing branch, update your local branch
```
// To make sure you are in your branch
git checkout <branch-name>

// To pull the remote branch to your local branch
git pull origin <branch-name>
```

2) Code some new feature etc. type shit

3) Commit new changes
```
// To stage/add the changes
git add .

// To commit changes and add a commit message (refer to commands above)
git commit
```

4) Push the changes from your local branch to the remote branch
```
git push origin <branch-name>
```

5) Open a pull request in GitHub to merge your role branch to the main branch
- Go to [Primal Black Market](https://github.com/necrokochou/Primal-Black-Market)
- Open the _Pull requests_ tab
- Click _New pull request_ button
- Select the _base_ branch - the branch that you will be merging with
- Select the _compare_ branch - the branch that you will merge with the base branch <br>

Example: Merging backend branch to the main branch
```
[base: main] <- [compare: backend]
```
- Add a title "Merge \<compare-branch> to \<base-branch>"
```
// Example:
Merge backend to main
```
- Click _Create pull request_
- DO NOT CLICK _MERGE_. Wait for QA or the respective branch developer to check for conflicts and wait for them to accept/decline the merge pull request.
