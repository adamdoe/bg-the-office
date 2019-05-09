# Copper Helper Class

To install this repository:

```
# Installation

1. Create an account at Copper. Under preferences > api keys click "generate a new API key".
2. Save the email and api key somewhere safe.
3. Clone the repository.
4. Change the .env.example file to .env and enter your credentials you saved.
4. Run Composer Install.
5. Navigate to the projects URL.
```

### Improvements
This is just a testing repository. More improvements could be made to make this project cleaner, such as moving the class to it's own file, creating tests, and better data validation.


### Behavior
Since the script is in a single file and doesnt have the above improvements, the script attempts to complete all tasks each time the script is loaded (creating a person, updating a person, and assigning a person an opportunity). In a real world situation this would be handled differently.

The script will print the response to your screen.
