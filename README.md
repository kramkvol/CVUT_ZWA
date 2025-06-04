# Book Catalog Web Application
This is a web-based application for managing a catalog of books with user registration, login, profiles, and comment functionality. The system supports both regular users and administrators.

https://zwa.toad.cz/~kramkvol/

[Produkt dokumentation](https://github.com/kramkvol/CVUT_ZWA/blob/main/documentation/Product%20documentation.pdf)

## Features
### Authentication & Authorization
- **Registration:** Users can register with a unique nickname and email. Password confirmation is required.
- **Login:** Users log in with a nickname and password.
- **Roles:** 
  - **User:** Can comment and edit their profile and books they've added.
  - **Administrator:** Can edit and delete any book or comment.

### Navigation
- If **logged in**, visible sections: `Home`, `Profile`, `Logout`.
- If **not logged in**, visible sections: `Home`, `Registration`, `Login`.

### User Profile (`profile.php`)
- View personal details: name, role, avatar.
- Edit nickname, email, avatar.

### Book List (`home.php`)
- Publicly accessible.
- Alphabetically sorted list of books (title, description, cover, authors).
- Pagination included.
- Clicking on a book opens its details page.

### Book Page (`bookpage.php`)
- Book information: title, description, cover, authors.
- User comments: includes avatar, name, comment text, date.
- Authenticated users can:
  - Add comments.
  - Edit their own comments.
  - Administrators can delete any comment.

### Book Management
- **Add Book (`bookadd.php`):** Authenticated users only. Redirects to editing page after checking for duplicates.
- **Edit Book (`bookedit.php`):** Only the user who added the book or an administrator can access. Editable fields include name, description, cover, authors.

## Access Control
- Unauthorized users trying to access restricted pages are redirected to `login.php`.
- Unauthorized actions (e.g., editing without login) will not display the relevant UI elements (buttons/messages instead).


##  Files Overview
###  Main Pages
- **index.php** – Homepage displaying the list of books.
- **bookpage.php** – Detailed view of a selected book.
- **bookadd.php** – Form for adding a new book to the library.
- **bookeditpage.php** – Page for editing an existing book's details.

###  Authentication and User Profile
- **register.php** – User registration form and logic.
- **login.php** – Login form and session handling.
- **logout.php** – Logs the user out and ends the session.
- **profile.php** – View and edit user profile information.

###  Common Components
- **header.php** – Website header and navigation menu.
- **about.php** – Information about the project or application.

###  Static and Helper Files
- **styles.css** – Core website styling.
- **.gitattributes** – Git attributes settings for version control.

###  Directories
#### `parts/` – Reusable page components and configuration files
- **bookcomments.php** – Displays and handles user comments for a book. Used on the book detail page.
- **bookedit.php** – Contains the form layout for editing book details. Included in `bookeditpage.php`.
- **bookhead.php** – Outputs the book title/header section, typically used on book cards or detail pages.
- **check.php** – Backend script for handling AJAX requests to validate username and email availability during registration or profile editing.
- **config.php** – Main configuration file for the project (e.g., database connection settings).
- **errors.php** – Displays error messages, such as form validation or server errors.
- **footer.php** – Website footer with copyright or links.
- **header.php** – Website header and main navigation, included on most pages.
- **pagination.php** – Component for rendering pagination links in lists (e.g., books).
- **scripts.js** – JavaScript for client-side validation:
  - Real-time validation of username and email (via AJAX to `check.php`)
  - Password match checks
  - Live feedback during registration, login, and profile editing

#### `functions/` – PHP logic helpers grouped by feature
- **f_bookadd.php** – Functions for adding books to the database.
- **f_bookcomments.php** – Logic for processing and saving book comments.
- **f_bookedit.php** – Functions for updating book records.
- **f_database.php** – Database connection and utility functions.
- **f_home.php** – Logic related to the homepage.
- **f_login.php** – User login logic and session management.
- **f_pagination.php** – Page splitting and navigation logic.
- **f_profile.php** – View and update user profile data.
- **f_reg.php** – User registration handler.
- **f_validate.php** – Input validation functions.

#### `images/` – Static images used on the site
_(Example: book covers, logos, UI icons)_

#### `documentation/` – Project documentation and technical notes
_(Could include setup instructions, architecture description, etc.)_

## Example User Profiles
You can use the following accounts to log in:
### Administrator
- **Nickname:** `admin`
- **Password:** `test`
### Regular User
- **Nickname:** `test1`
- **Password:** `test`

## Repository
[GitHub Project](https://github.com/kramkvol/CVUT_ZWA)
