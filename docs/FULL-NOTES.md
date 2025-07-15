## IMPORTANT
- make sure na nasa tamang branch kayo
- nasa bottom left ng vscode ung branches
- aybe avoid using [Source Control] sa VSCode coz that shit's ass for group works probably maybe i think
- sa pagcommit, dapat lahat ng changes ini-specify (ehem Pami)
- on _Source Control_ tab in VSCode, naka-lista ung _Changes_ mo. use that as reference
example: <br>
```
Add navbar and hero section

feat: create navbar.html with responsive layout
feat: add hero banner with background image
fix: adjusted padding in main.css for consistency
refactor: reorganized header styles into separate CSS block
```
- or refer to [MAKE CHANGES AND COMMIT]() and use that format
- ALWAYS CONFIRM YOUR BRANCH PLEASE PARA MADALI BUHAY NATING LAHAT THANSKGYUO

<br>


---


#### Folders that're specific to each roles:

## Frontend (PHP, HTML, CSS, JS)
- /assets
- /components
- /layouts
- /pages
- /staticData -> (constant values like navbar buttons' text)
- /errors -> (error page designs)


## Backend (PHP, JS)
- /assets/js
- /handlers ---(main logic)
- /utils


## Database (PHP, SQL)
- /database
- /sql
- /staticData/dummies -> (dummy data)
- /utils -> (seeders, etc.)


---


## MAIN BRANCHES
team or role
```
main
frontend
backend
database
```


## FEATURE/TASK BRANCHES
naming convention
```
<team or role>/<feature name or task name>
```
examples :
- backend/login-feature
- frontend/navbar-update
- database/user-seed-fix

<br>


---


## EXPECTED FLOW
1) make sure that your role's branch is selected and updated. refer to [CHECKOUT TEAM/ROLE BRANCH]()
2) code new feature etc. type shit
3) create a new branch. refer to [CREATE NEW FEATURE/TASK BRANCH]()
4) commit changes. refer to [MAKE CHANGES AND COMMIT]()
5) push these changes to remote repository. refer to [PUSH FEATURE/TASK BRANCH]()
6) create a pull request. refer to [OPEN PULL REQUEST]()
7) other developers should now sync these changes except the developer who created/opened the pull request. refer to [SYNCING CHANGES FROM MAIN]()

<br>


## CHECKOUT TEAM/ROLE BRANCH
to get your branch's current structure <br>
only do this if your branch already exists <br>
if it does not, refer to [CREATE NEW TEAM/ROLE BRANCH]()
```
git checkout <team-or-role>
git pull origin <team-or-role>
```
example :
```
git checkout backend
git pull origin backend
```
<br>


## CREATE NEW TEAM/ROLE BRANCH
make sure you are in the main branch
```
git checkout main
```
get latest files of main branch to your local repository
```
git pull origin main
```
create or switch to a new branch
```
git checkout -b <new-branch-name>
```
example :
```
git checkout -b backend
```
push this new branch to the remote repository (github)
```
git push origin <new-branch-name>
```
example :
```
git push origin backend
```


## CREATE NEW FEATURE/TASK BRANCH
to push your new feature/task to a unique branch
```
git checkout -b <team-or-role-branch>/<feature-or-task>
```
example :
```
git checkout -b backend/user-auth
```
<br>


## MAKE CHANGES AND COMMIT
```
git add .
git commit -m <commit-message>
```
> '-m' flag/parameter means you will be adding a commit message.

example :
```
git add .
git commit
```
using git commit will open a default editor, most likely VSCode, where you can write a multi-line message like this:
```
feat: added login validation logic

- added validation for empty fields
- included password hashing
- updated error message formatting
- etc.
```
or using multiple '-m' flag/parameter to indicate multiple messages
```
git add .
git commit -m "feat: added new file" -m "fix: fixed this bug" -m "refactor: changed this code or whatever"
```
optionally, use backslashes to indicate that the next line is connected to this line
```
git add .
git commit -m "feat: added new file" \
            -m "fix: fixed this bug" \
            -m "refactor: changed this code or whatever"
```
<br>


## PUSH FEATURE/TASK BRANCH
```
git push origin <team-or-role-branch>/<feature-or-task>
```
example :
```
git push origin backend/user-auth
```
<br>


## OPEN PULL REQUEST
open a pull request to merge feature/task to your branch <br>
example : merging frontend/navbar branch to frontend branch

1) after using the git command above, go to the repository [Primal Black Market](https://github.com/necrokochou/Primal-Black-Market)
2) go to _Pull requests_ tab
3) click _New pull request_
4) select the _base ref_ or where you want to merge the feature/task <br> example: [frontend] or [database]
5) select the _head ref_ or what you want to merge onto the base ref <br> example: [frontend/navbar] or [database/shopping-cart]
```
// this is what the UI would look like in GitHub
// (base)      (head)
   frontend <- frontend/navbar
```
6) the QA will check the pull request and look for merging conflicts then approve/disapprove the request accordingly

<br>


## SYNCING CHANGES FROM MAIN
after the QA had approved of a branch's pull request, they will also be sending a pull request to merge the main branch to other branches---excluding the branch that sent the pull request to begin with

scenario example :
1) database developer sent a pull request to merge database/shopping-cart branch to database branch
2) let's say, the QA approves of this pull request
3) QA sends a pull request to merge database branch to the main branch.
4) QA checks for merging conflicts then either approves/disapproves of their own pull request
5) after merging database to main, QA sends another pull request to merge the main branch to all branches other than where the original change originated which, in this example, is from the database branch.
6) other branches (backend and frontend) will approve of this request IF QA does their properly and actually checks for merging conflicts and role conflicts : )

<br>
