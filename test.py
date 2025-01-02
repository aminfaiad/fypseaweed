import mysql.connector
import firebase_admin
from firebase_admin import credentials, messaging

# Initialize the Firebase Admin SDK
cred_obj = credentials.Certificate('seaweedfyp-firebase-adminsdk-ky1sy-0eaca81589.json')
default_app = firebase_admin.initialize_app(cred_obj)


host = "localhost"  # or the IP address of your MySQL server
user = "seaweedadmin"
password = "SeaweedFarmingIot2024"
database = "seaweed_farming"
try:
    # Establish a connection
    connection = mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database
    )

    if connection.is_connected():
        print("Connected to MySQL database")

except mysql.connector.Error as e:
    print(f"Error connecting to MySQL: {e}")



# Create a cursor object
cursor = connection.cursor(prepared=True)  # Enable prepared statements
select_query = "SELECT * FROM users;"
#cursor.execute(select_query, ("value1",))
cursor.execute(select_query)
# Fetch and display results
result = cursor.fetchall()
