# Island Rush 2.5

K2.5 is a rewritten and refactored K2

- Server Side Architecture, 100% server side checks

- Separated Frontend / Backend Directories & File Structure

- Optimized SQL Queries and Table Structures (minimized)

- Refactored Logic for Battles and other Game Rules

- Unified player games (No more spectator dependencies)

- Combined all board dependencies (load, client, ajax)

- DOM Caching and other efficient methods

INSTRUCTIONS

The main directory should be hosted on a network (Using apache, or some web hosting with php installed)
The hosting service should also point users to the home.php page upon arrival to the site.

A MySQL Server must also be running, the database needs to be initilized using the backend/sql/db_create.sql
Modify the backend/sql/db_create.sql to insert the games for each teacher / section (follow the example for 'm1a1'-'adolph')
Note: Password hashes are MD5.
This file should be run once to insert the games and create the database tables.

Modify the backend/db.php file to point to the MySQL server. (hostname = server address)

To begin playing, navigate to the homepage through the hosting service. (ex: www.island-rush.com)
Each game must be initialized by logging into the admin, and pressing the Game Reset button.
Each game must also be set to 'active' through the admin page (when ready for gameplay).
