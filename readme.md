# TO DO

### Main Requirements 
1. ~~Import a CSV file into a SQLite DB named transactions, representing a typical monthly bank statement.~~
    - ~~Create an SQLite DB~~
    - ~~Create a table in the DB called 'transactions'~~
        - ~~column names are: transaction_date, description, debit, credit, balance~~
    - ~~Load the data from the csv into the DB~~  - *Completed* - Brett

2. ~~Create a Buckets table to help classify the expenses of the user.~~
    ~~- The table has 2 columns: category and the description(from the transactions table)~~
    ~~- Create a filter table containing %Like% words for the bucket categories.~~
    ~~- If the description of the transaction matches a category, add it to the buckets table~~ - *Compelted* - Brett

3. ~~Create a Table called Reports. This table will organzie the information from the buckets table.~~ ~~This table will be used to generate a report and to create a visual representation of the expenses.~~
    ~~- The table has 2 columns: category (matching from buckets), and total (a derrived value from summing up the number of each associated category)~~ - *Completed* - Brett

##

### Additional Requirements
1. ~~User's must be able to upload their own CSV file.~~
    ~~- Verify the file extension~~
    ~~- after success, rename the file extension to "file_name.imported"~~ - *Completed* - Brett

2. ~~The Transaction table must have Add, Edit, and Delete capabilities.~~

3. ~~The Buckets Table must have Add, Edit, and Delete capabilities.~~

4. ~~There must be a registeration page to allow users to register with a username and password~~
    ~~- Create a new DB called Users~~
    ~~- Create a new table called admin - admin will have 2 columns, username and password. An admin must be loaded upon creation of the table. The username is aa@aa.aa and password is P@$$w0rd.~~
    ~~- create a table called user. ~~
    ~~- user has 3 columns: username, password, approved(a boolean, 0 or 1).~~
    ~~- Only an Admin can approve(update) a user.~~ - *Completed* Brett
    - ~~User must have Add, Edit, and Delete capabilities.~~
    - Edge Case - username already exists.

5. ~~Users must be logged in to access the web app. Each page except the Log-in and Registration page must have authentication.~~
    ~~- Use session objects for this. We don't need cookies.~~ *Completed* - Brett

6. ~~Create a footer on the home page the displays the team names and student numbers.~~ - *Completed* - Brett

7. ~~Add JSChart pie chart to show transactions filtered by year~~ *Completed* - Brett

8. Once the above is complete, deploy the web app to Azure.

##

### When Handing In The Assignment
Put the following information into the learning-hub (D2L) as you upload your solution:
- The URL of your app deployed to Azure.
- your names, BCIT ID numbers and your preferred email addresses. Avoid your my.bcit.ca email
account because it has file attachment restrictions. This is necessary in case the assignment
marker wishes to urgently contact you.
- what you have NOT completed
- any major challenges
- any special instructions for testing your web app
- .zip the file when handing in to D2L
