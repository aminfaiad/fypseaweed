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


def send_fcm_notification(fcm_token, title, body):
    """
    Sends an FCM notification to a specific token.
    
    :param fcm_token: The recipient's FCM token
    :param title: Notification title
    :param body: Notification body
    """
    try:
        # Create a message
        message = messaging.Message(
            notification=messaging.Notification(
                title=title,
                body=body,
            ),
            token=fcm_token,
        )
        
        # Send the message
        response = messaging.send(message)
        print(f"Successfully sent message: {response}")
    except Exception as e:
        print(f"Error sending message: {e}")
# Create a cursor object
cursor = connection.cursor(prepared=True)  # Enable prepared statements
select_query = "SELECT * FROM users;"
#cursor.execute(select_query, ("value1",))
cursor.execute(select_query)
# Fetch and display results
result = cursor.fetchall()


send_fcm_notification("cdtIXfCoR22e2DZsCJaof2:APA91bGeozHFJb7CLV9gdlxMOPW1DTWZqE8jf7bjivAnyxN8G-IZ8DJV4onAqZjcxB4CHUbx1W7Zmug46pD_3wcdVUqn5sIzUFmZX4mWgMWn5jKawGuJSA0","test","test")