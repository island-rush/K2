# Island Rush 2.5

K2.5 is a rewritten and refactored K2

- Server Side Architecture, 100% server side checks

- Separated Frontend - Backend Directories & File Structure

- Optimized SQL Queries and Table Structures (minimized)

- Refactored Logic for Battles and other Game Rules

- Unified player games (No more spectator dependencies)

- Combined all board dependencies (load, client, ajax)

- DOM Caching

INSTRUCTIONS FOR SET UP

Run xampp with apache and sql

Using workbench (or another sql ide) connect to the sql and run db_create.sql script.
This will create the database and set up some backend.

Place the repository into the htdocs folder created by xampp
this is usually located in the C:/xampp/

with xampp on, you should be able to use GOOGLE CHROME

localhost/foldername

and game should show up

must log in to admin in order to "generate" the game
log in, press the reset button

activating and deactivating the game will force all players to log out if that becomes an issue
