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
#### `functions/` – PHP logic helpers grouped by feature
#### `images/` – Static images used on the site

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
