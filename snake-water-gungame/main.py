import random

'''
1 for snake
-1 for water 
0 for gun
'''

computer = random.choice([-1, 0, 1])
youstr = input("Enter your choice (s for snake, w for water, g for gun): ")

# Fix the dictionary syntax
youDict = {'s': 1, 'w': -1, 'g': 0}
reverseDict = {1: "Snake", -1: "Water", 0: "Gun"}

# Use .get() to handle invalid inputs gracefully
you = youDict.get(youstr)

# Check if the user's choice is valid
if you is None:
    print("Invalid choice! Please enter 's', 'w', or 'g'.")
else:
    print(f"You chose {reverseDict[you]}\nComputer chose {reverseDict[computer]}")

    if computer == you:
        print("It's a draw!")
    else:
        if computer == -1 and you == 1:
            print("You win!")
        elif computer == -1 and you == 0:
            print("You lose!")
        elif computer == 1 and you == -1:
            print("You lose!")
        elif computer == 1 and you == 0:
            print("You win!")
        elif computer == 0 and you == -1:
            print("You win!")
        elif computer == 0 and you == 1:
            print("You lose!")
        else:
            print("Something went wrong!")
