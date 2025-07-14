### [IMPORTANT]
make sure na nasa tamang branch kayo <br>
nasa bottom left ng vscode ung branches


---


Folders that're specific to each roles:

### [Frontend (PHP, HTML, CSS, JS)]
- /assets
- /components
- /layouts
- /pages
- /staticData -> (constant values like navbar buttons' text)
- /errors -> (error page designs)


## [Backend (PHP, JS)]
- /assets/js
- /handlers ---(main logic)
- /utils


## [Database (PHP, SQL)]
- /database
- /sql
- /staticData/dummies -> (dummy data)
- /utils -> (seeders, etc.)


---


### [MAIN BRANCHES]
(team or role)
```
main
frontend
backend
database
```


### [FEATURE/TASK BRANCHES]
(naming convention)
```
<team or role>/<feature name or task name>
```
(examples)
```
backend/login-feature
frontend/navbar-update
database/user-seed-fix
```


---


### [CHECKOUT TEAM/ROLE BRANCH]
(to get this branch's current structure)
```
git checkout <team or role>
git pull origin <team or role>
```
(example)
```
git checkout backend
git pull origin backend
```


### [CREATE NEW FEATURE/TASK BRANCH]
(to push a new feature/task to a unique branch)
```
git checkout -b <team or role>/<feature or task>
```


### [MAKE CHANGES AND COMMIT]