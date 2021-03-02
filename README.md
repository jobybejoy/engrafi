## To install and run the application

To install dependencies
```
composer update
```

To migrate the database ( make sure the correct db credentials in .env)
```
php artisan migrate
```

Create the OAuth keys
```
php artisan passport:install
```



## Note Demo Login

Have to create a user with (App accepts only @karunya.edu.in [ *role: Student* ])
username: jobyvarghesebejoy@karunya.edu.in
password: joby1234

Admin User with ( @karunya.edu [ Faculty ] )
username: admin_engrafi@karunya.edu
password: admin1234

HOD User with ( @karunya.edu [ HOD ] )
username: hod_cse@karunya.edu
password: hod_cse1234

Program Coordinator User with ( @karunya.edu [ Program Coordinator  ] )
username: program_coordinator@karunya.edu
password: **program_coordinator@karunya.edu1234**

Program Coordinator User with ( @karunya.edu [ Faculty ] )
username: faculty@karunya.edu
password: faculty1234


Requires the following users to have the app functional 
Student, Faculty, Program Coordinator, HOD, Admin  


##Event Approval Flow 

- **Faculty** Create a request to Host the event 
  - It goes out for approval to Program Coordinator and Head of Department
- **Program Coordinator** gets the event details
  - Aproves / Denies the event
  - Forwards the event to Department HOD
- **HOD** recieve the event details
  - Approves / Denies the event
  - Event get created.


  Only faculty who created the event can Drop registration
