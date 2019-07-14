# Island Rush 2

- Server Side Architecture, 100% server side checks

- Separated Frontend / Backend Directories & File Structure

- Optimized SQL Queries and Table Structures (minimized)

- Refactored Logic for Battles and other Game Rules

- Unified player games (No more spectator dependencies)

- Combined all board dependencies (load, client, ajax)

- DOM Caching and other efficient methods

------------

## INSTRUCTIONS (slightly outdated)

------------

# For sandbox network deployment (your own equipment)
- Recommend running XAMPP
- This comes with Apache and MySQL servers
- Load the repo into the htdocs folder generated by xampp (recommend complete clearing out of default files generated)
- Change the configurations / settings for apache to point to the home.php (may already be a default)
- Use MySQL Workbench to connect to the localhost msql server running
- Edit / Run the db_create script here. (put the games in)
- Configure the backend/db.php to point to the localhost mysql server
- Test by going to localhost on Chrome on the machine running XAMPP
- Test by going to the machine's ip on the sandbox network (still through chrome) when on another computer
- Play around by logging into the admin pages and clicking the 'reset-game' button (this puts initial pieces on the board)
- Test logging into a game and moving pieces around, changing phases, other game mechanics...
- Optional: Configure Apache to use custom DNS or set up custom DNS servers on the sandbox network (MaraDNS)
- Optional: Configure Apache to use SSL certs for browsers to trust the site (may not be an issue on sandbox networks)

------------

# For AZURE deployment
- Recommend using a web-app
- Use the deployment settings / slots to connect the web-app to the online repo (bitbutcket...etc)
- Use Azure's settings to set up Custom Domains, SSL, Auto-Scaling, External Session Storage (recommended for this game)
- Use Azure's MySQL server to host the database (this will come with it's own login, put this into db.php)
- Use Azure's VM to connect to the database to run commands (this worked best for us, could alternatively set up fully external access)
- All of these will (should) be on the same v-net to connect to each other
- If issues occur with web-app, can always fall back to installing XAMPP (Apache) on the Azure VM and still use the external DB
- Note: Azure provides simple dns records to connect (ex: islandrush.azurewebsites.net)
- We had issues with php sessions getting lost between instances, could resolve this with external session store

--------------

- Environment Variables to know / use
  - CD_LASTNAME -> CourseDirector Lastname
  - CD_PASSWORD -> MD5 hash of CourseDirector Password
  - DB_HOSTNAME -> Database host
  - DB_USERNAME -> Database username
  - DB_PASSWORD -> Database password
  - DB_NAME -> Database name (usually 'islandRushDB')

-------------
Basically:

- Files need to be hosted on a network (files = repository)
- Database accessible (use db_create) through that network (local or external)
- Have a good understanding of PHP, Javascript, HTML, CSS, MySQL and Networking (web-hosting)

# TODO

- Fix piece dragging and popups timing to be more stable.
- Add wiki type website to explain the game / rules. (in progress)
- Add credits page next to the troubleshooting page / Improve troubleshooting.
- Safety checks for environment variables missing from deployment
