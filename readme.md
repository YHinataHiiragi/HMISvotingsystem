# 1. Download and install XAMPP (For Local Host)

[XAMPP DOWNLOAD](https://www.apachefriends.org/download.html)

# 2. Open XAMPP 

- Start Apache and MySQL service 

# 3. Installation of HMISvotingsystem

- Copy the HMISvotingsystem folder and paste it on xampp/htdocs/
- On your browser, go to localhost/phpmyadmin
- Click 'New' and click 'Import' 
- Select the HMISvotingsystem.sql in the HMISvotingsystem folder, then import.

# 4. Go to localhost/HMISvotingsystem
- Username: admin Password: adminpassword

# Reminders
- To change username and password. Go to localhost/phpmyadmin. Select the 'hmisvoting' database. And go to 'users' table. Take note that you need to make your password in hash. 
- RoleID 1 and 2: 'RoleID 1' are for the users/students. While 'RoleID 2' are the admins. You can create more admin users by inserting user with RoleID 2. 
- To integrate this system, you can use webhosting or use a localhost to connect every pc on the same network. 

- https://www.youtube.com/watch?v=PuSopHMbyNs (local network method)
- https://www.youtube.com/watch?v=agtcl_I_y7s (How to change password for phpmyadmin) -> configure the config file from HMISvotingsystem/src/configuration/config.php


