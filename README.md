# Book Catalog Web Application
This is a web-based application for managing a catalog of books with user registration, login, profiles, and comment functionality. The system supports both regular users and administrators.

https://zwa.toad.cz/~kramkvol/

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

## Files Overview
- `header.php`: Page header and navigation.
- `register.php`: Registration form and logic.
- `login.php`: Login form and logic.
- `profile.php`: User profile view and edit.
- `index.php`: Main book list.
- `bookpage.php`: Book detail page.
- `bookedit.php`: Edit book page.
- `bookadd.php`: Add new book page.

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
[GitLab Project](https://gitlab.fel.cvut.cz/kramkvol)
