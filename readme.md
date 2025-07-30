<a name="readme-top">

<br/>

<br />
<div align="center">
  <a href="">
    <img src="assets\images\logo_arcc.png" alt="img" width="350" height="250">
  </a>
  <h3 align="center">Primal Black Market</h3>
</div>
<div align="center">
    Short description
</div>

<br />

![](https://visit-counter.vercel.app/counter.png?page=necrokochou/Primal-Black-Market)

[![wakatime](https://wakatime.com/badge/github/necrokochou/Primal-Black-Market.svg)](https://wakatime.com/badge/github/necrokochou/Primal-Black-Market)
---

<br />
<br />

<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#overview">Overview</a>
      <ol>
        <li>
          <a href="#key-components">Key Components</a>
        </li>
        <li>
          <a href="#technology">Technology</a>
        </li>
      </ol>
    </li>
    <li>
      <a href="#rule,-practices-and-principles">Rules, Practices and Principles</a>
    </li>
  </ol>
</details>

---

## Overview

Description

A Final Project showcasing Black Market website with the Prehistoric/Primal theme

### Members

- Banaag, Christiane Janiel
- Cendana, Carl Markruel
- Flores, Joseph Aiden
- Galang, Christian Allen
- Pamilar,Louie

### Key Components

- Login
- Multi-Page
- Admin Features
- Cart System

### Technology


#### Language
![HTML](https://img.shields.io/badge/HTML-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)


#### Databases
![MongoDB](https://img.shields.io/badge/MongoDB-47A248?style=for-the-badge&logo=mongodb&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-336791?style=for-the-badge&logo=postgresql&logoColor=white)


## Rules, Practices and Principles

<!-- Do not Change this -->

1. Always use `AD-` in the front of the Title of the Project for the Subject followed by your custom naming.
2. Do not rename `.php` files if they are pages; always use `index.php` as the filename.
3. Add `.component` to the `.php` files if they are components code; example: `footer.component.php`.
4. Add `.util` to the `.php` files if they are utility codes; example: `account.util.php`.
5. Place Files in their respective folders.
6. Different file naming Cases
   | Naming Case | Type of code         | Example                           |
   | ----------- | -------------------- | --------------------------------- |
   | Pascal      | Utility              | Accoun.util.php                   |
   | Camel       | Components and Pages | index.php or footer.component.php |
8. Renaming of Pages folder names are a must, and relates to what it is doing or data it holding.
9. Use proper label in your github commits: `feat`, `fix`, `refactor` and `docs`

10. Primal Black Market Project Structure:

```
Primal-Black-Market
├── assets
│   ├── css
│   │   ├── primal-about.css
│   │   ├── primal-account.css
│   │   ├── primal-admin.css
│   │   ├── primal-body.css
│   │   ├── primal-cart.css
│   │   ├── primal-login.css
│   │   ├── primal-register.css
│   │   ├── primal-registration.css
│   │   ├── primal-seller-modal.css
│   │   ├── primal-shop.css
│   │   └── homepage.css
│   ├── images
│   │   ├── Logo.png
│   │   ├── heroimg.jpg
│   │   ├── bc.png
│   │   ├── mc.png
│   │   ├── pp.png
│   │   ├── visa.png
│   │   ├── clothing/
│   │   ├── food/
│   │   ├── forging-materials/
│   │   ├── hunting-materials/
│   │   ├── infrastructure/
│   │   ├── pets/
│   │   ├── prehistoric-drugs/
│   │   ├── ritual-artifacts/
│   │   ├── spices-etc/
│   │   ├── user-uploads/
│   │   ├── voodoo/
│   │   └── Weps/
│   └── js
│       ├── primal-about.js
│       ├── primal-account.js
│       ├── primal-admin.js
│       ├── primal-body.js
│       ├── primal-cart.js
│       ├── primal-login.js
│       ├── primal-register.js
│       ├── primal-seller-modal.js
│       ├── primal-shop.js
│       └── main.js
├── components
│   ├── sellerProductModal.component.php
│   └── componentGroup/
│       └── templates/
├── handlers
│   ├── account.handler.php
│   ├── admin.handler.php
│   ├── auth.handler.php
│   ├── cart.handler.php
│   ├── emailCheck.handler.php
│   ├── logout.handler.php
│   ├── mongodbChecker.handler.php
│   ├── postgresChecker.handler.php
│   └── products.handler.php
├── layouts
│   ├── header.php
│   ├── footer.php
│   └── example.layout.php
├── pages
│   ├── about/
│   ├── account/
│   ├── admin/
│   ├── cart/
│   ├── login/
│   ├── logout/
│   ├── register/
│   └── shop/
├── staticData
│   ├── pgTables.staticData.php
│   └── dummies/
├── utils
│   ├── auth.util.php
│   ├── cart.util.php
│   ├── databaseHealthCheck.util.php
│   ├── DatabaseService.util.php
│   ├── dbConnect.util.php
│   ├── dbMigratePostgresql.util.php
│   ├── dbResetPostgresql.util.php
│   ├── dbSeederPostgresql.util.php
│   ├── dbVerifyTables.util.php
│   ├── envSetter.util.php
│   ├── fixCategoriesDatabase.util.php
│   ├── productCard.util.php
│   ├── register.util.php
│   ├── session.util.php
├── database
│   ├── users.model.sql
│   ├── categories.model.sql
│   ├── listings.model.sql
│   ├── transactions.model.sql
│   ├── cart.model.sql
│   └── purchase_history.model.sql
├── docs
│   ├── FULL-NOTES.md
│   ├── Reminder_Seeder-Migrate-Reset.md
│   ├── dummyDatas-Notes.md
│   └── GitSomeHelp.md
├── errors
│   └── example.error.php
├── vendor/
├── .gitignore
├── bootstrap.php
├── composer.json
├── composer.lock
├── index.php
├── readme.md
├── router.php
├── compose.yaml
├── Dockerfile
└── create-upload-directory.sh
```
> Use descriptive names for files and folders. Avoid generic names like `name.js` or `name.css`. Follow the Primal Black Market conventions for all new files and folders.

## Resources

| Title        | Purpose                                                                       | Link          |
| ------------ | ----------------------------------------------------------------------------- | ------------- |
| ChatGPT | General AI assistance for planning, learning concepts, and gather ideas. | https://chat.openai.com |
| GitHub Copilot | In-IDE code suggestions and boilerplate generation. | https://github.com/features/copilot |
| Professor's Sample | Used for reference, understanding concepts and logical flow. | https://github.com/zyx-0314/hands-on |
| Ian Ramirez | Course and Projecr Adviser | https://github.com/zyx-0314 |
